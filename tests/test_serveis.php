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

require_once 'PHPUnit/Framework.php';
require_once 'diposit.php';
require_once 'domini.php';
require_once 'moodle.php';

class fct_test_serveis extends PHPUnit_Framework_TestCase {

    var $diposit;
    var $serveis;

    function setup() {
        $this->diposit = $this->getMock('fct_diposit');
        $this->moodle = $this->getMock('fct_moodle');
        $this->serveis = new fct_serveis($this->diposit, $this->moodle);
    }

    function test_crear_quadern() {
        $this->serveis = $this->getMock('fct_serveis',
                                        array('ultim_quadern'),
                                        array($this->diposit, $this->moodle));

        $alumne = 3821;
        $cicle = 8427;

        $this->serveis->expects($this->once())->method('ultim_quadern')
            ->with($alumne, $cicle)->will($this->returnValue(false));

        $quadern = $this->serveis->crear_quadern($alumne, $cicle);

        $this->assertType('fct_quadern', $quadern);
        $this->assertEquals($alumne, $quadern->alumne);
        $this->assertEquals($cicle, $quadern->cicle);
        $this->assertEquals(1, count($quadern->convenis));
    }

    function test_crear_quadern__ultim() {
        $this->serveis = $this->getMock('fct_serveis',
                                        array('ultim_quadern'),
                                        array($this->diposit, $this->moodle));

        $ultim = new fct_quadern;
        $ultim->alumne = 3821;
        $ultim->cicle = 8427;
        $ultim->dades_alumne->adreca = 'adreca alumne';
        $ultim->hores_credit = 239;
        $ultim->exempcio = 25;
        $ultim->hores_anteriors = 34;
        $ultim->qualificacio_global->nota = 3;

        $this->serveis->expects($this->once())->method('ultim_quadern')
            ->with($ultim->alumne, $ultim->cicle)
            ->will($this->returnValue($ultim));


        $quadern = $this->serveis->crear_quadern($ultim->alumne,
                                                 $ultim->cicle);

        $this->assertType('fct_quadern', $quadern);
        $this->assertEquals(1, count($quadern->convenis));
        $this->assertEquals($ultim->alumne, $quadern->alumne);
        $this->assertEquals($ultim->cicle, $quadern->cicle);
        $this->assertEquals($ultim->dades_alumne, $quadern->dades_alumne);
        $this->assertEquals($ultim->hores_credit, $quadern->hores_credit);
        $this->assertEquals($ultim->exempcio, $quadern->exempcio);
        $this->assertEquals($ultim->hores_anteriors,
                            $quadern->hores_anteriors);
        $this->assertEquals($ultim->qualificacio_global,
                            $quadern->qualificacio_global);
    }

    function test_hores_realitzades_quadern() {
        $quadern = new fct_quadern;
        $quadern->id = 3394;
        $quinzena1 = new fct_quinzena;
        $quinzena1->hores = 23;
        $quinzena2 = new fct_quinzena;
        $quinzena2->hores = 31;

        $this->diposit->expects($this->once())
            ->method('quinzenes')->with($quadern->id)
            ->will($this->returnValue(array($quinzena1, $quinzena2)));

        $hores = $this->serveis->hores_realitzades_quadern($quadern);

        $this->assertEquals($quinzena1->hores + $quinzena2->hores, $hores);
    }

    function test_maxim_hores_quinzena() {
        $conveni = new fct_conveni;
        $conveni->data_inici = 0;
        $conveni->data_final = mktime(0, 0, 0, 1, 15, 2010);
        foreach (array('dilluns', 'dimarts', 'dimecres', 'dijous') as $dia) {
            $conveni->afegir_franja_horari(new fct_franja_horari($dia, 9, 13));
        }
        $quadern = new fct_quadern;
        $quadern->afegir_conveni($conveni);

        $hores = $this->serveis->maxim_hores_quinzena($quadern, 2010, 0, array(4, 5, 6));

        $this->assertEquals(12, $hores);
    }


