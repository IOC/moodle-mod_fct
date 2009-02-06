<?php

fct_require('pagines/base_cicles.php');

class fct_pagina_base_cicle extends fct_pagina_base {

    var $cicle;

    function configurar($id) {
        $this->cicle = fct_db::cicle($id);
        if (!$this->cicle) {
            $this->error('recuperar_cicle');
        }

        parent::configurar($this->cicle->fct);
        $this->comprovar_permis($this->permis_admin);

        $this->afegir_navegacio(fct_string('cicles_formatius'),
            fct_url::llista_cicles($this->fct->id));
        $this->afegir_navegacio($this->cicle->nom,
            fct_url::cicle($this->cicle->id));
    }

    function definir_pestanyes() {
        $this->pestanyes = array(array(
            new tabobject('activitats', fct_url::cicle($this->cicle->id), fct_string('activitats')),
            new tabobject('editar_nom', fct_url::editar_nom_cicle($this->cicle->id), fct_string('canvia_nom')),
            new tabobject('afegir_activitat', fct_url::afegir_activitat_cicle($this->cicle->id), fct_string('afegeix_activitat')),
            new tabobject('suprimir', fct_url::suprimir_cicle($this->cicle->id), fct_string('suprimeix')),
        ));
    }
}

