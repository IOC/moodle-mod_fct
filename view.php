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

require_once('../../config.php');

require_once($CFG->dirroot . '/mod/fct/version.php');
$installed_version = get_field('modules', 'version', 'name', 'fct');
if ($installed_version != $module->version) {
    print_error('mode_manteniment', 'fct');
}

require_once($CFG->dirroot . '/mod/fct/lib.php');

$pagina = optional_param('pagina', 'llista_quaderns', PARAM_ALPHAEXT);
fct_require('url.php' , "pagines/$pagina.php");
$class = "fct_pagina_$pagina";
if (!class_exists($class)) {
    print_error('error_pagina', 'fct');
}
new $class();
