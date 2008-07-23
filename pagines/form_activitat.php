<?php

require_once 'form_base.php';

class fct_form_activitat extends fct_form_base {

    function configurar() {

    	$this->afegir_header('activitat', $this->pagina->accio == 'afegir' ?
    		fct_string('nova_activitat'): fct_string('activitat'));

        $this->afegir_textarea('descripcio', fct_string('descripcio'), 3, 50, true);

        $this->afegir_comprovacio('comprovar_descripcio');

		if ($this->pagina->accio == 'afegir') {
		    $this->afegir_boto('afegir', fct_string('afegeix'));
		} else {
		    $this->afegir_boto('editar', fct_string('desa'));
		}

		$this->afegir_boto_cancellar();
    }

}

