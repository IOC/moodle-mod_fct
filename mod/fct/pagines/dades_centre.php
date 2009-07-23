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

fct_require('pagines/base.php',
            'pagines/form_dades_centre.php');

class fct_pagina_dades_centre extends fct_pagina_base {

    var $centre;
    var $form;
    var $desar;

    function configurar() {
        parent::configurar(required_param('fct', PARAM_INT));
        $this->comprovar_permis($this->usuari->es_administrador);
        $this->configurar_accio(array('veure', 'editar', 'desar', 'cancellar'), 'veure');

        $this->url = fct_url::dades_centre($this->fct->id);
        $this->pestanya = 'dades_centre';
        $this->afegir_navegacio(fct_string('dades_centre'));

        $this->form = new fct_form_dades_centre($this);
    }

    function mostrar() {
        $this->form->valors($this->fct->centre);
        $this->mostrar_capcalera();
        $this->form->mostrar();
        $this->mostrar_peu();
    }

    function processar_cancellar() {
        redirect($this->url);
    }

    function processar_desar() {
        if ($this->form->validar()) {
            foreach ($this->form->valors() as $nom => $valor) {
                $this->fct->centre->$nom = $valor;
            }
            $this->diposit->afegir_fct($this->fct);
            $this->registrar('update dades_centre');
            redirect($this->url);
        }
        $this->mostrar();
    }

    function processar_editar() {
        $this->mostrar();
    }

    function processar_veure() {
        $this->mostrar();
        $this->registrar('view dades_centre');
    }

}

