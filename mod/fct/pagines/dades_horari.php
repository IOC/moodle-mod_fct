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

class fct_form_element_horari extends fct_form_element_base_grup {

    function configurar() {
        $this->element('capcalera', "horari", 'horari_practiques');
        $this->element('estatic', "codi", 'conveni');
        $this->element('text', "dilluns", 'dilluns');
        $this->element('text', "dimarts", 'dimarts');
        $this->element('text', "dimecres", 'dimecres');
        $this->element('text', "dijous", 'dijous');
        $this->element('text', "divendres", 'divendres');
        $this->element('text', "dissabte", 'dissabte');
        $this->element('text', "diumenge", 'diumenge');
    }

}

class fct_form_dades_horari extends fct_form_base {

    function configurar($pagina) {

        foreach($pagina->quadern->convenis as $conveni) {
            $this->element('horari', "horari_{$conveni->id}", false);
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

class fct_pagina_dades_horari extends fct_pagina_base_dades_quadern {

    var $form;

    function configurar() {
        parent::configurar(required_param('quadern', PARAM_INT));
        $this->configurar_accio(array('veure', 'editar', 'desar', 'cancellar'), 'veure');

        if ($this->accio != 'veure') {
            $this->comprovar_permis($this->permis_editar);
        }

        $this->url = fct_url::dades_horari($this->quadern->id);
        $this->subpestanya = 'dades_horari';
        $this->form = new fct_form_dades_horari($this, true);
    }

    function mostrar() {
        foreach ($this->quadern->convenis as $conveni) {
            $valor = (object) array(
                'codi' => $conveni->codi,
                'dilluns' => $conveni->horari->dilluns,
                'dimarts' => $conveni->horari->dimarts,
                'dimecres' => $conveni->horari->dimecres,
                'dijous' => $conveni->horari->dijous,
                'divendres' => $conveni->horari->divendres,
                'dissabte' => $conveni->horari->dissabte,
                'diumenge' => $conveni->horari->diumenge,
            );
            $this->form->valor("horari_{$conveni->id}", $valor);
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
            foreach ($this->quadern->convenis as $conveni) {
                $valor = $this->form->valor("horari_{$conveni->id}");
                if ($valor) {
                    $conveni->horari->dilluns = $valor->dilluns;
                    $conveni->horari->dimarts = $valor->dimarts;
                    $conveni->horari->dimecres = $valor->dimecres;
                    $conveni->horari->dijous = $valor->dijous;
                    $conveni->horari->divendres = $valor->divendres;
                    $conveni->horari->dissabte = $valor->dissabte;
                    $conveni->horari->diumenge = $valor->diumenge;
                }
            }
            $this->diposit->afegir_quadern($this->quadern);
            $this->registrar('update dades_horari');
            redirect($this->url);
        }
        $this->mostrar();
    }

    function processar_editar() {
        $this->mostrar();
    }

    function processar_veure() {
        $this->mostrar();
        $this->registrar('view dades_horari');
    }
}
