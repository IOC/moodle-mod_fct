<?php

fct_require('pagines/base_cicle.php');

class fct_pagina_base_activitat_cicle extends fct_pagina_base_cicle {

    var $activitat;

    function configurar($activitat_id) {
        $this->activitat = fct_db::activitat_cicle($activitat_id);
        if (!$this->activitat) {
            $this->error('recuperar_activitat');
        }
        parent::configurar($this->activitat->cicle);
    }
}

