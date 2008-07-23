<?php

require_once 'base.php';

class fct_pagina_base_plantilles extends fct_pagina_base {

    function configurar($fct_id) {
        parent::configurar($fct_id);
        require_capability('mod/fct:admin', $this->context);
        $this->pestanya = 'plantilles';
    }

}

