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

fct_require('pagines/base_dades_quadern.php',
            'pagines/form_dades_conveni.php');

class fct_pagina_dades_conveni extends fct_pagina_base_dades_quadern {

    var $conveni;
    var $form;

    function configurar() {
        parent::configurar(required_param('quadern', PARAM_INT));
        $this->conveni = fct_db::dades_conveni($this->quadern->id);
        if (!$this->conveni) {
            $this->error('recuperar_covneni');
        }

        $this->configurar_accio(array('veure', 'editar', 'desar', 'cancellar'), 'veure');

        if ($this->accio != 'veure') {
            $this->comprovar_permis($this->permis_editar);
        }

        $this->url = fct_url::dades_conveni($this->quadern->id);
        $this->subpestanya = 'dades_conveni';
        $this->form = new fct_form_dades_conveni($this);
    }

    function mostrar() {
        $this->conveni->hores_realitzades = fct_db::hores_realitzades_quadern($this->quadern->id);
        $this->conveni->hores_pendents = $this->conveni->hores_practiques
            - $this->conveni->hores_realitzades;
        $this->form->set_data($this->conveni);
        $this->mostrar_capcalera();
        $this->form->display();
        $this->mostrar_peu();
    }

    function processar_cancellar() {
        redirect($this->url);
    }

    function processar_desar() {
        $data = $this->form->get_data();
        if ($data) {
            $data->id = $this->conveni->id;
            $data->quadern = $this->conveni->quadern;
            $data->data_inici = $this->form->date2unix($data->data_inici);
            $data->data_final = $this->form->date2unix($data->data_final);
            $ok = fct_db::actualitzar_dades_conveni($data);
            if ($ok) {
                $this->registrar('update dades_conveni');
            } else {
                $this->error('desar_conveni');
            }
            redirect($this->url);
        }
        $this->mostrar();
    }

    function processar_editar() {
        if (!$this->conveni->data_inici) {
            $this->conveni->data_inici = (int) time();
        }
        if (!$this->conveni->data_final) {
            $this->conveni->data_final = (int) time();
        }
        $this->mostrar();
    }

    function processar_veure() {
        $this->mostrar();
        $this->registrar('view dades_relatives');
    }
}

