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
        $this->titol = 'qualificacio_quadern';
        $this->form = new fct_form_qualificacio($this);
        $this->subpestanya = 'qualificacio_quadern';
    }

    function mostrar() {
        $this->form->valors($this->qualificacio);
        $this->mostrar_capcalera();
        $this->form->mostrar();
        $this->mostrar_peu();
    }

    function processar_cancellar() {
        redirect($this->url);
    }

    function processar_desar() {
        if ($this->form->validar()) {
            $qualificacio = $this->form->valors();
            $qualificacio->id = $this->qualificacio->id;
            $qualificacio->quadern = $this->quadern->id;
            $ok = fct_db::actualitzar_qualificacio_quadern($qualificacio);
            if ($ok) {
                $barem = $this->form->barem_qualificacio();
                $nota = $barem[$qualificacio->qualificacio];
                $this->registrar('update qualificacio_quadern', null, $nota);
            } else {
                $this->error('desar_qualificacio_quadern');
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
        $this->registrar('view qualificacio_quadern');
    }
}
