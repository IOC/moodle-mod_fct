<?php

require_once 'base_plantilla.php';

class fct_pagina_base_activitat_plantilla extends fct_pagina_base_plantilla {

    var $activitat;

    function configurar($activitat_id) {
        $this->activitat = fct_db::activitat_plantilla($activitat_id);
        if (!$this->activitat) {
            $this->error('recuperar_activitat');
        }
        parent::configurar($this->activitat->plantilla);
    }
}

