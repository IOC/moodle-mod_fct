<?php
/* Quadern virtual d'FCT

   Copyright © 2008,2009,2010  Institut Obert de Catalunya

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

fct_require('pagines/base_quadern');

class fct_pagina_base_seguiment extends fct_pagina_base_quadern {

    var $permis_editar;
    var $permis_editar_centre;
    var $permis_editar_alumne;
    var $permis_editar_empresa;

    function configurar($quadern_id) {
        parent::configurar($quadern_id);

        $this->pestanya = 'seguiment_quinzenal';
        $this->permis_editar = ($this->usuari->es_administrador or
                                $this->quadern->estat == 'obert');
        $this->permis_editar_centre = ($this->usuari->es_administrador or
                                       ($this->quadern->estat == 'obert' and
                                        $this->es_tutor_centre()));
        $this->permis_editar_alumne = ($this->usuari->es_administrador or
                                       ($this->quadern->estat == 'obert' and
                                        ($this->es_tutor_centre() or
                                         $this->es_alumne())));
        $this->permis_editar_empresa = ($this->usuari->es_administrador or
                                        ($this->quadern->estat == 'obert' and
                                         ($this->es_tutor_centre() or
                                          $this->es_tutor_empresa())));
    }

    function definir_pestanyes() {
        parent::definir_pestanyes();
        $pestanyes = array(
            new tabobject('quinzenes',
                          fct_url('seguiment', array('quadern' => $this->quadern->id)),
                          fct_string('quinzenes')),
        );
        if ($this->permis_editar_alumne) {
            $pestanyes[] = new tabobject('afegir_quinzena',
                                         fct_url('afegir_quinzena', array('quadern' => $this->quadern->id)),
                                         fct_string('afegeix_quinzena'));
        }
        $pestanyes[] = new tabobject('resum_seguiment',
                                     fct_url('resum_seguiment', array('quadern' => $this->quadern->id)),
                                     fct_string('resum_seguiment'));
        $this->pestanyes[] = $pestanyes;
    }

    static function any_data($data) {
        return (int) date('Y', $data);
    }


    static function calendari_periode($any, $periode) {
        $mes = floor($periode / 2) + 1;
        $calendari = new object();
        $calendari->any = $any;
        $calendari->periode = $periode;
        if ($periode % 2 == 0) {
            $calendari->dia_inici = 1;
            $calendari->dia_final = 15;
        } else {
            $calendari->dia_inici = 16;
            $calendari->dia_final =
                cal_days_in_month(CAL_GREGORIAN, $mes, $any);
        }
        $calendari->dia_setmana = date('w', mktime(0, 0, 0, $mes,
            $calendari->dia_inici, $any)) - 1;
        if ($calendari->dia_setmana < 0) {
           $calendari->dia_setmana = 6;
        }
        return $calendari;
    }

    function dies_periode($periode, $any) {
        $mes = floor($periode / 2) + 1;
        $dia_min = ($periode % 2 == 0) ? 1 : 16;
        $dia_max = ($periode % 2 == 0) ? 15
            : cal_days_in_month(CAL_GREGORIAN, $mes, $any);
        return range($dia_min, $dia_max);
    }

    function filtrar_dies($dies, $periode, $any) {
        return array_intersect(array_unique($dies),
                               $this->dies_periode($periode, $any));
    }

    static function nom_trimestre($trimestre) {
        return fct_string('trimestre_' . ($trimestre + 1));
    }

    function opcions_any() {
        $opcions = array();
        $any_min = date('Y', $this->quadern->data_inici());
        $any_max = date('Y', $this->quadern->data_final());
        for ($any = $any_min; $any <= $any_max; $any++) {
            $opcions[$any] = "$any";
        }
        return $opcions;
    }

    function opcions_periode() {
        $opcions = array();
        for ($periode = 0; $periode <= 23; $periode++) {
            $opcions[$periode] = $this->nom_periode($periode);
        }
        return $opcions;
    }

    static function periode_data($data) {
        $data = getdate($data);
         return ((int) $data['mon'] - 1) * 2 +
            ((int) $data['mday'] > 15 ? 1 : 0);
    }

    static function quinzena_actual() {
        $any = (int) date('Y');
        $mes = (int) date('n');
        $dia = (int) date('j');
        $periode = ($mes - 1) * 2 + ($dia > 15 ? 1 : 0);
        return array($any, $periode);
    }

}

