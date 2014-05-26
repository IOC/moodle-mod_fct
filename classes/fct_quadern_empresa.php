<?php

require_once('form/quadern_empresa_edit_form.php');
require_once('fct_quadern_base.php');
require_once('fct_base.php');
require_once('fct_cicle.php');

class fct_quadern_empresa extends fct_quadern_base {


    protected static $dataobject = 'empresa';

    protected $editform = 'fct_quadern_empresa_edit_form';

    protected static $dataobjectkeys = array('nom',
                                             'adreca',
                                             'poblacio',
                                             'codi_postal',
                                             'telefon',
                                             'fax',
                                             'email',
                                             'nif',
                                             'codi_agrupacio',
                                             'sic',
                                             'nom_responsable',
                                             'cognoms_responsable',
                                             'dni_responsable',
                                             'carrec_responsable',
                                             'nom_tutor',
                                             'cognoms_tutor',
                                             'dni_tutor',
                                             'email_tutor',
                                             'nom_lloc_practiques',
                                             'adreca_lloc_practiques',
                                             'poblacio_lloc_practiques',
                                             'codi_postal_lloc_practiques',
                                             'telefon_lloc_practiques');

    public function tabs($id, $type = 'view') {

        $tab = parent::tabs_quadern($id, $this->id);
        $subtree = parent::subtree($id, $this->id);

        $row = $tab['row'];
        $row['quadern_dades']->subtree = $subtree;
        $tab['row'] = $row;
        $tab['currentab'] = 'quadern_empresa';

        return $tab;
    }

    public function view() {
        global $PAGE;

        $output = $PAGE->get_renderer('mod_fct',
            'quadern_empresa');

        $output->view($this);

        return true;

    }
}
