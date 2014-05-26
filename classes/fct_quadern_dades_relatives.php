<?php

require_once('form/quadern_dades_relatives_edit_form.php');
require_once('fct_quadern_base.php');
require_once('fct_base.php');
require_once('fct_cicle.php');
require_once('fct_resum_hores.php');

class fct_quadern_dades_relatives extends fct_quadern_base{

    protected $editform = 'fct_quadern_dades_relatives_edit_form';

    public function tabs($id, $type = 'view') {

        $tab = parent::tabs_quadern($id, $this->id);
        $subtree = parent::subtree($id, $this->id);

        $row = $tab['row'];
        $row['quadern_dades']->subtree = $subtree;
        $tab['row'] = $row;
        $tab['currentab'] = 'quadern_dades_relatives';

        return $tab;
    }

    public function view() {
        global $PAGE;

        $output = $PAGE->get_renderer('mod_fct', 'quadern_dades_relatives');
        $output->view($this);

        return true;

    }

    public function resum_hores_fct() {
        global $DB;

        $hores_practiques = 0;

        $quadernsrecords = $DB->get_records('fct_quadern', array('cicle' => $this->cicle, 'alumne' => $this->alumne));

        foreach ($quadernsrecords as $record) {
            $quadern = new fct_quadern_base((int)$record->id);
            if ($quadern->apte != 2) {
                $hores_practiques += $quadern->hores_realitzades_quadern($quadern->id);
            }
        }

        $resum = new fct_resum_hores_fct($quadern->hores_credit,
                                       $quadern->hores_anteriors,
                                       $quadern->exempcio,
                                       $hores_practiques);

        return $resum;
     }

    public function prepare_form_data($data) {
        $data->excempcions = array('0' => '-', '25' => '25', '50' => '50');
    }
}