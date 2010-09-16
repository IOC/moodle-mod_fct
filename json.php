<?php

class fct_json {

    private static function copy($source, $dest, $include=false, $exclude=array()) {
        if (!$include) {
            $include = array_keys((array) $dest);
        }
        foreach ($source as $key => $value) {
            if (in_array($key, $include) and !in_array($key, $exclude)) {
                $dest->$key = $value;
            }
        }
    }

    function deserialitzar_activitat($json) {
        $objecte = json_decode($json, true);
        $activitat = new fct_activitat;
        self::copy($objecte, $activitat);
        return $activitat;
    }

    function deserialitzar_avis($json) {
        $objecte = json_decode($json, true);
        $avis = new fct_avis;
        self::copy($objecte, $avis);
        return $avis;
    }

    function deserialitzar_cicle($json) {
        $objecte = json_decode($json, true);
        $cicle = new fct_cicle;
        self::copy($objecte, $cicle);
        return $cicle;
    }

    function deserialitzar_fct($json) {
        $objecte = json_decode($json, true);
        $fct = new fct;
        self::copy($objecte, $fct, false, array('centre'));
        self::copy($objecte['centre'], $fct->centre);
        return $fct;
    }

    function deserialitzar_quadern($json) {
        $objecte = json_decode($json, true);
        $quadern = new fct_quadern;
        self::copy($objecte, $quadern, false,
                      array('convenis', 'dades_alumne', 'empresa',
                            'qualificacio', 'qualificacio_global'));
        foreach ($objecte['convenis'] as $objecte_conveni) {
            $conveni = new fct_conveni;
            self::copy($objecte_conveni, $conveni, false, array('horari'));
            foreach ($objecte_conveni['horari'] as $objecte_franja) {
                $franja = new fct_franja_horari($objecte_franja['dia'],
                                                $objecte_franja['hora_inici'],
                                                $objecte_franja['hora_final']);
                $conveni->afegir_franja_horari($franja);
            }
            $quadern->afegir_conveni($conveni);
        }
        self::copy($objecte['dades_alumne'], $quadern->dades_alumne);
        self::copy($objecte['empresa'], $quadern->empresa);
        self::copy($objecte['qualificacio'], $quadern->qualificacio);
        self::copy($objecte['qualificacio_global'],
                      $quadern->qualificacio_global);
        return $quadern;
    }

    function deserialitzar_quinzena($json) {
        $objecte = json_decode($json, true);
        $quinzena = new fct_quinzena;
        self::copy($objecte, $quinzena);
        return $quinzena;
    }

    function serialitzar_activitat($activitat) {
        return json_encode($activitat);
    }

    function serialitzar_avis($avis) {
        return json_encode($avis);
    }

    function serialitzar_cicle($cicle) {
        return json_encode($cicle);
    }

    function serialitzar_fct($fct) {
        return json_encode($fct);
    }

    function serialitzar_quadern($quadern) {
        return json_encode($quadern);
    }

    function serialitzar_quinzena($quinzena) {
        return json_encode($quinzena);
    }
}
