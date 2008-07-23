<?php

require_once 'base_quadern.php';

class fct_pagina_base_seguiment extends fct_pagina_base_quadern {

    var $seguiment;
    var $conveni;
    var $pla;

    function configurar($quadern_id, $seguiment_id=false) {
        if ($seguiment_id) {
            $this->seguiment = fct_db::seguiment($seguiment_id);
            if (!$this->seguiment) {
                $this->error('recuperar_seguiment');
            }
            parent::configurar($this->seguiment->quadern);
        } else if ($quadern_id) {
            $this->seguiment = fct_db::seguiment_quadern($quadern_id);
            if (!$this->seguiment) {
                $this->error('recuperar_seguiment');
            }
            parent::configurar($quadern_id);
        } else {
            $this->error('recuperar_seguiment');
        }

        $this->pla = fct_db::pla_actvitats_quadern($this->quadern->id);
        if (!$this->pla) {
            $this->error('recuperar_pla_activitats');
        }

        $this->conveni = fct_db::dades_conveni($this->quadern->id);
        if (!$this->conveni) {
            $this->error('recuperar_conveni');
        }

        $this->afegir_navegacio(fct_string('seguiment_quinzenal'),
            fct_url::seguiment($this->quadern->id));

        $this->pestanya = 'seguiment_quinzenal';
    }

    function definir_pestanyes() {
        parent::definir_pestanyes();
        $pestanyes = array(
            new tabobject('quinzenes', fct_url::seguiment($this->quadern->id), fct_string('quinzenes')),
        );
        if (($this->permis_alumne and $this->quadern->estat) or $this->permis_admin) {
            $pestanyes[] = new tabobject('afegir_quinzena',
                fct_url::afegir_quinzena($this->seguiment->id), fct_string('afegeix_quinzena'));
        }
        $pestanyes[] = new tabobject('resum_seguiment',
            fct_url::resum_seguiment($this->quadern->id), fct_string('resum_seguiment'));
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

    static function nom_mes($mes) {
        $time = mktime(0, 0, 0, $mes+1, 1, 2000);
        return strftime('%B', $time);
    }

    static function nom_periode($periode, $any=2001) {
        $mes = floor((int) $periode / 2);
        $dies = ($periode % 2 == 0) ? '1-15' :
            '16-' . cal_days_in_month(CAL_GREGORIAN, $mes + 1, $any);
        return $dies . ' ' . self::nom_mes($mes);
    }

    static function nom_trimestre($trimestre) {
        return fct_string('trimestre_' . ($trimestre + 1));
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

