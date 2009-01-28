<?php

require_once 'base_activitat_cicle.php';
require_once 'form_activitat.php';

class fct_pagina_editar_activitat_cicle extends fct_pagina_base_activitat_cicle  {

    function comprovar_descripcio($data) {
       if (fct_db::activitat_cicle_duplicada(
               $this->cicle->id, addslashes($data['descripcio']),
               $this->cicle->id)) {
           return array('descripcio' => fct_string('activitat_duplicada'));
       }
        return true;
    }

    function configurar() {
        $this->configurar_accio(array('editar', 'cancellar'), 'editar');
        parent::configurar(required_param('id', PARAM_INT));
        $this->url = fct_url::editar_activitat_cicle($this->activitat->id);
        $this->pestanya = 'activitats';
    }

    function processar_cancellar() {
        redirect(fct_url::cicle($this->cicle->id));
    }

    function processar_editar() {
        $form = new fct_form_activitat($this);
        $data = $form->get_data();
        if ($data) {
            $activitat = (object) array(
                'id' => $this->activitat->id,
                'descripcio' => $data->descripcio));
            $ok = fct_db::actualitzar_activitat_cicle($activitat);
            if ($ok) {
                $this->registrar('update activitat_cicle',
                                 fct_url::cicle($this->cicle->id),
                                 $data->descripcio);
            } else {
                $this->error('desar_activitat');
            }
            redirect(fct_url::cicle($this->cicle->id));
        }
        $this->mostrar_capcalera();
        $form->set_data($this->activitat);
        $form->display();
        $this->mostrar_peu();
    }

}

