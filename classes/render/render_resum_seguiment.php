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
 * Renderers for outputting fct resum seguiment.
 *
 * @package    mod
 * @subpackage fct
 * @copyright  2014 Institut Obert de Catalunya
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class mod_fct_resum_seguiment_renderer extends plugin_renderer_base {

    public $resum;

    public function view($resum, $total_hores, $total_dies) {

        $this->resum = $resum;


        foreach ($resum as $any => $resum_any) {
            foreach ($resum_any as $trimestre => $resum_trimestre) {
                echo $this->mostrar_resum_trimestre($any, $trimestre);
            }
        }
        $duradatotal = '';
        $duradatotal .= html_writer::start_div('notifyproblem');
        $duradatotal .= get_string('durada_total_practiques', 'fct', array('dies' => $total_dies, 'hores' => $total_hores));
        $duradatotal .= html_writer::end_div();
        echo $duradatotal;
    }

    public function mostrar_resum_trimestre($any, $trimestre) {

        $data = array();
        $table = new html_table();
        $table->head = array($this->nom_trimestre($trimestre) .' ' . $any, get_string('dies', 'fct'), get_string('hores', 'fct'));
        $dies = 0;
        $hores = 0;
        for ($mes = $trimestre * 3; $mes < $trimestre * 3 + 3; $mes++) {
            if (isset($this->resum[$any][$trimestre][$mes])) {
                $record = $this->resum[$any][$trimestre][$mes];
                $data[] = (array($this->nom_mes($mes), $record->dies, $record->hores));
                $dies += $record->dies;
                $hores += $record->hores;
            } else {
                $data[] = array($this->nom_mes($mes), 0, 0);
            }
        }
        $data[] = (array('<strong>' . fct_string('total') . '</strong>', "<strong>$dies</strong>", "<strong>$hores</strong>"));

        $table->data = $data;

        $table->attributes['class'] = 'resumseguiment generaltable';
        return html_writer::table($table);
    }

    public function make_resum_line() {

    }

    private function nom_mes($mes) {
        $time = mktime(0, 0, 0, $mes + 1, 1, 2000);
        return strftime('%B', $time);
    }

    private function nom_trimestre($trimestre) {
        return get_string('trimestre_' . ($trimestre + 1), 'fct');
    }

}
