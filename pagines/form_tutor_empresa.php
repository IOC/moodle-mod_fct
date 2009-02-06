<?php

fct_require('pagines/form_base.php');

class fct_form_tutor_empresa extends fct_form_base {

    function configurar() {
        $this->afegir_header('tutor_empresa',  fct_string('tutor_de_empresa'));

        $this->afegir_text( 'dni', fct_string('dni'), 48, true);
        $this->afegir_text( 'nom', fct_string('nom'), 48, true);
        $this->afegir_text( 'cognoms', fct_string('cognoms'), 48, true);
        $this->afegir_text('email', fct_string('email'), 48);

        $this->afegir_comprovacio('comprovar_dni');
        $this->afegir_comprovacio('comprovar_nom');
        $this->afegir_comprovacio('comprovar_email');

        $this->afegir_boto('afegir', fct_string('afegeix'));
        $this->afegir_boto_cancellar();
    }

}
