<?php

require_once 'form_base.php';

class fct_form_dades_centre extends fct_form_base {

    function configurar() {
        $this->afegir_header( 'dades_centre', fct_string('dades_centre'));

        $this->afegir_text('nom', fct_string('nom'), 32);
        $this->afegir_text('adreca', fct_string('adreca'), 32);
        $this->afegir_text('codi_postal', fct_string('codi_postal'), 8);
        $this->afegir_text('poblacio', fct_string('poblacio'), 32);
        $this->afegir_text('telefon', fct_string('telefon'), 32);
        $this->afegir_text('fax', fct_string('fax'), 32);
        $this->afegir_text('email', fct_string('email'), 32);

        if (!$this->pagina->accio) {
            $this->afegir_static('tutor', fct_string('tutor_centre'),
                $this->pagina->nom_usuari($this->pagina->quadern->tutor_centre, true));
            $this->congelar();
        } else if ($this->pagina->accio == 'veure') {
            $this->afegir_boto('editar', fct_string('edita'));
            $this->congelar();
        } else {
            $this->afegir_boto('desar', fct_string('desa'));
            $this->afegir_boto_cancellar();
        }
    }


}

