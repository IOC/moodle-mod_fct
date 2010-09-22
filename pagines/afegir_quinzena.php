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

class fct_pagina_afegir_quinzena extends fct_pagina_base_seguiment {

    var $activitats;

    function comprovar_quinzena($valors) {
        $quinzenes = $this->diposit->quinzenes($this->quadern->id,
                                               $valors->any,
                                               $valors->periode);
        if ($quinzenes) {
            return array('any' => fct_string('quinzena_duplicada'),
                         'periode' => fct_string('quinzena_duplicada'));
        }
        $max_hores = $this->serveis->maxim_hores_quinzena(
            $this->quadern, $valors->any, $valors->periode, $valors->dies);
        if ($valors->hores > $max_hores) {
            return array('hores' => fct_string('hores_superior_horari'));
        }
        return true;
    }

    function configurar() {
        parent::configurar(required_param('quadern', PARAM_INT));
        $this->comprovar_permis($this->permis_editar_alumne);
        $this->configurar_accio(array('afegir', 'cancellar'), 'afegir');
        $this->url = fct_url('afegir_quinzena', array('quadern' => $this->quadern->id));
        $this->subpestanya = 'afegir_quinzena';
        $this->activitats = $this->diposit->activitats($this->quadern->id);
    }

    function processar_afegir() {
        $form = new fct_form_quinzena($this);
        if ($form->validar()) {
            $quinzena = new fct_quinzena;
            $quinzena->quadern = $this->quadern->id;
            $quinzena->any = $form->valor('any');
            $quinzena->periode = $form->valor('periode');
            $quinzena->hores = $form->valor('hores');
            $quinzena->valoracions = $form->valor('valoracions');
            $quinzena->observacions_alumne = $form->valor('observacions_alumne');
            if ($this->permis_editar_centre) {
                $quinzena->observacions_centre = $form->valor('observacions_centre');
            }
            if ($this->permis_editar_empresa) {
                $quinzena->observacions_empresa = $form->valor('observacions_empresa');
            }
            $quinzena->dies = $this->filtrar_dies($form->valor('dies'),
                                                  $form->valor('periode'),
                                                  $form->valor('any'));
            $quinzena->activitats = $form->valor('activitats_realitzades');
            $this->diposit->afegir_quinzena($quinzena);
            $this->registrar('add quinzena',
                             fct_url('quinzena', array('quinzena' => $quinzena->id)),
                             $this->nom_periode($quinzena->periode,
                                                $quinzena->any));
            redirect(fct_url('quinzena', array('quinzena' => $quinzena->id)));
        }

        list($any, $periode) = $this->quinzena_actual();
        $form->valor('any', $any);
        $form->valor('periode', $periode);
        $form->valor('any_inici',
                     $this->any_data($this->quadern->data_inici()));
        $form->valor('any_final',
                     $this->any_data($this->quadern->data_final()));
        $form->valor('periode_inici',
                     $this->periode_data($this->quadern->data_inici()));
        $form->valor('periode_final',
                     $this->periode_data($this->quadern->data_final()));
        $this->mostrar_capcalera();
        $form->mostrar();
        $this->mostrar_peu();
    }

    function processar_cancellar() {
        redirect(fct_url('seguiment', array('quadern' => $this->quadern->id)));
    }
}

