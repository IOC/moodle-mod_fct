<?php

fct_require('pagines/base_dades_quadern.php',
            'pagines/form_dades_empresa.php');

class fct_pagina_dades_empresa extends fct_pagina_base_dades_quadern {

    var $empresa;
    var $form;

    function configurar() {
        parent::configurar(required_param('quadern', PARAM_INT));
        $this->empresa = fct_db::dades_empresa($this->quadern->id);
        if (!$this->empresa) {
            $this->error('recuperar_empresa');
        }
        $this->configurar_accio(array('veure', 'editar', 'desar', 'cancellar'), 'veure');

        if ($this->accio != 'veure') {
            $this->comprovar_permis($this->permis_editar);
        }

        $this->url = fct_url::dades_empresa($this->quadern->id);
        $this->afegir_navegacio(fct_string('empresa'), $this->url);
        $this->form = new fct_form_dades_empresa($this);
    }

    function mostrar() {
        $this->form->set_data($this->empresa);
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
            $data->id = $this->empresa->id;
            $data->quadern = $this->empresa->quadern;
            $ok = fct_db::actualitzar_dades_empresa($data);
            if ($ok) {
                $this->registrar('update dades_empresa');
            } else {
                $this->error('desar_empresa');
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
        $this->registrar('view dades_empresa');
    }
}

