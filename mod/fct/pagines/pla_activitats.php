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

require_once($CFG->libdir . '/tablelib.php');
fct_require('pagines/base_pla_activitats.php');

class fct_pagina_pla_activitats extends fct_pagina_base_pla_activitats {

    function configurar() {
        parent::configurar(required_param('quadern', PARAM_INT));
        $this->url = fct_url::pla_activitats($this->quadern->id);
        $this->subpestanya = 'activitats_pla';
    }

    function processar() {
        $this->mostrar_capcalera();

        $taula = new flexible_table('fct_activitats');
        $taula->define_columns(array('descripcio', 'accions'));
        $taula->column_class('accions', 'columna_accions');
        $taula->define_headers(array(fct_string('descripcio'), ""));
        $taula->set_attribute('class', 'generaltable');
        $taula->setup();

        $activitats = $this->diposit->activitats($this->quadern->id);

        if (!$activitats) {
           echo '<p>' . fct_string('cap_activitat') . '</p>';
        } else {
            foreach ($activitats as $activitat) {
                $accions = '';
                if ($this->permis_editar) {
                    $url_editar = fct_url::editar_activitat_pla($activitat->id);
                    $url_suprimir = fct_url::suprimir_activitat_pla($activitat->id);
                    $icona_editar = $this->icona_editar($url_editar, fct_string('edita_activitat'));
                    $icona_suprimir = $this->icona_suprimir($url_suprimir, fct_string('suprimeix_activitat'));
                    $accions = "$icona_editar $icona_suprimir";
                }
                $taula->add_data(array($activitat->descripcio, $accions));
            }
            $taula->print_html();
        }

        $this->mostrar_peu();

        $this->registrar('view pla_activitats');
    }

}

