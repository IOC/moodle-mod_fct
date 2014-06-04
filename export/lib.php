<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * FCT export class
 *
 * @package    mod
 * @subpackage fct
 * @copyright  2014 Institut Obert de Catalunya
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once($CFG->dirroot . '/mod/fct/classes/fct_dades_centre.php');
require_once($CFG->dirroot . '/mod/fct/classes/fct_activitat.php');

class fct_export {

    private $format;
    //private $diposit;
    //private $seveis;
    private $baremvaloracio;
    private $baremqualificacio;

    public function __construct() {
        //$this->diposit = new fct_diposit();
        //$this->serveis = new fct_serveis($this->diposit);

        $this->baremvaloracio = array(
            0 => '-',
            1 => fct_string('barem_a'),
            2 => fct_string('barem_b'),
            3 => fct_string('barem_c'),
            4 => fct_string('barem_d'),
            5 => fct_string('barem_e'),
        );

        $this->baremqualificacio = array(
            0 => '-',
            1 => fct_string('apte'),
            2 => fct_string('noapte'),
        );
    }

    public function dades_generals_html($id) {
        $this->format = 'html';

        $doc = $this->template('dades_generals');

        $quadern = $this->diposit->quadern($id);
        $cicle = $this->diposit->cicle($quadern->cicle);
        $fct = $this->diposit->fct($cicle->fct);
        $horesrealitzades = $this->serveis->hores_realitzades_quadern($quadern);
        $resumhores = $this->serveis->resumhores_fct($quadern);

        $doc = $this->subst($doc, (object) array(
            'quadern' => $quadern,
            'fct' => $fct,
            'cicle' => $cicle,
            'hores_realitzades' => $horesrealitzades,
            'hores_practiques_pendents' => max(0, $quadern->hores_practiques - $horesrealitzades),
            'hores_realitzades_detall' => fct_string('hores_realitzades_detall', $resumhores),
            'hores_anteriors' => $resumhores->anteriors,
            'hores_pendents' => $resumhores->pendents,
        ));

        return $doc;
    }

    public function quadern_latex($id) {
        $this->format = 'latex';

        $doc = $this->template('quadern');

        $quadern = new fct_quadern_base($id);
        $cicle = new fct_cicle((int)$quadern->cicle);
        $fct = new fct_dades_centre($quadern->fct);
        $activitats = fct_quadern_activitat::get_records($id);
        $quinzenes = fct_quadern_quinzena::get_records($id);
        //print_object($quadern);die;

        //$quadern = $this->diposit->quadern($id);
        //$cicle = $this->diposit->cicle($quadern->cicle);
        //$fct = $this->diposit->fct($cicle->fct);
        //$activitats = $this->diposit->activitats($id);
        //$quinzenes = $this->diposit->quinzenes($id);
        $filter = function ($a) {
            return $a->id;
        };
        $idsactivitats = array_map($filter, $activitats);
        foreach ($quinzenes as $q) {
            $q->activitats = array_intersect($q->activitats, $idsactivitats);
        }
        //$horesrealitzades = $this->serveis->hores_realitzades_quadern($quadern);
        //$ultimquadern = $this->serveis->ultim_quadern($quadern->alumne, $quadern->cicle);
        //$resumhores = $this->serveis->resum_hores_fct($quadern);
        $horesrealitzades = 0;
        $ultimquadern = new stdClass;
        $ultimquadern->id = 0;
        $resumhores = new stdClass;
        $resumhores->anteriors = 0;
        $resumhores->pendents = 0;
        $doc = $this->subst($doc, (object) array(
            'quadern' => $quadern,
            'fct' => $fct,
            'cicle' => $cicle,
            'activitats' => $activitats,
            'quinzenes' => $quinzenes,
            'lloc_practiques' => (
                trim($quadern->empresa->nom) or
                trim($quadern->empresa->adreca) or
                trim($quadern->empresa->poblacio) or
                trim($quadern->empresa->codi_postal) or
                trim($quadern->empresa->telefon)
            ),
            'hores_realitzades' => $horesrealitzades,
            'hores_practiques_pendents' => max(0, $quadern->hores_practiques - $horesrealitzades),
            'hores_realitzades_detall' => fct_string('hores_realitzades_detall', $resumhores),
            'hores_anteriors' => $resumhores->anteriors,
            'hores_pendents' => $resumhores->pendents,
            'ultim_quadern' => ($ultimquadern->id == $id),
        ));

        return $doc;
    }