    function test_registrar_avis__existent() {
        $quadern = new fct_quadern;
        $quadern->id = 2834;
        $quadern->alumne = 8921;

        $avis = new fct_avis;
        $avis->id = 4241;
        $avis->tipus = 'tipus_avis';
        $avis->quadern = $quadern->id;
        $avis->data = 29382;
        $avis->quinzena = 2374;

        $nou_avis = clone($avis);
        $nou_avis->data = 96241;

        $this->moodle->expects($this->any())->method('time')
            ->will($this->returnValue($nou_avis->data));

        $this->diposit->expects($this->any())
            ->method('avisos_quadern')->with($quadern->id)
            ->will($this->returnValue(array($avis)));

        $this->diposit->expects($this->once())
            ->method('afegir_avis')->with($nou_avis);

        $this->serveis->registrar_avis($quadern, $avis->tipus, $avis->quinzena);
    }

    function test_registrar_avis__inexistent() {
        $quadern = new fct_quadern;
        $quadern->id = 2834;
        $quadern->alumne = 8921;

        $avis = new fct_avis;
        $avis->tipus = 'tipus_avis';
        $avis->quadern = $quadern->id;
        $avis->data = 29382;
        $avis->quinzena = 2374;

        $this->moodle->expects($this->any())->method('time')
            ->will($this->returnValue($avis->data));

        $this->diposit->expects($this->any())
            ->method('avisos_quadern')->with($quadern->id)
            ->will($this->returnValue(array(new fct_avis)));

        $this->diposit->expects($this->once())
            ->method('afegir_avis')->with($avis);

        $this->serveis->registrar_avis($quadern, $avis->tipus, $avis->quinzena);
    }

    function test_resum_hores_fct() {
        $this->serveis = $this->getMock('fct_serveis',
                                        array('hores_realitzades_quadern'),
                                        array($this->diposit, $this->moodle));

        $quadern1 = new fct_quadern;
        $quadern1->id = 3840;
        $quadern1->alumne = 2842;
        $quadern1->cicle = 4831;
        $quadern1->afegir_conveni(new fct_conveni);

        $especificacio = new fct_especificacio_quaderns;
        $especificacio->alumne = $quadern1->alumne;
        $especificacio->cicle = $quadern1->cicle;
        $especificacio->data_final_max = $quadern1->data_final();

        $quadern2 = new fct_quadern;
        $quadern2->id = 9038;

        $quaderns = array($quadern1, $quadern2);
        $hores_quadern = array(65, 103);

        $this->diposit->expects($this->once())
            ->method('quaderns')->with($especificacio)
            ->will($this->returnValue($quaderns));

        $hores_quaderns = 0;
        foreach ($quaderns as $index => $quadern) {
            $hores_quaderns += $hores_quadern[$index];
            $this->serveis->expects($this->at($index))
                ->method('hores_realitzades_quadern')->with($quadern)
                ->will($this->returnValue($hores_quadern[$index]));
        }

        $resum = new fct_resum_hores_fct($quadern1->hores_credit,
                                         $quadern1->hores_anteriors,
                                         $quadern1->exempcio,
                                         $hores_quaderns);

        $resultat = $this->serveis->resum_hores_fct($quadern1);

        $this->assertEquals($resum, $resultat);
    }

    function test_suprimir_fct() {
        $this->serveis = $this->getMock('fct_serveis',
                                        array('suprimir_quadern'),
                                        array($this->diposit, $this->moodle));

        $fct = new fct;
        $fct->id = 3848;
        $cicles = array(1432, 3623, 5242);
        $quaderns = array(4382, 1240, 0874);

        $index_d = 0;
        $index_s = 0;

        $especificacio = new fct_especificacio_quaderns;
        $especificacio->fct = $fct->id;

        $this->diposit->expects($this->at($index_d++))
            ->method('quaderns')->with($especificacio)
            ->will($this->returnValue($quaderns));
        foreach ($quaderns as $quadern) {
            $this->serveis->expects($this->at($index_s++))
                ->method('suprimir_quadern')->with($quadern);
        }

        $index = 0;

        $this->diposit->expects($this->at($index_d++))
            ->method('cicles')->with($fct->id)
            ->will($this->returnValue($cicles));
        foreach ($cicles as $cicle) {
            $this->diposit->expects($this->at($index_d++))
                ->method('suprimir_cicle')->with($cicle);
        }

        $this->diposit->expects($this->once())
            ->method('suprimir_fct')->with($fct);

        $this->serveis->suprimir_fct($fct);
    }

