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

fct_require('pagines/base', 'pagines/form_base');

class fct_form_tutor_empresa extends fct_form_base {

    function configurar($pagina) {
        $this->element('capcalera', 'tutor_empresa', 'tutor_de_empresa');
        $this->element('text', 'dni', 'dni',
                       array('size' => 48, 'required' => true));
        $this->element('text', 'nom', 'nom',
                       array('size' => 48, 'required' => true));
        $this->element('text', 'cognoms', 'cognoms',
                       array('size' => 48, 'required' => true));
        $this->element('text', 'email', 'email',
                       array('size' => 48, 'required' => true));

        $this->comprovacio($pagina, 'comprovar_dni');
        $this->comprovacio($pagina, 'comprovar_nom');
        $this->comprovacio($pagina, 'comprovar_email');

        $this->element('boto', 'afegir', 'afegeix');
    }
}

class fct_pagina_afegir_tutor_empresa extends fct_pagina_base {

    function comprovar_dni($data) {
        global $CFG;
        $dni = strtolower(trim($data->dni));
        if (!preg_match('/^[0-9]{8}[a-z]$/', $dni)) {
            return array('dni' => fct_string('dni_no_valid'));
        }
        if (record_exists('user', 'username', $dni, 'deleted', 0,
                          'mnethostid', $CFG->mnet_localhost_id)) {
            return array('dni' => fct_string('dni_existent'));
        }
        return true;
    }

    function comprovar_email($valors) {
        $email = addslashes(trim($valors->email));
        if (!$email or !validate_email($email)) {
            return array('email' => get_string('invalidemail'));
        }
        return true;
    }

    function comprovar_nom($valors) {
        global $CFG;
        $nom = addslashes(trim($valors->nom));
        $cognoms = addslashes(trim($valors->cognoms));
        $select = ("deleted = 0 AND mnethostid = {$CFG->mnet_localhost_id}" .
                   " AND firstname LIKE '$nom' AND lastname LIKE '$cognoms'");
        $count = count_records_select('user', $select);
        if ($count > 0) {
            return array('nom' => fct_string('usuari_duplicat'),
                         'cognoms' => fct_string('usuari_duplicat'));
        }
        return true;
    }

    function configurar() {
        parent::configurar(required_param('fct', PARAM_INT));
        $this->comprovar_permis($this->usuari->es_administrador);
        $this->url = fct_url('afegir_tutor_empresa', array('fct' => $this->fct->id));
        $this->pestanya = 'afegir_tutor_empresa';
        $this->afegir_navegacio(fct_string('afegeix_tutor_empresa'), $this->url);
    }

    function processar() {
        global $CFG;

        $form = new fct_form_tutor_empresa($this);
        if ($form->validar()) {
            $username = trim(strtolower($form->valor('dni')));
            $id = $this->moodle->create_user($username,
                                             trim($form->valor('email')),
                                             trim($form->valor('nom')),
                                             trim($form->valor('cognoms')));
            $this->moodle->assign_role($id, $this->fct->course, 'tutorempresa');
            $url = "{$CFG->wwwroot}/user/view.php?id=$id&course={$this->fct->course}";
            $this->registrar('add tutor_empresa', $url, $id);

            $this->mostrar_capcalera();
            $nom = $form->valor('nom') . ' ' . $form->valor('cognoms');
            echo '<p>' . fct_string('afegit_tutor_empresa', $nom) . '</p>';
            print_continue($this->url);
            $this->mostrar_peu();
            return;
        }

        $this->mostrar_capcalera();
        $form->mostrar();
        $this->mostrar_peu();
    }
}
