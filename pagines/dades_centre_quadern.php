<?php

fct_require('pagines/base_dades_quadern.php',
            'pagines/form_dades_centre.php');

class fct_pagina_dades_centre_quadern extends fct_pagina_base_dades_quadern {

    var $centre;

    function configurar() {
        parent::configurar(required_param('quadern', PARAM_INT));
        $this->centre = fct_db::dades_centre($this->fct->id);
        if (!$this->centre) {
           $this->error('recuperar_centre_docent');
        }
        $this->url = fct_url::dades_centre_quadern($this->quadern->id);
        $this->subpestanya = 'dades_centre_quadern';
    }

    function processar() {
        $this->mostrar_capcalera();
        $form = new fct_form_dades_centre($this);
        $form->set_data($this->centre);
        $form->display();
        $this->mostrar_peu();
        $this->registrar('view dades_centre_quadern');
    }
}

