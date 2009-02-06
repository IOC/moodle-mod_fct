<?php

fct_require('pagines/form_base.php');

class fct_form_valoracio_activitats extends fct_form_base {

    function configurar() {
        $this->afegir_header('activitats', fct_string('valoracio_activitats'));

        $this->afegir_llista_select('activitat', $this->pagina->activitats, self::options_barem(), 'descripcio', '');

        if ($this->pagina->accio == 'veure') {
            if ($this->pagina->permis_editar) {
                $this->afegir_boto_enllac('editar', fct_string('edita'));
            }
        } else {
            $this->afegir_boto('desar', fct_string('desa'));
            $this->afegir_boto_cancellar();
        }

        if ($this->pagina->accio == 'veure') {
            $this->congelar();
        }
    }

}

