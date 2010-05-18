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
        $this->url = fct_url::qualificacio_quadern($this->quadern->id);
        $this->titol = 'qualificacio_quadern';
        $this->form = new fct_form_qualificacio($this);
        $this->subpestanya = 'qualificacio_quadern';
    }

    function mostrar() {
        $this->form->valors($this->quadern->qualificacio);
        $this->mostrar_capcalera();
        $this->form->mostrar();
        $this->mostrar_peu();
    }

    function processar_cancellar() {
        redirect($this->url);
    }

    function processar_desar() {
        if ($this->form->validar()) {
            fct_copy_vars($this->form->valors(), $this->quadern->qualificacio);
            $this->diposit->afegir_quadern($this->quadern);
            $this->registrar('update qualificacio_quadern');
            redirect($this->url);
        }
        $this->mostrar();
    }

    function processar_editar() {
        $this->mostrar();
    }

    function processar_veure() {
        $this->mostrar();
        $this->registrar('view qualificacio_quadern');
    }
}
