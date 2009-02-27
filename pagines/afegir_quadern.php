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

fct_require('pagines/base_quaderns.php',
            'pagines/form_quadern.php');

class fct_pagina_afegir_quadern extends fct_pagina_base_quaderns {

    var $n_cicles;

    function comprovar_nom_empresa($data) {
        if (fct_db::quadern_duplicat($this->fct->id, addslashes($data['alumne']),
                addslashes($data['nom_empresa']))) {
            return array('nom_empresa' => fct_string('quadern_duplicat'));
        }
        return true;
    }

    function configurar() {
        $this->configurar_accio(array('afegir', 'cancellar'), 'afegir');
        parent::configurar(required_param('fct', PARAM_INT));
        $this->n_cicles = fct_db::nombre_cicles($this->fct->id);
        $this->comprovar_permis($this->permis_admin);
        $this->url = fct_url::afegir_quadern($this->fct->id);
        $this->subpestanya = 'afegir_quadern';
    }

    function processar_afegir() {
        $form = new fct_form_quadern($this);
        $data = $form->get_data();
        if ($data) {
           $quadern = (object) array(
                'fct' => $this->fct->id,
                'alumne' => $data->alumne,
                'nom_empresa' => $data->nom_empresa,
                'tutor_centre' => $data->tutor_centre,
                'tutor_empresa' => $data->tutor_empresa,
                'cicle' => $data->cicle,
                'estat' => $data->estat);
            $id = fct_db::afegir_quadern($quadern);
            if ($id) {
                $this->registrar('add quadern', fct_url::quadern($id),
                    $this->nom_usuari($data->alumne) . " ({$data->nom_empresa})");
            } else {
                $this->error('afegir_quadern');
            }
            redirect(fct_url::quadern($id));
        }

        $this->mostrar_capcalera();
        if (!$this->n_cicles) {
            $missatge = fct_string('cicle_necessari_per_afegir_quaderns');
            echo "<p>$missatge</p>";
        } else {
            $form->display();
        }
        $this->mostrar_peu();
    }

    function processar_cancellar() {
        redirect(fct_url::llista_quaderns($this->fct->id));
    }

    function alumne() {
        return false;
    }

    function tutor_centre() {
        return false;
    }

    function tutor_empresa() {
        return false;
    }
}

