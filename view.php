<?php

require_once '../../config.php';

require_once 'version.php';
require_once 'lib.php';
require_once 'db.php';
require_once 'url.php';

$installed_version = get_field('modules', 'version', 'name', 'fct');
if ($installed_version != $module->version) {
    error("No es pot utilitzar temporalment el mòdul FCT "
          . "perquè s'estan realitzant tasques de manteniment.");
}

$pagina = optional_param('pagina', 'llista_quaderns', PARAM_ALPHAEXT);
include_once "pagines/$pagina.php";
$class = "fct_pagina_$pagina";
if (!class_exists($class)) {
    error("S'ha produït un error intern: la classe <em>$class</em> no existeix.");
}
new $class();

