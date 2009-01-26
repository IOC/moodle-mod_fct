<?php

class fct_url {

    function afegir_activitat_pla($quadern_id) {
        return self::url('afegir_activitat_pla', array('quadern' => $quadern_id));
    }

    function afegir_activitats_plantilla_pla($quadern_id) {
        return self::url('afegir_activitats_plantilla_pla', array('quadern' => $quadern_id));
    }

    function afegir_activitat_plantilla($plantilla_id) {
        return self::url('afegir_activitat_plantilla', array('plantilla' => $plantilla_id));
    }

    function afegir_plantilla($fct_id) {
        return self::url('afegir_plantilla', array('fct' => $fct_id));
    }

    function afegir_quadern($fct_id) {
        return self::url('afegir_quadern', array('fct' => $fct_id));
    }

    function afegir_quinzena($quadern_id) {
        return self::url('afegir_quinzena', array('quadern' => $quadern_id));
    }

    function afegir_tutor_empresa($fct_id) {
        return self::url('afegir_tutor_empresa', array('fct' => $fct_id));
    }

    function configurar_quadern($quadern_id) {
        return self::url('configurar_quadern', array('quadern' => $quadern_id));
    }

    function dades_alumne($quadern_id) {
        return self::url('dades_alumne', array('quadern' => $quadern_id));
    }

    function dades_centre($fct_id) {
        return self::url('dades_centre', array('fct' => $fct_id));
    }

    function dades_centre_concertat($quadern_id) {
        return self::url('dades_centre_concertat', array('quadern' => $quadern_id));
    }

    function dades_centre_quadern($quadern_id) {
        return self::url('dades_centre_quadern', array('quadern' => $quadern_id));
    }

    function dades_conveni($quadern_id) {
        return self::url('dades_conveni', array('quadern' => $quadern_id));
    }

    function dades_empresa($quadern_id) {
        return self::url('dades_empresa', array('quadern' => $quadern_id));
    }

    function dades_horari($quadern_id) {
        return self::url('dades_horari', array('quadern' => $quadern_id));
    }

    function dades_relatives($quadern_id) {
        return self::url('dades_relatives', array('quadern' => $quadern_id));
    }

    function editar_activitat_pla($activitat_id) {
        return self::url('editar_activitat_pla', array('activitat' => $activitat_id));
    }

    function editar_activitat_plantilla($activitat_id) {
        return self::url('editar_activitat_plantilla', array('activitat' => $activitat_id));
    }

    function llista_empreses($fct_id) {
        return self::url('llista_empreses', array('fct' => $fct_id));
    }

    function editar_plantilla($plantilla_id) {
        return self::url('editar_plantilla', array('plantilla' => $plantilla_id));
    }

    function llista_plantilles($fct_id) {
        return self::url('llista_plantilles', array('fct' => $fct_id));
    }

    function llista_quaderns($fct_id) {
        return self::url('llista_quaderns', array('fct' => $fct_id));
    }

    function pla_activitats($quadern_id) {
        return self::url('pla_activitats', array('quadern' => $quadern_id));
    }

    function plantilla($plantilla_id) {
        return self::url('plantilla', array('plantilla' => $plantilla_id));
    }

    function quadern($quadern_id) {
        return self::url('quadern', array('quadern' => $quadern_id));
    }

    function qualificacio_global($quadern_id) {
        return self::url('qualificacio_global',
            array('quadern' => $quadern_id));
    }

    function qualificacio_quadern($quadern_id) {
        return self::url('qualificacio_quadern',
            array('quadern' => $quadern_id));
    }

    function quinzena($quinzena_id) {
        return self::url('quinzena', array('quinzena' => $quinzena_id));
    }

    function resum_seguiment($quadern_id) {
        return self::url('resum_seguiment', array('quadern' => $quadern_id));
    }

    function seguiment($quadern_id) {
        return self::url('seguiment', array('quadern' => $quadern_id));
    }

    function seleccionar_plantilla($quadern_id) {
        return self::url('seleccionar_plantilla', array('quadern' => $quadern_id));
    }

    function suprimir_activitat_pla($activitat_id) {
        return self::url('suprimir_activitat_pla', array('activitat' => $activitat_id));
    }

    function suprimir_activitats_pla($quadern_id) {
        return self::url('suprimir_activitats_pla', array('quadern' => $quadern_id));
    }

    function suprimir_activitat_plantilla($activitat_id) {
        return self::url('suprimir_activitat_plantilla', array('activitat' => $activitat_id));
    }

    function suprimir_plantilla($plantilla_id) {
        return self::url('suprimir_plantilla', array('plantilla' => $plantilla_id));
    }

    function url($pagina, $params) {
        $url = "view.php?pagina=$pagina";
        foreach ($params as $nom => $valor) {
            $url .= "&$nom=$valor";
        }
        return $url;
    }

    function valoracio_activitats($quadern_id) {
        return self::url('valoracio_activitats', array('quadern' => $quadern_id));
    }

    function valoracio_actituds($quadern_id, $final) {
        return self::url('valoracio_actituds',
            array('quadern' => $quadern_id, 'final' => $final));
    }

}

