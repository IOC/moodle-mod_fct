<?php

fct_require('pagines/form_base.php');

class fct_form_activitats_cicle_pla extends fct_form_base {

    function configurar() {
        $this->afegir_header('activitats_cicle', fct_string('afegeix_activitats_cicle'));
        $activitats = fct_db::activitats_cicle($this->pagina->quadern->cicle);
        $this->afegir_llista_checkbox('activitat', $activitats, 'descripcio');
        $this->afegir_boto('afegir', fct_string('afegeix'));
        $this->afegir_boto_cancellar();
    }
}

