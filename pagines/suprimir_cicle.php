<?php

require_once 'base_cicle.php';

class fct_pagina_suprimir_cicle extends fct_pagina_base_cicle {

    function configurar() {
        parent::configurar(required_param('id', PARAM_INT));
        $this->configurar_accio(array('suprimir', 'confirmar'), 'suprimir');
        $this->url = fct_url::suprimir_cicle($this->cicle->id);
        $this->pestanya = 'suprimir';
    }

    function processar_confirmar() {
        $this->comprovar_sessio();
        $ok = fct_db::suprimir_plantilla($this->cicle->id);
        if ($ok) {
            $this->registrar('delete cicle',
                fct_url::llista_cicles($this->fct->id),
                $this->cicle->nom);
        } else {
            $this->error('suprimir_cicle');
        }
        redirect(fct_url::llista_cicles($this->fct->id));
    }

    function processar_suprimir() {
        $this->mostrar_capcalera();

        notice_yesno(fct_string('segur_suprimir_cicle_formatiu', $this->cicle->nom),
            $this->url, fct_url::cicle($this->cicle->id),
            array('confirmar' => 1, 'sesskey' => sesskey()));

        $this->mostrar_peu();
    }

}
