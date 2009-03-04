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

class fct_pagina_base_activitat_pla extends fct_pagina_base_pla_activitats {

    var $activitat;

    function configurar($activitat_id) {
        $this->activitat = fct_db::activitat_pla($activitat_id);
        if (!$this->activitat) {
            $this->error('recuperar_activitat');
        }
        parent::configurar($this->activitat->quadern);
    }
}

