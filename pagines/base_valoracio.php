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

fct_require('pagines/base_quadern.php');

class fct_pagina_base_valoracio extends fct_pagina_base_quadern {

    var $permis_editar;

    function configurar() {
        parent::configurar(required_param('quadern', PARAM_INT));
        $this->configurar_accio(array('veure', 'editar', 'desar', 'cancellar'), 'veure');
        $this->pestanya = 'valoracio';
        $this->permis_editar = ($this->permis_admin or
                                ($this->quadern->estat == 'obert'
                                 and ($this->permis_tutor_centre
                                      or $this->permis_tutor_empresa)));
        if ($this->accio != 'veure') {
            $this->comprovar_permis($this->permis_editar);
        }
    }

    function definir_pestanyes() {
        parent::definir_pestanyes();
        $this->pestanyes[] = array(
            new tabobject('valoracio_actituds_parcial',
                fct_url::valoracio_actituds($this->quadern->id, 0),
                fct_string('valoracio_parcial_actituds')),
            new tabobject('valoracio_actituds_final',
                fct_url::valoracio_actituds($this->quadern->id, 1),
                fct_string('valoracio_final_actituds')),
            new tabobject('valoracio_activitats',
                fct_url::valoracio_activitats($this->quadern->id),
                fct_string('valoracio_activitats')),
            new tabobject('qualificacio_quadern',
                fct_url::qualificacio_quadern($this->quadern->id),
                fct_string('qualificacio_quadern')),
        );
    }

}
