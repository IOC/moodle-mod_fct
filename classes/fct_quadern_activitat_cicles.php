<?php

require_once('form/quadern_activitat_cicles_edit_form.php');
require_once('fct_base.php');
require_once('fct_quadern_base.php');
require_once('fct_cicle.php');
require_once('fct_usuari.php');
require_once('fct_avisos.php');
require_once('fct_activitat.php');

class fct_quadern_activitat_cicles extends fct_base {

    public $id;
    public $fct;
    public $quadern;
    public $descripcio;
    public $nota;
    public $objecte;


    protected static $table = 'fct_activitat';
    protected $record_keys = array('id', 'quadern', 'objecte');
    protected $objecte_keys = array('id', 'quadern', 'descripcio', 'nota');
    protected $editform = 'fct_quadern_activitat_cicles_edit_form';

    public function tabs($id, $type = 'view') {

        $row = parent::tabs_quadern($id, $this->quadern);

        $subtree = array();

        $subtree[] = new tabobject('activitatllist', new moodle_url('/mod/fct/view.php',
                                            array('id' => $id, 'quadern' => $this->quadern, 'page'=> 'quadern_activitat')),
                                            get_string('activitat', 'fct'));

        $subtree[] = new tabobject('afegeix_activitat', new moodle_url('/mod/fct/edit.php',
                                            array('cmid' => $id, 'quadern' => $this->quadern, 'page'=> 'quadern_activitat',)),
                                            get_string('afegeix_activitat', 'fct'));

        $subtree[] = new tabobject('afegeix_activitats_cicle', new moodle_url('/mod/fct/edit.php',
                                            array('cmid' => $id, 'quadern' => $this->quadern, 'page'=> 'quadern_activitat', 'subpage' => 'quadern_activitat_cicles')),
                                            get_string('afegeix_activitats_cicle', 'fct'));

        $subtree[] = new tabobject('suprimeix_activitats', new moodle_url('/mod/fct/edit.php',
                                            array('cmid' => $id, 'quadern' => $this->quadern, 'page'=> 'quadern_activitat', 'delete' => true, 'deleteall' => true)),
                                            get_string('suprimeix_activitats', 'fct'));

        $row['quadern_activitat']->subtree = $subtree;
        $tab['currentab'] = 'afegeix_activitats_cicle';
        $tab['row'] = $row;

        return $tab;
    }

    public function get_cicle_activitats($quadern) {
        global $DB;

        if ($record = $DB->get_record('fct_quadern', array('id' => $quadern), 'cicle')) {
                $cicle = new fct_cicle((int)$record->cicle);

                return $cicle->activitats;
        } else {
            return false;
        }

    }

    public function set_data($data) {
    }

    public function insert($data) {
        global $USER;

        if (!isset($data->quadern)) {
            print_error('noquadern');
        }

        $datakeys = array_keys((array)$data);

        $pregmatchexp = '"'.'/^'.'activity'.'_/'.'"';

        $arrayfiltered = array_filter($datakeys, create_function('$a', 'return preg_match('.$pregmatchexp.', $a);'));

        $activitieskeys = array_map(create_function('$a', 'return preg_replace('.$pregmatchexp.', '."''".', $a);'), $arrayfiltered);
        $activitieskeys = array_flip($activitieskeys);

        if ($activitascicle = $this->get_cicle_activitats($data->quadern)) {
                $activitats = array_intersect_key($activitascicle, $activitieskeys);
                 if (!empty($activitats)) {
                    foreach ($activitats as $activitat) {
                        $data->descripcio = $activitat;
                        parent::insert($data);
                    }
                 }
                 $quadern = new fct_quadern($data->quadern);

                if ($quadern->tutor_empresa == $USER->id) {
                    fct_avisos::registrar_avis($this->quadern, 'pla_activitats');
                }
        } else {
            return;
        }

    }

    public function prepare_form_data($data) {

        if (!isset($data->quadern)) {
            print_error('noquadern');
        }

        $data->activitatscicle = $this->get_cicle_activitats($data->quadern);
    }

    public function checkpermissions($type = 'view') {
        if (!isset($this->quadern)) {
            print_error('noquadern');
        }

        $quadern = new fct_quadern_base((int)$this->quadern);

        if (($this->usuari->es_alumne && ($this->usuari->id != $quadern->alumne)) ||
           ($this->usuari->es_tutor_centre && ($this->usuari->id != $quadern->tutor_centre)) ||
           ($this->usuari->es_tutor_empresa && ($this->usuari->id != $quadern->tutor_empresa))) {
                print_error('nopermisions');
        }
    }

    public function validation($data) {
    }


}