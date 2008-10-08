<?php

require_once '../../config.php';

require_once 'lib.php';
require_once 'db.php';
require_once 'url.php';

$pagina = optional_param('pagina', 'llista_quaderns', PARAM_ALPHAEXT);
include_once "pagines/$pagina.php";
$class = "fct_pagina_$pagina";
if (!class_exists($class)) {
    error("S'ha produït un error intern: la classe <em>$class</em> no existeix.");
}
new $class();

