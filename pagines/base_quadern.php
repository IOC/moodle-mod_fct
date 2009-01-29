<?php

require_once 'base.php';

class fct_pagina_base_quadern extends fct_pagina_base {

    var $quadern;
    var $titol;

    function configurar($quadern_id) {
        global $USER;

        $this->quadern = fct_db::quadern($quadern_id);
        if (!$this->quadern) {
            $this->error('recuperar_quadern');
        }

        parent::configurar($this->quadern->fct);

        $this->comprovar_permis($this->permis_admin
            or ($this->permis_alumne and $this->quadern->alumne == $USER->id)
            or ($this->permis_tutor_centre and $this->quadern->tutor_centre == $USER->id)
            or ($this->permis_tutor_empresa and $this->quadern->tutor_empresa == $USER->id));

        $this->titol = $this->nom_usuari($this->quadern->alumne)
            .' ('.$this->quadern->nom_empresa.')';
        $this->afegir_navegacio($this->titol,
            fct_url::quadern($this->quadern->id));
    }

    function definir_pestanyes() {
        $this->pestanyes = array(array(
            new tabobject('quadern',
                fct_url::quadern($this->quadern->id),
                fct_string('quadern')),
            new tabobject('dades_generals',
                fct_url::dades_centre_quadern($this->quadern->id),
                fct_string('dades_generals')),
            new tabobject('pla_activitats',
                fct_url::pla_activitats($this->quadern->id),
                fct_string('pla_activitats')),
            new tabobject('seguiment_quinzenal',
                fct_url::seguiment($this->quadern->id),
                fct_string('seguiment_quinzenal')),
            new tabobject('valoracio',
                fct_url::valoracio_actituds($this->quadern->id, 0),
                fct_string('valoracio')),
            new tabobject('qualificacio_global',
                fct_url::qualificacio_global($this->quadern->id),
                fct_string('qualificacio_global')),
        ));
    }

    function alumne() {
        return $this->quadern->alumne;
    }

    function tutor_centre() {
        return $this->quadern->tutor_centre;
    }

    function tutor_empresa() {
        return $this->quadern->tutor_empresa;
    }

}
