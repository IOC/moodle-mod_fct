<?php

require_once 'base_plantilla.php';
require_once 'form_plantilla.php';

class fct_pagina_editar_plantilla extends fct_pagina_base_plantilla {

    function comprovar_nom($data) {
        if (fct_db::plantilla_duplicada($this->fct->id, addslashes($data['nom']),
                $this->plantilla->id)) {
            return array('nom' => fct_string('plantilla_duplicada'));
        }

        return true;
    }

    function configurar() {
        parent::configurar(required_param('plantilla', PARAM_INT));
        $this->configurar_accio(array('editar', 'cancellar'), 'editar');
        $this->url = fct_url::editar_plantilla($this->plantilla->id);
        $this->pestanya = 'editar';
    }

    function processar_editar() {
        $form = new fct_form_plantilla($this);

        $data = $form->get_data();
        if ($data) {
            $plantilla = (object) array('id' => $this->plantilla->id, 'nom' => $data->nom);
            $ok = fct_db::actualitzar_plantilla($plantilla);
            if ($ok) {
                $this->registrar('update plantilla',
                    fct_url::plantilla($this->plantilla->id), $data->nom);
            } else {
                $this->error('desar_plantilla');
            }
            redirect(fct_url::plantilla($this->plantilla->id));
        }

        $this->mostrar_capcalera();
        $form->set_data($this->plantilla);
        $form->display();
        $this->mostrar_peu();
     }

    function processar_cancellar() {
        redirect(fct_url::plantilla($this->plantilla->id));
     }
}

