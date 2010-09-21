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

fct_require('pagines/form_base');

class fct_form_cicle extends fct_form_base {

    function configurar($pagina) {
        $this->element('capcalera', 'cicle_formatiu',
                       $pagina->accio == 'afegir' ? 'nou_cicle_formatiu' : '');
        $this->element('text', 'nom', 'nom',
                       array('size' => 48, 'required' => true));
        $this->element('areatext', 'activitats', 'activitats',
                       array('cols' => 60, 'rows' => 20));

        $this->comprovacio($pagina, 'comprovar_nom');

        if ($pagina->accio == 'afegir') {
            $this->element('boto', 'afegir', 'afegeix');
            $this->element('boto', 'cancellar');
        } elseif ($pagina->accio == 'veure') {
            $this->element('boto', 'editar', 'edita');
            $this->element('boto', 'suprimir', 'suprimeix');
            $this->congelar();
        } else {
            $this->element('boto', 'desar', 'desa');
            $this->element('boto', 'cancellar');
        }
    }
}
