<?php

require_once 'base_valoracio.php';
require_once 'form_qualificacio.php';

class fct_pagina_qualificacio_quadern extends fct_pagina_base_valoracio {

    var $qualificacio;
    var $form;

    function configurar() {
        parent::configurar();
        $this->qualificacio = fct_db::qualificacio_quadern($this->quadern->id);
        if (!$this->qualificacio) {
            $this->error('recuperar_qualificacio_quadern');
        }
        $this->url = fct_url::qualificacio_quadern($this->quadern->id);
        $this->titol = fct_string('qualificacio_quadern');
        $this->form = new fct_form_qualificacio($this);
        $this->afegir_navegacio(fct_string('qualificacio_quadern'),
            $this->url);
    }

    function mostrar() {
        $this->form->set_data($this->qualificacio);
        $this->mostrar_capcalera();
        $this->form->display();
        $this->mostrar_peu();
    }

    function processar_cancellar() {
        redirect($this->url);
    }

    function processar_desar() {
        $data = $this->form->get_data();
        if ($data) {
            $data->id = $this->qualificacio->id;
            $data->quadern = $this->quadern->id;
            $data->data = $this->form->date2unix($data->data);
            $ok = fct_db::actualitzar_qualificacio_quadern($data);
            if ($ok) {
                $barem = fct_form_qualificacio::options_barem();
                $this->registrar('update qualificacio_quadern', null,
                    $barem[$data->qualificacio]);
            } else {
                $this->error('desar_qualificacio_quadern');
            }
            redirect($this->url);
        }
        $this->mostrar();
    }

    function processar_editar() {
        if (!$this->qualificacio->data) {
            $this->qualificacio->data = (int) time();
        }
        $this->mostrar();
    }

    function processar_veure() {
        $this->mostrar();
        $this->registrar('view qualificacio_quadern');
    }
}

