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

class fct_form_element_conveni extends fct_form_element_base_grup {

    function configurar() {
        $this->element('capcalera', 'conveni', $this->etiqueta);
        $this->element('text', 'codi', 'codi');
        $this->element('data', 'data_inici', 'data_inici');
        $this->element('data', 'data_final', 'data_final');
        if (!empty($this->params->suprimir)) {
            $this->element('opcio', 'suprimir', 'suprimeix');
        }
    }

}

class fct_form_dades_conveni extends fct_form_base {

    function configurar($pagina) {
        foreach ($pagina->quadern->convenis as $conveni) {
            $this->element('conveni', 'conveni_' . $conveni->uuid, 'conveni',
                           array('suprimir' => $pagina->accio != 'veure'));
        }

        if ($pagina->accio != 'veure') {
            $this->element('conveni', 'conveni_nou', 'nou_conveni');
        }

        $this->element('capcalera', 'daddes_convenis', 'general');
        $this->element('areatext', 'prorrogues', 'prorrogues');
        $this->element('hores', 'hores_practiques', 'hores_practiques');
        $this->element('estatic', 'hores_realitzades', 'hores_realitzades');
        $this->element('estatic', 'hores_pendents',
                       'hores_practiques_pendents');

        $this->comprovacio($pagina, 'comprovar_dates');

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

class fct_pagina_dades_conveni extends fct_pagina_base_dades_quadern {

    var $dades;
    var $convenis;
    var $form;

    function comprovar_dates($valors) {
        $errors = array();
        foreach ($this->quadern->convenis as $conveni) {
            $index = 'conveni_' . $conveni->uuid;
            if (isset($valors->$index)) {
                if ($valors->$index->data_inici > $valors->$index->data_final) {
                    $errors["$index_data_inici"] = fct_string('anterior_data_final');
                    $errors["$index_data_final"] = fct_string('posterior_data_inici');
                }
            }
        }
        return $errors ? $errors : true;
    }

    function configurar() {
        parent::configurar(required_param('quadern', PARAM_INT));
        $this->configurar_accio(array('veure', 'editar', 'desar', 'cancellar'), 'veure');

        if ($this->accio != 'veure') {
            $this->comprovar_permis($this->permis_editar);
        }

        $this->url = fct_url('dades_conveni', array('quadern' => $this->quadern->id));
        $this->subpestanya = 'dades_conveni';
        $this->form = new fct_form_dades_conveni($this);
    }

    function mostrar() {
        $hores = $this->serveis->hores_realitzades_quadern($this->quadern);
        $this->form->valor('prorrogues', $this->quadern->prorrogues);
        $this->form->valor('hores_practiques',
                           $this->quadern->hores_practiques);
        $this->form->valor('hores_realitzades', $hores);
        $this->form->valor('hores_pendents',
                           $this->quadern->hores_practiques - (float) $hores);
        foreach ($this->quadern->convenis as $conveni) {
            $this->form->valor('conveni_' .  $conveni->uuid, $conveni);
        }
        $this->mostrar_capcalera();
        $this->form->mostrar();
        $this->mostrar_peu();
    }

    function processar_cancellar() {
        redirect($this->url);
    }

    function processar_desar() {
        if ($this->form->validar()) {
            $valors = $this->form->valors();

            $this->quadern->prorrogues = $this->form->valor('prorrogues');
            $this->quadern->hores_practiques =
                $this->form->valor('hores_practiques');

            foreach ($this->quadern->convenis as $conveni) {
                $index = 'conveni_' . $conveni->uuid;
                if (isset($valors->$index)) {
                    $conveni->codi = $valors->$index->codi;
                    $conveni->data_inici = $valors->$index->data_inici;
                    $conveni->data_final = $valors->$index->data_final;
                    if (!empty($valors->$index->suprimir)) {
                        $this->quadern->suprimir_conveni($conveni);
                    }
                }
            }

            if ($valors->conveni_nou->codi or !$this->quadern->convenis) {
                $conveni = new fct_conveni;
                $conveni->quadern = $this->quadern->id;
                $conveni->codi = $valors->conveni_nou->codi;
                $conveni->data_inici = $valors->conveni_nou->data_inici;
                $conveni->data_final = $valors->conveni_nou->data_final;
                $this->quadern->afegir_conveni($conveni);
            }

            $this->diposit->afegir_quadern($this->quadern);
            $this->registrar('update dades_conveni');
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

