<?php
/* Quadern virtual d'FCT

   Copyright © 2011  Institut Obert de Catalunya

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

fct_require('pagines/base_valoracio', 'pagines/form_base');

class fct_form_valoracio_resultats extends fct_form_base {

    function elements() {
        foreach (array(1 => 11, 2 => 11, 3 => 8) as $seccio => $n) {
            $elements["$seccio"] = fct_string("resultat_aprenentatge_{$seccio}");
            for ($i = 1; $i <= $n; $i++) {
                $elements["$seccio.$i"] = fct_string("resultat_aprenentatge_{$seccio}.{$i}");
            }
        }
        return $elements;
    }

    function configurar($pagina) {
        $this->element('llista_menu', 'valoracio_resultats', 'valoracio_resultats',
                       array('elements' => $this->elements(),
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

class fct_pagina_valoracio_resultats extends fct_pagina_base_valoracio {

    var $final;
    var $titol;

    function configurar() {
        parent::configurar(required_param('quadern', PARAM_INT));
        $this->url = fct_url('valoracio_resultats', array('quadern' => $this->quadern->id));
        $this->subpestanya = 'valoracio_resultats';
        $this->form = new fct_form_valoracio_resultats($this);
    }

    function mostrar() {
        $this->mostrar_capcalera();
        $this->form->valor('valoracio_resultats', $this->quadern->valoracio_resultats);
        $this->form->mostrar();
        $this->mostrar_peu();
    }

    function processar_cancellar() {
        redirect($this->url);
    }

    function processar_desar() {
        if ($this->form->validar()) {
            $this->quadern->valoracio_resultats = $this->form->valor('valoracio_resultats');
            $this->diposit->afegir_quadern($this->quadern);
            if ($this->quadern->tutor_empresa == $this->usuari->id) {
               $this->serveis->registrar_avis($this->quadern, 'valoracio_resultats');
            }
            $this->registrar('update valoracio_resultats');
            redirect($this->url);
        }
        $this->mostrar();
    }

    function processar_editar() {
        $this->mostrar();
    }

    function processar_veure() {
        $this->mostrar();
        $this->registrar('view valoracio_resultats');
    }
}
