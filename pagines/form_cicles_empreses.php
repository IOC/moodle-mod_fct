<?php

require_once 'form_base.php';

class fct_form_cicles_empreses extends fct_form_base {

    function configurar() {
        $this->afegir_header('cicles_empreses', fct_string('empreses'));
        $this->afegir_llista_checkbox('cicle', $this->pagina->cicles, 'nom');
        $this->afegir_boto('baixar', fct_string('baixa'));
    }
}

