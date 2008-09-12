<?php

require_once 'form_base.php';

class fct_form_dades_centre_concertat extends fct_form_base {

    function configurar() {
        $this->afegir_header( 'dades_centre_concertat', 'Centre concertat');

        $this->afegir_text('entitat', fct_string('entitat'), 32);
        $this->afegir_text('adreca', fct_string('adreca'), 32);
        $this->afegir_text('codi_postal', fct_string('codi_postal'), 8);
        $this->afegir_text('poblacio', fct_string('poblacio'), 32);

        if ($this->pagina->accio == 'veure') {
            if ($this->pagina->permis_admin or ($this->pagina->permis_alumne and $this->pagina->quadern->estat)) {
                $this->afegir_boto_enllac('editar', fct_string('edita'));
            }
            $this->congelar();
        } else {
            $this->afegir_boto('desar', fct_string('desa'));
            $this->afegir_boto_cancellar();
        }
    }

}