    private function escape($value) {
        switch ($this->format) {
            case 'latex':
                $map = array(
                    '\\' => '\textbackslash{}',
                    '{' => '\{',
                    '}' => '\}',
                    '#' => '\#',
                    '$' => '\$',
                    '%' => '\%',
                    '&' => '\&',
                    '^' => '\textasciicircum{}',
                    '_' => '\_',
                    '~' => '\textasciitilde{}'
                );
                $callback = function($matches) use ($map) {
                    return $map[$matches[1]];
                };
                $pattern = '/(' . implode('|', array_map('preg_quote', array_keys($map))) . ')/';
                return preg_replace_callback($pattern, $callback, (string) $value);
            case 'html':
                return s((string) $value);
            default:
                return (string) $value;
        }
    }

    private function filter($value, $type=null) {
        global $DB;

        switch ($type) {
            case 'actitud':
                return fct_string('actitud_' . ($value + 1));
            case 'activitat':
                //$activitat = $this->diposit->activitat($value);
                $activitat = (object) array('descripcio' => 'PROVA');
                return $activitat->descripcio;
            case 'count':
                return count($value);
            case 'data':
                return (int) $value ? strftime('%e / %B / %Y', (int) $value) : '-';
            case 'estat':
                return fct_string('estat_' . $value);
            case 'float':
                return (float) $value;
            case 'exempcio':
                return $value ? $value . '%' : '-';
            case 'hora':
                $hora = floor($value);
                $minuts = round(($value - floor($value)) * 4) * 15;
                return sprintf("%d:%02d", $hora, $minuts);
            case 'hores':
                $minuts = round($value * 60);
                $hores = floor($minuts / 60);
                $minuts = $minuts % 60;
                return sprintf('%d %s %d %s', $hores, fct_string('hores_i'),
                                $minuts, fct_string('minuts'));
            case 'list':
                return implode(', ', $value);
            case 'periode':
                $mes = floor((int) $value->periode / 2);
                $dies = (($value->periode % 2 == 0) ? '1-15' :
                         '16-' . cal_days_in_month(CAL_GREGORIAN, $mes + 1, $value->any));
                $time = mktime(0, 0, 0, $mes + 1, 1, 2000);
                return $dies . ' ' . strftime('%B', $time);
            case 'qualificacio':
                return $this->baremqualificacio[$value];
            case 'resultat':
                return fct_string('resultat_aprenentatge_' . $value);
            case 'string':
                return $value ? fct_string($value) : '';
            case 'usuari':
                $user = $DB->get_record('user', array('id' => (int) $value));
                return $user ? $user->firstname . ' ' . $user->lastname : '';
            case 'valoracio':
                return $this->baremvaloracio[$value];
            default:
                return $value;
        }
    }

    private function get_value($values, $name) {
        $value = $values;
        foreach (explode(':', $name) as $key) {
            $key = str_replace('-', '_', $key);
            if (!is_object($value) or !isset($value->$key)) {
                return null;
            }
            $value = $value->$key;
        }
        return $value;
    }

    private function subst($doc, $values) {
        $doc = $this->subst_strings($doc);
        $doc = $this->subst_blocks($doc, $values);
        return $this->subst_vars($doc, $values);
    }

    private function subst_strings($doc) {
        $pattern = '/##([a-z0-9-]+)/';
        $export = $this;
        $callback = function($matches) use ($export) {
            $identifier = str_replace('-', '_', $matches[1]);
            return $export->escape(fct_string($identifier));
        };
        return preg_replace_callback($pattern, $callback, $doc);
    }

    private function subst_vars($doc, $values) {
        $pattern = '/@@([a-z:-]+)(?:\|([a-z]+))?(?:@@)?/';
        $export = $this;
        $callback = function($matches) use ($export, $values) {
            $value = $export->get_value($values, $matches[1]);
            if ($value !== null) {
                $filter = isset($matches[2]) ? $matches[2] : null;
                return $export->escape($export->filter($value, $filter));
            } else {
                return $matches[0];
            }
        };
        return preg_replace_callback($pattern, $callback, $doc);
    }

    private function subst_blocks($doc, $values) {
        $pattern = '/\\{\{ *(if|loop) +([a-z:-]+) *\}\}(.*?)\{\{ *end\1 +\2 *\}\}/s';
        $export = $this;
        $callback = function($matches) use ($export, $values) {
            $result = '';
            $value = $export->get_value($values, $matches[2]);
            if ($matches[1] == 'if') {
                if ($value) {
                    $result = $export->subst($matches[3], $values);
                }
            } else if ($matches[1] == 'loop' and is_array($value)) {
                foreach ($value as $key => $value) {
                    $value = array('key' => $key, 'value' => $value);
                    $result .= $export->subst($matches[3], (object) $value);
                }
            } else {
                $result = '\begin{verbatim}' . $matches[0] . '\end{verbatim}';
            }
            return $result;
        };
        return preg_replace_callback($pattern, $callback, $doc);
    }

    private function template($name) {
        global $CFG;
        $ext = $this->format == 'latex' ? 'ltx' : 'html';
        return file_get_contents("$CFG->dirroot/mod/fct/export/{$name}.{$ext}");
    }
}