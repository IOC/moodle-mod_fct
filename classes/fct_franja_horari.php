<?php

class fct_franja_horari {
    var $dia;
    var $hora_inici;
    var $hora_final;

    function __construct($record) {
        $this->dia = $record->dia;
        $this->hora_inici = $record->hora_inici;
        $this->hora_final = $record->hora_final;
    }

    static function cmp($a, $b) {
        $ordre_dia = array('dilluns', 'dimarts', 'dimecres', 'dijous',
                           'divendres', 'dissabte', 'diumenge');
        $cmp_dia = (array_search($a->dia, $ordre_dia) -
                    array_search($b->dia, $ordre_dia));
        if ($cmp_dia != 0) {
            return $cmp_dia;
        }
        if ($a->hora_inici != $b->hora_inici) {
            return $a->hora_inici - $b->hora_inici;
        }
        return $a->hora_final - $b->hora_final;
    }

    function text_hora_final() {
        return self::text_hora($this->hora_final);
    }

    function text_hora_inici() {
        return self::text_hora($this->hora_inici);
    }

    static function text_hora($hora) {
        $minuts = round(($hora - floor($hora)) * 60);
        return sprintf("%02d:%02d", floor($hora), $minuts);
    }

    function hores() {
        return ($this->hora_inici <= $this->hora_final ?
                $this->hora_final - $this->hora_inici :
                $this->hora_final - $this->hora_inici + 24);
    }
}
