<?php
/* Quadern virtual d'FCT

   Copyright © 2009,2010,2011  Institut Obert de Catalunya

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

class fct {
    var $id;
    var $course;
    var $name = '';
    var $intro = '';
    var $timecreated = 0;
    var $timemodified = 0;
    var $centre;
    var $frases_centre;
    var $frases_empresa;

    function __construct() {
        $this->centre = new fct_centre;
        $this->frases_centre = array();
        $this->frases_empresa = array();
    }
}

class fct_activitat {
    var $id;
    var $quadern;
    var $descripcio;
    var $nota = 0;

    function cmp($a, $b) {
        return strcmp($a->descripcio, $b->descripcio);
    }
}

class fct_avis {
    var $id;
    var $quadern;
    var $data;
    var $tipus;
    var $quinzena;
}

class fct_centre {
    var $nom = '';
    var $adreca = '';
    var $codi_postal = '';
    var $poblacio = '';
    var $telefon = '';
    var $fax = '';
    var $email = '';
}

class fct_cicle {
    var $id;
    var $fct;
    var $nom;
    var $activitats;
    var $n_quaderns = 0;

    function __construct() {
        $this->activitats = array();
    }
}

class fct_conveni {
    var $uuid;
    var $codi = '';
    var $data_inici;
    var $data_final;
    var $horari;

    function __construct() {
        $this->uuid = fct_uuid();
        $date = getdate();
        $this->data_inici = mktime(0, 0, 0, (int) $date['mon'],
                                   (int) $date['mday'], (int) $date['year']);
        $this->data_final = mktime(0, 0, 0, (int) $date['mon'],
                                   (int) $date['mday'], $date['year'] + 1);
        $this->horari = array();
    }

    function afegir_franja_horari($franja) {
        if (array_search($franja, $this->horari) === false) {
            $this->horari[] = $franja;
            usort($this->horari, array('fct_franja_horari', 'cmp'));
        }
    }

    function hores_dia($dia) {
        $hores = 0.0;
        foreach ($this->horari as $franja) {
            if ($franja->dia == $dia) {
                $hores += $franja->hores();
            }
        }
        return $hores;
    }

    function suprimir_franja_horari($franja) {
        $index = array_search($franja, $this->horari);
        if ($index !== false) {
            unset($this->horari[$index]);
        }
    }

}

class fct_dades_alumne {
    var $dni = '';
    var $data_naixement = 0;
    var $adreca = '';
    var $poblacio = '';
    var $codi_postal = '';
    var $telefon = '';
    var $email = '';
    var $inss = '';
    var $targeta_sanitaria = '';
    var $procedencia = '';
}

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


class fct_empresa {
    var $nom;
    var $adreca = '';
    var $poblacio = '';
    var $codi_postal = '';
    var $telefon = '';
    var $fax = '';
    var $email = '';
    var $nif = '';
    var $codi_agrupacio = '';
    var $sic = '';
    var $nom_responsable = '';
    var $cognoms_responsable = '';
    var $dni_responsable = '';
    var $carrec_responsable = '';
    var $nom_tutor = '';
    var $cognoms_tutor = '';
    var $dni_tutor = '';
    var $email_tutor = '';
    var $nom_lloc_practiques = '';
    var $adreca_lloc_practiques = '';
    var $poblacio_lloc_practiques = '';
    var $codi_postal_lloc_practiques = '';
    var $telefon_lloc_practiques = '';
}

class fct_especificacio_quaderns {
    var $fct = false;
    var $usuari = false;
    var $data_final_min = false;
    var $data_final_max = false;
    var $cicle = false;
    var $estat = false;
    var $cerca = false;
    var $alumne = false;
    var $empresa = false;
}


class fct_franja_horari {
    var $dia;
    var $hora_inici;
    var $hora_final;

    function __construct($dia, $hora_inici, $hora_final) {
        $this->dia = $dia;
        $this->hora_inici = $hora_inici;
        $this->hora_final = $hora_final;
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


class fct_quadern {
    var $id;
    var $cicle;
    var $alumne;
    var $tutor_centre = 0;
    var $tutor_empresa = 0;
    var $estat = 'proposat';
    var $dades_alumne;
    var $empresa;
    var $convenis;
    var $hores_credit = 0;
    var $hores_practiques = 0;
    var $hores_anteriors = 0;
    var $exempcio = 0;
    var $prorrogues = '';
    var $valoracio_parcial;
    var $valoracio_final;
    var $valoracio_resultats;
    var $qualificacio;
    var $qualificacio_global;

    function __construct() {
        $this->dades_alumne = new fct_dades_alumne;
        $this->empresa = new fct_empresa;
        $this->convenis = array();
        $this->valoracio_parcial = array();
        $this->valoracio_final = array();
        $this->valoracio_resultats = array();
        $this->qualificacio = new fct_qualificacio;
        $this->qualificacio_global = new fct_qualificacio;
    }

    function afegir_conveni($conveni) {
        $this->convenis[$conveni->uuid] = $conveni;
    }

    function conveni($uuid) {
        return isset($this->convenis[$uuid]) ? $this->convenis[$uuid] : false;
    }

    function conveni_data($data) {
        foreach ($this->convenis as $conveni) {
            if ($data->en_periode(fct_data::time($conveni->data_inici),
                                  fct_data::time($conveni->data_final))) {
                return $conveni;
            }
        }
    }

    function data_inici() {
        $data = false;
        foreach ($this->convenis as $conveni) {
            if (!$data or $conveni->data_inici < $data) {
                $data = $conveni->data_inici;
            }
        }
        return $data;
    }

    function data_final() {
        $data = false;
        foreach ($this->convenis as $conveni) {
            if (!$data or $conveni->data_final > $data) {
                $data = $conveni->data_final;
            }
        }
        return $data;
    }

    function suprimir_conveni($conveni) {
        if (isset($this->convenis[$conveni->uuid])) {
            unset($this->convenis[$conveni->uuid]);
        }
    }

    function ultim_conveni() {
        return end($this->convenis);
    }
}

class fct_qualificacio {
    var $apte = 0;
    var $nota = 0;
    var $data = 0;
    var $observacions = '';
}

class fct_quinzena {
    var $id;
    var $quadern;
    var $any = 0;
    var $periode = 0;
    var $hores = 0;
    var $dies;
    var $activitats;
    var $valoracions = '';
    var $observacions_alumne = '';
    var $observacions_centre = '';
    var $observacions_empresa = '';

    function __construct() {
        $this->dies = array();
        $this->activitats = array();
    }
}

class fct_resum_hores_fct {
    var $credit;
    var $exempcio;
    var $anteriors;
    var $practiques;

    var $realitzades;
    var $pendents;


    function __construct($hores_credit, $hores_anteriors,
                         $exempcio, $hores_practiques) {
        $this->credit = $hores_credit;
        $this->anteriors = $hores_anteriors;
        $this->practiques = $hores_practiques;
        $this->exempcio = ceil((float) $exempcio / 100 * $hores_credit);

        $this->realitzades = $this->anteriors + $this->exempcio
            + $this->practiques;
        $this->pendents = max(0, $this->credit - $this->realitzades);
    }
}

class fct_serveis {

    var $diposit;
    var $moodle;

    function __construct($diposit, $moodle) {
        $this->diposit = $diposit;
        $this->moodle = $moodle;
    }

    function crear_quadern($alumne, $cicle) {
        $quadern = new fct_quadern;
        $quadern->alumne = $alumne;
        $quadern->cicle = $cicle;
        $quadern->afegir_conveni(new fct_conveni);

        $ultim_quadern = $this->ultim_quadern($alumne, $cicle);
        if ($ultim_quadern) {
            $quadern->dades_alumne = clone($ultim_quadern->dades_alumne);
            $quadern->hores_credit = $ultim_quadern->hores_credit;
            $quadern->exempcio = $ultim_quadern->exempcio;
            $quadern->hores_anteriors = $ultim_quadern->hores_anteriors;
            $quadern->qualificacio_global =
                clone($ultim_quadern->qualificacio_global);
        }

        return $quadern;
    }

    function data_prevista_valoracio_parcial($quadern) {
        $conveni = $quadern->ultim_conveni();
        $inici = DateTime::createFromFormat('U', $conveni->data_inici);
        $final = DateTime::createFromFormat('U', $conveni->data_final);
        $dies = (int) ($inici->diff($final)->format('%a') / 2);
        $interval = new DateInterval("P{$dies}D");
        return $inici->add($interval)->getTimestamp();
    }

    function hores_realitzades_quadern($quadern) {
        $hores = 0;
        $quinzenes = $this->diposit->quinzenes($quadern->id);
        foreach ($quinzenes as $quinzena) {
            $hores += $quinzena->hores;
        }
        return $hores;
    }

    function maxim_hores_quinzena($quadern, $any, $periode, $dies) {
        $hores = 0.0;
        foreach ($dies as $dia) {
            $data = new fct_data($dia, floor($periode / 2) + 1, $any);
            if ($conveni = $quadern->conveni_data($data)) {
                $hores += $conveni->hores_dia($data->dia_setmana());
            }
        }
        return $hores;
    }

    function registrar_avis($quadern, $tipus, $quinzena=false) {
        $avisos = $this->diposit->avisos_quadern($quadern->id);
        foreach ($avisos as $avis) {
            if ($avis->tipus == $tipus and $avis->quinzena == $quinzena) {
                $avis->data = $this->moodle->time();
                $this->diposit->afegir_avis($avis);
                return;
            }
        }
        $avis = new fct_avis;
        $avis->quadern = $quadern->id;
        $avis->data = $this->moodle->time();
        $avis->tipus = $tipus;
        $avis->quinzena = $quinzena;
        $this->diposit->afegir_avis($avis);
    }

    function resum_hores_fct($quadern) {
        $hores_practiques = 0;

        $especificacio = new fct_especificacio_quaderns;
        $especificacio->cicle = $quadern->cicle;
        $especificacio->alumne = $quadern->alumne;
        $especificacio->data_final_max = $quadern->data_final();

        $quaderns = $this->diposit->quaderns($especificacio);
        foreach ($quaderns as $q) {
            if ($q->qualificacio->apte != 2) {
                $hores_practiques += $this->hores_realitzades_quadern($q);
            }
        }

        return new fct_resum_hores_fct($quadern->hores_credit,
                                       $quadern->hores_anteriors,
                                       $quadern->exempcio,
                                       $hores_practiques);
    }

    function suprimir_fct($fct) {
        $especificacio = new fct_especificacio_quaderns;
        $especificacio->fct = $fct->id;
        $quaderns = $this->diposit->quaderns($especificacio);
        foreach ($quaderns as $quadern) {
            $this->suprimir_quadern($quadern);
        }

        $cicles = $this->diposit->cicles($fct->id);
        foreach ($cicles as $cicle) {
            $this->diposit->suprimir_cicle($cicle);
        }

        $this->diposit->suprimir_fct($fct);
    }

    function suprimir_quadern($quadern) {
        $avisos = $this->diposit->avisos_quadern($quadern->id);
        foreach ($avisos as $avis) {
            $this->diposit->suprimir_avis($avis);
        }

        $quinzenes = $this->diposit->quinzenes($quadern->id);
        foreach ($quinzenes as $quinzena) {
            $this->diposit->suprimir_quinzena($quinzena);
        }

        $activitats = $this->diposit->activitats($quadern->id);
        foreach ($activitats as $activitat) {
            $this->diposit->suprimir_activitat($activitat);
        }

        $cicle = $this->diposit->cicle($quadern->cicle);
        $fct = $this->diposit->fct($cicle->fct);
        $this->moodle->delete_dir($fct->course, "quadern-{$quadern->id}");

        $this->diposit->suprimir_quadern($quadern);
    }

    function suprimir_quinzena($quinzena) {
        $avisos = $this->diposit->avisos_quadern($quinzena->quadern);
        foreach ($avisos as $avis) {
            if ($avis->quinzena == $quinzena->id) {
                $this->diposit->suprimir_avis($avis);
            }
        }
        $this->diposit->suprimir_quinzena($quinzena);
    }

    function ultim_quadern($alumne, $cicle) {
        $especificacio = new fct_especificacio_quaderns;
        $especificacio->alumne = $alumne;
        $especificacio->cicle = $cicle;

        $quaderns = $this->diposit->quaderns($especificacio, 'data_final');

        return array_pop($quaderns);
    }
}

class fct_usuari {
    var $id;
    var $fct;
    var $nom;
    var $cognoms;
    var $email;
    var $es_administrador;
    var $es_alumne;
    var $es_tutor_centre;
    var $es_tutor_empresa;

    function nom_sencer() {
        return $this->nom . ' ' . $this->cognoms;
    }
}

function fct_uuid() {
    $octets = array();
    for ($n = 0; $n < 16; $n++) {
        $octets[] = mt_rand(0, 255);
    }
    $octets[8] = ($octets[8] | 0x80) & 0xbf; // variant ISO/IEC 11578:1996
    $octets[6] = ($octets[6] & 0x0f) | 0x40; // version 4 (random)

    return sprintf('%02x%02x%02x%02x-%02x%02x-%02x%02x-%02x%02x'
                   .'-%02x%02x%02x%02x%02x%02x',
                   $octets[0], $octets[1], $octets[2], $octets[3],
                   $octets[4], $octets[5], $octets[6], $octets[7],
                   $octets[8], $octets[9], $octets[10], $octets[11],
                   $octets[12], $octets[13], $octets[14], $octets[15]);
}
