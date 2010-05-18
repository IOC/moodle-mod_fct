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

class fct_form_dades_horari extends fct_form_base {

    function configurar($pagina) {

        foreach(array_keys($pagina->horaris) as $id) {
            $this->element('capcalera', "horari_$id", 'horari_practiques');
            $this->element('estatic', "codi_$id", 'conveni');
            $this->element('text', "dilluns_$id", 'dilluns');
            $this->element('text', "dimarts_$id", 'dimarts');
            $this->element('text', "dimecres_$id", 'dimecres');
            $this->element('text', "dijous_$id", 'dijous');
            $this->element('text', "divendres_$id", 'divendres');
            $this->element('text', "dissabte_$id", 'dissabte');
            $this->element('text', "diumenge_$id", 'diumenge');
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

    var $dies = array('dilluns', 'dimarts', 'dimecres', 'dijous',
                      'divendres', 'dissabte', 'diumenge');
    var $horaris;
    var $form;

    function configurar() {
        parent::configurar(required_param('quadern', PARAM_INT));

        $this->horaris = fct_db::horaris($this->quadern->id);
        $this->configurar_accio(array('veure', 'editar', 'desar', 'cancellar'), 'veure');

        if ($this->accio != 'veure') {
            $this->comprovar_permis($this->permis_editar);
        }

        $this->url = fct_url::dades_horari($this->quadern->id);
        $this->subpestanya = 'dades_horari';
        $this->form = new fct_form_dades_horari($this);
    }

    function mostrar() {
        foreach ($this->horaris as $id => $horari) {
            $this->form->valor("codi_$id", $horari->codi);
            foreach ($this->dies as $dia) {
                $this->form->valor("{$dia}_{$id}", $horari->$dia);
            }
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
            $ok = true;
            foreach ($this->horaris as $id => $horari) {
                foreach ($this->dies as $dia) {
                    $horari->$dia = $this->form->valor("{$dia}_{$id}");
                }
                $ok = $ok && fct_db::actualitzar_horari($horari);
            }
            if ($ok) {
                $this->registrar('update dades_horari');
            } else {
                $this->error('error_desar_horari');
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
        $this->registrar('view dades_horari');
    }
}
