<?php

class fct {
    var $id;
    var $name = '';
    var $intro = '';
    var $timecreated = 0;
    var $timemodified = 0;
    var $frases_centre = '';
    var $frases_empresa = '';
    var $cm;
    var $context;
    var $course;
    var $centre;

    function __construct() {
        $this->centre = new fct_centre;
    }
}

class fct_activitat {
    var $id;
    var $quadern;
    var $descripcio;
    var $nota = 0;
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
    var $n_quaderns;

    function __construct() {
        $this->activitats = array();
    }

    function text_activitats($text=false) {
        if ($text === false) {
            return implode("\n", $this->activitats);
        } else {
            $this->activitats = array();
            foreach (explode("\n", $text) as $activitat) {
                if (trim($activitat)) {
                    $this->activitats[] = trim($activitat);
                }
            }
        }
    }

}

class fct_conveni {
    var $id = 0;
    var $codi = '';
    var $data_inici;
    var $data_final;
    var $horari;

    function __construct() {
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
    var $fct;
    var $usuari = false;
    var $data_final_min = false;
    var $data_final_max = false;
    var $cicle = false;
    var $estat = false;
    var $cerca = false;
    var $alumne = false;
    var $empresa = false;

    function __construct($fct) {
        $this->fct = $fct;
    }
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
        if ($conveni->id) {
            $this->suprimir_conveni($conveni->id);
        }
        $this->convenis[] = $conveni;
    }

    function conveni($id) {
        foreach ($this->convenis as $conveni) {
            if ($conveni->id == $id) {
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

    function suprimir_conveni($id) {
        foreach (array_keys($this->convenis) as $index) {
            if ($this->convenis[$index]->id == $id) {
                unset($this->convenis[$index]);
            }
        }
    }
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

class fct_qualificacio {
    var $apte = 0;
    var $nota = 0;
    var $data = 0;
    var $observacions = '';
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

function fct_copy_vars($source, $dest, $vars=false) {
    foreach ($source as $key => $value) {
        if (($vars and in_array($key, $vars))
            or (!$vars and property_exists($dest, $key))) {
            $dest->$key = $value;
        }
    }
}
