<?php

fct_require('pagines/form_base.php');

class fct_form_cicles_empreses extends fct_form_base {

    function configurar() {
        $this->afegir_header('llista_empreses', fct_string('llista_empreses'));
        $this->afegir_llista_checkbox('cicle', $this->pagina->cicles, 'nom');
        $this->afegir_header('configuracio', fct_string('configuracio'));
        $this->afegir_select('format', fct_string('format'),
                             array(1 => 'Excel', 2 => 'CSV'));
        $this->afegir_boto('baixar', fct_string('baixa'));
    }
}

