<?php

fct_require('pagines/form_base.php');

class fct_form_dades_relatives extends fct_form_base {

    function configurar() {
        $this->afegir_header('dades_relatives', fct_string('dades_relatives'));
        $this->afegir_text('hores_credit', fct_string('hores_credit'), 6, false, true);
        $this->afegir_select('exempcio', fct_string('exempcio'),
            array(0 => '-', 25 => '25%', 50 => '50%'));
        if ($this->pagina->accio == 'veure') {
            $this->afegir_static('hores_exempcio',fct_string('hores_exempcio'), '');
            $this->afegir_static('hores_practiques',
                                 fct_string('hores_practiques'), '', ':',
                                 'dades_relatives_hores_practiques');
        }
        $this->afegir_text('hores_anteriors', fct_string('hores_anteriors'), 6,
                           false, true, ':', 'dades_relatives_hores_anteriors');
        if ($this->pagina->accio == 'veure') {
            $this->afegir_static('hores_realitzades',
                                 fct_string('hores_realitzades'), '', ':',
                                 'dades_relatives_hores_realitzades');
            $this->afegir_static('hores_pendents', fct_string('hores_pendents'), '');
        }

        if ($this->pagina->accio == 'veure') {
            if ($this->pagina->permis_editar) {
                $this->afegir_boto_enllac('editar', 'Edita');
            }
            $this->congelar();
        } else {
            $this->afegir_boto('desar', 'Desa');
            $this->afegir_boto_cancellar();
        }
    }

}

