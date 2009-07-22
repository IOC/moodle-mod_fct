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
fct_require('pagines/base_cicles.php');

class fct_pagina_llista_cicles extends fct_pagina_base_cicles {

    function configurar() {
        parent::configurar(required_param('fct', PARAM_INT));
        $this->url = fct_url::llista_cicles($this->fct->id);
        $this->subpestanya = 'llista_cicles';
    }

    function processar() {
        $this->mostrar_capcalera();

        $taula = new flexible_table('fct_quaderns');
        $taula->define_columns(array('nom'));
        $taula->define_headers(array(fct_string('nom')));
        $taula->set_attribute('class', 'generaltable');
        $taula->setup();

        $cicles = $this->diposit->cicles($this->fct->id);

        if (!$cicles) {
           echo '<p>' . fct_string('cap_cicle_formatiu') . '</p>';
        } else {
            foreach ($cicles as $cicle) {
                $taula->add_data(array('<a href="' . fct_url::cicle($cicle->id)
                                       .'">' . $cicle->nom . '</a>'));
            }
            $taula->print_html();
        }

        $this->mostrar_peu();

        $this->registrar('view llista_cicles');
    }

}

