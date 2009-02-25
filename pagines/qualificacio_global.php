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

fct_require('pagines/base_quadern.php',
            'pagines/form_qualificacio.php');

class fct_pagina_qualificacio_global extends fct_pagina_base_quadern {

    var $qualificacio;
    var $form;
    var $permis_editar;

    function configurar() {
        parent::configurar(required_param('quadern', PARAM_INT));
        $this->qualificacio = fct_db::qualificacio_global($this->fct->id,
            $this->quadern->alumne);
        if (!$this->qualificacio) {
            $this->error('recuperar_qualificacio_global');
        }
        $this->configurar_accio(array('veure', 'editar', 'desar', 'cancellar'), 'veure');

        $this->permis_editar = ($this->permis_admin or ($this->quadern->estat and
                                                        $this->permis_tutor_centre));
        if ($this->accio != 'veure') {
            $this->comprovar_permis($this->permis_editar);
        }

        $this->url = fct_url::qualificacio_global($this->quadern->id);
        $this->titol = fct_string('qualificacio_global');
        $this->pestanya = 'qualificacio_global';
        $this->form = new fct_form_qualificacio($this);
    }

    function mostrar() {
        $this->form->set_data($this->qualificacio);
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
            $data->id = $this->qualificacio->id;
            $data->fct = $this->qualificacio->fct;
            $data->alumne = $this->qualificacio->alumne;
            $data->data = $this->form->date2unix($data->data);
            $ok = fct_db::actualitzar_qualificacio_global($data);
            if ($ok) {
                $barem = fct_form_qualificacio::options_barem();
                $this->registrar('update qualificacio_global', null,
                    $barem[$data->qualificacio]);
            } else {
                $this->error('desar_qualificacio_global');
            }
            redirect($this->url);
        }
        $this->mostrar();
    }

    function processar_editar() {
        if (!$this->qualificacio->data) {
            $this->qualificacio->data = (int) time();
        }
        $this->mostrar();
    }

    function processar_veure() {
        $this->mostrar();
        $this->registrar('view qualificacio_global');
    }
}

