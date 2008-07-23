<?php

require_once 'base_plantilles.php';
require_once 'form_plantilla.php';

class fct_pagina_afegir_plantilla extends fct_pagina_base_plantilles {

    function comprovar_nom($data) {
        if (fct_db::plantilla_duplicada($this->fct->id, addslashes($data['nom']))) {
            return array('nom' => fct_string('plantilla_duplicada'));
        }

        return true;
    }

    function configurar() {
        $this->configurar_accio(array('afegir', 'cancellar'), 'afegir');
        parent::configurar(required_param('fct', PARAM_INT));
        $this->url = fct_url::afegir_plantilla($this->fct->id);
        $this->pestanya = 'afegir_plantilla';
        $this->afegir_navegacio(fct_string('afegeix_plantilla'), $this->url);
    }

    function processar_afegir() {
        $form = new fct_form_plantilla($this);

        $data = $form->get_data();
        if ($data) {
            $id = fct_db::afegir_plantilla($this->fct->id, $data->nom, $data->activitats);
            if ($id) {
                $this->registrar('add plantilla', fct_url::plantilla($id), $data->nom);
            } else {
                $this->error('afegir_plantilla');
            }
            redirect(fct_url::plantilla($id));
        }

        $this->mostrar_capcalera();
        $form->display();
        $this->mostrar_peu();
    }

    function processar_cancellar() {
        redirect(fct_url::llista_plantilles($this->fct->id));
    }

}

