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

fct_require('pagines/base_activitat_pla.php');

class fct_pagina_suprimir_activitats_pla extends fct_pagina_base_pla_activitats  {

    function configurar() {
        $this->configurar_accio(array('suprimir', 'confirmar'), 'suprimir');
        parent::configurar( required_param('quadern', PARAM_INT));
        $this->url = fct_url::suprimir_activitats_pla($this->quadern->id);
        $this->comprovar_permis($this->permis_editar);
        $this->subpestanya = 'suprimir_activitats_pla';
    }

    function processar_confirmar() {
        $this->comprovar_sessio();
        $ok = fct_db::suprimir_activitats_pla($this->quadern->id);
        if ($ok) {
            $this->registrar('delete activitats_pla',
                fct_url::pla_activitats($this->quadern->id));
        } else {
           $this->error('suprimir_activitats');
        }
        redirect(fct_url::pla_activitats($this->quadern->id));
    }

    function processar_suprimir() {
        $this->mostrar_capcalera();
        notice_yesno(fct_string('segur_suprimir_activitats'),
            $this->url, fct_url::pla_activitats($this->quadern->id),
            array('confirmar' => 1, 'sesskey' => sesskey()));
        $this->mostrar_peu();
    }

}

