<?php
/* Quadern virtual d'FCT

   Copyright © 2008-2013 Institut Obert de Catalunya

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

fct_require('pagines/base_dades_quadern', 'pagines/form_base', 'export/lib');

class fct_pagina_imprimir_dades_quadern extends fct_pagina_base_quadern {

    function configurar() {
        parent::configurar(required_param('quadern', PARAM_INT));
        $this->url = fct_url('imprimir_dades_quadern', array('quadern' => $this->quadern->id));
    }

    function processar() {
        $export = new fct_export;
        echo $export->dades_generals_html($this->quadern->id);
    }
}
