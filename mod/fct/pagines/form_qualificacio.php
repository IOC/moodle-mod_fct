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

class fct_form_qualificacio extends fct_form_base {

    function configurar() {
        $this->afegir_header( 'header_qualificacio', $this->pagina->titol);

        $this->afegir_select('qualificacio', fct_string('qualificacio'),
            self::options_barem_qualificacio());
        $this->afegir_select('nota', '', self::options_barem(), false, '');
        $this->afegir_date('data', fct_string('data'));
        $this->afegir_textarea('observacions', fct_string('observacions'), 4, 40);

        if ($this->pagina->accio == 'veure') {
            if ($this->pagina->permis_editar) {
                $this->afegir_boto_enllac('editar', fct_string('edita'));
            }
            $this->congelar();
        } else {
            $this->afegir_boto('desar', fct_string('desa'));
            $this->afegir_boto_cancellar();
        }
    }

}

