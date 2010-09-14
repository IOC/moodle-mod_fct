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
fct_require('pagines/base_seguiment.php');

class fct_pagina_seguiment extends fct_pagina_base_seguiment {

    function configurar() {
        parent::configurar(required_param('quadern', PARAM_INT));
        $this->url = fct_url('seguiment', array('quadern' => $this->quadern->id));
        $this->subpestanya = 'quinzenes';
    }

    function processar() {
        $this->mostrar_capcalera();

        $taula = new flexible_table('fct_seguiment');
        $taula->set_attribute('id', 'fct_seguiment');
        $taula->define_columns(array('any_', 'periode', 'dies', 'hores'));
        $taula->define_headers(array(fct_string('any'), fct_string('periode'),
            fct_string('dies'), fct_string('hores')));
        $taula->set_attribute('class', 'generaltable');
        $taula->setup();

        $quinzenes = $this->diposit->quinzenes($this->quadern->id);

        if (!$quinzenes) {
           echo '<p>' . fct_string('cap_quinzena') . '</p>';
        } else {
            foreach ($quinzenes as $quinzena) {
                $url = fct_url('quinzena', array('quinzena' => $quinzena->id));
                $any = '<a href="'.$url.'">'.$quinzena->any.'</a>';
                $periode = '<a href="'.$url.'">'.self::nom_periode($quinzena->periode).'</a>';
                $dies = '<a href="'.$url.'">'.count($quinzena->dies).'</a>';
                $hores = '<a href="'.$url.'">'.(float)$quinzena->hores.'</a>';
                $taula->add_data(array($any, $periode, $dies, $hores));
            }
            $taula->print_html();
        }

        $this->mostrar_peu();

        $this->registrar('view seguiment');
    }

}

