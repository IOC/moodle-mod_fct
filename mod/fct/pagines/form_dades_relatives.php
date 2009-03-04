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

class fct_form_dades_relatives extends fct_form_base {

    function configurar() {
        $this->afegir_header('dades_relatives', fct_string('dades_relatives'),
                             'dades_relatives');
        $this->afegir_text('hores_credit', fct_string('hores_credit'), 6, false, true);
        $this->afegir_select('exempcio', fct_string('exempcio'),
            array(0 => '-', 25 => '25%', 50 => '50%'));
        $this->afegir_text('hores_anteriors', fct_string('hores_anteriors'),
                           6, false, true);
        if ($this->pagina->accio == 'veure') {
            $this->afegir_static('hores_realitzades_detall',
                                 fct_string('hores_realitzades'),
                                 fct_string('hores_realitzades_detall',
                                            $this->pagina->dades));
            $this->afegir_static('hores_pendents', fct_string('hores_pendents'), '');
        }

        if ($this->pagina->accio == 'veure') {
            if ($this->pagina->permis_editar) {
                $this->afegir_boto_enllac('editar', 'Edita');
            }
            $this->congelar();
        } else {
            $this->afegir_boto('desar', 'Desa');
            $this->afegir_boto_cancellar();
        }
    }

}

