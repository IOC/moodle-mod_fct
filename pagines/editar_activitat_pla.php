<?php

require_once 'base_activitat_pla.php';
require_once 'form_activitat.php';

class fct_pagina_editar_activitat_pla extends fct_pagina_base_activitat_pla  {

    function comprovar_descripcio($data) {
       if (fct_db::activitat_pla_duplicada($this->pla->id,
                addslashes($data['descripcio']), $this->activitat->id)) {
            return array('descripcio' => fct_string('activitat_duplicada'));
        }
        return true;
    }

    function configurar() {
        $this->configurar_accio(array('editar', 'cancellar'), 'editar');
        parent::configurar(required_param('activitat', PARAM_INT));
        $this->comprovar_permis($this->permis_editar);
        $this->url = fct_url::editar_activitat_pla($this->activitat->id);
    }

    function processar_cancellar() {
        redirect(fct_url::pla_activitats($this->quadern->id));
    }

    function processar_editar() {
        $this->comprovar_estat_obert();
        $form = new fct_form_activitat($this);
        $data = $form->get_data();
        if ($data) {
            $activitat = (object) array(
                'id' => $this->activitat->id,
                'descripcio' => $data->descripcio,
            );
            $ok = fct_db::actualitzar_activitat_pla($activitat);
            if ($ok) {
                $this->registrar('update activitat_pla',
                    fct_url::pla_activitats($this->quadern->id),
                    $data->descripcio);
            } else {
                $this->error('desar_activitat');
            }
            redirect(fct_url::pla_activitats($this->quadern->id));
        }
        $this->mostrar_capcalera();
        $form->set_data($this->activitat);
        $form->display();
        $this->mostrar_peu();
    }

}

