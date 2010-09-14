<?php

class fct_json {

    function deserialitzar_activitat($json) {
        $objecte = json_decode($json, true);
        $activitat = new fct_activitat;
        fct_copy_vars($objecte, $activitat);
        return $activitat;
    }

    function deserialitzar_avis($json) {
        $objecte = json_decode($json, true);
        $avis = new fct_avis;
        fct_copy_vars($objecte, $avis);
        return $avis;
    }

    function deserialitzar_cicle($json) {
        $objecte = json_decode($json, true);
        $cicle = new fct_cicle;
        fct_copy_vars($objecte, $cicle);
        return $cicle;
    }

    function deserialitzar_fct($json) {
        $objecte = json_decode($json, true);
        $fct = new fct;
        fct_copy_vars($objecte, $fct, false, array('centre'));
        fct_copy_vars($objecte['centre'], $fct->centre);
        return $fct;
    }

    function deserialitzar_quadern($json) {
        $objecte = json_decode($json, true);
        $quadern = new fct_quadern;
        fct_copy_vars($objecte, $quadern, false,
                      array('convenis', 'dades_alumne', 'empresa',
                            'qualificacio', 'qualificacio_global'));
        foreach ($objecte['convenis'] as $objecte_conveni) {
            $conveni = new fct_conveni;
            fct_copy_vars($objecte_conveni, $conveni, false, array('horari'));
            foreach ($objecte_conveni['horari'] as $objecte_franja) {
                $franja = new fct_franja_horari($objecte_franja['dia'],
                                                $objecte_franja['hora_inici'],
                                                $objecte_franja['hora_final']);
                $conveni->afegir_franja_horari($franja);
            }
            $quadern->afegir_conveni($conveni);
        }
        fct_copy_vars($objecte['dades_alumne'], $quadern->dades_alumne);
        fct_copy_vars($objecte['empresa'], $quadern->empresa);
        fct_copy_vars($objecte['qualificacio'], $quadern->qualificacio);
        fct_copy_vars($objecte['qualificacio_global'],
                      $quadern->qualificacio_global);
        return $quadern;
    }

    function deserialitzar_quinzena($json) {
        $objecte = json_decode($json, true);
        $quinzena = new fct_quinzena;
        fct_copy_vars($objecte, $quinzena);
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
