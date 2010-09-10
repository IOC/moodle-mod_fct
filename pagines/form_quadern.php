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

fct_require('pagines/form_base.php');

class fct_form_quadern extends fct_form_base {

    function configurar($pagina) {
        $this->element('capcalera', 'quadern',
                       $pagina->accio == 'afegir' ? 'nou_quadern' : '');

        $opcions = $this->opcions_usuari($pagina, 'mod/fct:alumne',
                                         $pagina->alumne(), false);
        $this->element('menu', 'alumne', 'alumne',
                       array('opcions' => $opcions));

        $this->element('text', 'nom_empresa', 'empresa',
                       array('required' => true, 'size' => 48));

        $opcions = $this->opcions_usuari($pagina, 'mod/fct:tutor_centre',
                                         $pagina->tutor_centre());
        $this->element('menu', 'tutor_centre', 'tutor_centre',
                       array('opcions' => $opcions));

        $opcions = $this->opcions_usuari($pagina, 'mod/fct:tutor_empresa',
                                         $pagina->tutor_empresa());
        $this->element('menu', 'tutor_empresa', 'tutor_empresa',
                       array('opcions' => $opcions));

        $this->element('menu', 'cicle', 'cicle_formatiu',
                       array('opcions' => $this->opcions_cicle($pagina)));

        $opcions = array();
        foreach (array('proposat', 'obert', 'tancat') as $estat) {
            $opcions[$estat] = '<span class="estat_' . $estat . '">'
                . fct_string("estat_$estat") . '</span>';
        }
        $this->element('menu' , 'estat', 'estat', array('opcions' => $opcions));

        $this->comprovacio($pagina, 'comprovar_nom_empresa');

        if ($pagina->accio == 'afegir') {
            $this->element('boto', 'afegir', 'afegeix');
            if (!$pagina->permis_admin) {
                $this->ocultar(array('alumne', 'tutor_centre',
                                     'tutor_empresa', 'estat'));
            }
        } else if ($pagina->accio == 'veure') {
            if ($pagina->permis_admin) {
                $this->element('boto', 'editar', 'edita');
                $this->element('boto', 'suprimir', 'suprimeix');
            }
            $this->congelar();
        } else {
            $this->element('boto', 'desar', 'desa');
            $this->element('boto', 'cancellar');
        }
    }

    function opcions_cicle($pagina) {
        $opcions = array();
        $cicles = $pagina->diposit->cicles($pagina->fct->id);
        foreach ($cicles as $cicle) {
            $opcions[$cicle->id] = $cicle->nom;
        }
        return $opcions;
    }

    function opcions_usuari($pagina, $capability, $usuari, $usuari_nul=true) {
        $opcions = array();
        if ($usuari_nul) {
            $opcions[0] = '';
        }

        $email = ($pagina->accio == 'vuere');

        $context = get_context_instance(CONTEXT_MODULE, $pagina->cm->id);
        $records = get_users_by_capability($context, $capability,
                                           'u.id, u.firstname, u.lastname',
                                           'u.firstname, u.lastname',
                                           '', '', '', '', false);
        if ($records) {
            foreach ($records as $id => $record) {
                $opcions[$id] = $pagina->nom_usuari($record, true, $email);
            }
        }

        if ($usuari) {
            $opcions[$usuari] = $pagina->nom_usuari($usuari, true, $email);
        }

        return $opcions;
    }
}
