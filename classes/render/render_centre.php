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
 * Renderer for dades centre.
 *
 * @package    mod
 * @subpackage fct
 * @copyright  2014 IOC (Institut Obert de Catalunya)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

class mod_fct_centre_renderer extends plugin_renderer_base {

    public function centre($centre) {

        $output = '';

        $output .= html_writer::start_div('name_centre');
        $output .= html_writer::tag('span', get_string('nom', 'fct').':', array('class' => 'titlecentre'));
        $output .= html_writer::tag('span', isset($centre->nom)?$centre->nom:'', array('class' => 'contentcentre'));
        $output .= html_writer::end_div();

        $output .= html_writer::start_div('adress_centre');
        $output .= html_writer::tag('span', get_string('adreca', 'fct').':');
        $output .= html_writer::tag('span', isset($centre->adreca)?$centre->adreca:'', array('class' => 'contentcentre'));
        $output .= html_writer::end_div();

        $output .= html_writer::start_div('codi_centre');
        $output .= html_writer::tag('span', get_string('codi_postal', 'fct').':', array('class' => 'titlecentre'));
        $output .= html_writer::tag('span', isset($centre->codi_postal)?$centre->codi_postal:'', array('class' => 'contentcentre'));
        $output .= html_writer::end_div();

        $output .= html_writer::start_div('poblacio_centre');
        $output .= html_writer::tag('span', get_string('poblacio', 'fct').':', array('class' => 'titlecentre'));
        $output .= html_writer::tag('span', isset($centre->poblacio)?$centre->poblacio:'', array('class' => 'contentcentre'));
        $output .= html_writer::end_div();

        $output .= html_writer::start_div('telefon_centre');
        $output .= html_writer::tag('span', get_string('telefon', 'fct').':', array('class' => 'titlecentre'));
        $output .= html_writer::tag('span', isset($centre->telefon)?$centre->telefon:'', array('class' => 'contentcentre'));
        $output .= html_writer::end_div();

        $output .= html_writer::start_div('fax_centre');
        $output .= html_writer::tag('span', get_string('fax', 'fct').':', array('class' => 'titlecentre'));
        $output .= html_writer::tag('span', isset($centre->fax)?$centre->fax:'', array('class' => 'contentcentre'));
        $output .= html_writer::end_div();

        $output .= html_writer::start_div('email_centre');
        $output .= html_writer::tag('span', get_string('email', 'fct').':', array('class' => 'titlecentre'));
        $output .= html_writer::tag('span', isset($centre->email)?$centre->email:'', array('class' => 'contentcentre'));
        $output .= html_writer::end_div();

        return $output;

    }


}
