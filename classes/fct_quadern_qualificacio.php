<?php

require_once('form/quadern_qualificacio_edit_form.php');
require_once('fct_quadern_base.php');
require_once('fct_base.php');
require_once('fct_cicle.php');

class fct_quadern_qualificacio extends fct_quadern_base {


    public $typequalificacio = 'parcial';
    protected static $dataobject = 'qualificacio';

    protected $editform = 'fct_quadern_qualificacio_edit_form';

    protected static $dataobjectkeys = array('apte',
                                             'nota',
                                             'data',
                                             'observacions');

     public function tabs($id, $type = 'view') {

        $tab = parent::tabs_quadern($id, $this->id);

        $subtree = array();

        if ($type == 'parcial') {
            $subtree[] = new tabobject('valoracio_parcial_actituds',
                                  new moodle_url('view.php', array('id' => $id, 'quadern' => $this->id, 'page' => 'quadern_valoracio', 'valoracio' => 'parcial')),
                                  get_string('valoracio_parcial_actituds', 'fct'));

            $subtree[] = new tabobject('valoracio_final_actituds',
                                  new moodle_url('view.php', array('id' => $id, 'quadern' => $this->id, 'page' => 'quadern_valoracio', 'valoracio' => 'final')),
                                  get_string('valoracio_final_actituds', 'fct'));

            $subtree[] = new tabobject('valoracio_resultats',
                                  new moodle_url('view.php', array('id' => $id, 'quadern' => $this->id, 'page' => 'quadern_valoracio', 'valoracio' => 'resultats')),
                                  get_string('valoracio_resultats', 'fct'));

            $subtree[] = new tabobject('valoracio_activitats',
                                  new moodle_url('view.php', array('id' => $id, 'quadern' => $this->id, 'page' => 'quadern_valoracio', 'subpage' => 'quadern_valoracio_activitat')),
                                  get_string('valoracio_activitats', 'fct'));

            $subtree[] = new tabobject('qualificacio_quadern',
                                  new moodle_url('view.php', array('id' => $id, 'quadern' => $this->id, 'page' => 'quadern_valoracio', 'subpage'=> 'quadern_qualificacio')),
                                  get_string('qualificacio_quadern', 'fct'));

            $row = $tab['row'];
            $row['quadern_valoracio']->subtree = $subtree;
            $tab['currentab'] = 'qualificacio_quadern';
            $tab['row'] = $row;
        } else {
            $tab['currentab'] = 'quadern_qualificacio';
        }

        return $tab;
    }

    public function __construct($record = null) {
        if (isset($record->typequalificacio) && $record->typequalificacio == 'global') {
            self::$dataobject = 'qualificacio_global';
        }
        parent::__construct($record);
    }

    public function view() {
        global $PAGE;

        $output = $PAGE->get_renderer('mod_fct',
            'quadern_qualificacio');

        self::__construct((int)$this->id);

        $output->view($this);

        return true;

    }

    public static function validation($data) {
    }

    public function barem_valoracio() {
        return array(
            0 => '-',
            1 => get_string('barem_a', 'fct'),
            2 => get_string('barem_b', 'fct'),
            3 => get_string('barem_c', 'fct'),
            4 => get_string('barem_d', 'fct'),
            5 => get_string('barem_e', 'fct'),
        );
    }

    public function qualificacions(){
        return array(
            0 => '-',
            1 => get_string('apte', 'fct'),
            2 => get_string('noapte', 'fct')
        );
    }



}
