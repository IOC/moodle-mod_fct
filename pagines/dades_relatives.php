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

fct_require('pagines/base_dades_quadern', 'pagines/form_base');

class fct_form_dades_relatives extends fct_form_base {

    function configurar($pagina) {
        $this->element('capcalera', 'dades_relatives', 'dades_relatives');
        $this->element('hores', 'hores_credit' ,'hores_credit');
        $opcions = array(0 => '-', 25 => '25%', 50 => '50%');
        $this->element('menu', 'exempcio', 'exempcio',
                       array('opcions' => $opcions));
        $this->element('hores', 'hores_anteriors', 'hores_anteriors');

        if ($pagina->accio == 'veure') {
            $this->element('estatic', 'hores_realitzades_detall',
                           'hores_realitzades');
            $this->element('estatic', 'hores_pendents', 'hores_pendents');
        }

        if ($pagina->accio == 'veure') {
            if ($pagina->permis_editar) {
                $this->element('boto', 'editar', 'edita');
            }
            $this->congelar();
        } else {
            $this->element('boto', 'desar', 'desa');
            $this->element('boto', 'cancellar');
        }
    }
}

class fct_pagina_dades_relatives extends fct_pagina_base_dades_quadern {

    var $dades;
    var $form;

    function configurar() {
        parent::configurar(required_param('quadern', PARAM_INT));
        $this->configurar_accio(array('veure', 'editar', 'desar', 'cancellar'),
                                'veure');

        if ($this->accio != 'veure') {
            $this->comprovar_permis($this->permis_editar);
        }

        $this->url = fct_url('dades_relatives', array('quadern' => $this->quadern->id));
        $this->subpestanya = 'dades_relatives';
        $this->form = new fct_form_dades_relatives($this);
    }

    function mostrar() {
        $hores = $this->serveis->resum_hores_fct($this->quadern);

        $this->form->valor('hores_credit', $this->quadern->hores_credit);
        $this->form->valor('exempcio', $this->quadern->exempcio);
        $this->form->valor('hores_anteriors', $this->quadern->hores_anteriors);
        $this->form->valor('hores_realitzades_detall', fct_string('hores_realitzades_detall', $hores));
        $this->form->valor('hores_pendents', $hores->pendents);

        $this->mostrar_capcalera();
        $this->form->mostrar();
        $this->mostrar_peu();
    }

    function processar_cancellar() {
        redirect($this->url);
    }

    function processar_desar() {
        if ($this->form->validar()) {
            $this->quadern->hores_credit = $this->form->valor('hores_credit');
            $this->quadern->exempcio = $this->form->valor('exempcio');
            $this->quadern->hores_anteriors = $this->form->valor('hores_anteriors');
            $this->diposit->afegir_quadern($this->quadern);
            if ($this->quadern->alumne == $this->usuari->id) {
                $this->serveis->registrar_avis($this->quadern, 'dades_relatives');
            }
            $this->registrar('update dades_relatives');
            redirect($this->url);
        }
        $this->mostrar();
    }

    function processar_editar() {
        $this->mostrar();
    }

    function processar_veure() {
        $this->mostrar();
        $this->registrar('view dades_relatives');
    }
}

