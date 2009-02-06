<?php

fct_require('pagines/form_base.php');

class fct_form_dades_alumne extends fct_form_base {

    function configurar() {
        $this->afegir_header( 'dades_alumne', 'Alumne/a');

        $this->afegir_static('nom', fct_string('nom'),
            $this->pagina->nom_usuari($this->pagina->quadern->alumne, true));
        $this->afegir_text('adreca', fct_string('adreca'), 32);
        $this->afegir_text('codi_postal', fct_string('codi_postal'), 8);
        $this->afegir_text('poblacio', fct_string('poblacio'), 32);
        $this->afegir_text('telefon', fct_string('telefon'), 32);
        $this->afegir_text('dni', fct_string('dni'), 16);
        $this->afegir_text('email', fct_string('email'), 32);
        $this->afegir_text('targeta_sanitaria', fct_string('targeta_sanitaria'), 32);

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

