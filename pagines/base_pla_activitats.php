<?php

require_once 'base_quadern.php';

class fct_pagina_base_pla_activitats extends fct_pagina_base_quadern {

    var $pla;

    function configurar($quadern_id, $pla_id=false) {
        if ($pla_id) {
            $this->pla = fct_db::pla_actvitats($pla_id);
            if (!$this->pla) {
                $this->error('recuperar_pla_activitats');
            }
            parent::configurar($this->pla->quadern);
        } else if ($quadern_id) {
            $this->pla = fct_db::pla_actvitats_quadern($quadern_id);
            if (!$this->pla) {
                $this->error('recuperar_pla_activitats');
            }
            parent::configurar($quadern_id);
        } else {
            $this->error('pla_activitats_no_indicat');
        }

        $this->afegir_navegacio(fct_string('pla_activitats'),
            fct_url::pla_activitats($this->quadern->id));

        $this->pestanya = 'pla_activitats';
    }

    function definir_pestanyes() {
        parent::definir_pestanyes();
        $pestanyes = array();
        if (($this->permis_tutor_centre and $this->quadern->estat) or $this->permis_admin) {
            $pestanyes[] = new tabobject('activitats_pla',
                fct_url::pla_activitats($this->quadern->id), fct_string('activitats'));
            $pestanyes[] = new tabobject('afegir_activitat_pla',
                fct_url::afegir_activitat_pla($this->quadern->id), fct_string('afegeix_activitat'));
            $pestanyes[] = new tabobject('afegir_activitats_plantilla_pla',
                fct_url::afegir_activitats_plantilla_pla($this->quadern->id), fct_string('importa_plantilla'));
            $pestanyes[] = new tabobject('suprimeix_activitats_pla',
                fct_url::suprimir_activitats_pla($this->quadern->id), fct_string('suprimeix_activitats'));
        }
        $this->pestanyes[] = $pestanyes;
    }
}

