<?php

require_once 'form_base.php';

class fct_form_dades_empresa extends fct_form_base {

    function configurar() {
        $this->afegir_header( 'dades_empresa', fct_string('empresa'));

        $this->afegir_static('nom', fct_string('nom'), $this->pagina->quadern->nom_empresa);
        $this->afegir_text('adreca', fct_string('adreca'), 32);
        $this->afegir_text('codi_postal', fct_string('codi_postal'), 8);
        $this->afegir_text('poblacio', fct_string('poblacio'), 32);
        $this->afegir_text('telefon', fct_string('telefon'), 32);
        $this->afegir_text('fax', fct_string('fax'), 32);
        $this->afegir_text('email', fct_string('email'), 32);
        $this->afegir_text('nif', fct_string('nif'), 16);

        if ($this->pagina->accio == 'veure') {
            if ($this->pagina->permis_editar) {
                $this->afegir_boto_enllac('editar', fct_string('edita'));
            }
            $this->congelar();
        } else {
            $this->afegir_boto('desar', fct_string('desa'));
            $this->afegir_boto_cancellar();
        }
    }

}

