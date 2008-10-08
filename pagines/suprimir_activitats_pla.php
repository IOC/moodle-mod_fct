<?php

require_once 'base_activitat_pla.php';

class fct_pagina_suprimir_activitats_pla extends fct_pagina_base_pla_activitats  {

    function configurar() {
        $this->configurar_accio(array('suprimir', 'confirmar'), 'suprimir');
        parent::configurar( required_param('quadern', PARAM_INT));
        $this->url = fct_url::suprimir_activitats_pla($this->quadern->id);
        $this->comprovar_permis($this->permis_editar);
    }

    function processar_confirmar() {
        $this->comprovar_sessio();
        $ok = fct_db::suprimir_activitats_pla($this->quadern->id);
        if ($ok) {
            $this->registrar('delete activitats_pla',
                fct_url::pla_activitats($this->quadern->id));
        } else {
           $this->error('suprimir_activitats');
        }
        redirect(fct_url::pla_activitats($this->quadern->id));
    }

    function processar_suprimir() {
        $this->mostrar_capcalera();
        notice_yesno(fct_string('segur_suprimir_activitats'),
            $this->url, fct_url::pla_activitats($this->quadern->id),
            array('confirmar' => 1, 'sesskey' => sesskey()));
        $this->mostrar_peu();
    }

}

