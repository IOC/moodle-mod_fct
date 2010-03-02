<?php

fct_require('pagines/base_dades_quadern.php');

class fct_pagina_avisos_quadern extends fct_pagina_base_quadern {

    function configurar() {
        parent::configurar(required_param('quadern', PARAM_INT));
        $this->configurar_accio(array('veure', 'suprimir'), 'veure');

        //$this->comprovar_permis($this->permis_tutor_centre);

        $this->url = fct_url::avisos_quadern($this->quadern->id);
        $this->pestanya = 'avisos';
    }

    function processar_veure() {
        $this->mostrar_capcalera();
        print_object($this->diposit->avisos_quadern($this->quadern->id));
        $this->mostrar_peu();
        $this->registrar('view avisos_quadern');
    }
}

