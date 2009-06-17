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
        $this->element('capcalera', 'dades_conveni', 'conveni');
        $this->element('text', 'codi', 'codi');
        $this->element('data', 'data_inici', 'data_inici');
        $this->element('data', 'data_final', 'data_final');
        $this->element('areatext', 'prorrogues', 'prorrogues');
        $this->element('text', 'codi', 'codi');
        $this->element('hores', 'hores_practiques', 'hores_practiques');
        $this->element('estatic', 'hores_realitzades', 'hores_realitzades');
        $this->element('estatic', 'hores_pendents',
                       'hores_practiques_pendents');

        $this->comprovacio($this, 'comprovar_dates');

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

    function comprovar_dates($valors) {
        if ($valors->data_inici > $valors->data_final) {
            return array('data_inici' => fct_string('anterior_data_final'),
                         'data_final' => fct_string('posterior_data_inici'));
        }
        return true;
    }
}

class fct_pagina_dades_conveni extends fct_pagina_base_dades_quadern {

    var $conveni;
    var $form;

    function configurar() {
        parent::configurar(required_param('quadern', PARAM_INT));
        $this->conveni = fct_db::dades_conveni($this->quadern->id);
        if (!$this->conveni) {
            $this->error('recuperar_covneni');
        }

        $this->configurar_accio(array('veure', 'editar', 'desar', 'cancellar'), 'veure');

        if ($this->accio != 'veure') {
            $this->comprovar_permis($this->permis_editar);
        }

        $this->url = fct_url::dades_conveni($this->quadern->id);
        $this->subpestanya = 'dades_conveni';
        $this->form = new fct_form_dades_conveni($this);
    }

    function mostrar() {
        $this->conveni->hores_realitzades =
            (float) fct_db::hores_realitzades_quadern($this->quadern->id);
        $this->conveni->hores_pendents = $this->conveni->hores_practiques
            - $this->conveni->hores_realitzades;
        $this->form->valors($this->conveni);
        $this->mostrar_capcalera();
        $this->form->mostrar();
        $this->mostrar_peu();
    }

    function processar_cancellar() {
        redirect($this->url);
    }

    function processar_desar() {
        if ($this->form->validar()) {
            $dades = $this->form->valors();
            $dades->id = $this->conveni->id;
            $dades->quadern = $this->conveni->quadern;
            $ok = fct_db::actualitzar_dades_conveni($dades);
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

