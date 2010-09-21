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

fct_require('pagines/form_base');

class fct_form_qualificacio extends fct_form_base {

    function configurar($pagina) {
        $this->element('capcalera', 'qualificacio_quadern',  $pagina->titol);
        $this->element('menu', 'apte', 'qualificacio',
                       array('opcions' => $this->barem_qualificacio()));
        $this->element('menu', 'nota', false,
                       array('opcions' => $this->barem_valoracio()));
        $this->element('data', 'data', 'data', array('opcional' => true));
        $this->element('areatext', 'observacions', 'observacions');

        if ($pagina->accio == 'veure') {
            if ($pagina->permis_editar) {
                $this->element('boto', 'editar', 'edita');
            }
            $this->congelar();
        } else {
            $this->element('boto', 'desar' ,'desa');
            $this->element('boto', 'cancellar');
        }
    }

}

