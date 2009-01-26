<?php

require_once 'base.php';

class fct_pagina_base_quaderns extends fct_pagina_base {

    function configurar($fct_id=false, $cm_id=false) {
        parent::configurar($fct_id, $cm_id);
        $this->pestanya = 'quaderns';
    }

    function definir_pestanyes() {
        parent::definir_pestanyes();
        $pestanyes = array(
            new tabobject('llista_quaderns', fct_url::llista_quaderns($this->fct->id), fct_string('quaderns')),
            new tabobject('afegir_quadern', fct_url::afegir_quadern($this->fct->id), fct_string('afegeix_quadern')),
        );
        $this->pestanyes[] = $pestanyes;
    }
}

