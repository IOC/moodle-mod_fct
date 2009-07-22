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

fct_require('pagines/base_pla_activitats.php');

class fct_pagina_suprimir_activitat_pla extends fct_pagina_base_pla_activitats  {

    var $activitat;

    function configurar() {
        $this->configurar_accio(array('suprimir', 'confirmar'), 'suprimir');
        $id = required_param('activitat', PARAM_INT);
        $this->activitat = $this->diposit->activitat($id);
        parent::configurar($this->activitat->quadern);
        $this->url = fct_url::suprimir_activitat_pla($this->activitat->id);
        $this->comprovar_permis($this->permis_editar);
    }

    function processar_confirmar() {
        $this->comprovar_sessio();
        $this->diposit->suprimir_activitat($this->activitat);
        $this->registrar('delete activitat_pla',
                         fct_url::pla_activitats($this->quadern->id),
                         $this->activitat->descripcio);
        redirect(fct_url::pla_activitats($this->quadern->id));
    }

    function processar_suprimir() {
        $this->mostrar_capcalera();
        notice_yesno(fct_string('segur_suprimir_activitat')
            . '</p><p>' . $this->activitat->descripcio,
            $this->url, fct_url::pla_activitats($this->quadern->id),
            array('confirmar' => 1, 'sesskey' => sesskey()));
        $this->mostrar_peu();
    }

}

