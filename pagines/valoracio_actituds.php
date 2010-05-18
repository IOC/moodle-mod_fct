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

class fct_form_valoracio_actituds extends fct_form_base {

    function configurar($pagina) {
        $elements = array();
        for ($i = 0; $i < 15; $i++) {
            $elements[$i] = fct_string('actitud_' . ($i+1));
        }

        $this->element('llista_menu', 'valoracio_actituds', $pagina->titol,
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

class fct_pagina_valoracio_actituds extends fct_pagina_base_valoracio {

    var $final;
    var $titol;

    function configurar() {
        parent::configurar(required_param('quadern', PARAM_INT));
        $this->final = required_param('final', PARAM_BOOL);
        $this->url = fct_url::valoracio_actituds($this->quadern->id, $this->final);
        $this->titol = ($this->final ? 'valoracio_final_actituds'
                        : 'valoracio_parcial_actituds');
        $this->subpestanya = ($this->final ? 'valoracio_actituds_final'
                              : 'valoracio_actituds_parcial');
        $this->form = new fct_form_valoracio_actituds($this);
    }

    function mostrar() {
        $this->mostrar_capcalera();
        $this->form->valor('valoracio_actituds',
                           fct_db::valoracio_actituds($this->quadern->id,
                                                      $this->final));
        $this->form->mostrar();
        $this->mostrar_peu();
    }

    function processar_cancellar() {
        redirect($this->url);
    }

    function processar_desar() {
        if ($this->form->validar()) {
            $ok = fct_db::actualitzar_valoracio_actituds(
                $this->quadern->id, $this->final,
                $this->form->valor('valoracio_actituds'));
            if ($ok) {
                $this->registrar('update valoracio_actituds', null,
                    $this->final ? 'final' : 'parcial');
            } else {
                $this->error('desar_valoracio_actituds');
            }
            redirect($this->url);
        }
        $this->mostrar();
    }

    function processar_editar() {
        $this->mostrar();
    }

    function processar_veure() {
        $this->mostrar();
        $this->registrar('view valoracio_acittuds', null,
            $this->final ? 'final' : 'parcial');
    }

}

