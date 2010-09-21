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

fct_require('pagines/base_pla_activitats.php',
            'pagines/form_base.php');

class fct_form_activitats_cicle_pla extends fct_form_base {

    function configurar($pagina) {
        $this->element('llista', 'activitats_cicle', 'afegeix_activitats_cicle',
                       array('elements' => $pagina->activitats));
        $this->element('boto', 'afegir', 'afegeix');
    }
}


class fct_pagina_afegir_activitats_cicle_pla extends fct_pagina_base_pla_activitats  {

    var $activitats;

    function configurar() {
        parent::configurar(required_param('quadern', PARAM_INT));
        $this->comprovar_permis($this->permis_editar);
        $this->configurar_accio(array('afegir', 'cancellar'), 'afegir');
        $this->url = fct_url::afegir_activitats_cicle_pla($this->quadern->id);
        $this->subpestanya = 'afegir_activitats_cicle_pla';
        $this->activitats = fct_db::activitats_cicle($this->quadern->cicle);
    }

    function processar_afegir() {
        if ($this->activitats) {
            $form = new fct_form_activitats_cicle_pla($this);
            if ($form->validar()) {
                $ok = fct_db::afegir_activitats_cicle_pla(
                    $this->quadern->id, $form->valor('activitats_cicle'));
                if ($ok) {
                    $this->registrar('add activitats_cicle_pla',
                                     fct_url::pla_activitats($this->quadern->id));
                } else {
                    $this->error('afegir_activitats');
                }
                redirect(fct_url::pla_activitats($this->quadern->id));
            }
        }

        $this->mostrar_capcalera();
        if ($this->activitats) {
            $form->mostrar();
        } else {
            echo '<p>' . fct_string('cicle_formatiu_sense_activitats',
                                     $this->cicle->nom) . '</p>';
        }
        $this->mostrar_peu();
    }

    function processar_cancellar() {
        redirect(fct_url::pla_activitats($this->quadern->id));
    }

}
