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

fct_require('pagines/base_quadern.php');

class fct_pagina_base_pla_activitats extends fct_pagina_base_quadern {

    var $permis_editar;

    function configurar($quadern_id) {
        parent::configurar($quadern_id);

        $this->pestanya = 'pla_activitats';
        $this->permis_editar = ($this->permis_admin
                                or ($this->quadern->estat
                                    and ($this->permis_tutor_centre
                                         or $this->permis_tutor_empresa)));
    }

    function definir_pestanyes() {
        parent::definir_pestanyes();
        $pestanyes = array();
        if ($this->permis_editar) {
            $pestanyes[] = new tabobject('activitats_pla',
                fct_url::pla_activitats($this->quadern->id), fct_string('activitats'));
            $pestanyes[] = new tabobject('afegir_activitat_pla',
                fct_url::afegir_activitat_pla($this->quadern->id), fct_string('afegeix_activitat'));
            $pestanyes[] = new tabobject('afegir_activitats_cicle_pla',
                fct_url::afegir_activitats_cicle_pla($this->quadern->id), fct_string('afegeix_activitats_cicle'));
            $pestanyes[] = new tabobject('suprimir_activitats_pla',
                fct_url::suprimir_activitats_pla($this->quadern->id), fct_string('suprimeix_activitats'));
        }
        $this->pestanyes[] = $pestanyes;
    }
}

