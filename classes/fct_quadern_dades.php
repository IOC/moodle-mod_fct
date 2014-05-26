<?php


require_once('fct_base.php');
require_once('fct_quadern_base.php');
require_once('fct_dades_centre.php');


class fct_quadern_dades extends fct_quadern_base {

    public $quadern;

    protected static $table = 'fct_quadern';

    public $id;
    public $alumne;
    public $tutor_centre;
    public $tutor_empresa;
    public $nom_empresa;
    public $cicle;
    public $estat;
    public $fct;

    public function __construct($record) {
        if (isset($record->fct)) {
            $this->fct = $record->fct;
        }
        if (isset($record->quadern)) {
            parent::__construct((int)$record->quadern);
        } else{
            parent::__construct($record);
        }
    }

    public function tabs($id, $type = 'view') {

        $tab = parent::tabs_quadern($id, $this->id);
        $subtree = parent::subtree($id, $this->id);

        $row = $tab['row'];
        $row['quadern_dades']->subtree = $subtree;
        $tab['row'] = $row;
        $tab['currentab'] = 'quadern_centre';

        return $tab;
    }

    public function view() {
        global $PAGE;

        $output = $PAGE->get_renderer('mod_fct', 'centre');
        $fct = new fct_dades_centre((int)$this->fct);

        $centre = $output->centre($fct->centre);

        echo $centre;
    }

    public function prepare_form_data($data) {}
}
