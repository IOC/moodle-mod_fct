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

class fct_form_cicle extends fct_form_base {

    function configurar() {
        $this->afegir_header('cicle_formatiu',
            $this->pagina->accio == 'afegir' ?
            fct_string('nou_cicle_formatiu') : fct_string('cicle_formatiu'));

        $this->afegir_text('nom', fct_string('nom'), 48, true);
        $this->afegir_textarea('activitats', fct_string('activitats'), 20, 60);

        $this->afegir_comprovacio('comprovar_nom');

        if ($this->pagina->accio == 'afegir'){
            $this->afegir_boto('afegir', fct_string('afegeix'));
            $this->afegir_boto_cancellar();
        } elseif ($this->pagina->accio == 'veure') {
            $this->afegir_boto_enllac('editar', fct_string('edita'));
            $this->afegir_boto_enllac('suprimir', fct_string('suprimeix'));
            $this->congelar();
        } else {
            $this->afegir_boto('desar', fct_string('desa'));
            $this->afegir_boto_cancellar();
        }
    }

}