    function test_suprimir_quadern() {
        $quadern = new fct_quadern;
        $quadern->id = 2849;
        $activitats = array(1232, 3123, 4242);
        $quinzenes = array(3492, 1840, 0374);
        $avisos = array(2932, 0548, 2937);
        $cicle = new fct_cicle;
        $cicle->fct = 5971;
        $fct = new fct;
        $fct->course = 8914;

        $index = 0;

        $this->diposit->expects($this->at($index++))
            ->method('avisos_quadern')->with($quadern->id)
            ->will($this->returnValue($avisos));
        foreach ($avisos as $avis) {
            $this->diposit->expects($this->at($index++))
                ->method('suprimir_avis')->with($avis);
        }

        $this->diposit->expects($this->at($index++))
            ->method('quinzenes')->with($quadern->id)
            ->will($this->returnValue($quinzenes));
        foreach ($quinzenes as $quinzena) {
            $this->diposit->expects($this->at($index++))
                ->method('suprimir_quinzena')->with($quinzena);
        }

        $this->diposit->expects($this->at($index++))
            ->method('activitats')->with($quadern->id)
            ->will($this->returnValue($activitats));
        foreach ($activitats as $activitat) {
            $this->diposit->expects($this->at($index++))
                ->method('suprimir_activitat')->with($activitat);
        }

        $this->diposit->expects($this->at($index++))
            ->method('cicle')->with($quadern->cicle)
            ->will($this->returnValue($cicle));
        $this->diposit->expects($this->at($index++))
            ->method('fct')->with($cicle->fct)
            ->will($this->returnValue($fct));
        $this->moodle->expects($this->once())
            ->method('delete_dir')->with($fct->course, "quadern-{$quadern->id}");

        $this->diposit->expects($this->at($index++))
            ->method('suprimir_quadern')->with($quadern);

        $this->serveis->suprimir_quadern($quadern);
    }

    function test_suprimir_quinzena() {
        $quinzena = new fct_quinzena;
        $quinzena->id = 2849;
        $avis1 = new fct_avis;
        $avis1->id = 5124;
        $avis1->quinzena = $quinzena->id;
        $avis2 = new fct_avis;
        $avis2->id = 8824;
        $avis2->quinzena = 9268;

        $this->diposit->expects($this->any())
            ->method('avisos_quadern')->with($quinzena->quadern)
            ->will($this->returnValue(array($avis1, $avis2)));

        $this->diposit->expects($this->once())
            ->method('suprimir_avis')->with($avis1);

        $this->diposit->expects($this->once())
            ->method('suprimir_quinzena')->with($quinzena);

        $this->serveis->suprimir_quinzena($quinzena);
    }

    function test_ultim_quadern() {
        $quadern1 = new fct_quadern;
        $quadern->id = 3837;
        $quadern2 = new fct_quadern;
        $quadern2->id = 3381;

        $especificacio = new fct_especificacio_quaderns;
        $especificacio->alumne = 2931;
        $especificacio->cicle = 4129;

        $this->diposit->expects($this->once())
            ->method('quaderns')->with($especificacio, 'data_final')
            ->will($this->returnValue(array($quadern1, $quadern2)));

        $ultim_quadern = $this->serveis->ultim_quadern($especificacio->alumne,
                                                       $especificacio->cicle);

        $this->assertEquals($quadern2, $ultim_quadern);
    }

}
