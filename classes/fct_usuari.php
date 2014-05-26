<?php

class fct_usuari {
    public $id;
    public $fctid;
    public $nom;
    public $cognoms;
    public $email;
    public $es_administrador;
    public $es_alumne;
    public $es_tutor_centre;
    public $es_tutor_empresa;

    public function __construct($fctid, $userid) {

        global $DB;

        $record = $DB->get_record('user', array('id' => $userid));
        $this->id = $record->id;
        $this->fct = $fctid;
        $this->nom = $record->firstname;
        $this->cognoms = $record->lastname;
        $this->email = $record->email;

        $cm = get_coursemodule_from_instance('fct', $fctid);
        $context = get_context_instance(CONTEXT_MODULE, $cm->id);

        $this->es_administrador = has_capability(
            "mod/fct:admin", $context, $userid, false);
        $this->es_alumne = has_capability(
            "mod/fct:alumne", $context, $userid, false);
        $this->es_tutor_centre = has_capability(
            "mod/fct:tutor_centre", $context, $userid, false);
        $this->es_tutor_empresa = has_capability(
            "mod/fct:tutor_empresa", $context, $userid, false);

    }

}