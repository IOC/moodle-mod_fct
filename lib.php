<?php
/* Quadern virtual d'FCT

   Copyright © 2008,2009  Institut Obert de Catalunya

   This program is free software: you can redistribute it and/or modify
   it under the terms of the GNU General Public License as published by
   the Free Software Foundation, either version 3 of the License, or
   (at your option) any later version.

   This program is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

   You should have received a copy of the GNU General Public License
   along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

class fct_exception extends Exception {}

function fct_require() {
    global $CFG;
    foreach (func_get_args() as $fitxer) {
        require_once($CFG->dirroot . '/mod/fct/' . $fitxer);
    }
}

function fct_string($identifier, $a=null) {
    if (is_array($a)) {
        $a = (object) $a;
    }
    return get_string($identifier, 'fct', $a);
}

fct_require('moodle.php', 'domini.php', 'diposit.php', 'json.php');

function fct_add_instance($data) {
    $fct = new fct;
    $fct->course = (int) $data->course;
    $fct->name = stripslashes($data->name);
    $fct->intro = stripslashes($data->intro);
    $fct->timecreated = time();
    $fct->timemodified = time();
    $diposit = new fct_diposit;
    $diposit->afegir_fct($fct);
    return $fct->id;
}

function fct_update_instance($data) {
    $diposit = new fct_diposit;
    $fct = $diposit->fct($data->instance);
    $fct->name = stripslashes($data->name);
    $fct->intro = stripslashes($data->intro);
    $fct->timemodified = time();
    $diposit->afegir_fct($fct);
    return true;
}

function fct_delete_instance($id) {
    $diposit = new fct_diposit;
    $serveis = new fct_serveis($diposit);
    $fct = $diposit->fct($id);
    $serveis->suprimir_fct($fct);
    return true;
}
