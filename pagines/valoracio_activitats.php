<?php

require_once 'base_valoracio.php';
require_once 'form_valoracio_activitats.php';

class fct_pagina_valoracio_activitats extends fct_pagina_base_valoracio {

    var $pla;
    var $activitats;

    function configurar() {
        parent::configurar(required_param('quadern', PARAM_INT));

        $this->pla = fct_db::pla_actvitats_quadern($this->quadern->id);
        if (!$this->pla) {
            $this->error('recuperar_pla_activitats');
        }

        $this->activitats = fct_db::activitats_pla($this->pla->id);

        $this->url = fct_url::valoracio_activitats($this->quadern->id);
        $this->afegir_navegacio(fct_string('valoracio_activitats'), $this->url);
    }

    function configurar_formulari() {
        if ($this->activitats) {
            $this->form = new fct_form_valoracio_activitats($this);
            return $this->form->get_data();
        }
    }

    function mostrar() {
        $this->mostrar_capcalera();
        if ($this->activitats) {
            $this->form->set_data_llista('activitat',
                fct_db::notes_activitats_pla($this->pla->id));
            $this->form->display();
        } else {
            print_heading(fct_string('valoracio_activitats'));
            echo '<p>' . fct_string('cap_activitat') . '</p>';
        }
        $this->mostrar_peu();
    }

    function processar_cancellar() {
        redirect($this->url);
    }

    function processar_desar() {
        $data = $this->configurar_formulari();
        if ($data) {
            $notes = $this->form->get_data_llista('activitat');
            $ok = fct_db::actualitzar_notes_activitats_pla($notes);
            if ($ok) {
                $this->registrar('update valoracio_activitats');
            } else {
                $this->error('desar_valoracio_activitats');
            }
            redirect($this->url);
        }
        $this->mostrar();
    }

    function processar_editar() {
        $this->configurar_formulari();
        $this->mostrar();
    }

    function processar_veure() {
        $this->configurar_formulari();
        $this->mostrar();
        $this->registrar('view valoracio_activitats');
    }

}

