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

fct_require('pagines/base_valoracio.php',
            'pagines/form_base.php');

class fct_form_valoracio_activitats extends fct_form_base {

    function configurar($pagina) {
        $elements = array();
        foreach ($pagina->activitats as $activitat) {
            $elements[$activitat->id] = $activitat->descripcio;
        }
        $this->element('llista_menu', 'activitats', 'valoracio_activitats',
                       array('elements' => $elements,
                             'opcions' => $this->barem_valoracio()));

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

class fct_pagina_valoracio_activitats extends fct_pagina_base_valoracio {

    var $activitats;

    function configurar() {
        parent::configurar(required_param('quadern', PARAM_INT));
        $this->activitats = $this->diposit->activitats($this->quadern->id);
        $this->url = fct_url::valoracio_activitats($this->quadern->id);
        $this->subpestanya = 'valoracio_activitats';
        $this->form = new fct_form_valoracio_activitats($this, true);
    }

    function mostrar() {
        $this->mostrar_capcalera();
        if ($this->activitats) {
            $notes = array();
            foreach ($this->activitats as $activitat) {
                $notes[$activitat->id] = $activitat->nota;
            }
            $this->form->valor('activitats', $notes);
            $this->form->mostrar();
        } else {
            echo '<p>' . fct_string('cap_activitat') . '</p>';
        }
        $this->mostrar_peu();
    }

    function processar_cancellar() {
        redirect($this->url);
    }

    function processar_desar() {
        if ($this->form->validar()) {
            $notes = $this->form->valor('activitats');
            foreach ($this->activitats as $activitat) {
                if (isset($notes[$activitat->id])) {
                    $activitat->nota = $notes[$activitat->id];
                    $this->diposit->afegir_activitat($activitat);
                }
            }
            $this->registrar('update valoracio_activitats');
            redirect($this->url);
        }
        $this->mostrar();
    }

    function processar_editar() {
        $this->mostrar();
    }

    function processar_veure() {
        $this->mostrar();
        $this->registrar('view valoracio_activitats');
    }

}
