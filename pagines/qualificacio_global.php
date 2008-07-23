<?php

require_once 'base_quadern.php';
require_once 'form_qualificacio.php';

class fct_pagina_qualificacio_global extends fct_pagina_base_quadern {

    var $qualificacio;
    var $form;

    function configurar() {
        parent::configurar(required_param('quadern', PARAM_INT));
        $this->qualificacio = fct_db::qualificacio_global($this->fct->id,
            $this->quadern->alumne);
        if (!$this->qualificacio) {
            $this->error('recuperar_qualificacio_global');
        }
        $this->configurar_accio(array('veure', 'editar', 'desar', 'cancellar'), 'veure');

        if ($this->accio != 'veure' and !$this->permis_admin and !$this->permis_tutor_centre) {
            $this->error('permis_pagina');
        }

        $this->url = fct_url::qualificacio_global($this->quadern->id);
        $this->titol = fct_string('qualificacio_global');
        $this->pestanya = 'qualificacio_global';
        $this->afegir_navegacio(fct_string('qualificacio_global'),
            $this->url);
        $this->form = new fct_form_qualificacio($this);
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
            $data->fct = $this->qualificacio->fct;
            $data->alumne = $this->qualificacio->alumne;
            $data->data = $this->form->date2unix($data->data);
            $ok = fct_db::actualitzar_qualificacio_global($data);
            if ($ok) {
                $barem = fct_form_qualificacio::options_barem();
                $this->registrar('update qualificacio_global', null,
                    $barem[$data->qualificacio]);
            } else {
                $this->error('desar_qualificacio_global');
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
        $this->registrar('view qualificacio_global');
    }
}

