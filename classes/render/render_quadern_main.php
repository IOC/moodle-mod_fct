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
 * Renderers for outputting fct quadern main page.
 *
 * @package    mod
 * @subpackage fct
 * @copyright  2014 Institut Obert de Catalunya
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class mod_fct_quadern_main_renderer extends plugin_renderer_base {

    public function view($quadern) {

        global $DB, $PAGE;

        $output = '';

        $user = $DB->get_record('user', array('id' => $quadern->alumne));
        $fullname = fullname($user);
        $userurl = new moodle_url('/user/view.php', array('id'=>$quadern->alumne, 'course' => $PAGE->course->id));
        $userlink = html_writer::link($userurl, $fullname);

        $output .= html_writer::start_div('alumnequadern');
        $output .= html_writer::tag('span', get_string('alumne', 'fct').':', array('class' => 'titlealumne'));
        $output .= html_writer::tag('span', $userlink, array('class' => 'contentalumne'));
        $output .= html_writer::end_div();

        $output .= html_writer::start_div('empresaquadern');
        $output .= html_writer::tag('span', get_string('empresa', 'fct').':', array('class' => 'titleempresa'));
        $output .= html_writer::tag('span', $quadern->nom_empresa, array('class' => 'contentempresa'));
        $output .= html_writer::end_div();

        $user = $DB->get_record('user', array('id' => $quadern->tutor_centre));
        $fullname = fullname($user);
        $userurl = new moodle_url('/user/view.php', array('id'=>$quadern->tutor_centre, 'course' => $PAGE->course->id));
        $userlink = html_writer::link($userurl, $fullname);

        $output .= html_writer::start_div('tutorcentrequadern');
        $output .= html_writer::tag('span', get_string('tutor_centre', 'fct').':', array('class' => 'titletutorcentre'));
        $output .= html_writer::tag('span', $userlink, array('class' => 'contenttutorcentre'));
        $output .= html_writer::end_div();

        $user = $DB->get_record('user', array('id' => $quadern->tutor_empresa));
        $fullname = fullname($user);
        $userurl = new moodle_url('/user/view.php', array('id'=>$quadern->tutor_empresa, 'course' => $PAGE->course->id));
        $userlink = html_writer::link($userurl, $fullname);

        $output .= html_writer::start_div('tutorempresaquadern');
        $output .= html_writer::tag('span', get_string('tutor_empresa', 'fct').':', array('class' => 'titletutorempresa'));
        $output .= html_writer::tag('span', $userlink, array('class' => 'contenttutorempresa'));
        $output .= html_writer::end_div();

        $output .= html_writer::start_div('estatquadern');
        $output .= html_writer::tag('span', get_string('estat', 'fct').':', array('class' => 'titleestat'));
        $output .= html_writer::tag('span', $quadern->estat, array('class' => 'contentestat'));
        $output .= html_writer::end_div();

        echo $output;

    }

}
