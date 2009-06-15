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
        $this->element('capcalera', 'dades_horari', 'horari_practiques');
        $this->element('text', 'dilluns', 'dilluns');
        $this->element('text', 'dimarts', 'dimarts');
        $this->element('text', 'dimecres', 'dimecres');
        $this->element('text', 'dijous', 'dijous');
        $this->element('text', 'divendres', 'divendres');
        $this->element('text', 'dissabte', 'dissabte');
        $this->element('text', 'diumenge', 'diumenge');

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

    var $horari;
    var $form;

    function configurar() {
        parent::configurar(required_param('quadern', PARAM_INT));
        $this->horari = fct_db::dades_horari($this->quadern->id);
        if (!$this->horari) {
            $this->error('recuperar_horari');
        }
        $this->configurar_accio(array('veure', 'editar', 'desar', 'cancellar'), 'veure');

        if ($this->accio != 'veure') {
            $this->comprovar_permis($this->permis_editar);
        }

        $this->url = fct_url::dades_horari($this->quadern->id);
        $this->subpestanya = 'dades_horari';
        $this->form = new fct_form_dades_horari($this);
    }

    function mostrar() {
        $this->form->valors($this->horari);
        $this->mostrar_capcalera();
        $this->form->mostrar();
        $this->mostrar_peu();
    }

    function processar_cancellar() {
        redirect($this->url);
    }

    function processar_desar() {
        if ($this->form->validar()) {
            $horari = $this->form->valors();
            $horari->id = $this->horari->id;
            $horari->quadern = $this->horari->quadern;
            $ok = fct_db::actualitzar_dades_horari($horari);
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
