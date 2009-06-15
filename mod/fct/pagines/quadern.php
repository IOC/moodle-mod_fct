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
            'pagines/form_quadern.php');

class fct_pagina_quadern extends fct_pagina_base_quadern {

    function comprovar_nom_empresa($valors) {
        if (fct_db::quadern_duplicat($this->fct->id, addslashes($valors->alumne),
                addslashes($valors->nom_empresa), $this->quadern->id)) {
            return array('nom_empresa' => fct_string('quadern_duplicat'));
        }
        return true;
    }

    function configurar() {
        parent::configurar(required_param('quadern', PARAM_INT));
        $this->configurar_accio(array('veure', 'editar', 'desar',
            'cancellar', 'suprimir', 'confirmar'), 'veure');

        if ($this->accio != 'veure') {
            $this->comprovar_permis($this->permis_admin);
        }

        $this->url = fct_url::quadern($this->quadern->id);
        $this->pestanya = 'quadern';
        $this->form = new fct_form_quadern($this);
    }

    function mostrar() {
        $this->form->valors($this->quadern);
        $this->mostrar_capcalera();
        $this->form->mostrar();
        $this->mostrar_peu();
    }

    function processar_cancellar() {
        redirect($this->url);
    }

    function processar_confirmar() {
        $this->comprovar_sessio();
        if (!fct_db::suprimir_quadern($this->quadern->id)) {
            $this->error('suprimir_quadern');
        }
        redirect(fct_url::llista_quaderns($this->fct->id));
    }

    function processar_desar() {
        if ($this->form->validar()) {
            $quadern = $this->form->valors();
            $quadern->id = $this->quadern->id;
            $ok = fct_db::actualitzar_quadern($quadern);
            if ($ok) {
                $this->registrar('update quadern');
            } else {
                $this->error('desar_quadern');
            }
            redirect(fct_url::quadern($this->quadern->id));
        }
        $this->mostrar();
    }

    function processar_editar() {
        $this->mostrar();
    }

    function processar_suprimir() {
        $this->mostrar_capcalera();
        notice_yesno(fct_string('segur_suprimir_quadern', $this->titol),
            $this->url, fct_url::quadern($this->quadern->id),
            array('confirmar' => 1, 'sesskey' => sesskey()));
        $this->mostrar_peu();
    }

    function processar_veure() {
        $this->mostrar();
        $this->registrar('view quadern');
    }
}

