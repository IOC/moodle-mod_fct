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

fct_require('pagines/base_pla_activitats', 'pagines/form_activitat');

class fct_pagina_afegir_activitat_pla extends fct_pagina_base_pla_activitats {

    function comprovar_descripcio($valors) {
       if ($this->diposit->activitats($this->quadern->id,
                                      $valors->descripcio)) {
           return array('descripcio' => fct_string('activitat_duplicada'));
        }
        return true;
    }

    function configurar() {
        parent::configurar(required_param('quadern', PARAM_INT));
        $this->comprovar_permis($this->permis_editar);
        $this->configurar_accio(array('afegir', 'cancellar'), 'afegir');
        $this->url = fct_url('afegir_activitat_pla', array('quadern' => $this->quadern->id));
        $this->subpestanya = 'afegir_activitat_pla';
    }

    function processar_afegir() {
        $form = new fct_form_activitat($this);
        if ($form->validar()) {
            $activitat = new fct_activitat;
            $activitat->quadern = $this->quadern->id;
            $activitat->descripcio = $form->valor('descripcio');
            $this->diposit->afegir_activitat($activitat);
            if ($this->quadern->tutor_empresa == $this->usuari->id) {
                $this->serveis->registrar_avis($this->quadern, 'pla_activitats');
            }
            $this->registrar('add activitat_pla',
                             fct_url('pla_activitats', array('quadern' => $this->quadern->id)),
                             $activitat->descripcio);
            redirect(fct_url('pla_activitats', array('quadern' => $this->quadern->id)));
        }
        $this->mostrar_capcalera();
        $form->mostrar();
        $this->mostrar_peu();
    }

    function processar_cancellar() {
        redirect(fct_url('pla_activitats', array('quadern' => $this->quadern->id)));
    }

}

