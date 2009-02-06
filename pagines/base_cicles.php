<?php

fct_require('pagines/base.php');

class fct_pagina_base_cicles extends fct_pagina_base {

    function configurar($fct_id) {
        parent::configurar($fct_id);
        $this->comprovar_permis($this->permis_admin);
        $this->pestanya = 'cicles';
        $this->afegir_navegacio(fct_string('cicles_formatius'));
    }

    function definir_pestanyes() {
        parent::definir_pestanyes();
        $pestanyes = array(
            new tabobject('llista_cicles', fct_url::llista_cicles($this->fct->id), fct_string('cicles_formatius')),
            new tabobject('afegir_cicle', fct_url::afegir_cicle($this->fct->id), fct_string('afegeix_cicle_formatiu')),
        );
        $this->pestanyes[] = $pestanyes;
    }

}

