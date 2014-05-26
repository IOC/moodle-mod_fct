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
 * Fct json class.
 *
 * @package    mod
 * @subpackage fct
 * @copyright  2014 Institut Obert de Catalunya
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


class fct_json {

    private static function copy($source, $dest, $include=false, $exclude=array()) {
        if (!$include) {
            $include = array_keys((array) $dest);
        }
        foreach ($source as $key => $value) {
            if (in_array($key, $include) and !in_array($key, $exclude)) {
                $dest->$key = $value;
            }
        }
    }

    public static function deserialitzar_activitat($json) {
        $objecte = json_decode($json, true);
        $activitat = new fct_activitat;
        self::copy($objecte, $activitat);
        return $activitat;
    }

    public static function deserialitzar_avis($json) {
        $objecte = json_decode($json, true);
        $avis = new fct_avis;
        self::copy($objecte, $avis);
        return $avis;
    }

    public static function deserialitzar_cicle($json) {
        $objecte = json_decode($json, true);
        $cicle = new fct_cicle($objecte->fct);
        self::copy($objecte, $cicle);
        return $cicle;
    }

    public static function deserialitzar_fct($json) {
        $objecte = json_decode($json, true);
        $fct = new fct;
        self::copy($objecte, $fct, false, array('centre'));
        self::copy($objecte['centre'], $fct->centre);
        return $fct;
    }

    public static function deserialitzar_quadern($json) {
        $objecte = json_decode($json, true);
        $quadern = new fct_quadern;
        self::copy($objecte, $quadern, false,
                      array('convenis', 'dades_alumne', 'empresa',
                            'qualificacio', 'qualificacio_global'));
        foreach ($objecte['convenis'] as $objecteconveni) {
            $conveni = new fct_conveni;
            self::copy($objecteconveni, $conveni, false, array('horari'));
            foreach ($objecteconveni['horari'] as $objectefranja) {
                $franja = new fct_franja_horari($objectefranja['dia'],
                                                $objectefranja['hora_inici'],
                                                $objectefranja['hora_final']);
                $conveni->afegir_franja_horari($franja);
            }
            $quadern->afegir_conveni($conveni);
        }
        self::copy($objecte['dades_alumne'], $quadern->dades_alumne);
        self::copy($objecte['empresa'], $quadern->empresa);
        self::copy($objecte['qualificacio'], $quadern->qualificacio);
        self::copy($objecte['qualificacio_global'],
                      $quadern->qualificacio_global);
        return $quadern;
    }

    public static function deserialitzar_quinzena($json) {
        $objecte = json_decode($json, true);
        $quinzena = new fct_quinzena;
        self::copy($objecte, $quinzena);
        return $quinzena;
    }

    public static function serialitzar_activitat($activitat) {
        return json_encode($activitat);
    }

    public static function serialitzar_avis($avis) {
        return json_encode($avis);
    }

    public static function serialitzar_cicle($cicle) {
        return json_encode($cicle);
    }

    public static function serialitzar_fct($fct) {
        return json_encode($fct);
    }

    public static function serialitzar_quadern($quadern) {
        return json_encode($quadern);
    }

    public static function serialitzar_quinzena($quinzena) {
        return json_encode($quinzena);
    }
}
