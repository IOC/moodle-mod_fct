<?php

require_once 'base.php';

class fct_pagina_base_plantilles extends fct_pagina_base {

    function configurar($fct_id) {
        parent::configurar($fct_id);
        $this->comprovar_permis($this->permis_admin);
        $this->pestanya = 'plantilles';
    }

    function definir_pestanyes() {
        parent::definir_pestanyes();
        $pestanyes = array(
            new tabobject('llista_plantilles', fct_url::llista_plantilles($this->fct->id), fct_string('plantilles_activitats')),
            new tabobject('afegir_plantilla', fct_url::afegir_plantilla($this->fct->id), fct_string('afegeix_plantilla')),
        );
        $this->pestanyes[] = $pestanyes;
    }

}

