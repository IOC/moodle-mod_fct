<?php

require_once 'form_base.php';

class fct_form_dades_conveni extends fct_form_base {

    function configurar() {
        $this->afegir_header( 'dades_conveni', fct_string('conveni'));

        $this->afegir_text('codi', fct_string('codi'), 32);
        $this->afegir_date('data_inici', fct_string('data_inici'));
        $this->afegir_date('data_final', fct_string('data_final'));
        $this->afegir_textarea('prorrogues', fct_string('prorrogues'), 3, 50);
        $this->afegir_text('hores_practiques', fct_string('hores_practiques'), 6, false, true);
        $this->afegir_static('hores_realitzades', fct_string('hores_practiques_realitzades'), '');
        $this->afegir_static('hores_pendents', fct_string('hores_practiques_pendents'), '');

        $this->afegir_comprovacio('comprovar_dates', $this);

        if ($this->pagina->accio == 'veure') {
            if ($this->pagina->permis_admin or ($this->pagina->permis_alumne and $this->pagina->quadern->estat)) {
                $this->afegir_boto('editar', fct_string('edita'));
            }
            $this->congelar();
        } else {
            $this->afegir_boto('desar', fct_string('desa'));
            $this->afegir_boto_cancellar();
        }
    }

    function comprovar_dates($data) {
        $data_inici = $this->date2unix($data['data_inici']);
        $data_final = $this->date2unix($data['data_final']);
        if ($data_inici >= $data_final) {
            return array('data_inici' => fct_string('anterior_data_final'),
            	'data_final' => fct_string('posterior_data_inici'));
        }
        return true;
    }
}

