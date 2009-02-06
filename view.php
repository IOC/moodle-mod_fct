<?php

require_once('../../config.php');

require_once($CFG->dirroot . '/mod/fct/version.php');
$installed_version = get_field('modules', 'version', 'name', 'fct');
if ($installed_version != $module->version) {
    error("No es pot utilitzar temporalment el mòdul FCT "
          . "perquè s'estan realitzant tasques de manteniment.");
}

require_once($CFG->dirroot . '/mod/fct/lib.php');

$pagina = optional_param('pagina', 'llista_quaderns', PARAM_ALPHAEXT);
fct_require('db.php', 'url.php' , "pagines/$pagina.php");
$class = "fct_pagina_$pagina";
if (!class_exists($class)) {
    error("S'ha produït un error intern: la classe <em>$class</em> no existeix.");
}
new $class();
