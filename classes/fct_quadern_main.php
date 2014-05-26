<?php

require_once('form/quadern_edit_form.php');
require_once('fct_quadern_base.php');



class fct_quadern_main extends fct_quadern_base {

    public function tabs($id, $type = 'view') {

        $tab = parent::tabs_quadern($id, $this->id);
        $tab['currentab'] = 'quadern_main';

        return $tab;
    }

    public function view() {
        global $PAGE;

        $output = $PAGE->get_renderer('mod_fct', 'quadern_main');
        $output->view($this);

        return true;

    }

    public function prepare_form_data($data) {}
}
