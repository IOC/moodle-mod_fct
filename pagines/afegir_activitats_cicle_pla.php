<?php

require_once 'base_pla_activitats.php';
require_once 'form_cicle_pla.php';

class fct_pagina_afegir_activitats_cicle_pla extends fct_pagina_base_pla_activitats  {

    var $cicles;

    function configurar() {
        parent::configurar(required_param('quadern', PARAM_INT));
        $this->comprovar_permis($this->permis_editar);
        $this->configurar_accio(array('afegir', 'cancellar'), 'afegir');
        $this->url = fct_url::afegir_activitats_cicle_pla($this->quadern->id);
    }

    function processar_afegir() {
        $this->cicles = fct_db::cicles($this->fct->id);
        if ($this->cicles) {
            $form = new fct_form_cicle_pla($this);
            $data = $form->get_data();
            if ($data) {
                $id = fct_db::afegir_activitats_cicle_pla($this->quadern->id, $data->cicle);
                if ($id) {
                    $this->registrar('add activitats_cicle_pla',
                        fct_url::pla_activitats($this->quadern->id),
                        $this->cicles[$data->cicle]->nom);
                } else {
                   $this->error('afegir_activitats');
                }
                redirect(fct_url::pla_activitats($this->quadern->id));
            }
        }

        $this->mostrar_capcalera();
        if ($this->cicles) {
            $form->display();
        } else {
            print_heading(fct_string('afegeix_activitats_cicle'));
            echo '<p>' . fct_string('cap_cicle_formatiu') . '</p>';
        }
        $this->mostrar_peu();
    }

    function processar_cancellar() {
        redirect(fct_url::pla_activitats($this->quadern->id));
    }

}

