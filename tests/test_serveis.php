<?php

require_once 'PHPUnit/Framework.php';
require_once 'mod/fct/diposit.php';
require_once 'mod/fct/domini.php';

class fct_test_serveis extends PHPUnit_Framework_TestCase {

    var $diposit;
    var $serveis;

    function setup() {
        $this->diposit = $this->getMock('fct_diposit');
        $this->serveis = new fct_serveis($this->diposit);
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

    function test_resum_hores_fct() {
        $this->serveis = $this->getMock('fct_serveis',
                                        array('hores_realitzades_quadern'),
                                        array($this->diposit));

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
                                        array($this->diposit));

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

        $index = 0;

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
            ->method('suprimir_quadern')->with($quadern);

        $this->serveis->suprimir_quadern($quadern);
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
