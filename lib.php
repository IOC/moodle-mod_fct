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
    fct_require('diposit', 'domini', 'json');
    $fct = new stdClass;
    $fct->course = $data->course;
    $fct->name = $data->name;
    $fct->intro = $data->intro;
    $fct->timecreated = time();
    $fct->timemodified = time();
    $fct->objecte = 'a';
    $diposit = new fct_diposit();
    $diposit->afegir_fct($fct);

    return $fct->id;
}

function fct_update_instance($data) {
    fct_require('diposit', 'domini', 'json');

    $diposit = new fct_diposit();
    $fct = $diposit->fct($data->instance);
    $fct->name = stripslashes($data->name);
    $fct->intro = stripslashes($data->intro);
    $fct->timemodified = time();
    $diposit->afegir_fct($fct);

    return true;
}

function fct_delete_instance($id) {
    fct_require('diposit', 'domini', 'json');

    $diposit = new fct_diposit();
    $serveis = new fct_serveis($diposit);
    $fct = $diposit->fct($id);
    $serveis->suprimir_fct($fct);

    return true;
}

function fct_string($identifier, $a=null) {
    if (is_array($a)) {
        $a = (object) $a;
    }
    return get_string($identifier, 'fct', $a);
}

function fct_require() {
    global $CFG;
    foreach (func_get_args() as $fitxer) {
        if (substr($fitxer, 0, 7) === 'pagines') {
            require_once("{$CFG->dirroot}/mod/fct/{$fitxer}.php");
        } else {
            require_once("{$CFG->dirroot}/mod/fct/classes/{$fitxer}.php");
        }
    }
}

function fct_url($pagina, $params) {
    $url = 'view.php?pagina=' . urlencode($pagina);
    foreach ($params as $nom => $valor) {
        $url .= "&$nom=" . urlencode($valor);
    }
    return $url;
}
