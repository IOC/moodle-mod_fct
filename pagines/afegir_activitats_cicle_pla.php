<?php

require_once 'base_pla_activitats.php';
require_once 'form_activitats_cicle_pla.php';

class fct_pagina_afegir_activitats_cicle_pla extends fct_pagina_base_pla_activitats  {

    function configurar() {
        parent::configurar(required_param('quadern', PARAM_INT));
        $this->comprovar_permis($this->permis_editar);
        $this->configurar_accio(array('afegir', 'cancellar'), 'afegir');
        $this->url = fct_url::afegir_activitats_cicle_pla($this->quadern->id);
    }

    function processar_afegir() {
        if ($this->quadern->cicle) {
            $form = new fct_form_activitats_cicle_pla($this);
            $data = $form->get_data();
            if ($data) {
                $activitats = $form->get_data_llista('activitat');
                $ok = fct_db::afegir_activitats_cicle_pla($this->quadern->id,
                                                          $activitats);
                if ($ok) {
                    $this->registrar('add activitats_cicle_pla',
                                     fct_url::pla_activitats($this->quadern->id));
                } else {
                   $this->error('afegir_activitats');
                }
                redirect(fct_url::pla_activitats($this->quadern->id));
            }
        }

        $this->mostrar_capcalera();
        if ($this->quadern->cicle) {
            $form->display();
        } else {
            print_heading(fct_string('afegeix_activitats_cicle'));
            echo '<p>' . fct_string('quadern_sense_cicle_formatiu') . '</p>';
        }
        $this->mostrar_peu();
    }

    function processar_cancellar() {
        redirect(fct_url::pla_activitats($this->quadern->id));
    }

}
