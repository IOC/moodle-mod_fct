<?php

require_once('fct_base.php');
require_once('fct_quadern_quinzena.php');
require_once('fct_cicle.php');
require_once('fct_conveni.php');
require_once('fct_usuari.php');

class fct_quadern_base extends fct_base {

    protected static $table = 'fct_quadern';

    public $id;
    public $fct;
    public $quadern;
    public $alumne;
    public $objecte;
    public $nom_empresa;
    public $cicle;
    public $tutor_centre;
    public $tutor_empresa;
    public $estat;
    public $empresa;
    public $dades_alumne;
    public $convenis;
    public $hores_credit;
    public $hores_practiques;
    public $hores_anteriors;
    public $exempcio;
    public $prorrogues;
    public $valoracio_parcial;
    public $valoracio_final;
    public $valoracio_resultats;
    public $qualificacio;
    public $qualificacio_global;

    public $inssimage;

    protected static $dataobject;

    protected $editform = 'fct_quadern_alumne_edit_form';

    protected $objecte_keys = array('id',
                                    'cicle',
                                    'alumne',
                                    'tutor_centre',
                                    'tutor_empresa',
                                    'estat',
                                    'dades_alumne',
                                    'empresa',
                                    'convenis',
                                    'hores_credit',
                                    'hores_practiques',
                                    'hores_anteriors',
                                    'exempcio',
                                    'prorrogues',
                                    'valoracio_parcial',
                                    'valoracio_final',
                                    'valoracio_resultats',
                                    'qualificacio',
                                    'qualificacio_global'
                                    );

    protected $record_keys = array('id',
                                    'alumne',
                                    'tutor_centre',
                                    'tutor_empresa',
                                    'nom_empresa',
                                    'cicle',
                                    'estat',
                                    'data_final',
                                    'objecte');

    public static $estats = array(
                             OBERT => 'Obert',
                             TANCAT =>  'Tancat',
                             PROPOSAT => 'Proposat');

    protected static $dataobjectkeys = array();

    public function __construct($record = null){
        global $DB, $USER;

        if (isset($record->fct)) {
            $this->fct = $record->fct;
        }

        if (isset($record->quadern)) {
            parent::__construct((int)$record->quadern);
        } else{
            parent::__construct($record);
        }

        if (!isset($this->fct)) {
            if ($record = $DB->get_record('fct_cicle', array('id' => $this->cicle), 'fct')) {
                $this->fct = $record->fct;
                $this->usuari = new fct_usuari($this->fct, $USER->id);
            } else {
                print_error('nofct');
            }
        }

        if (isset($this->convenis)) {
            $convenisobject = new stdClass;
            foreach ($this->convenis as $conveni) {
                $uuid = $conveni->uuid;
                $convenisobject->$uuid = new fct_conveni($conveni);
            }
            $this->convenis = $convenisobject;
        }

    }

    public function __get($name) {

        $dataobjectkeys = static::$dataobjectkeys;
        $dataobject = static::$dataobject;

        if (!isset($this->$dataobject)) {
            return false;
        }

        if (array_key_exists($name, array_flip($dataobjectkeys))) {
             return $this->$dataobject->$name;

        }
        return false;
    }

    public function set_data($data) {

        if ($dataobject = static::$dataobject) {
            $this->$dataobject = (object)array_intersect_key((array)$data, array_flip(static::$dataobjectkeys));
        }

        parent::set_data($data);
    }

    public function prepare_form_data($data) {

        $dataobject = static::$dataobject;

        if (isset($data->$dataobject)) {
            $formdata = (array)$data->$dataobject;

            foreach ($formdata as $key => $value) {
                $data->$key = $value;
            }
        }

    }

    public function data_inici() {
        $data = false;
        foreach ($this->convenis as $conveni) {
            if (!$data or $conveni->data_inici < $data) {
                $data = $conveni->data_inici;
            }
        }
        return $data;
    }

    public function data_final() {
        $data = false;
        foreach ($this->convenis as $conveni) {
            if (!$data or $conveni->data_final > $data) {
                $data = $conveni->data_final;
            }
        }
        return $data;
    }

