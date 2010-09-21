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

fct_require('pagines/base', 'pagines/form_base');

class fct_form_frases_retroaccio extends fct_form_base {

    function configurar($pagina) {
        $this->element('capcalera', 'frases_retroaccio', 'frases_retroaccio');
        $this->element('areatext', 'frases_centre', 'tutor_centre',
                       array('cols' => 60, 'rows' => 15));
        $this->element('areatext', 'frases_empresa', 'tutor_empresa',
                       array('cols' => 60, 'rows' => 15));

        if (!$pagina->accio) {
            $this->congelar();
        } elseif ($pagina->accio == 'veure') {
            $this->element('boto', 'editar', 'edita');
            $this->congelar();
        } else {
            $this->element('boto', 'desar', 'desa');
            $this->element('boto', 'cancellar');
        }
    }
}

class fct_pagina_frases_retroaccio extends fct_pagina_base {

    var $form;

    function configurar() {
        parent::configurar(required_param('fct', PARAM_INT));
        $this->comprovar_permis($this->permis_admin);

        $this->configurar_accio(array('veure', 'editar', 'desar', 'cancellar'), 'veure');

        $this->url = fct_url('frases_retroaccio', array('fct' => $this->fct->id));
        $this->pestanya = 'frases_retroaccio';
        $this->afegir_navegacio(fct_string('frases_retroaccio'));

        $this->form = new fct_form_frases_retroaccio($this);
    }

    function mostrar() {
        $this->form->valors($this->fct);
        $this->form->valor('frases_centre',
                           implode("\n", $this->fct->frases_centre));
        $this->form->valor('frases_empresa',
                           implode("\n", $this->fct->frases_empresa));
        $this->mostrar_capcalera();
        $this->form->mostrar();
        $this->mostrar_peu();
    }

    function processar_cancellar() {
        redirect($this->url);
    }

    function processar_desar() {
        if ($this->form->validar()) {
            $this->fct->frases_centre = fct_linies_text(
                $this->form->valor('frases_centre'));
            $this->fct->frases_empresa = fct_linies_text(
                $this->form->valor('frases_empresa'));
            $this->diposit->afegir_fct($this->fct);
            $this->registrar('update frases_retroaccio');
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

