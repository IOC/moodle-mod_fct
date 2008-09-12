<?php

require_once 'base_activitat_pla.php';

class fct_pagina_suprimir_activitat_pla extends fct_pagina_base_activitat_pla  {

    function configurar() {
        $this->configurar_accio(array('suprimir', 'confirmar'), 'suprimir');
        parent::configurar( required_param('activitat', PARAM_INT));
        $this->url = fct_url::suprimir_activitat_pla($this->activitat->id);
        $this->comprovar_permis($this->permis_editar);
    }

    function processar_confirmar() {
        $this->comprovar_sessio();
        $ok = fct_db::suprimir_activitat_pla($this->activitat->id);
        if ($ok) {
            $this->registrar('delete activitat_pla',
                fct_url::pla_activitats($this->quadern->id),
                $this->activitat->descripcio);
        } else {
           $this->error('suprimir_activitat');
        }
        redirect(fct_url::pla_activitats($this->quadern->id));
    }

    function processar_suprimir() {
        $this->mostrar_capcalera();
        notice_yesno(fct_string('segur_suprimir_activitat')
            . '</p><p>' . $this->activitat->descripcio,
            $this->url, fct_url::pla_activitats($this->quadern->id),
            array('confirmar' => 1, 'sesskey' => sesskey()));
        $this->mostrar_peu();
    }

}

