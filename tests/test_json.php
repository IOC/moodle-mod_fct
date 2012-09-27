<?php
/* Quadern virtual d'FCT

   Copyright © 2009,2010  Institut Obert de Catalunya

   This program is free software: you can redistribute it and/or modify
   it under the terms of the GNU General Public License as published by
   the Free Software Foundation, either version 3 of the License, or
   (at your option) any later version.

   This program is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

   You should have received a copy of the GNU General Public License
   along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

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
        $this->assertInternalType('string', $json);
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
        $this->assertInternalType('string', $json);
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
        $this->assertInternalType('string', $json);
        $resultat = fct_json::deserialitzar_fct($json);
        $this->assertEquals($fct, $resultat);
    }

    function test_quadern() {
        $quadern = new fct_quadern;
        $quadern->id = 3849;
        $quadern->dades_alumne->adreca = "adreça alumne";
        $quadern->empresa->nom = "nom empresa";
        $conveni = new fct_conveni;
        $franja = new fct_franja_horari("dilluns", 9.5, 13.5);
        $conveni->afegir_franja_horari($franja);
        $quadern->afegir_conveni($conveni);
        $quadern->valoracio_parcial[3] = 4;
        $quadern->qualificacio->apte = 1;
        $quadern->qualificacio_global->apte = 2;

        $json = fct_json::serialitzar_quadern($quadern);
        $this->assertInternalType('string', $json);
        $resultat = fct_json::deserialitzar_quadern($json);
        $this->assertEquals($quadern, $resultat);
    }

    function test_quinzena() {
        $quinzena = new fct_quinzena;
        $quinzena->id = 9372;
        $quinzena->dies = array(12, 13, 14, 23, 24);
        $quinzena->valoracions = "valoracions";

        $json = fct_json::serialitzar_quinzena($quinzena);
        $this->assertInternalType('string', $json);
        $resultat = fct_json::deserialitzar_quinzena($json);
        $this->assertEquals($quinzena, $resultat);
    }

}

