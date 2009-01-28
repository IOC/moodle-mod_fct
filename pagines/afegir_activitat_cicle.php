<?php

require_once 'base_cicle.php';
require_once 'form_activitat.php';

class fct_pagina_afegir_activitat_cicle extends fct_pagina_base_cicle {

    function comprovar_descripcio($data) {
       if (fct_db::activitat_cicle_duplicada(
               $this->cicle->id, addslashes($data['descripcio']))) {
            return array('descripcio' => fct_string('activitat_duplicada'));
        }
        return true;
    }

    function configurar() {
        $this->configurar_accio(array('afegir', 'cancellar'), 'afegir');
        parent::configurar(required_param('id', PARAM_INT));
        $this->url = fct_url::afegir_activitat_cicle($this->cicle->id);
        $this->pestanya = 'afegir_activitat';
    }

    function processar_afegir() {
        $form = new fct_form_activitat($this);
        $data = $form->get_data();
        if ($data) {
            $id = fct_db::afegir_activitat_cicle($this->cicle->id,
                                                 $data->descripcio);
            if ($id) {
                $this->registrar('add activitat_cicle',
                                 fct_url::cicle($this->cicle->id),
                                 $data->descripcio);
            } else {
                $this->error('afegir_activitat');
            }
            redirect(fct_url::cicle($this->cicle->id));
        }
        $this->mostrar_capcalera();
        $form->display();
        $this->mostrar_peu();
    }

    function processar_cancellar() {
        redirect(fct_url::cicle($this->cicle->id));
    }

}

