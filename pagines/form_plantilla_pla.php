<?php

require_once 'form_base.php';

class fct_form_plantilla_pla extends fct_form_base {

    function configurar() {
        $this->afegir_header('activitats_plantilla', fct_string('importa_activitats'));
        $this->afegir_select('plantilla', fct_string('plantilla'), $this->pagina->plantilles, 'nom');
        $this->afegir_boto('afegir', fct_string('afegeix'));
        $this->afegir_boto_cancellar();
    }

}

