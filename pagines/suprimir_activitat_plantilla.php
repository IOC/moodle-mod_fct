<?php

require_once 'base_activitat_plantilla.php';

class fct_pagina_suprimir_activitat_plantilla extends fct_pagina_base_activitat_plantilla  {

    function configurar() {
        $this->configurar_accio(array('suprimir', 'confirmar'), 'suprimir');
        parent::configurar( required_param('activitat', PARAM_INT));
        $this->url = fct_url::suprimir_activitat_plantilla($this->activitat->id);
        $this->pestanya = 'suprimir';
    }

    function processar_confirmar() {
        $this->comprovar_sessio();
        $ok = fct_db::suprimir_activitat_plantilla($this->activitat->id);
        if ($ok) {
           $this->registrar('delete activitat_plantilla',
                fct_url::plantilla($this->plantilla->id),
                $this->activitat->plantilla);
        } else {
            $this->error('suprimir_activitat');
        }
        redirect(fct_url::plantilla($this->plantilla->id));
    }

    function processar_suprimir() {
        $this->mostrar_capcalera();
        notice_yesno(fct_string('segur_suprimir_activitat')
            . '</p><p>' . $this->activitat->descripcio,
            $this->url, fct_url::plantilla($this->plantilla->id),
            array('confirmar' => 1, 'sesskey' => sesskey()));
        $this->mostrar_peu();
    }

}

