<?php

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
    var $pendent;
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
        $this->horari = new fct_horari;
    }
}

class fct_dades_alumne {
    var $adreca = '';
    var $poblacio = '';
    var $codi_postal = '';
    var $telefon = '';
    var $email = '';
    var $dni = '';
    var $targeta_sanitaria = '';
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

class fct_horari {
    var $dilluns = '';
    var $dimarts = '';
    var $dimecres = '';
    var $dijous = '';
    var $divendres = '';
    var $dissabte = '';
    var $diumenge = '';
}

class fct_quadern {
    var $id;
    var $cicle;
    var $alumne;
    var $tutor_centre = 0;
    var $tutor_empresa = 0;
    var $estat = 1;
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
    var $qualificacio;
    var $qualificacio_global;

    function __construct() {
        $this->dades_alumne = new fct_dades_alumne;
        $this->empresa = new fct_empresa;
        $this->convenis = array();
        $this->valoracio_parcial = array();
        $this->valoracio_final = array();
        $this->qualificacio = new fct_qualificacio;
        $this->qualificacio_global = new fct_qualificacio;
    }

    function afegir_conveni($conveni) {
        $this->convenis[$conveni->uuid] = $conveni;
    }

    function conveni($uuid) {
        return isset($this->convenis[$uuid]) ? $this->convenis[$uuid] : false;
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

    function __construct($diposit) {
        $this->diposit = $diposit;
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

    function empreses($cicles) {
        $empreses = array();
        if ($cicles) {
            foreach ($cicles as $cicle) {
                $noms = $this->diposit->noms_empreses($cicle);
                foreach ($noms as $nom) {
                    if (!isset($empreses[$nom])) {
                        $especificacio = new fct_especificacio_quaderns;
                        $especificacio->cicle = $cicle;
                        $especificacio->empresa = $nom;
                        $quaderns = $this->diposit->quaderns($especificacio,
                                                             'data_final');
                        if ($quaderns)  {
                            $quadern = array_pop($quaderns);
                            $empreses[$nom] = $quadern->empresa;
                        }
                    }
                }
            }
        }
        return $empreses;
    }

    function hores_realitzades_quadern($quadern) {
        $hores = 0;
        $quinzenes = $this->diposit->quinzenes($quadern->id);
        foreach ($quinzenes as $quinzena) {
            $hores += $quinzena->hores;
        }
        return $hores;
    }

    function resum_hores_fct($quadern) {
        $hores_practiques = 0;

        $especificacio = new fct_especificacio_quaderns;
        $especificacio->cicle = $quadern->cicle;
        $especificacio->alumne = $quadern->alumne;
        $especificacio->data_final_max = $quadern->data_final();

        $quaderns = $this->diposit->quaderns($especificacio);
        foreach ($quaderns as $quadern) {
            $hores_practiques += $this->hores_realitzades_quadern($quadern);
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
        $quinzenes = $this->diposit->quinzenes($quadern->id);
        foreach ($quinzenes as $quinzena) {
            $this->diposit->suprimir_quinzena($quinzena);
        }

        $activitats = $this->diposit->activitats($quadern->id);
        foreach ($activitats as $activitat) {
            $this->diposit->suprimir_activitat($activitat);
        }

        $this->diposit->suprimir_quadern($quadern);
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

function fct_copy_vars($source, $dest, $include=false, $exclude=array()) {
    if (!$include) {
        $include = array_keys((array) $dest);
    }
    foreach ($source as $key => $value) {
        if (in_array($key, $include) and !in_array($key, $exclude)) {
            $dest->$key = $value;
        }
    }
}

function fct_linies_text($text) {
    $linies = array();
    foreach (explode("\n", $text) as $linia) {
        if (trim($linia)) {
            $linies[] = trim($linia);
        }
    }
    return $linies;
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
