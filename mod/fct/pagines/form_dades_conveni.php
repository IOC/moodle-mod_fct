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

class fct_form_dades_conveni extends fct_form_base {

    function configurar() {
        $this->afegir_header( 'dades_conveni', fct_string('conveni'));

        $this->afegir_text('codi', fct_string('codi'), 32);
        $this->afegir_date('data_inici', fct_string('data_inici'));
        $this->afegir_date('data_final', fct_string('data_final'));
        $this->afegir_textarea('prorrogues', fct_string('prorrogues'), 3, 50);
        $this->afegir_text('hores_practiques', fct_string('hores_practiques'), 6, false, true);
        $this->afegir_static('hores_realitzades', fct_string('hores_practiques_realitzades'), '');
        $this->afegir_static('hores_pendents', fct_string('hores_practiques_pendents'), '');

        $this->afegir_comprovacio('comprovar_dates', $this);

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

    function comprovar_dates($data) {
        $data_inici = $this->date2unix($data['data_inici']);
        $data_final = $this->date2unix($data['data_final']);
        if ($data_inici >= $data_final) {
            return array('data_inici' => fct_string('anterior_data_final'),
            	'data_final' => fct_string('posterior_data_inici'));
        }
        return true;
    }
}

