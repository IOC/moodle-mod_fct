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
 * Renderers for outputting fct quadern alumne.
 *
 * @package    mod
 * @subpackage fct
 * @copyright  2014 Institut Obert de Catalunya
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class mod_fct_quadern_alumne_renderer extends plugin_renderer_base {

    public function view($quadern) {

        global $DB;

        $output = '';

        $output .= html_writer::start_div('databox');

        $user = $DB->get_record('user', array('id' => $quadern->alumne));
        $fullname = fullname($user);

        $output .= html_writer::start_div('datagroup');
        $output .= html_writer::tag('span', get_string('nom', 'fct').':', array('class' => 'datatitle'));
        $output .= html_writer::tag('span', $fullname, array('class' => 'datacontent'));
        $output .= html_writer::end_div();

        $output .= html_writer::start_div('datagroup');
        $output .= html_writer::tag('span', get_string('dni', 'fct').':', array('class' => 'datatitle'));
        $output .= html_writer::tag('span', $quadern->dni, array('class' => 'datacontent'));
        $output .= html_writer::end_div();

        $output .= html_writer::start_div('datagroup');
        $output .= html_writer::tag('span', get_string('data_naixement', 'fct').':', array('class' => 'datatitle'));
        $output .= html_writer::tag('span', $quadern->data_naixement, array('class' => 'datacontent'));
        $output .= html_writer::end_div();

        $output .= html_writer::start_div('datagroup');
        $output .= html_writer::tag('span', get_string('adreca', 'fct').':', array('class' => 'datatitle'));
        $output .= html_writer::tag('span', $quadern->adreca, array('class' => 'datacontent'));
        $output .= html_writer::end_div();

        $output .= html_writer::start_div('datagroup');
        $output .= html_writer::tag('span', get_string('poblacio', 'fct').':', array('class' => 'datatitle'));
        $output .= html_writer::tag('span', $quadern->poblacio, array('class' => 'datacontent'));
        $output .= html_writer::end_div();

        $output .= html_writer::start_div('datagroup');
        $output .= html_writer::tag('span', get_string('codi_postal', 'fct').':', array('class' => 'datatitle'));
        $output .= html_writer::tag('span', $quadern->codi_postal, array('class' => 'datacontent'));
        $output .= html_writer::end_div();

        $output .= html_writer::start_div('datagroup');
        $output .= html_writer::tag('span', get_string('telefon', 'fct').':', array('class' => 'datatitle'));
        $output .= html_writer::tag('span', $quadern->telefon, array('class' => 'datacontent'));
        $output .= html_writer::end_div();

        $output .= html_writer::start_div('datagroup');
        $output .= html_writer::tag('span', get_string('email', 'fct').':', array('class' => 'datatitle'));
        $output .= html_writer::tag('span', $quadern->email, array('class' => 'datacontent'));
        $output .= html_writer::end_div();

        if (isset($quadern->procedencia)) {
            $procedencia = $quadern->procedencies()[$quadern->procedencia];
        } else {
            $procedencia = '';
        }
        $output .= html_writer::start_div('datagroup');
        $output .= html_writer::tag('span', get_string('procedencia', 'fct').':', array('class' => 'datatitle'));
        $output .= html_writer::tag('span', $procedencia, array('class' => 'datacontent'));
        $output .= html_writer::end_div();

        //targeta_sanitaria
        $output .= html_writer::start_div('datagroup');
        $output .= html_writer::tag('span', get_string('targeta_sanitaria', 'fct').':', array('class' => 'datatitle'));
        $output .= html_writer::tag('span', $quadern->targeta_sanitaria, array('class' => 'datacontent'));
        $output .= html_writer::end_div();

        //inns
        $output .= html_writer::start_div('datagroup');
        $output .= html_writer::tag('span', get_string('inss', 'fct').':', array('class' => 'datatitle'));
        $output .= html_writer::tag('span', $quadern->inss, array('class' => 'datacontent'));
        $output .= html_writer::end_div();

        $cm = get_coursemodule_from_instance('fct', $quadern->fct);
        $link = new moodle_url('./edit.php', array('cmid'=>$cm->id, 'quadern' => $quadern->id, 'page' => 'quadern_dades', 'subpage' => 'quadern_alumne'));
        $output .= html_writer::link($link, get_string('edit'));

        $output .= html_writer::end_div();

        echo $output;

    }

}
