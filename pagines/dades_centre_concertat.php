<?php

require_once 'base_dades_quadern.php';
require_once 'form_dades_centre_concertat.php';

class fct_pagina_dades_centre_concertat extends fct_pagina_base_dades_quadern {

    var $centre_concertat;
    var $form;

    function configurar() {
        parent::configurar(required_param('quadern', PARAM_INT));
        $this->centre_concertat = fct_db::dades_centre_concertat($this->quadern->id);
        if (!$this->centre_concertat) {
            $this->error('recuperar_centre_concertat');
        }
        $this->configurar_accio(array('veure', 'editar', 'desar', 'cancellar'), 'veure');

        if ($this->accio != 'veure') {
            if (!$this->permis_admin and !$this->permis_alumne) {
                $this->error('permis_pagina');
            }
            $this->comprovar_estat_obert();
        }

        $this->url = fct_url::dades_centre_concertat($this->quadern->id);
        $this->afegir_navegacio(fct_string('centre_concertat'), $this->url);
        $this->form = new fct_form_dades_centre_concertat($this);
    }

    function mostrar() {
        $this->form->set_data($this->centre_concertat);
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
            $data->id = $this->centre_concertat->id;
            $data->quadern = $this->centre_concertat->quadern;
            $ok = fct_db::actualitzar_dades_centre_concertat($data);
            if ($ok) {
                $this->registrar('update dades_centre_concertat');
            } else {
                $this->error('desar_centre_concertat');
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
        $this->registrar('view dades_centre_concertat');
    }
}

