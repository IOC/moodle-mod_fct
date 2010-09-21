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

fct_require('pagines/base_cicles.php',
            'pagines/form_cicle.php');

class fct_pagina_afegir_cicle extends fct_pagina_base_cicles {

    function comprovar_nom($valors) {
        if (fct_db::cicle_duplicat($this->fct->id, addslashes($valors->nom))) {
            return array('nom' => fct_string('cicle_formatiu_duplicat'));
        }

        return true;
    }

    function configurar() {
        $this->configurar_accio(array('afegir', 'cancellar'), 'afegir');
        parent::configurar(required_param('fct', PARAM_INT));
        $this->url = fct_url::afegir_cicle($this->fct->id);
        $this->subpestanya = 'afegir_cicle';
    }

    function processar_afegir() {
        $form = new fct_form_cicle($this);

        if ($form->validar()) {
            $id = fct_db::afegir_cicle($this->fct->id, $form->valor('nom'),
                                       $form->valor('activitats'));
            if ($id) {
                $this->registrar('add cicle', fct_url::cicle($id),
                                 $form->valor('nom'));
            } else {
                $this->error('afegir_cicle');
            }
            redirect(fct_url::cicle($id));
        }

        $this->mostrar_capcalera();
        $form->mostrar();
        $this->mostrar_peu();
    }

    function processar_cancellar() {
        redirect(fct_url::llista_cicles($this->fct->id));
    }

}

