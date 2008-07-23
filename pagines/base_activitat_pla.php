<?php

require_once 'base_pla_activitats.php';

class fct_pagina_base_activitat_pla extends fct_pagina_base_pla_activitats {

    var $activitat;

    function configurar($activitat_id) {
        $this->activitat = fct_db::activitat_pla($activitat_id);
        if (!$this->activitat) {
            $this->error('recuperar_activitat');
        }
        parent::configurar(false, $this->activitat->pla);
    }
}

