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

class fct_form_activitat extends fct_form_base {

    function configurar($pagina) {
        $this->element('capcalera', 'acivitat',
                       $pagina->accio == 'afegir' ? 'nova_activitat' : '');
        $this->element('areatext', 'descripcio', 'descripcio',
                       array('required' => true));

        $this->comprovacio($pagina, 'comprovar_descripcio');

        if ($pagina->accio == 'afegir') {
            $this->element('boto', 'afegir', 'afegeix');
        } else {
            $this->element('boto', 'desar', 'desa');
            $this->element('boto', 'cancellar');
        }
    }

}

