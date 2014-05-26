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
 * Renderers for outputting fct frases.
 *
 * @package    mod
 * @subpackage fct
 * @copyright  2014 Institut Obert de Catalunya
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class mod_fct_frases_renderer extends plugin_renderer_base {

    public function frases_table($frases) {

        $output = '';
        $output .= html_writer::tag('span', get_string('tutor_centre', 'fct'));

        $output .= html_writer::start_tag('ul');

        if (isset($frases['frases_centre'])) {
            foreach ($frases['frases_centre'] as $key => $value) {
                $output .= html_writer::tag('li', $value);
            }
        }

        $output .= html_writer::end_tag('ul');

        $output .= html_writer::tag('span', get_string('tutor_empresa', 'fct'));

        $output .= html_writer::start_tag('ul');

       if (isset($frases['frases_empresa'])) {
            foreach ($frases['frases_empresa'] as $key => $value) {
                $output .= html_writer::tag('li', $value);
            }
        }

        $output .= html_writer::end_tag('ul');

        return $output;

    }


}