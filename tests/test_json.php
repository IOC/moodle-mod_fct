<?php

require_once 'PHPUnit/Framework.php';
require_once 'json.php';
require_once 'domini.php';

class fct_test_json extends PHPUnit_Framework_TestCase {

    function test_activitat() {
        $activitat = new fct_activitat;
        $activitat->id = 4832;
        $activitat->quadern = 4783;
        $activitat->descripcio = 'descripcio';
        $activitat->nota = 3;

        $json = fct_json::serialitzar_activitat($activitat);
        $this->assertType('string', $json);
        $resultat = fct_json::deserialitzar_activitat($json);
        $this->assertEquals($activitat, $resultat);
    }

    function test_cicle() {
        $cicle = new fct_cicle;
        $cicle->id = 3849;
        $cicle->fct = 8372;
        $cicle->nom = 'nom cicle';
        $cicle->activitats = array('Activitat 1', 'Activitat 2');

        $json = fct_json::serialitzar_cicle($cicle);
        $this->assertType('string', $json);
        $resultat = fct_json::deserialitzar_cicle($json);
        $this->assertEquals($cicle, $resultat);
    }

    function test_fct() {
        $fct = new fct;
        $fct->id = 3819;
        $fct->course = 8472;
        $fct->name = 'fct name';
        $fct->intro = 'fct intro';
        $fct->timecreated = 12334;
        $fct->timemodified = 38423;
        $fct->frases_centre = 'frases centre';
        $fct->frases_empresa = 'frases empresa';
        $fct->centre = new fct_centre;
        $fct->centre->nom = 'nom centre';

        $json = fct_json::serialitzar_fct($fct);
        $this->assertType('string', $json);
        $resultat = fct_json::deserialitzar_fct($json);
        $this->assertEquals($fct, $resultat);
    }

    function test_quadern() {
        $quadern = new fct_quadern;
        $quadern->id = 3849;
        $quadern->dades_alumne->adreca = "adreça alumne";
        $quadern->empresa->nom = "nom empresa";
        $conveni = new fct_conveni;
        $conveni->horari->dilluns = "horari";
        $quadern->afegir_conveni($conveni);
        $quadern->valoracio_parcial[3] = 4;
        $quadern->qualificacio->apte = 1;
        $quadern->qualificacio_global->apte = 2;

        $json = fct_json::serialitzar_quadern($quadern);
        $this->assertType('string', $json);
        $resultat = fct_json::deserialitzar_quadern($json);
        $this->assertEquals($quadern, $resultat);
    }

    function test_quinzena() {
        $quinzena = new fct_quinzena;
        $quinzena->id = 9372;
        $quinzena->dies = array(12, 13, 14, 23, 24);
        $quinzena->valoracions = "valoracions";

        $json = fct_json::serialitzar_quinzena($quinzena);
        $this->assertType('string', $json);
        $resultat = fct_json::deserialitzar_quinzena($json);
        $this->assertEquals($quinzena, $resultat);
    }

}

