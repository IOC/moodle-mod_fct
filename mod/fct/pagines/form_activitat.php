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

class fct_form_activitat extends fct_form_base {

    function configurar() {

    	$this->afegir_header('activitat', $this->pagina->accio == 'afegir' ?
    		fct_string('nova_activitat'): fct_string('activitat'));

        $this->afegir_textarea('descripcio', fct_string('descripcio'), 3, 50, true);

        $this->afegir_comprovacio('comprovar_descripcio');

        if ($this->pagina->accio == 'afegir') {
            $this->afegir_boto('afegir', fct_string('afegeix'));
        } else {
            $this->afegir_boto('editar', fct_string('desa'));
        }

        $this->afegir_boto_cancellar();
    }

}

