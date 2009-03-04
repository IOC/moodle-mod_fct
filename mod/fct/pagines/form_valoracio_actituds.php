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

class fct_form_valoracio_actituds extends fct_form_base {

    static function actituds() {
        $actituds = array();
        for ($i = 0; $i < 15; $i++) {
            $actituds[$i] = fct_string('actitud_' . ($i+1));
        }
        return $actituds;
    }

    function configurar() {
        $this->afegir_header('valoracio_actituds', $this->pagina->titol);

        $this->afegir_llista_select('actitud', $this->actituds(), $this->options_barem(), false, '');

        if ($this->pagina->accio == 'veure') {
            if ($this->pagina->permis_editar) {
                $this->afegir_boto_enllac('editar', fct_string('edita'));
            }
        } else {
            $this->afegir_boto('desar', fct_string('desa'));
            $this->afegir_boto_cancellar();
        }

         if ($this->pagina->accio == 'veure') {
            $this->congelar();
        }
    }

}

