<?php

require_once 'base_pla_activitats.php';
require_once 'form_plantilla_pla.php';

class fct_pagina_afegir_activitats_plantilla_pla extends fct_pagina_base_pla_activitats  {

    var $plantilles;

    function configurar() {
        parent::configurar(required_param('quadern', PARAM_INT));
        $this->comprovar_permis($this->permis_editar);
        $this->configurar_accio(array('afegir', 'cancellar'), 'afegir');
        $this->url = fct_url::afegir_activitats_plantilla_pla($this->quadern->id);
    }

    function processar_afegir() {
        $this->plantilles = fct_db::plantilles($this->fct->id);
        if ($this->plantilles) {
            $form = new fct_form_plantilla_pla($this);
            $data = $form->get_data();
            if ($data) {
                $id = fct_db::afegir_activitats_plantilla_pla($this->quadern->id, $data->plantilla);
                if ($id) {
                    $this->registrar('add activitats_plantilla_pla',
                        fct_url::pla_activitats($this->quadern->id),
                        $this->plantilles[$data->plantilla]->nom);
                } else {
                   $this->error('afegir_activitats');
                }
                redirect(fct_url::pla_activitats($this->quadern->id));
            }
        }

        $this->mostrar_capcalera();
        if ($this->plantilles) {
            $form->display();
        } else {
            print_heading(fct_string('importa_activitats'));
            echo '<p>' . fct_string('cap_plantilla') . '</p>';
        }
        $this->mostrar_peu();
    }

    function processar_cancellar() {
        redirect(fct_url::pla_activitats($this->quadern->id));
    }

}

