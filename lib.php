<?php
/* Quadern virtual d'FCT

   Copyright © 2008,2009,2010  Institut Obert de Catalunya

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

function fct_param($nom) {
    return optional_param($nom, null, PARAM_RAW);
}

function fct_require() {
    global $CFG;
    foreach (func_get_args() as $fitxer) {
        require_once("{$CFG->dirroot}/mod/fct/{$fitxer}.php");
    }
}

function fct_string($identifier, $a=null) {
    if (is_array($a)) {
        $a = (object) $a;
    }
    return get_string($identifier, 'fct', $a);
}

function fct_url($pagina, $params) {
    $url = 'view.php?pagina=' . urlencode($pagina);
    foreach ($params as $nom => $valor) {
        $url .= "&$nom=" . urlencode($valor);
    }
    return $url;
}

function fct_add_instance($data) {
    fct_require('diposit', 'domini', 'json');
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
    fct_require('diposit', 'json');
    $diposit = new fct_diposit;
    $fct = $diposit->fct($data->instance);
    $fct->name = stripslashes($data->name);
    $fct->intro = stripslashes($data->intro);
    $fct->timemodified = time();
    $diposit->afegir_fct($fct);
    return true;
}

function fct_delete_instance($id) {
    fct_require('diposit', 'domini', 'json');
    $diposit = new fct_diposit;
    $serveis = new fct_serveis($diposit);
    $fct = $diposit->fct($id);
    $serveis->suprimir_fct($fct);
    return true;
}
