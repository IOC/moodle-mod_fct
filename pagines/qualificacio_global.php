<?php
/* Quadern virtual d'FCT

   Copyright © 2008,2009,2010  Institut Obert de Catalunya

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

    var $ultim_quadern;
    var $qualificacio;
    var $form;
    var $permis_editar;

    function configurar() {
        parent::configurar(required_param('quadern', PARAM_INT));
        $this->ultim_quadern = fct_db::ultim_quadern($this->quadern->id);
        $this->qualificacio = fct_db::qualificacio_global($this->quadern->cicle,
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
        $this->titol = 'qualificacio_global';
        $this->pestanya = 'qualificacio_global';
        $this->form = new fct_form_qualificacio($this);
    }

    function mostrar() {
        $this->mostrar_capcalera();
        if ($this->ultim_quadern->id != $this->quadern->id) {
            $url = fct_url::qualificacio_global($this->ultim_quadern->id);
            $avis = fct_string('qualificacio_global_a_ultim_quadern', $url);
            echo "<p>$avis</p>";
        } else {
            $this->form->valors($this->qualificacio);
            $this->form->mostrar();
        }
        $this->mostrar_peu();
    }

    function processar_cancellar() {
        redirect($this->url);
    }

    function processar_desar() {
        if ($this->form->validar()) {
            $qualificacio = $this->form->valors();
            $qualificacio->id = $this->qualificacio->id;
            $qualificacio->cicle = $this->qualificacio->cicle;
            $qualificacio->alumne = $this->qualificacio->alumne;
            $ok = fct_db::actualitzar_qualificacio_global($qualificacio);
            if ($ok) {
                $barem = $this->form->barem_valoracio();
                $this->registrar('update qualificacio_global', null,
                                 $barem[$qualificacio->qualificacio]);
            } else {
                $this->error('desar_qualificacio_global');
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
        $this->registrar('view qualificacio_global');
    }
}
