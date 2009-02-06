<?php

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

fct_require('db.php');

function fct_add_instance($fct) {
    return fct_db::afegir_fct($fct);
}

function fct_update_instance($fct) {
    return fct_db::actualitzar_fct($fct);
}

function fct_delete_instance($id) {
    return fct_db::suprimir_fct($id);
}
