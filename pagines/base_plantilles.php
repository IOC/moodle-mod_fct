<?php

require_once 'base.php';

class fct_pagina_base_plantilles extends fct_pagina_base {

    function configurar($fct_id) {
        parent::configurar($fct_id);
        $this->comprovar_permis($this->permis_admin);
        $this->pestanya = 'plantilles';
    }

}

