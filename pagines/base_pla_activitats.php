<?php

require_once 'base_quadern.php';

class fct_pagina_base_pla_activitats extends fct_pagina_base_quadern {

    var $permis_editar;

    function configurar($quadern_id) {
        parent::configurar($quadern_id);

        $this->afegir_navegacio(fct_string('pla_activitats'),
            fct_url::pla_activitats($this->quadern->id));

        $this->pestanya = 'pla_activitats';
        $this->permis_editar = ($this->permis_admin or $this->quadern->estat
                                and $this->permis_tutor_centre);
    }

    function definir_pestanyes() {
        parent::definir_pestanyes();
        $pestanyes = array();
        if ($this->permis_editar) {
            $pestanyes[] = new tabobject('activitats_pla',
                fct_url::pla_activitats($this->quadern->id), fct_string('activitats'));
            $pestanyes[] = new tabobject('afegir_activitat_pla',
                fct_url::afegir_activitat_pla($this->quadern->id), fct_string('afegeix_activitat'));
            $pestanyes[] = new tabobject('afegir_activitats_cicle_pla',
                fct_url::afegir_activitats_cicle_pla($this->quadern->id), fct_string('afegeix_activitats_cicle'));
            $pestanyes[] = new tabobject('suprimeix_activitats_pla',
                fct_url::suprimir_activitats_pla($this->quadern->id), fct_string('suprimeix_activitats'));
        }
        $this->pestanyes[] = $pestanyes;
    }
}

