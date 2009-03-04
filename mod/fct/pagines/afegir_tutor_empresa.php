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

fct_require('pagines/base.php',
            'pagines/form_tutor_empresa.php');

class fct_pagina_afegir_tutor_empresa extends fct_pagina_base {

    function comprovar_dni($data) {
        $dni = trim(addslashes($data['dni']));
        if (!preg_match('/^[0-9]{8}[a-zA-Z]$/', $dni)) {
            return array('dni' => fct_string('dni_no_valid'));
        }
        if (record_exists('user', 'username', $dni)) {
            return array('dni' => fct_string('dni_existent'));
        }
        return true;
    }

    function comprovar_email($data) {
        $email = trim(addslashes($data['email']));
        if (!empty($email) and !validate_email($email)) {
            return array('email' => get_string('invalidemail'));
        }
        return true;
    }

    function comprovar_nom($data) {
        $nom = trim(addslashes($data['nom']));
        $cognoms = trim(addslashes($data['cognoms']));
        $count = count_records_select('user',
                     "firstname LIKE '$nom' AND lastname LIKE '$cognoms'");
        if ($count > 0) {
            return array('nom' => fct_string('usuari_duplicat'),
                         'cognoms' => fct_string('usuari_duplicat'));
        }
        return true;
    }

    function configurar() {
        $this->configurar_accio(array('afegir', 'cancellar'), 'afegir');
        parent::configurar(required_param('fct', PARAM_INT));
        $this->comprovar_permis($this->permis_admin);
        $this->url = fct_url::afegir_tutor_empresa($this->fct->id);
        $this->pestanya = 'afegir_tutor_empresa';
        $this->afegir_navegacio(fct_string('afegeix_tutor_empresa'), $this->url);
    }

    function generar_contrasenya() {
        $contrasenya = '';
        for ($i = 0; $i < 4; $i++) {
            $contrasenya .= chr(rand(ord('A'), ord('Z')));
        }
        for ($i = 0; $i < 2; $i++) {
            $contrasenya .= rand(0, 9);
        }
        return $contrasenya;
    }

    function processar_afegir() {
        $form = new fct_form_tutor_empresa($this);
        $data = $form->get_data();
        if ($data) {
            $data->dni = strtolower($data->dni);
            $contrasenya = $this->generar_contrasenya();
            $id = fct_db::afegir_tutor_empresa($this->course->id, $data->dni, $contrasenya,
                                               $data->nom, $data->cognoms, $data->email);
            if ($id) {
                global $CFG;
                $url = "{$CFG->wwwroot}/user/view.php?id=$id&course={$this->course->id}";
                $this->registrar('add tutor_empresa', $url, $id);
            } else {
                $this->error('afegir_tutor_empresa');
            }

            $this->mostrar_capcalera();
            echo '<dl><dt>' . fct_string('tutor_de_empresa')
                . "</dt><dd>{$data->nom} {$data->cognoms}</dd><dt>"
                . fct_string('nom_usuari') . "</dt><dd>{$data->dni}</dd><dt>"
                . fct_string('contrasenya') . "</dt><dd>$contrasenya</dd>";
            $this->mostrar_peu();            
            return;
        }

        $this->mostrar_capcalera();
        $form->display();
        $this->mostrar_peu();
    }

    function processar_cancellar() {
        redirect(fct_url::llista_quaderns($this->fct->id));
    }

}

