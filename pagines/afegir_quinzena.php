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

fct_require('pagines/base_seguiment.php',
            'pagines/form_quinzena.php');

class fct_pagina_afegir_quinzena extends fct_pagina_base_seguiment {

    var $activitats;

    function comprovar_quinzena($valors) {
        if (fct_db::quinzena_duplicada($this->quadern->id,
                addslashes($valors->any),
                addslashes($valors->periode))) {
            return array('any' => fct_string('quinzena_duplicada'),
                         'periode' => fct_string('quinzena_duplicada'));
        }
        return true;
    }

    function configurar() {
        parent::configurar(required_param('quadern', PARAM_INT));
        $this->comprovar_permis($this->permis_editar_alumne);
        $this->configurar_accio(array('afegir', 'cancellar'), 'afegir');
        $this->url = fct_url::afegir_quinzena($this->quadern->id);
        $this->subpestanya = 'afegir_quinzena';
        $this->activitats = fct_db::activitats_pla($this->quadern->id);
    }

    function processar_afegir() {
        $form = new fct_form_quinzena($this);
        if ($form->validar()) {
            $quinzena = (object) array(
                'quadern' => $this->quadern->id,
                'any' => $form->valor('any'),
                'periode' => $form->valor('periode'),
                'hores' => $form->valor('hores'),
                'valoracions' => $form->valor('valoracions'),
                'observacions_alumne' => $form->valor('observacions_alumne'),
            );
            if ($this->permis_editar_centre) {
                $quinzena->observacions_centre = $form->valor('observacions_centre');
            }
            if ($this->permis_editar_empresa) {
                $quinzena->observacions_empresa = $form->valor('observacions_empresa');
            }
            $dies = $this->filtrar_dies($form->valor('dies'),
                                        $form->valor('periode'),
                                        $form->valor('any'));
            $id = fct_db::afegir_quinzena($quinzena, $dies,
                                          $form->valor('activitats_realitzades'));
            if ($id) {
                $this->registrar('add quinzena', fct_url::quinzena($id),
                                 $this->nom_periode($form->valor('periode'),
                                                    $form->valor('any')));
            } else {
                $this->error('afegir_quinzena');
            }
            redirect(fct_url::quinzena($id));
        }

        list($any, $periode) = $this->quinzena_actual();
        $form->valor('any', $any);
        $form->valor('periode', $periode);
        $form->valor('any_inici',
                     $this->any_data($this->data_inici));
        $form->valor('any_final',
                     $this->any_data($this->data_final));
        $form->valor('periode_inici',
                     $this->periode_data($this->data_inici));
        $form->valor('periode_final',
                     $this->periode_data($this->data_final));
        $this->mostrar_capcalera();
        $form->mostrar();
        $this->mostrar_peu();
    }

    function processar_cancellar() {
        redirect(fct_url::seguiment($this->quadern->id));
    }
}

