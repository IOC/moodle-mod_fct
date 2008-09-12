<?php

require_once 'form_base.php';

class fct_form_valoracio_actituds extends fct_form_base {

    static function actituds() {
        $actituds = array();
        for ($i = 0; $i < 15; $i++) {
            $actituds[$i] = fct_string('actitud_' . ($i+1));
        }
        return $actituds;
    }

    function configurar() {
        $this->afegir_header('valoracio_actituds', $this->pagina->titol);

        $this->afegir_llista_select('actitud', $this->actituds(), $this->options_barem(), false, '');

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

