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
 * Renderers for outputting fct qualificacio.
 *
 * @package    mod
 * @subpackage fct
 * @copyright  2014 Institut Obert de Catalunya
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

class mod_fct_quadern_qualificacio_renderer extends plugin_renderer_base {

    public function view($quadern) {

        $output = '';

        $qualificacions = $quadern->qualificacions();
        $barems = $quadern->barem_valoracio();
        if ($quadern->apte) {
            $apte = $qualificacions[$quadern->apte];
        } else {
            $apte = '';
        }
        if ($quadern->nota) {
            $nota = $quadern->barem_valoracio()[(int)$quadern->nota];
        } else {
            $nota = '';
        }

        $output .= html_writer::start_div('datagroup');
        $output .= html_writer::tag('span', get_string('qualificacio', 'fct').':', array('class' => 'datatitle'));
        $output .= html_writer::tag('span', $apte, array('class' => 'datacontent'));
        $output .= html_writer::end_div();

        $output .= html_writer::start_div('datagroup');
        $output .= html_writer::tag('span', $nota, array('class' => 'datacontent'));
        $output .= html_writer::end_div();

        $output .= html_writer::start_div('datagroup');
        $output .= html_writer::tag('span', get_string('data', 'fct').':', array('class' => 'datatitle'));

        if (isset($quadern->data)) {
            $output .= html_writer::tag('span', userdate($quadern->data, get_string('strftimedate')), array('class' => 'datacontent'));
        }
        $output .= html_writer::end_div();

        $output .= html_writer::start_div('datagroup');
        $output .= html_writer::tag('span', get_string('observacions', 'fct').':', array('class' => 'datatitle'));
        $output .= html_writer::tag('span', $quadern->observacions, array('class' => 'datacontent'));
        $output .= html_writer::end_div();

        $cm = get_coursemodule_from_instance('fct', $quadern->fct);
        $link = new moodle_url('./edit.php', array('cmid'=>$cm->id, 'quadern' => $quadern->id, 'page' => 'quadern_valoracio', 'subpage' => 'quadern_qualificacio'));
        $output .= html_writer::link($link, get_string('edit'));


        echo $output;

    }

}