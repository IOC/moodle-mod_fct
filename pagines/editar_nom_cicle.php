<?php

require_once 'base_cicle.php';
require_once 'form_cicle.php';

class fct_pagina_editar_nom_cicle extends fct_pagina_base_cicle {

    function comprovar_nom($data) {
        if (fct_db::plantilla_duplicada($this->fct->id, addslashes($data['nom']),
                                        $this->cicle->id)) {
            return array('nom' => fct_string('cicle_formatiu_duplicat'));
        }

        return true;
    }

    function configurar() {
        parent::configurar(required_param('id', PARAM_INT));
        $this->configurar_accio(array('editar', 'cancellar'), 'editar');
        $this->url = fct_url::editar_nom_cicle($this->cicle->id);
        $this->pestanya = 'editar_nom';
    }

    function processar_editar() {
        $form = new fct_form_cicle($this);

        $data = $form->get_data();
        if ($data) {
            $cicle = (object) array('id' => $this->cicle->id, 'nom' => $data->nom);
            $ok = fct_db::actualitzar_plantilla($cicle);
            if ($ok) {
                $this->registrar('update cicle',
                    fct_url::cicle($this->cicle->id), $data->nom);
            } else {
                $this->error('desar_cicle');
            }
            redirect(fct_url::cicle($this->cicle->id));
        }

        $this->mostrar_capcalera();
        $form->set_data($this->cicle);
        $form->display();
        $this->mostrar_peu();
     }

    function processar_cancellar() {
        redirect(fct_url::cicle($this->cicle->id));
     }
}

