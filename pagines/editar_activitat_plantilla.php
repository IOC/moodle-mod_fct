<?php

require_once 'base_activitat_plantilla.php';
require_once 'form_activitat.php';

class fct_pagina_editar_activitat_plantilla extends fct_pagina_base_activitat_plantilla  {

    function comprovar_descripcio($data) {
       if (fct_db::activitat_plantilla_duplicada($this->plantilla->id,
                addslashes($data['descripcio']), $this->activitat->id)) {
            return array('descripcio' => fct_string('activitat_duplicada'));
        }
        return true;
    }

    function configurar() {
        $this->configurar_accio(array('editar', 'cancellar'), 'editar');
        parent::configurar(required_param('activitat', PARAM_INT));
        $this->url = fct_url::editar_activitat_plantilla($this->activitat->id);
        $this->pestanya = 'activitats';
    }

    function processar_cancellar() {
        redirect(fct_url::plantilla($this->plantilla->id));
    }

    function processar_editar() {
        $form = new fct_form_activitat($this);
        $data = $form->get_data();
        if ($data) {
            $activitat = (object) array(
                'id' => $this->activitat->id,
                'descripcio' => $data->descripcio);
            $ok = fct_db::actualitzar_activitat_plantilla($activitat);
            if ($ok) {
                $this->registrar('update activitat_plantilla',
                    fct_url::plantilla($this->plantilla->id),
                    $data->descripcio);
            } else {
                $this->error('desar_activitat');
            }
            redirect(fct_url::plantilla($this->plantilla->id));
        }
        $this->mostrar_capcalera();
        $form->set_data($this->activitat);
        $form->display();
        $this->mostrar_peu();
    }

}

