<?php

require_once '../../config.php';

function fct_string($identifier, $a=null) {
    if (is_array($a)) {
        $a = (object) $a;
    }
    return get_string($identifier, 'fct', $a);
}

require_once 'db.php';
require_once 'url.php';

$pagina = optional_param('pagina', 'llista_quaderns', PARAM_ALPHAEXT);
include_once "pagines/$pagina.php";
$class = "fct_pagina_$pagina";
if (!class_exists($class)) {
    error("S'ha produït un error intern: la classe <em>$class</em> no existeix.");
}
new $class();

