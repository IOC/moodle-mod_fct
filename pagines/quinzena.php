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

class fct_pagina_quinzena extends fct_pagina_base_seguiment {

    var $quinzena;
    var $titol;
    var $form;
    var $activitats;
    var $activitats_quinzena;
    var $dies_quinzena;

    function comprovar_quinzena($valors) {
        if (fct_db::quinzena_duplicada($this->quadern->id,
                                       addslashes($valors->any),
                                       addslashes($valors->periode),
                                       $this->quinzena->id)) {
            return array('any' => fct_string('quinzena_duplicada'),
                         'periode' => fct_string('quinzena_duplicada'));
        }
        return true;
    }

    function configurar() {
        $this->quinzena = fct_db::quinzena(required_param('quinzena', PARAM_INT));
        if (!$this->quinzena) {
            $this->error('recuperar_quinzena');
        }

        parent::configurar($this->quinzena->quadern);

        $this->configurar_accio(array('veure', 'editar', 'desar', 'suprimir',
        	'confirmar','cancellar'), 'veure');

        if ($this->accio != 'veure') {
            $this->comprovar_permis($this->permis_editar);
        }
        if ($this->accio == 'suprimir' or $this->accio == 'confirmar') {
            $this->comprovar_permis($this->permis_editar_alumne);
        }

        $this->activitats = fct_db::activitats_pla($this->quadern->id);
        $this->activitats_quinzena = fct_db::activitats_quinzena($this->quinzena->id);
        $this->dies_quinzena = fct_db::dies_quinzena($this->quinzena->id);

        $this->titol = self::nom_periode($this->quinzena->periode).' '.$this->quinzena->any_;
        $this->url = fct_url::quinzena($this->quinzena->id);

        $this->form = new fct_form_quinzena($this);
    }

    function mostrar() {
        $this->form->valors($this->quinzena);
        $this->form->valor('dies', $this->dies_quinzena);
        $this->form->valor('activitats_realitzades', $this->activitats_quinzena);
        $this->form->valor('any_inici',
                           $this->any_data($this->data_inici));
        $this->form->valor('any_final',
                           $this->any_data($this->data_final));
        $this->form->valor('periode_inici',
                           $this->periode_data($this->data_inici));
        $this->form->valor('periode_final',
                           $this->periode_data($this->data_final));
        $this->mostrar_capcalera();
        $this->form->mostrar();
        $this->mostrar_peu();
    }

    function processar_cancellar() {
        redirect($this->url);
    }

    function processar_confirmar() {
        $this->comprovar_sessio();
        $ok = fct_db::suprimir_quinzena($this->quinzena->id);
        if ($ok) {
            $this->registrar('delete quinzena', null, $this->titol);
        } else {
            $this->error('suprimir_quinzena');
        }
        redirect(fct_url::seguiment($this->quadern->id));
    }

    function processar_desar() {
        if ($this->form->validar()) {
            $quinzena = (object) array('id' => $this->quinzena->id);
            $activitats = false;
            $dies = false;

            if ($this->permis_editar_alumne) {
                $quinzena->any = $this->form->valor('any');
                $quinzena->periode = $this->form->valor('periode');
                $quinzena->hores = $this->form->valor('hores');
                $quinzena->valoracions = $this->form->valor('valoracions');
                $quinzena->observacions_alumne = $this->form->valor('observacions_alumne');
                $dies = $this->filtrar_dies($this->form->valor('dies'),
                                            $this->form->valor('periode'),
                                            $this->form->valor('any'));
                $activitats = $this->form->valor('activitats_realitzades');
            }
            if ($this->permis_editar_centre) {
                $quinzena->observacions_centre = $this->form->valor('observacions_centre');
            }
            if ($this->permis_editar_empresa) {
                $quinzena->observacions_empresa = $this->form->valor('observacions_empresa');
            }

            $ok = fct_db::actualitzar_quinzena($quinzena, $dies, $activitats);
            if ($ok) {
                $this->registrar('update quinzena', null, $this->titol);
            } else {
               $this->error('desar_quinzena');
            }
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
        $this->form->valor('dies_quinzena', implode(',', $this->dies_quinzena));
        $this->mostrar();
        $this->registrar('view quinzena', null, $this->titol);
    }

}
