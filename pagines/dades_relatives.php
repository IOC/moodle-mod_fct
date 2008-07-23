<?php

require_once 'base_dades_quadern.php';
require_once 'form_dades_relatives.php';

class fct_pagina_dades_relatives extends fct_pagina_base_dades_quadern {

    var $dades;
    var $form;

    function configurar() {
        parent::configurar(required_param('quadern', PARAM_INT));
        $this->dades = fct_db::dades_relatives($this->fct->id, $this->quadern->alumne);
        if (!$this->dades) {
            $this->error('recuperar_dades_relatives');
        }
        $this->configurar_accio(array('veure', 'editar', 'desar', 'cancellar'), 'veure');

        if ($this->accio != 'veure' and !$this->permis_admin and !$this->permis_alumne) {
            $this->error('permis_pagina');
        }

        $this->url = fct_url::dades_relatives($this->quadern->id);
        $this->afegir_navegacio(fct_string('dades_relatives'), $this->url);
        $this->form = new fct_form_dades_relatives($this);
    }

    function mostrar() {
        $this->dades->hores_realitzades = ceil((float) $this->dades->exempcio / 100
            * $this->dades->hores_credit)
            + fct_db::hores_realitzades_fct($this->fct->id, $this->quadern->alumne);
        $this->dades->hores_pendents = $this->dades->hores_credit
            - $this->dades->hores_realitzades;
        $this->form->set_data($this->dades);
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
            $data->id = $this->dades->id;
            $data->fct = $this->dades->fct;
            $data->alumne = $this->dades->alumne;
            $ok = fct_db::actualitzar_dades_relatives($data);
            if ($ok) {
                $this->registrar('update dades_relatives');
            } else {
                $this->error('desar_dades_relatives');
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
        $this->registrar('view dades_relatives');
    }
}

