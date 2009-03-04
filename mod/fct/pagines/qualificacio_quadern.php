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

fct_require('pagines/base_valoracio.php',
            'pagines/form_qualificacio.php');

class fct_pagina_qualificacio_quadern extends fct_pagina_base_valoracio {

    var $qualificacio;
    var $form;

    function configurar() {
        parent::configurar();
        $this->qualificacio = fct_db::qualificacio_quadern($this->quadern->id);
        if (!$this->qualificacio) {
            $this->error('recuperar_qualificacio_quadern');
        }
        $this->url = fct_url::qualificacio_quadern($this->quadern->id);
        $this->titol = fct_string('qualificacio_quadern');
        $this->form = new fct_form_qualificacio($this);
        $this->subpestanya = 'qualificacio_quadern';
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
            $data->quadern = $this->quadern->id;
            $data->data = $this->form->date2unix($data->data);
            $ok = fct_db::actualitzar_qualificacio_quadern($data);
            if ($ok) {
                $barem = fct_form_qualificacio::options_barem();
                $this->registrar('update qualificacio_quadern', null,
                    $barem[$data->qualificacio]);
            } else {
                $this->error('desar_qualificacio_quadern');
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
        $this->registrar('view qualificacio_quadern');
    }
}

