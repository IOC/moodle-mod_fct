<?php

require_once 'base_plantilles.php';

class fct_pagina_base_plantilla extends fct_pagina_base {

    var $plantilla;

    function configurar($plantilla_id) {
        $this->plantilla = fct_db::plantilla($plantilla_id);
        if (!$this->plantilla) {
            $this->error('recuperar_plantilla');
        }

        parent::configurar($this->plantilla->fct);
        $this->comprovar_permis($this->permis_admin);

        $this->afegir_navegacio(fct_string('plantilles_activitats'),
            fct_url::llista_plantilles($this->fct->id));
        $this->afegir_navegacio($this->plantilla->nom,
            fct_url::plantilla($this->plantilla->id));
    }

    function definir_pestanyes() {
        $this->pestanyes = array(array(
            new tabobject('activitats', fct_url::plantilla($this->plantilla->id), fct_string('activitats')),
            new tabobject('editar', fct_url::editar_plantilla($this->plantilla->id), fct_string('canvia_nom')),
            new tabobject('afegir_activitat', fct_url::afegir_activitat_plantilla($this->plantilla->id), fct_string('afegeix_activitat')),
            new tabobject('suprimir', fct_url::suprimir_plantilla($this->plantilla->id), fct_string('suprimeix')),
        ));
    }
}

