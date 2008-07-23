<?php

require_once 'base_valoracio.php';
require_once 'form_valoracio_actituds.php';

class fct_pagina_valoracio_actituds extends fct_pagina_base_valoracio {

    var $final;
    var $titol;

    function configurar() {
        parent::configurar(required_param('quadern', PARAM_INT));
        $this->final = required_param('final', PARAM_BOOL);
        $this->url = fct_url::valoracio_actituds($this->quadern->id, $this->final);
        $this->titol = $this->final ? fct_string('valoracio_final_actituds')
            : fct_string('valoracio_parcial_actituds');
        $this->afegir_navegacio($this->titol, $this->url);
    }

    function configurar_formulari() {
        $this->form = new fct_form_valoracio_actituds($this);
        return $this->form->get_data();
    }

    function definir_pestanyes() {
        parent::definir_pestanyes();
        $this->pestanyes[] = array(
            new tabobject('valoracio_actituds_parcial',
                fct_url::valoracio_actituds($this->quadern->id, 0),
                "Valoració parcial"),
            new tabobject('valoracio_actituds_final',
                fct_url::valoracio_actituds($this->quadern->id, 1),
                "Valoració ffinal"),
        );
    }

    function mostrar() {
        $this->mostrar_capcalera();
        $this->form->set_data_llista('actitud',
            fct_db::valoracio_actituds($this->quadern->id, $this->final));
        $this->form->display();
        $this->mostrar_peu();
    }

    function processar_cancellar() {
        redirect($this->url);
    }

    function processar_desar() {
        $data = $this->configurar_formulari();
        if ($data) {
            $notes = $this->form->get_data_llista('actitud');
            $ok = fct_db::actualitzar_valoracio_actituds($this->quadern->id, $this->final, $notes);
            if ($ok) {
                $this->registrar('update valoracio_actituds', null,
                    $this->final ? 'final' : 'parcial');
            } else {
                $this->error('desar_valoracio_actituds');
            }
            redirect($this->url);
        }
        $this->mostrar();
    }

    function processar_editar() {
        $this->configurar_formulari();
        $this->mostrar();
    }

    function processar_veure() {
        $this->configurar_formulari();
        $this->mostrar();
        $this->registrar('view valoracio_acittuds', null,
            $this->final ? 'final' : 'parcial');
    }

}

