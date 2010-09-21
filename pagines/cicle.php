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

fct_require('pagines/base_cicles', 'pagines/form_cicle');

class fct_pagina_cicle extends fct_pagina_base_cicles {

    var $cicle;
    var $n_quaderns;

    function comprovar_nom($valors) {
        if ($valors->nom != $this->cicle->nom) {
            if ($this->diposit->cicles($this->cicle->fct, $valors->nom)) {
                return array('nom' => fct_string('cicle_formatiu_duplicat'));
            }
        }
        return true;
    }

    function configurar() {
        $this->configurar_accio(array('veure', 'editar', 'desar', 'cancellar',
                                      'suprimir', 'confirmar'), 'veure');
        $this->cicle = $this->diposit->cicle(required_param('cicle', PARAM_INT));
        parent::configurar($this->cicle->fct);
        $this->url = fct_url('cicle', array('cicle' => $this->cicle->id));
        $this->form = new fct_form_cicle($this);
    }

    function mostrar() {
        $this->form->valor('nom', $this->cicle->nom);
        $this->form->valor('activitats',
                           implode("\n", $this->cicle->activitats));
        $this->mostrar_capcalera();
        $this->form->mostrar();
        $this->mostrar_peu();
    }

    function processar_cancellar() {
        redirect(fct_url('cicle', array('cicle' => $this->cicle->id)));
    }

    function processar_confirmar() {
        $this->comprovar_sessio();
        if ($this->cicle->n_quaderns == 0) {
            $this->diposit->suprimir_cicle($this->cicle);
            $this->registrar('delete cicle',
                             fct_url('llista_cicles', array('fct' => $this->fct->id)),
                             $this->cicle->nom);
        }
        redirect(fct_url('llista_cicles', array('cicle' => $this->fct->id)));
    }

    function processar_desar() {
        if ($this->form->validar()) {
            $this->cicle->nom = $this->form->valor('nom');
            $this->cicle->activitats = fct_linies_text(
                $this->form->valor('activitats'));
            $this->diposit->afegir_cicle($this->cicle);
            $this->registrar('update cicle',
                             fct_url('cicle', array('cicle' => $this->cicle->id)),
                             $this->cicle->nom);
            redirect(fct_url('cicle', array('cicle' => $this->cicle->id)));
        }

        $this->mostrar();
     }

    function processar_editar() {
        $this->mostrar();
    }

    function processar_suprimir() {
        $this->mostrar_capcalera();

        if ($this->cicle->n_quaderns) {
            $missatge = fct_string('cicle_formatiu_no_suprimible',
                                   array('nom_cicle' => $this->cicle->nom,
                                         'n_quaderns' => $this->cicle->n_quaderns));
            echo "<p>$missatge</p>";
        } else {
            notice_yesno(fct_string('segur_suprimir_cicle_formatiu', $this->cicle->nom),
                         $this->url, fct_url('cicle', array('cicle' => $this->cicle->id)),
                         array('confirmar' => 1, 'sesskey' => sesskey()));
        }

        $this->mostrar_peu();
    }

    function processar_veure() {
        $this->mostrar();
        $this->registrar('view cicle');
    }

}

