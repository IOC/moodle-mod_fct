<?php

require_once 'base_pla_activitats.php';
require_once 'form_activitat.php';

class fct_pagina_afegir_activitat_pla extends fct_pagina_base_pla_activitats {

    function comprovar_descripcio($data) {
       if (fct_db::activitat_pla_duplicada($this->pla->id,
                addslashes($data['descripcio']))) {
            return array('descripcio' => fct_string('activitat_duplicada'));
        }
        return true;
    }

    function configurar() {
        parent::configurar(required_param('quadern', PARAM_INT));
        if (!$this->permis_tutor_centre and !$this->permis_admin) {
            $this->error('permis_pagina');
        }
        $this->configurar_accio(array('afegir', 'cancellar'), 'afegir');
        $this->url = fct_url::afegir_activitat_pla($this->quadern->id);
        $this->comprovar_estat_obert();
    }

    function processar_afegir() {
        $form = new fct_form_activitat($this);
        $data = $form->get_data();
        if ($data) {
            $id = fct_db::afegir_activitat_pla($this->pla->id, $data->descripcio);
            if ($id) {
                $this->registrar('add activitat_pla',
                    fct_url::pla_activitats($this->quadern), $data->descripcio);
            } else {
                $this->error('afegir_activitat_pla');
            }
            redirect(fct_url::pla_activitats($this->quadern->id));
        }
        $this->mostrar_capcalera();
        $form->set_data(array('pla' => $this->pla->id));
        $form->display();
        $this->mostrar_peu();
    }

    function processar_cancellar() {
        redirect(fct_url::pla_activitats($this->quadern->id));
    }

}

