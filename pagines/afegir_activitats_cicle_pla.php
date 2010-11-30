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

fct_require('pagines/base_pla_activitats', 'pagines/form_base');

class fct_form_activitats_cicle_pla extends fct_form_base {

    function configurar($pagina) {
        $elements = array_combine($pagina->cicle->activitats,
                                  $pagina->cicle->activitats);
        $this->element('llista', 'activitats_cicle', 'afegeix_activitats_cicle',
                       array('elements' => $elements));
        $this->element('boto', 'afegir', 'afegeix');
    }
}


class fct_pagina_afegir_activitats_cicle_pla extends fct_pagina_base_pla_activitats  {

    var $form;

    function configurar() {
        parent::configurar(required_param('quadern', PARAM_INT));
        $this->comprovar_permis($this->permis_editar);
        $this->configurar_accio(array('seleccionar', 'afegir', 'cancellar'),
                                'seleccionar');
        $this->url = fct_url('afegir_activitats_cicle_pla', array('quadern' => $this->quadern->id));
        $this->subpestanya = 'afegir_activitats_cicle_pla';
    }

    function mostrar() {
        $this->mostrar_capcalera();
        if ($this->cicle->activitats) {
            $this->form->mostrar();
        } else {
            echo '<p>' . fct_string('cicle_formatiu_sense_activitats',
                                     $this->cicle->nom) . '</p>';
        }
        $this->mostrar_peu();
    }

    function processar_afegir() {
        $this->form = new fct_form_activitats_cicle_pla($this);
        if ($this->form->validar()) {
            foreach ($this->form->valor('activitats_cicle') as $descripcio) {
                $activitat = new fct_activitat;
                $activitat->quadern = $this->quadern->id;
                $activitat->descripcio = $descripcio;
                $this->diposit->afegir_activitat($activitat);
            }
            $this->registrar('add activitats_cicle_pla',
                             fct_url('pla_activitats', array('quadern' => $this->quadern->id)));
            if ($this->quadern->tutor_empresa == $this->usuari->id) {
                $this->serveis->registrar_avis($this->quadern, 'pla_activitats');
            }
            redirect(fct_url('pla_activitats', array('quadern' => $this->quadern->id)));
        }
    }

    function processar_seleccionar() {
        $this->form = new fct_form_activitats_cicle_pla($this);
        $this->mostrar();
    }

    function processar_cancellar() {
        redirect(fct_url('pla_activitats', array('quadern' => $this->quadern->id)));
    }

}
