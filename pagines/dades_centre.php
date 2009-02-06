<?php

fct_require('pagines/base.php',
            'pagines/form_dades_centre.php');

class fct_pagina_dades_centre extends fct_pagina_base {

    var $centre;
    var $form;
    var $desar;

    function configurar() {
        parent::configurar(required_param('fct', PARAM_INT));
        $this->comprovar_permis($this->permis_admin);

        $this->configurar_accio(array('veure', 'editar', 'desar', 'cancellar'), 'veure');

        $this->centre = fct_db::dades_centre($this->fct->id);
        if (!$this->centre) {
            $this->error('recuperar_centre_docent');
        }

        $this->url = fct_url::dades_centre($this->fct->id);
        $this->pestanya = 'dades_centre';
        $this->afegir_navegacio(fct_string('dades_centre'));

        $this->form = new fct_form_dades_centre($this);
    }

    function mostrar() {
        $this->form->set_data($this->centre);
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
            $data->id = $this->centre->id;
            $data->fct = $this->centre->fct;
            $ok = fct_db::actualitzar_dades_centre($data);
            if ($ok) {
                $this->registrar('update dades_centre');
            } else {
                $this->error('desar_centre_docent');
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
        $this->registrar('view dades_centre');
    }

}

