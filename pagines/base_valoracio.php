<?php

fct_require('pagines/base_quadern.php');

class fct_pagina_base_valoracio extends fct_pagina_base_quadern {

    var $permis_editar;

    function configurar() {
        parent::configurar(required_param('quadern', PARAM_INT));
        $this->configurar_accio(array('veure', 'editar', 'desar', 'cancellar'), 'veure');
        $this->pestanya = 'valoracio';
        $this->permis_editar = ($this->permis_admin or
            ($this->quadern->estat and ($this->permis_tutor_centre
            or $this->permis_tutor_empresa)));
        if ($this->accio != 'veure') {
            $this->comprovar_permis($this->permis_editar);
        }
    }

    function definir_pestanyes() {
        parent::definir_pestanyes();
        $this->pestanyes[] = array(
            new tabobject('valoracio_actituds_parcial',
                fct_url::valoracio_actituds($this->quadern->id, 0),
                fct_string('valoracio_parcial_actituds')),
            new tabobject('valoracio_actituds_final',
                fct_url::valoracio_actituds($this->quadern->id, 1),
                fct_string('valoracio_final_actituds')),
            new tabobject('valoracio_activitats',
                fct_url::valoracio_activitats($this->quadern->id),
                fct_string('valoracio_activitats')),
            new tabobject('qualificacio_quadern',
                fct_url::qualificacio_quadern($this->quadern->id),
                fct_string('qualificacio_quadern')),
        );
    }

}

