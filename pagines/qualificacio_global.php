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

    var $ultim_quadern;
    var $form;
    var $permis_editar;

    function configurar() {
        parent::configurar(required_param('quadern', PARAM_INT));
        $this->configurar_accio(array('veure', 'editar', 'desar', 'cancellar'), 'veure');

        $this->permis_editar = ($this->permis_admin or
                                ($this->quadern->estat == 'obert' and
                                 $this->permis_tutor_centre));
        if ($this->accio != 'veure') {
            $this->comprovar_permis($this->permis_editar);
        }

        $this->url = fct_url('qualificacio_global', array('quadern' => $this->quadern->id));
        $this->titol = 'qualificacio_global';
        $this->pestanya = 'qualificacio_global';
        $this->form = new fct_form_qualificacio($this);
    }

    function mostrar() {
        $this->mostrar_capcalera();
        $ultim_quadern = $this->serveis->ultim_quadern($this->quadern->alumne,
                                                       $this->quadern->cicle);

        if ($ultim_quadern->id != $this->quadern->id) {
            $url = fct_url('qualificacio_global', array('quadern' => $ultim_quadern->id));
            $avis = fct_string('qualificacio_global_a_ultim_quadern', $url);
            echo "<p>$avis</p>";
        } else {
            $this->form->valors($this->quadern->qualificacio_global);
            $this->form->mostrar();
        }
        $this->mostrar_peu();
    }

    function processar_cancellar() {
        redirect($this->url);
    }

    function processar_desar() {
        if ($this->form->validar()) {
            $this->quadern->qualificacio_global->apte = $this->form->valor('apte');
            $this->quadern->qualificacio_global->nota = $this->form->valor('nota');
            $this->quadern->qualificacio_global->data = $this->form->valor('data');
            $this->quadern->qualificacio_global->observacions = $this->form->valor('observacions');
            $this->diposit->afegir_quadern($this->quadern);
            $barem = $this->form->barem_valoracio();
            $this->registrar('update qualificacio_global', null,
                             $barem[$this->quadern->qualificacio_global->apte]);
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
