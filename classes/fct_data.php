<?php

class fct_data {

    var $dia;
    var $mes;
    var $any;

    function __construct($dia, $mes, $any) {
        $this->dia = $dia;
        $this->mes = $mes;
        $this->any = $any;
    }

    function anterior_a($data) {
        if ($this->any != $data->any) {
            return $this->any < $data->any;
        }
        if ($this->mes != $data->mes) {
            return $this->mes < $data->mes;
        }
        return $this->dia < $data->dia;
    }

    function dia_setmana() {
        $dow = array('diumenge', 'dilluns', 'dimarts', 'dimecres', 'dijous',
                     'divendres', 'dissabte');
        $jd = cal_to_jd(CAL_GREGORIAN, $this->mes, $this->dia, $this->any);
        return $jd > 0 ? $dow[jddayofweek($jd)] : false;
    }

    function en_periode($inici, $final) {
        return ($this->igual_a($inici) or $this->igual_a($final) or
                ($this->posterior_a($inici)) and $this->anterior_a($final));
    }

    function igual_a($data) {
        return ($this->any == $data->any and
                $this->mes == $data->mes and
                $this->dia == $data->dia);
    }

    function posterior_a($data) {
        return $data->anterior_a($this) and !$data->igual_a($this);
    }

    function valida() {
        $jd = cal_to_jd(CAL_GREGORIAN, $this->mes, $this->dia, $this->any);
        return ($jd > 0 and $this->dia <=
                cal_days_in_month(CAL_GREGORIAN, $this->mes, $this->any));
    }

    static function time($time) {
        $date = getdate($time);
        return new fct_data($date['mday'], $date['mon'], $date['year']);
    }

    static function final_periode($any, $periode) {
        $mes = floor($periode / 2) + 1;
        $dies_mes = cal_days_in_month(CAL_GREGORIAN, $mes, $any);
        return new fct_data($periode % 2 == 0 ? 15 : $dies_mes, $mes, $any);
    }

    static function inici_periode($any, $periode) {
        $mes = floor($periode / 2) + 1;
        return new fct_data($periode % 2 == 0 ? 1 : 16, $mes, $any);
    }
}