<?php

require_once 'form_base.php';

class fct_form_qualificacio extends fct_form_base {

    function configurar() {
        $this->afegir_header( 'header_qualificacio', $this->pagina->titol);

        $this->afegir_select('qualificacio', fct_string('qualificacio'),
            self::options_barem_qualificacio());
        $this->afegir_select('nota', '', self::options_barem(), false, '');
        $this->afegir_date('data', fct_string('data'));
        $this->afegir_textarea('observacions', fct_string('observacions'), 4, 40);

        if ($this->pagina->accio == 'veure') {
            if ($this->pagina->permis_admin or $this->pagina->permis_tutor_centre) {
                $this->afegir_boto_enllac('editar', fct_string('edita'));
            }
            $this->congelar();
        } else {
            $this->afegir_boto('desar', fct_string('desa'));
            $this->afegir_boto_cancellar();
        }
    }

}

