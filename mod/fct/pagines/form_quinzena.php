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

class fct_form_quinzena extends fct_form_base {

    function configurar($pagina) {
        $this->element('capcalera', 'quinzena', $pagina->accio == 'afegir' ?
                       'nova_quinzena' : 'quinzena');
        $this->element('menu', 'any', 'any',
                       array('opcions' => $pagina->opcions_any()));
        $this->element('menu', 'periode', 'periode',
                       array('opcions' => $pagina->opcions_periode()));
        $this->element('nombres', 'dies', 'dies');
        $this->element('hores', 'hores', 'hores', array('minuts' => true));

        $this->element('ocult', 'any_inici');
        $this->element('ocult', 'any_final');
        $this->element('ocult', 'periode_inici');
        $this->element('ocult', 'periode_final');

        if ($pagina->activitats) {
            $this->element('llista', 'activitats_realitzades',
                           'activitats_realitzades',
                           array('elements' => $pagina->activitats));
        }

        $this->element('capcalera', 'observacions', 'valoracions_observacions');
        $this->element('areatext' , 'valoracions', 'valoracions');
        $this->element('areatext' , 'observacions_alumne', 'observacions');

        if ($pagina->accio != 'afegir' or $pagina->permis_editar_centre) {
            $this->element('capcalera', 'retroaccio', 'retroaccio');
            $this->element('areatext' , 'observacions_centre', 'tutor_centre');
            $this->element('areatext' , 'observacions_empresa', 'tutor_empresa');
        }

        if ($pagina->accio == 'veure' or !$pagina->permis_editar_alumne) {
            $this->element('ocult', 'any_quinzena');
            $this->element('ocult', 'periode_quinzena');
            $this->element('ocult', 'dies_quinzena');
        }

        if ($pagina->permis_editar_alumne) {
            $this->comprovacio($pagina, 'comprovar_quinzena');
        }

        if ($pagina->accio == 'veure') {
            if ($pagina->permis_editar) {
                $this->element('boto', 'editar', 'edita');
                if ($pagina->permis_editar_alumne) {
                    $this->element('boto', 'suprimir', 'suprimeix');
                }
            }
        } else if ($pagina->accio == 'afegir') {
            $this->element('boto', 'afegir', 'afegeix');
            $this->element('boto', 'cancellar');
        } else {
            $this->element('boto', 'desar', 'desa');
            $this->element('boto', 'cancellar');
        }

        if (!$pagina->permis_editar_alumne) {
            $this->congelar(array('any', 'periode', 'dies', 'hores',
                                  'activitats_realitzades',
                                  'valoracions', 'observacions_alumne'));
        }
        if ($pagina->accio != 'afegir' and !$pagina->permis_editar_centre) {
            $this->congelar('observacions_centre');
        }
        if ($pagina->accio != 'afegir' and !$pagina->permis_editar_empresa) {
            $this->congelar('observacions_empresa');
        }
        if ($pagina->accio == 'veure') {
            $this->congelar();
        }
    }

}

