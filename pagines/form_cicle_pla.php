<?php

require_once 'form_base.php';

class fct_form_cicle_pla extends fct_form_base {

    function configurar() {
        $this->afegir_header('activitats_cicle', fct_string('afegeix_activitats_cicle'));
        $this->afegir_select('cicle', fct_string('cicle_formatiu'), $this->pagina->cicles, 'nom');
        $this->afegir_boto('afegir', fct_string('afegeix'));
        $this->afegir_boto_cancellar();
    }

}

