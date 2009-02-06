<?php

fct_require('pagines/base_dades_quadern.php',
            'pagines/form_dades_alumne.php');

class fct_pagina_dades_alumne extends fct_pagina_base_dades_quadern {

    var $alumne;
    var $form;

    function configurar() {
        parent::configurar(required_param('quadern', PARAM_INT));
        $this->alumne = fct_db::dades_alumne($this->fct->id, $this->quadern->alumne);
        if (!$this->alumne) {
            $this->error('recuperar_alumne');
        }
        $this->configurar_accio(array('veure', 'editar', 'desar', 'cancellar'), 'veure');

        if ($this->accio != 'veure') {
            $this->comprovar_permis($this->permis_editar);
        }

        $this->url = fct_url::dades_alumne($this->quadern->id);
        $this->afegir_navegacio(fct_string('alumne'), $this->url);
        $this->form = new fct_form_dades_alumne($this);
    }

    function mostrar() {
        $this->form->set_data($this->alumne);
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
            $data->id = $this->alumne->id;
            $data->fct = $this->fct->id;
            $data->alumne = $this->quadern->alumne;
            $ok = fct_db::actualitzar_dades_alumne($data);
            if ($ok) {
                $this->registrar('update dades_alumne');
            } else {
                $this->error('desar_alumne');
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
        $this->registrar('view dades_alumne');
    }
}

