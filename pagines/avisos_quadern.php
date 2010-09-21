<?php
/* Quadern virtual d'FCT

   Copyright © 2009,2010  Institut Obert de Catalunya

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

fct_require('pagines/base_dades_quadern');

class fct_pagina_avisos_quadern extends fct_pagina_base_quadern {

    function configurar() {
        parent::configurar(required_param('quadern', PARAM_INT));
        $this->configurar_accio(array('veure', 'suprimir'), 'veure');

        $this->comprovar_permis($this->permis_tutor_centre);

        $this->url = fct_url('avisos_quadern', array('quadern' => $this->quadern->id));
        $this->pestanya = 'avisos';
    }

    function processar_veure() {
        $this->mostrar_capcalera();
        print_object($this->diposit->avisos_quadern($this->quadern->id));
        $this->mostrar_peu();
        $this->registrar('view avisos_quadern');
    }
}

