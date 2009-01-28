<?php

require_once 'base_plantilla.php';
require_once 'form_plantilla.php';

class fct_pagina_suprimir_plantilla extends fct_pagina_base_plantilla {

    function configurar() {
        parent::configurar(required_param('plantilla', PARAM_INT));
        $this->configurar_accio(array('suprimir', 'confirmar'), 'suprimir');
        $this->url = fct_url::suprimir_plantilla($this->plantilla->id);
        $this->pestanya = 'suprimir';
    }

    function processar_confirmar() {
        $this->comprovar_sessio();
        $ok = fct_db::suprimir_plantilla($this->plantilla->id);
        if ($ok) {
            $this->registrar('delete plantilla',
                fct_url::llista_plantilles($this->fct->id),
                $this->plantilla->nom);
        } else {
            $this->error('suprimir_cicle_formatiu');
        }
        redirect(fct_url::llista_plantilles($this->fct->id));
    }

    function processar_suprimir() {
        $this->mostrar_capcalera();

        notice_yesno(fct_string('segur_suprimir_cicle_formatiu', $this->plantilla->nom),
            $this->url, fct_url::plantilla($this->plantilla->id),
            array('confirmar' => 1, 'sesskey' => sesskey()));

        $this->mostrar_peu();
    }

}

