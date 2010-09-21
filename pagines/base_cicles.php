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

fct_require('pagines/base.php');

class fct_pagina_base_cicles extends fct_pagina_base {

    function configurar($fct_id) {
        parent::configurar($fct_id);
        $this->comprovar_permis($this->permis_admin);
        $this->pestanya = 'cicles';
        $this->afegir_navegacio(fct_string('cicles_formatius'));
    }

    function definir_pestanyes() {
        parent::definir_pestanyes();
        $pestanyes = array(
            new tabobject('llista_cicles', fct_url::llista_cicles($this->fct->id), fct_string('cicles_formatius')),
            new tabobject('afegir_cicle', fct_url::afegir_cicle($this->fct->id), fct_string('afegeix_cicle_formatiu')),
        );
        $this->pestanyes[] = $pestanyes;
    }

}

