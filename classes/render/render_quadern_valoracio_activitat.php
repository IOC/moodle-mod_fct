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
 * Renderers for outputting fct activitat valoracio.
 *
 * @package    mod
 * @subpackage fct
 * @copyright  2014 Institut Obert de Catalunya
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class mod_fct_quadern_valoracio_activitat_renderer extends plugin_renderer_base {

    public function view($activitats) {

       global $PAGE;

       $output = '';

       $barems = $this->barem_valoracio();

        // $barem = $barems[0];
        $output .= html_writer::start_tag('div', array('class' => 'fcontainer clearfix'));
        //$valoraciotype = 'valoracio_'.$valoracio->valoracio;
        //$valoracions = $valoracio->$valoraciotype;

        foreach ($activitats as $activitat) {

            /*if (isset($valoracions) && !empty($valoracions)) {
                if (is_array($valoracions)) {
                    $barem = $barems[$valoracions[$key]];
                } else {
                    $barem = $barems[$valoracions->$key];
                }
            }*/

            $output .= html_writer::start_tag('div', array('class' => 'fitem'));
            $output .= html_writer::tag('div', $activitat->descripcio, array('class' => 'fitemtitle'));
            $output .= html_writer::tag('div', $barems[$activitat->nota], array('class' => 'felement fstatic'));
            $output .= html_writer::end_tag('div');
        }


        $link = new moodle_url('./edit.php', array('cmid'=>$PAGE->cm->id, 'page' => 'quadern_valoracio_activitat'));
        $output .= html_writer::link($link, get_string('edit'));
        $output .= html_writer::end_tag('div');

        echo $output;

    }

    public function barem_valoracio() {
        return array(
            0 => '-',
            1 => get_string('barem_a', 'fct'),
            2 => get_string('barem_b', 'fct'),
            3 => get_string('barem_c', 'fct'),
            4 => get_string('barem_d', 'fct'),
            5 => get_string('barem_e', 'fct'),
        );
    }

}
