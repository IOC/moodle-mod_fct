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

fct_require('pagines/base_dades_quadern.php',
            'pagines/form_base.php');

class fct_form_dades_conveni extends fct_form_base {

    function configurar($pagina) {
        foreach (array_keys($pagina->convenis) as $id) {
            $this->element('capcalera', "conveni_$id", 'conveni');
            $this->element('text', "codi_$id", 'codi');
            $this->element('data', "data_inici_$id", 'data_inici');
            $this->element('data', "data_final_$id", 'data_final');
            if ($pagina->accio != 'veure') {
                $this->element('opcio', "suprimir_$id", 'suprimeix');
            }
        }

        if ($pagina->accio != 'veure') {
            $this->element('capcalera', "conveni_nou", 'nou_conveni');
            $this->element('text', "codi_nou", 'codi');
            $this->element('data', "data_inici_nou", 'data_inici');
            $this->element('data', "data_final_nou", 'data_final');
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
        $valors = (array) $valors;
        foreach (array_keys($this->convenis) as $id) {
            if ($valors["data_inici_$id"] > $valors["data_final_$id"]) {
                return array("data_inici_$id" => fct_string('anterior_data_final'),
                             "data_final_$id" => fct_string('posterior_data_inici'));
            }
        }
        return $errors ? $errors : true;
    }

    function configurar() {
        parent::configurar(required_param('quadern', PARAM_INT));

        $this->dades = fct_db::dades_conveni($this->quadern->id);
        if (!$this->dades) {
            $this->error('recuperar_covneni');
        }
        $this->convenis = fct_db::convenis($this->quadern->id);

        $this->configurar_accio(array('veure', 'editar', 'desar', 'cancellar'), 'veure');

        if ($this->accio != 'veure') {
            $this->comprovar_permis($this->permis_editar);
        }

        $this->url = fct_url::dades_conveni($this->quadern->id);
        $this->subpestanya = 'dades_conveni';
        $this->form = new fct_form_dades_conveni($this);
    }

    function mostrar() {
        $this->dades->hores_realitzades =
            (float) fct_db::hores_realitzades_quadern($this->quadern->id);
        $this->dades->hores_pendents = $this->dades->hores_practiques
            - $this->dades->hores_realitzades;
        $this->form->valors($this->dades);
        foreach ($this->convenis as $id => $conveni) {
            $this->form->valor("codi_$id", $conveni->codi);
            $this->form->valor("data_inici_$id", $conveni->data_inici);
            $this->form->valor("data_final_$id", $conveni->data_final);
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
            $this->dades->prorrogues = $this->form->valor('prorrogues');
            $this->dades->hores_practiques =
                $this->form->valor('hores_practiques');
            $ok = fct_db::actualitzar_dades_conveni($this->dades);
            foreach (array_keys($this->convenis) as $id) {
                $conveni = array(
                    'id' => $id,
                    'quadern' => $this->quadern->id,
                    'codi' => $this->form->valor("codi_$id"),
                    'data_inici' => $this->form->valor("data_inici_$id"),
                    'data_final' => $this->form->valor("data_final_$id"),
                );
                if ($this->form->valor("suprimir_$id")) {
                    $ok = $ok && fct_db::suprimir_conveni($id);
                    unset($this->convenis[$id]);
                } else {
                    $ok = $ok && fct_db::actualitzar_conveni((object) $conveni);
                }
            }

            if ($this->form->valor('codi_nou') or !$this->convenis) {
                $conveni = array(
                    'quadern' => $this->quadern->id,
                    'codi' => $this->form->valor('codi_nou'),
                    'data_inici' => $this->form->valor('data_inici_nou'),
                    'data_final' => $this->form->valor('data_final_nou'),
                );
                $ok = $ok && fct_db::afegir_conveni((object) $conveni);
            }

            if ($ok) {
                $this->registrar('update dades_conveni');
            } else {
                $this->error('desar_conveni');
            }
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

