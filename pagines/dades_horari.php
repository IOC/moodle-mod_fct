<?php

require_once 'base_dades_quadern.php';
require_once 'form_dades_horari.php';

class fct_pagina_dades_horari extends fct_pagina_base_dades_quadern {

    var $horari;
    var $form;

    function configurar() {
        parent::configurar(required_param('quadern', PARAM_INT));
        $this->horari = fct_db::dades_horari($this->quadern->id);
        if (!$this->horari) {
            $this->error('recuperar_horari');
        }
        $this->configurar_accio(array('veure', 'editar', 'desar', 'cancellar'), 'veure');

        if ($this->accio != 'veure') {
            $this->comprovar_permis($this->permis_editar);
        }

        $this->url = fct_url::dades_horari($this->quadern->id);
        $this->afegir_navegacio(fct_string('horari_practiques'), $this->url);
        $this->form = new fct_form_dades_horari($this);
    }

    function mostrar() {
        $this->form->set_data($this->horari);
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
            $data->id = $this->horari->id;
            $data->quadern = $this->horari->quadern;
            $ok = fct_db::actualitzar_dades_horari($data);
            if ($ok) {
                $this->registrar('update dades_horari');
            } else {
                $this->error('error_desar_horari');
            }
            redirect($this->url);
        }
        $this->mostrar();
    }

    function processar_editar() {
        $this->mostrar();
    }

    function processar_veure() {
        $this->mostrar();
        $this->registrar('view dades_horari');
    }
}

