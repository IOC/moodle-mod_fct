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

fct_require('pagines/base_dades_quadern', 'pagines/form_dades_centre');

class fct_pagina_dades_centre_quadern extends fct_pagina_base_dades_quadern {

    var $centre;

    function configurar() {
        parent::configurar(required_param('quadern', PARAM_INT));
        $this->url = fct_url('dades_centre_quadern', array('quadern' => $this->quadern->id));
        $this->subpestanya = 'dades_centre_quadern';
    }

    function processar() {
        $this->mostrar_capcalera();
        $form = new fct_form_dades_centre($this);
        $form->valors($this->fct->centre);
        $form->mostrar();
        $this->mostrar_peu();
        $this->registrar('view dades_centre_quadern');
    }
}

