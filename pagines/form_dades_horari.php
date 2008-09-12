<?php

require_once 'form_base.php';

class fct_form_dades_horari extends fct_form_base {

    function configurar() {
        $this->afegir_header( 'dades_horari', fct_string('horari_practiques'));

        $this->afegir_text('dilluns', fct_string('dilluns'), 32);
        $this->afegir_text('dimarts', fct_string('dimarts'), 32);
        $this->afegir_text('dimecres', fct_string('dimecres'), 32);
        $this->afegir_text('dijous', fct_string('dijous'), 32);
        $this->afegir_text('divendres', fct_string('divendres'), 32);
        $this->afegir_text('dissabte', fct_string('dissabte'), 32);
        $this->afegir_text('diumenge', fct_string('diumenge'), 32);

        if ($this->pagina->accio == 'veure') {
            if ($this->pagina->permis_editar) {
                $this->afegir_boto_enllac('editar', fct_string('edita'));
            }
            $this->congelar();
        } else {
            $this->afegir_boto('desar',  fct_string('desa'));
            $this->afegir_boto_cancellar();
        }
    }

}

