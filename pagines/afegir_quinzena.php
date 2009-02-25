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

fct_require('pagines/base_seguiment.php',
            'pagines/form_quinzena.php');

class fct_pagina_afegir_quinzena extends fct_pagina_base_seguiment {

    var $activitats;

    function comprovar_quinzena($data) {
        if (fct_db::quinzena_duplicada($this->quadern->id,
                addslashes($data['periode'][0]),
                addslashes($data['periode'][1]))) {
            return array('periode' => fct_string('quinzena_duplicada'));
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
        list($any, $periode) = self::quinzena_actual();
        $form = new fct_form_quinzena($this, $any, $periode);
        $data = $form->get_data();

        if ($data) {
            $quinzena = (object) array(
                'quadern' => $this->quadern->id,
                'any_' => $data->periode[0],
                'periode' => $data->periode[1],
                'hores' => $data->hores,
                'valoracions' => $data->valoracions,
                'observacions_alumne' => $data->observacions_alumne,
            );
            $dies = $form->get_data_calendari('dia',
                $this->calendari_periode($quinzena->any_, $quinzena->periode));
            $activitats = $form->get_data_llista('activitat');
            if ($this->permis_editar_centre) {
                $quinzena->observacions_centre = $data->observacions_centre;
            }
            if ($this->permis_editar_empresa) {
                $quinzena->observacions_empresa = $data->observacions_empresa;
            }

            $id = fct_db::afegir_quinzena($quinzena, $dies, $activitats);
            if ($id) {
                $this->registrar('add quinzena', fct_url::quinzena($id),
                    self::nom_periode($data->periode[1]) . ' '
                    . $data->periode[0]);
            } else {
                $this->error('afegir_quinzena');
            }
            redirect(fct_url::quinzena($id));
        }

        $form->set_data((object) array(
            'periode' => array(0 => $any, 1 => $periode)));
        $this->mostrar_capcalera();
        $form->display();
        $this->mostrar_peu();
    }

    function processar_cancellar() {
        redirect(fct_url::seguiment($this->quadern->id));
    }
}

