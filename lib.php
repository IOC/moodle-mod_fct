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

function fct_add_instance($data) {
    global $DB;
    //fct_require('diposit', 'domini', 'json', 'moodle');
    $fct = new stdClass;
    $fct->course = $data->course;
    $fct->name = $data->name;
    $fct->intro = $data->intro;
    $fct->timecreated = time();
    $fct->timemodified = time();
    $fct->objecte = 'a';
    //$diposit = new fct_diposit(new fct_moodle);
    //$diposit->afegir_fct($fct);
    $fct->id = $DB->insert_record('fct', $fct);

    return $fct->id;
}

function fct_update_instance($data) {
   // fct_require('diposit', 'domini', 'json', 'moodle');
   // $diposit = new fct_diposit(new fct_moodle);
    //$fct = $diposit->fct($data->instance);
    //$fct->name = $data->name;
    //$fct->intro = $data->intro;
    //$fct->timemodified = time();
    //$diposit->afegir_fct($fct);
    return true;
}

function fct_delete_instance($id) {
    //fct_require('diposit', 'domini', 'json', 'moodle');
    //$diposit = new fct_diposit(new fct_moodle);
    //$serveis = new fct_serveis($diposit, new fct_moodle);
    //$fct = $diposit->fct($id);
    //$serveis->suprimir_fct($fct);
    return true;
}
