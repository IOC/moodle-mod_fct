<?php
/* Quadern virtual d'FCT

   Copyright © 2009,2010  Institut Obert de Catalunya

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

fct_require('pagines/base_quadern');

class fct_pagina_suprimir_avis extends fct_pagina_base_quadern {

    var $avis;

    function configurar() {
        $id = required_param('avis', PARAM_INT);
        $this->avis = $this->diposit->avis($id);
        parent::configurar($this->avis->quadern);
        $this->comprovar_permis($this->es_tutor_centre());
    }

    function processar() {
        $this->diposit->suprimir_avis($this->avis);
        redirect(fct_url('avisos', array('fct' => $this->fct->id)));
    }
}
