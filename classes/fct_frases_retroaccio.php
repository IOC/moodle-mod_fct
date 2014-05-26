<?php

require_once('form/frases_edit_form.php');
require_once('fct_base.php');

class fct_frases_retroaccio extends fct_base {

    public $id;
    public $course;
    public $name;
    public $intro;
    public $timecreated;
    public $timemodified;
    public $objecte;
    public $fct;

    public $frases_centre;
    public $frases_empresa;
    public $centre;

    protected $record_keys = array('id', 'course', 'name', 'intro', 'timecreated', 'timemodified', 'objecte');
    protected $objecte_keys = array('id', 'course', 'name', 'intro', 'timecreated', 'timemodified', 'centre', 'frases_centre', 'frases_empresa');
    protected static $table = 'fct';
    protected $editform = 'fct_frases_edit_form';

    public function __construct($record=null) {
        if (isset($record->fct)) {
            $this->fct = $record->fct;
            parent::__construct((int)$record->fct);
        } else {
            if (is_int($record)) {
                $this->fct = $record;
            }
            parent::__construct($record);
        }
    }

    public function tabs($id, $type = 'view') {
        $tab = parent::tabs_general($id);
        $tab['currentab'] = 'frases_retroaccio';

        return $tab;
    }

    public function view() {
        global $PAGE, $OUTPUT;

        $output = $PAGE->get_renderer('mod_fct', 'frases');

        $frases = array('frases_centre' => $this->frases_centre, 'frases_empresa' => $this->frases_empresa);
        $table = $output->frases_table($frases);

        echo $table;

        $cm = get_coursemodule_from_instance('fct', $this->id);
        echo $OUTPUT->edit_button(new moodle_url('/mod/fct/edit.php', array('cmid' => $cm->id, 'id'=>$this->id, 'page' => 'frases_retroaccio')));

    }

    public function set_data($data) {
        if (isset($data->frases_empresa)) {
            $data->frases_empresa = explode("\n", $data->frases_empresa);
        }

        if (isset($data->frases_centre)) {
            $data->frases_centre = explode("\n", $data->frases_centre);
        }
        parent::set_data($data);
    }

    public function checkpermissions($type = 'view') {

        if (!$this->usuari->es_administrador) {
            print_error('nopermisions');
        }
    }

    public function prepare_form_data($data) {}

}
