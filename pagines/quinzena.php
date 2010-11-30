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

fct_require('pagines/base_seguiment', 'pagines/form_quinzena');

class fct_pagina_quinzena extends fct_pagina_base_seguiment {

    var $quinzena;
    var $titol;
    var $form;
    var $activitats;

    function comprovar_quinzena($valors) {
        if ($valors->any != $this->quinzena->any
            or $valors->periode != $this->quinzena->periode) {
            $quinzenes = $this->diposit->quinzenes($this->quadern->id,
                                                   $valors->any,
                                                   $valors->periode);
            if ($quinzenes) {
                return array('any' => fct_string('quinzena_duplicada'),
                             'periode' => fct_string('quinzena_duplicada'));
            }
        }
        $max_hores = $this->serveis->maxim_hores_quinzena(
            $this->quadern, $valors->any, $valors->periode, $valors->dies);
        if ($valors->hores > $max_hores) {
            return array('hores' => fct_string('hores_superior_horari'));
        }
        return true;
    }

    function configurar() {
        $quinzena_id = required_param('quinzena', PARAM_INT);
        $this->quinzena = $this->diposit->quinzena($quinzena_id);

        parent::configurar($this->quinzena->quadern);

        $this->configurar_accio(array('veure', 'editar', 'desar', 'suprimir',
        	'confirmar','cancellar'), 'veure');

        if ($this->accio != 'veure') {
            $this->comprovar_permis($this->permis_editar);
        }
        if ($this->accio == 'suprimir' or $this->accio == 'confirmar') {
            $this->comprovar_permis($this->permis_editar_alumne);
        }

        $this->activitats = $this->diposit->activitats($this->quadern->id);

        $this->titol = self::nom_periode($this->quinzena->periode).' '.$this->quinzena->any;
        $this->url = fct_url('quinzena', array('quinzena' => $this->quinzena->id));

        $this->form = new fct_form_quinzena($this);
    }

    function mostrar() {
        $this->form->valors($this->quinzena);
        $this->form->valor('dies', $this->quinzena->dies);
        $this->form->valor('activitats_realitzades', $this->quinzena->activitats);
        $this->form->valor('any_inici',
                           $this->any_data($this->quadern->data_inici()));
        $this->form->valor('any_final',
                           $this->any_data($this->quadern->data_final()));
        $this->form->valor('periode_inici',
                           $this->periode_data($this->quadern->data_inici()));
        $this->form->valor('periode_final',
                           $this->periode_data($this->quadern->data_final()));
        $this->mostrar_capcalera();
        $this->form->mostrar();
        $this->mostrar_peu();
    }

    function processar_cancellar() {
        redirect($this->url);
    }

    function processar_confirmar() {
        $this->comprovar_sessio();
        $this->serveis->suprimir_quinzena($this->quinzena);
        if ($this->quadern->alumne == $this->usuari->id) {
            $this->serveis->registrar_avis($this->quadern, 'quinzena_suprimida',
                                           $this->quinzena->id);
        }
        $this->registrar('delete quinzena', null, $this->titol);
        redirect(fct_url('seguiment', array('quadern' => $this->quadern->id)));
    }

    function processar_desar() {
        if ($this->form->validar()) {
            if ($this->permis_editar_alumne) {
                $this->quinzena->any = $this->form->valor('any');
                $this->quinzena->periode = $this->form->valor('periode');
                $this->quinzena->hores = $this->form->valor('hores');
                $this->quinzena->valoracions = $this->form->valor('valoracions');
                $this->quinzena->observacions_alumne = $this->form->valor('observacions_alumne');
                $this->quinzena->dies = $this->filtrar_dies($this->form->valor('dies'),
                                                            $this->form->valor('periode'),
                                                            $this->form->valor('any'));
                $this->quinzena->activitats = $this->form->valor('activitats_realitzades');
            }
            if ($this->permis_editar_centre) {
                $this->quinzena->observacions_centre = $this->form->valor('observacions_centre');
            }
            if ($this->permis_editar_empresa) {
                $this->quinzena->observacions_empresa = $this->form->valor('observacions_empresa');
            }

            $this->diposit->afegir_quinzena($this->quinzena);
            if ($this->quadern->alumne == $this->usuari->id) {
                $this->serveis->registrar_avis($this->quadern, 'quinzena_alumne',
                                               $this->quinzena->id);
            }
            if ($this->quadern->tutor_empresa == $this->usuari->id) {
                $this->serveis->registrar_avis($this->quadern, 'quinzena_empresa',
                                               $this->quinzena->id);
            }
            $this->registrar('update quinzena', null, $this->titol);
            redirect($this->url);
        }

        $this->mostrar();
    }

    function processar_editar() {
        $this->mostrar();
    }

    function processar_suprimir() {
        $this->mostrar_capcalera();
        notice_yesno(fct_string('segur_suprimir_quinzena', $this->titol),
            $this->url, $this->url, array('confirmar' => 1, 'sesskey' => sesskey()));
        $this->mostrar_peu();
    }

    function processar_veure() {
        $this->form->valor('any_quinzena', $this->quinzena->any);
        $this->form->valor('periode_quinzena', $this->quinzena->periode);
        $this->form->valor('dies_quinzena', implode(',', $this->quinzena->dies));
        $this->mostrar();
        $this->registrar('view quinzena', null, $this->titol);
    }

}
