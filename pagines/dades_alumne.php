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
            'pagines/form_dades_alumne.php');

class fct_pagina_dades_alumne extends fct_pagina_base_dades_quadern {

    var $alumne;
    var $form;

    function configurar() {
        parent::configurar(required_param('quadern', PARAM_INT));
        $this->alumne = fct_db::dades_alumne($this->fct->id, $this->quadern->alumne);
        if (!$this->alumne) {
            $this->error('recuperar_alumne');
        }
        $this->configurar_accio(array('veure', 'editar', 'desar', 'cancellar'), 'veure');

        if ($this->accio != 'veure') {
            $this->comprovar_permis($this->permis_editar);
        }

        $this->url = fct_url::dades_alumne($this->quadern->id);
        $this->subpestanya = 'dades_alumne';
        $this->form = new fct_form_dades_alumne($this);
    }

    function mostrar() {
        $this->form->set_data($this->alumne);
        $this->mostrar_capcalera();
        $this->form->display();
        $this->mostrar_peu();
    }

    function processar_cancellar() {
        redirect($this->url);
    }

    function processar_desar() {
        $data = $this->form->get_data();
        if ($data) {
            $data->id = $this->alumne->id;
            $data->fct = $this->fct->id;
            $data->alumne = $this->quadern->alumne;
            $ok = fct_db::actualitzar_dades_alumne($data);
            if ($ok) {
                $this->registrar('update dades_alumne');
            } else {
                $this->error('desar_alumne');
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
        $this->registrar('view dades_alumne');
    }
}

