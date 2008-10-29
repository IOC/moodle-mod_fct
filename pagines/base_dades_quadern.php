<?php

require_once 'base_quadern.php';

class fct_pagina_base_dades_quadern extends fct_pagina_base_quadern {

    var $permis_editar;

    function configurar($quadern_id) {
        parent::configurar($quadern_id);
        $this->pestanya = 'dades_generals';
        $this->permis_editar = ($this->permis_admin or $this->quadern->estat
                                and ($this->permis_tutor_centre or $this->permis_alumne));
    }

    function definir_pestanyes() {
        parent::definir_pestanyes();
        $pestanyes = array(
            new tabobject('dades_centre_quadern',
                fct_url::dades_centre_quadern($this->quadern->id), fct_string('centre_docent')),
            new tabobject('dades_alumne',
                fct_url::dades_alumne($this->quadern->id), fct_string('alumne')),
            // Pestanya de centre concertat ocultada temporalment
            // new tabobject('dades_centre_concertat',
            //    fct_url::dades_centre_concertat($this->quadern->id), fct_string('centre_concertat')),
            new tabobject('dades_emoresa',
                fct_url::dades_empresa($this->quadern->id), fct_string('empresa')),
            new tabobject('dades_cconveni',
                fct_url::dades_conveni($this->quadern->id), fct_string('conveni')),
            new tabobject('dades_horari',
                fct_url::dades_horari($this->quadern->id), fct_string('horari_practiques')),
            new tabobject('dades_relatives',
                fct_url::dades_relatives($this->quadern->id), fct_string('dades_relatives')),
        );
        $this->pestanyes[] = $pestanyes;
    }
}

