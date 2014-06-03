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
 * This file contains the forms to create and edit an instance of this module
 *
 * @package    mod
 * @subpackage fct
 * @copyright  2014 Institut Obert de Catalunya
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../../config.php');
require_once('classes/fct_instance.php');

function fct_add_instance($data) {
    $fct = new stdClass;
    $fct->course = $data->course;
    $fct->name = $data->name;
    $fct->intro = $data->intro;
    $fct->timecreated = time();
    $fct->timemodified = time();
    $fct->objecte = '';

    $fctinstance = new fct_instance($fct);
    $fctinstance->add();
    return $fctinstance->id;
}

function fct_update_instance($data) {
    global $DB;

    $fctrecord = $DB->get_record('fct', array('id' => $data->instance));
    $fctrecord->name = $data->name;
    $fctrecord->intro = $data->intro;
    $fctrecord->timemodified = time();

    $fctinstance = new fct_instance($fctrecord);
    $fctinstance->add();

    return true;
}

function fct_delete_instance($id) {global $CFG;

    global $DB;

    $fctrecord = $DB->get_record('fct', array('id' => $id));
    $fctinstance = new fct_instance($fctrecord);
    $fctinstance->delete();

    return true;
}