    public function opcions_any() {

        $opcions = array();
        $any_min = date('Y', $this->data_inici());
        $any_max = date('Y', $this->data_final());
        for ($any = $any_min; $any <= $any_max; $any++) {
            $opcions[$any] = "$any";
        }
        return $opcions;
    }

    public function opcions_periode() {
        $opcions = array();
        for ($periode = 0; $periode <= 23; $periode++) {
            $opcions[$periode] = $this->nom_periode($periode);
        }
        return $opcions;
    }

    public function nom_periode($periode, $any=2001) {
        $mes = floor((int) $periode / 2);
        $dies = ($periode % 2 == 0) ? '1-15' :
            '16-' . cal_days_in_month(CAL_GREGORIAN, $mes + 1, $any);
        return $dies . ' ' . $this->nom_mes($mes);
    }

    public function nom_mes($mes) {
        $time = mktime(0, 0, 0, $mes+1, 1, 2000);
        return strftime('%B', $time);
    }

    public function get_frases_cicle() {
        $cicle = new fct_cicle((int)$this->cicle);
        return $cicle->activitats;
    }

    public function hores_realitzades_quadern($quadernid) {
        $hores = 0;
        $quinzenes = fct_quadern_quinzena::get_records($quadernid);
        foreach ($quinzenes as $quinzena) {
            $hores += $quinzena->hores;
        }
        return $hores;
    }

    public function checkpermissions($type = 'view') {

        if (($this->usuari->es_alumne && ($this->usuari->id != $this->alumne)) ||
           ($this->usuari->es_tutor_centre && ($this->usuari->id != $this->tutor_centre)) ||
           ($this->usuari->es_tutor_empresa && ($this->usuari->id != $this->tutor_empresa))) {
                print_error('nopermisions');
        }
    }

    public function conveni_data($data) {
        foreach ($this->convenis as $conveni) {
            if ($data->en_periode(fct_data::time($conveni->data_inici),
                                  fct_data::time($conveni->data_final))) {
                return $conveni;
            }
        }
    }

    public function es_alumne() {
        return $this->usuari->id == $this->alumne;
    }

    public function es_tutor_centre() {
        if (isset($this->tutor_centre)) {
            return $this->usuari->id == $this->tutor_centre;
        }
        return false;
    }

    public function es_tutor_empresa() {
        if (isset($this->tutor_empresa)) {
            return $this->usuari->id == $this->tutor_empresa;
        }
    }

    protected function subtree($id, $quadern) {

        $subtree = array();

        $subtree[] = new tabobject('quadern_centre', new moodle_url('/mod/fct/view.php',
                                array('id' => $id, 'quadern' => $quadern, 'page'=> 'quadern_dades')),
                                get_string('centre_docent', 'fct'));

        $subtree[] = new tabobject('quadern_alumne', new moodle_url('/mod/fct/view.php',
                                array('id' => $id, 'quadern' => $quadern, 'page'=> 'quadern_dades', 'subpage' => 'quadern_alumne')),
                                get_string('alumne', 'fct'));

        $subtree[] = new tabobject('quadern_empresa', new moodle_url('/mod/fct/view.php',
                                array('id' => $id, 'quadern' => $quadern, 'page'=> 'quadern_dades', 'subpage' => 'quadern_empresa')),
                                get_string('empresa', 'fct'));

        $subtree[] = new tabobject('quadern_conveni', new moodle_url('/mod/fct/view.php',
                                array('id' => $id, 'quadern' => $quadern, 'page'=> 'quadern_dades', 'subpage' => 'quadern_conveni')),
                                get_string('conveni', 'fct'));

        $subtree[] = new tabobject('quadern_horari', new moodle_url('/mod/fct/view.php',
                                array('id' => $id, 'quadern' => $quadern, 'page'=> 'quadern_dades', 'subpage' => 'quadern_horari')),
                                get_string('horari_practiques', 'fct'));

        $subtree[] = new tabobject('quadern_dades_relatives', new moodle_url('/mod/fct/view.php',
                                array('id' => $id, 'quadern' => $quadern, 'page'=> 'quadern_dades', 'subpage' => 'quadern_dades_relatives')),
                                get_string('dades_relatives', 'fct'));

        return $subtree;
    }

    public function ultim_conveni() {
        return end($this->convenis);
    }



}
