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

fct_require('pagines/base_dades_quadern', 'pagines/form_base');

class fct_form_dades_empresa extends fct_form_base {

    function configurar($pagina) {
        $this->element('capcalera', 'dades_empresa', 'empresa');
        $this->element('estatic', 'nom_empresa', 'nom');
        $this->element('text', 'adreca', 'adreca');
        $this->element('text', 'codi_postal', 'codi_postal',
                       array('size' => 8));
        $this->element('text', 'poblacio', 'poblacio');
        $this->element('text', 'telefon', 'telefon');
        $this->element('text', 'fax', 'fax');
        $this->element('text', 'email', 'email');
        $this->element('text', 'nif', 'nif', array('size' => 16));
        $this->element('text', 'codi_agrupacio', 'codi_agrupacio');
        $this->element('text', 'sic', 'sic');

        $this->element('capcalera', 'responsable_conveni', 'responsable_conveni');
        $this->element('text', 'nom_responsable', 'nom');
        $this->element('text', 'cognoms_responsable', 'cognoms');
        $this->element('text', 'dni_responsable', 'dni', array('size' => 16));
        $this->element('text', 'carrec_responsable', 'carrec');

        $this->element('capcalera', 'tutor_empresa', 'tutor_empresa');
        $this->element('text', 'nom_tutor', 'nom');
        $this->element('text', 'cognoms_tutor', 'cognoms');
        $this->element('text', 'dni_tutor', 'dni', array('size' => 16));
        $this->element('text', 'email_tutor', 'email');

        $this->element('capcalera', 'lloc_practiques', 'lloc_practiques');
        $this->element('text', 'nom_lloc_practiques', 'nom');
        $this->element('text', 'adreca_lloc_practiques', 'adreca');
        $this->element('text', 'codi_postal_lloc_practiques', 'codi_postal');
        $this->element('text', 'poblacio_lloc_practiques', 'poblacio');
        $this->element('text', 'telefon_lloc_practiques', 'telefon');


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

class fct_pagina_dades_empresa extends fct_pagina_base_dades_quadern {

    var $empresa;
    var $form;

    function configurar() {
        parent::configurar(required_param('quadern', PARAM_INT));
        $this->configurar_accio(array('veure', 'editar', 'desar', 'cancellar'), 'veure');

        if ($this->accio != 'veure') {
            $this->comprovar_permis($this->permis_editar);
        }

        $this->url = fct_url('dades_empresa', array('quadern' => $this->quadern->id));
        $this->subpestanya = 'dades_empresa';
        $this->form = new fct_form_dades_empresa($this);
    }

    function mostrar() {
        $this->form->valors($this->quadern->empresa);
        $this->form->valor('nom_empresa', $this->quadern->empresa->nom);
        $this->mostrar_capcalera();
        $this->form->mostrar();
        $this->mostrar_peu();
    }

    function processar_cancellar() {
        redirect($this->url);
    }

    function processar_desar() {
        if ($this->form->validar()) {
            $this->quadern->empresa->adreca = $this->form->valor('adreca');
            $this->quadern->empresa->poblacio = $this->form->valor('poblacio');
            $this->quadern->empresa->codi_postal = $this->form->valor('codi_postal');
            $this->quadern->empresa->telefon = $this->form->valor('telefon');
            $this->quadern->empresa->fax = $this->form->valor('fax');
            $this->quadern->empresa->email = $this->form->valor('email');
            $this->quadern->empresa->nif = $this->form->valor('nif');
            $this->quadern->empresa->codi_agrupacio = $this->form->valor('codi_agrupacio');
            $this->quadern->empresa->sic = $this->form->valor('sic');
            $this->quadern->empresa->nom_responsable = $this->form->valor('nom_responsable');
            $this->quadern->empresa->cognoms_responsable = $this->form->valor('cognoms_responsable');
            $this->quadern->empresa->dni_responsable = $this->form->valor('dni_responsable');
            $this->quadern->empresa->carrec_responsable = $this->form->valor('carrec_responsable');
            $this->quadern->empresa->nom_tutor = $this->form->valor('nom_tutor');
            $this->quadern->empresa->cognoms_tutor = $this->form->valor('cognoms_tutor');
            $this->quadern->empresa->dni_tutor = $this->form->valor('dni_tutor');
            $this->quadern->empresa->email_tutor = $this->form->valor('email_tutor');
            $this->quadern->empresa->nom_lloc_practiques = $this->form->valor('nom_lloc_practiques');
            $this->quadern->empresa->adreca_lloc_practiques = $this->form->valor('adreca_lloc_practiques');
            $this->quadern->empresa->poblacio_lloc_practiques = $this->form->valor('poblacio_lloc_practiques');
            $this->quadern->empresa->codi_postal_lloc_practiques = $this->form->valor('codi_postal_lloc_practiques');
            $this->quadern->empresa->telefon_lloc_practiques = $this->form->valor('telefon_lloc_practiques');
            $this->diposit->afegir_quadern($this->quadern);
            if ($this->quadern->alumne == $this->usuari->id) {
                $this->serveis->registrar_avis($this->quadern, 'dades_empresa');
            }
            $this->registrar('update dades_empresa');
            redirect($this->url);
        }
        $this->mostrar();
    }

    function processar_editar() {
        $this->mostrar();
    }

    function processar_veure() {
        $this->mostrar();
        $this->registrar('view dades_empresa');
    }
}

