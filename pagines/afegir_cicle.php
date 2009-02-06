<?php

fct_require('pagines/base_cicles.php',
            'pagines/form_cicle.php');

class fct_pagina_afegir_cicle extends fct_pagina_base_cicles {

    function comprovar_nom($data) {
        if (fct_db::cicle_duplicat($this->fct->id, addslashes($data['nom']))) {
            return array('nom' => fct_string('cicle_formatiu_duplicat'));
        }

        return true;
    }

    function configurar() {
        $this->configurar_accio(array('afegir', 'cancellar'), 'afegir');
        parent::configurar(required_param('fct', PARAM_INT));
        $this->url = fct_url::afegir_cicle($this->fct->id);
    }

    function processar_afegir() {
        $form = new fct_form_cicle($this);

        $data = $form->get_data();
        if ($data) {
            $id = fct_db::afegir_cicle($this->fct->id, $data->nom, $data->activitats);
            if ($id) {
                $this->registrar('add cicle', fct_url::cicle($id), $data->nom);
            } else {
                $this->error('afegir_cicle');
            }
            redirect(fct_url::cicle($id));
        }

        $this->mostrar_capcalera();
        $form->display();
        $this->mostrar_peu();
    }

    function processar_cancellar() {
        redirect(fct_url::llista_cicles($this->fct->id));
    }

}

