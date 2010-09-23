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

fct_require('pagines/base');

class fct_pagina_base_quaderns extends fct_pagina_base {

    function configurar($fct_id=false, $cm_id=false) {
        parent::configurar($fct_id, $cm_id);
        $this->pestanya = 'quaderns';
    }

    function definir_pestanyes() {
        if ($this->usuari->es_administrador) {
            parent::definir_pestanyes();
            $pestanyes = array(
                new tabobject('llista_quaderns',
                              fct_url('llista_quaderns', array('fct' => $this->fct->id)),
                              fct_string('quaderns')),
                new tabobject('afegir_quadern',
                              fct_url('afegir_quadern', array('fct' => $this->fct->id)),
                              fct_string('afegeix_quadern')),
            );
            $this->pestanyes[] = $pestanyes;
        }
    }
}

