<?php

require_once 'PHPUnit/Framework.php';
require_once 'mod/fct/diposit.php';
require_once 'mod/fct/domini.php';
require_once 'mod/fct/moodle.php';

class fct_test_diposit extends PHPUnit_Framework_TestCase {

    var $activitat;
    var $cicle;
    var $diposit;
    var $fct;
    var $moodle;
    var $quadern;
    var $quinzena;
    var $record_activitat;
    var $record_centre;
    var $record_cicle;
    var $record_conveni;
    var $record_dades_alumne;
    var $record_dades_cnveni;
    var $record_dades_relatives;
    var $record_empresa;
    var $record_fct;
    var $record_horari;
    var $record_quadern;
    var $record_qualificacio_global;
    var $record_qualificacio_quadern;
    var $record_quinzena;
    var $record_user;
    var $records_activitat_cicle;
    var $records_activitat_quinzena;
    var $records_dia_quinzena;
    var $records_valoracio_actituds;
    var $usuari;

    function setup() {
        $this->moodle = $this->getMock('fct_moodle');
        $this->diposit = new fct_diposit($this->moodle);
        $this->setup_fct();
        $this->setup_usuari();
        $this->setup_cicle();
        $this->setup_quadern();
        $this->setup_activitat();
        $this->setup_quinzena();
    }

    function setup_activitat() {
        $this->record_activitat = (object) array(
            'id' => 3945,
            'quadern' => $this->record_quadern->id,
            'descripcio' => 'Activitat',
            'nota' => 2,
        );

        $this->activitat = new fct_activitat;
        fct_copy_vars($this->record_activitat, $this->activitat);
    }

    function setup_fct() {
        $this->record_fct = (object) array(
            'id' => 5446,
            'course' => 4165,
            'name' => 'fct name',
            'intro' => 'fct intro',
            'timecreated' => 135446,
            'timemodified' => 546315,
            'frases_centre' => 'abc',
            'frases_empresa' => 'def',
        );

        $this->record_centre = (object) array(
            'id' => 4536,
            'fct' => $this->record_fct->id,
            'nom' => 'nom',
            'adreca' => 'adreca',
            'codi_postal' => '08666',
            'poblacio' => 'poblacio',
            'telefon' => '85154616',
            'fax' => '545613216',
            'email' => 'centre@fct',
        );

        $this->fct = new fct;
        fct_copy_vars($this->record_fct, $this->fct);
        $this->fct->course = (object) array(
            'id' => $this->record_fct->course
        );
        $this->fct->cm = (object) array(
            'id' => 2123,
            'instance' => $this->record_fct->id,
        );
        $this->fct->context = (object) array(
            'id' => 2301,
            'contextlevel' => CONTEXT_MODULE,
            'instanceid' => $this->cm->id,
        );
        fct_copy_vars($this->record_centre, $this->fct->centre);
    }

    function setup_cicle() {
        $this->record_cicle = (object) array(
            'id' => 8402,
            'fct' => $this->record_fct->id,
            'nom' => 'Nom cicle',
        );

        $this->records_activitat_cicle = array(
            (object) array('id' => 5070,
                           'cicle' => $this->record_cicle->id,
                           'descripcio' => 'Activitat 1'),
            (object) array('id' => 5071,
                           'cicle' => $this->record_cicle->id,
                           'descripcio' => 'Activitat 2'),
            (object) array('id' => 5072,
                           'cicle' => $this->record_cicle->id,
                           'descripcio' => 'Activitat 3'),
        );

        $this->cicle = new fct_cicle;
        $this->cicle->id = $this->record_cicle->id;
        $this->cicle->fct = $this->fct->id;
        $this->cicle->nom = $this->record_cicle->nom;
        $this->cicle->activitats = array(
            $this->records_activitat_cicle[0]->descripcio,
            $this->records_activitat_cicle[1]->descripcio,
            $this->records_activitat_cicle[2]->descripcio,
        );
        $this->cicle->n_quaderns = 13;
    }

    function setup_quadern() {
        $this->record_quadern = (object) array(
            'id' => 3249,
            'cicle' => 1369,
            'alumne' => 3179,
            'tutor_centre' => 7192,
            'tutor_empresa' => 0394,
            'nom_empresa' => 'nom empresa',
            'estat' => 1,
        );

        $this->record_dades_alumne = (object) array(
            'id' => 2994,
            'fct' => 4012,
            'alumne' => $this->record_quadern->alumne,
            'adreca' => 'adreça alumne',
            'poblacio' => 'població alumne',
            'codi_postal' => '08666',
            'telefon' => '383565989',
            'email' => 'alumne@fct',
            'dni' => '12345678A',
            'targeta_sanitaria' => 'ABC83982',
        );

        $this->record_empresa = (object) array(
            'id' => 7430,
            'quadern' => $this->record_quadern->id,
            'adreca' => 'adreça emoresa',
            'poblacio' => 'població empresa',
            'codi_postal' => '08909',
            'telefon' => '5651637',
            'fax' => '8492154',
            'email' => 'empresa@fct',
            'nif' => '123456789ABC',
        );

        $this->record_dades_conveni = (object) array(
            'id' => 7431,
            'quadern' => $this->record_quadern->id,
            'prorrogues' => 'prorrogues',
            'hores_practiques' => 90,
        );

        $this->record_dades_relatives = (object) array(
            'quadern' => $this->record_quadern->id,
            'hores_credit' => 100,
            'exempcio' => 25,
            'hores_anteriors' => 10,
        );

        $this->record_qualificacio_quadern = (object) array(
            'id' => 7432,
            'quadern' => $this->record_quadern->id,
            'qualificacio' => 1,
            'nota' => 3,
            'data' => 4154113,
            'observacions' => 'observacions',
        );

        $this->record_qualificacio_global = (object) array(
            'id' => 4094,
            'cicle' => $this->record_quadern->cicle,
            'alumne' => $this->record_quadern->alumne,
            'qualificacio' => 1,
            'nota' => 2,
            'data' => 3846039,
            'observacions' => 'observacions',
        );

        $this->record_conveni = (object) array(
            'id' => 2651,
            'quadern' => $this->record_quadern->id,
            'codi' => 'ANM394',
            'data_inici' => 1323143,
            'data_final' => 2434213,
        );

        $this->record_horari = (object) array(
            'id' => 7433,
            'conveni' => $this->record_conveni->id,
            'dilluns' => 'dl',
            'dimarts' => 'dt',
            'dimecres' => 'dc',
            'dijous' => 'dj',
            'divendres' => 'dv',
            'dissabte' => 'ds',
            'diumenge' => 'dg',
        );

        $this->records_valoracio_actituds = array(
            (object) array('id' => 7434,
                           'quadern' => $this->record_quadern->id,
                           'final' => 0, 'actitud' => 1, 'nota' => 2),
            (object) array('id' => 7435,
                           'quadern' => $this->record_quadern->id,
                           'final' => 1, 'actitud' => 3, 'nota' => 4),
        );

        $this->quadern = new fct_quadern;
        fct_copy_vars($this->record_quadern, $this->quadern);
        $this->quadern->empresa->nom = $this->record_quadern->nom_empresa;
        fct_copy_vars($this->record_dades_alumne, $this->quadern->dades_alumne);
        fct_copy_vars($this->record_empresa, $this->quadern->empresa);
        fct_copy_vars($this->record_dades_conveni, $this->quadern,
                      array('prorrogues', 'hores_practiques'));
        fct_copy_vars($this->record_dades_relatives, $this->quadern,
                      array('hores_credit', 'exempcio', 'hores_anteriors'));
        fct_copy_vars($this->record_qualificacio_quadern,
                      $this->quadern->qualificacio);
        $this->quadern->qualificacio->apte =
            $this->record_qualificacio_quadern->qualificacio;
        fct_copy_vars($this->record_qualificacio_global,
                      $this->quadern->qualificacio_global);
        $this->quadern->qualificacio_global->apte =
            $this->record_qualificacio_global->qualificacio;
        $conveni = new fct_conveni;
        fct_copy_vars($this->record_conveni, $conveni);
        fct_copy_vars($this->record_horari, $conveni->horari);
        $this->quadern->convenis[] = $conveni;
        $this->quadern->valoracio_parcial = array(1 => 2);
        $this->quadern->valoracio_final = array(3 => 4);
    }

    function setup_quinzena() {
        $this->record_quinzena = (object) array(
            'id' => 2086,
            'hores' => 2930,
            'quadern' => 3940,
            'any_' => 2009,
            'periode' => 14,
            'valoracions' => 'valoracions alumne',
            'observacions_alumne' => 'observacions alumne',
            'observacions_centre' => 'observacions tutor centre',
            'observacions_empresa' => 'observacions tutor empresa',
        );

        $this->records_dia_quinzena = array(
            (object) array('id' => 2840,
                           'quinzena' => $this->record_quinzena->id,
                           'dia' => 18),
            (object) array('id' => 0384,
                           'quinzena' => $this->record_quinzena->id,
                           'dia' => 23),
        );

        $this->records_activitat_quinzena = array(
            (object) array('id' => 7926,
                           'quinzena' => $this->record_quinzena->id,
                           'activitat' => 8472),
            (object) array('id' => 9273,
                           'quinzena' => $this->record_quinzena->id,
                           'activitat' => 3846),
        );

        $this->quinzena = new fct_quinzena;
        fct_copy_vars($this->record_quinzena, $this->quinzena);
        $this->quinzena->any = $this->record_quinzena->any_;
        foreach ($this->records_dia_quinzena as $record) {
            $this->quinzena->dies[] = $record->dia;
        }
        foreach ($this->records_activitat_quinzena as $record) {
            $this->quinzena->activitats[] = $record->activitat;
        }
    }

    function setup_usuari() {
        $this->record_user = (object) array(
            'id' => 1429,
            'firstname' => 'Nom',
            'lastname' => 'Cognoms',
            'email' => 'user@fct',
        );

        $this->usuari = new fct_usuari;
        $this->usuari->id = $this->record_user->id;
        $this->usuari->fct = $this->record_fct->id;
        $this->usuari->nom = $this->record_user->firstname;
        $this->usuari->cognoms = $this->record_user->lastname;
        $this->usuari->email= $this->record_user->email;
        $this->usuari->es_administrador = true;
        $this->usuari->es_alumne = true;
        $this->usuari->es_tutor_centre = true;
        $this->usuari->es_tutor_empresa = true;
    }

    function test_activitat() {
        $this->moodle->expects($this->once())->method('get_record')
            ->with('fct_activitat_pla', 'id', $this->record_activitat->id)
            ->will($this->returnValue($this->record_activitat));

        $activitat = $this->diposit->activitat($this->activitat->id);

        $this->assertEquals($this->activitat, $activitat);
    }

    function test_activitats() {
        $this->diposit = $this->getMock('fct_diposit', array('activitat'),
                                        array($this->moodle));

        $select = "quadern = {$this->quadern->id} AND descripcio = '"
            . addslashes($this->activitat->descripcio) . "'";

        $this->moodle->expects($this->once())->method('get_records_select')
            ->with('fct_activitat_pla', $select, 'descripcio', 'id')
            ->will($this->returnValue(array($this->record_activitat)));

        $this->diposit->expects($this->once())->method('activitat')
            ->with($this->activitat->id)
            ->will($this->returnValue($this->activitat));

        $activitats = $this->diposit->activitats($this->quadern->id,
                                                 $this->activitat->descripcio);

        $this->assertEquals(array($this->activitat), $activitats);
    }

    function test_afegir_activitat__existent() {
        $this->moodle->expects($this->once())->method('update_record')
            ->with('fct_activitat_pla', $this->record_activitat);

        $this->diposit->afegir_activitat($this->activitat);
    }

    function test_afegir_activitat__inexistent() {
        $id = $this->activitat->id;
        $this->activitat->id = false;
        unset($this->record_activitat->id);

        $this->moodle->expects($this->once())->method('insert_record')
            ->with('fct_activitat_pla', $this->record_activitat)
            ->will($this->returnValue($id));

        $this->diposit->afegir_activitat($this->activitat);

        $this->assertEquals($id, $this->activitat->id);
    }

    function test_afegir_cicle__existent() {
        $this->moodle->expects($this->at(0))->method('update_record')
            ->with('fct_cicle', $this->record_cicle);

        $this->moodle->expects($this->at(1))->method('delete_records')
            ->with('fct_activitat_cicle', 'cicle', $this->cicle->id);

        foreach ($this->records_activitat_cicle as $index => $record) {
            unset($record->id);
            $this->moodle->expects($this->at($index+2))->method('insert_record')
                ->with('fct_activitat_cicle', $record);
        }

        $this->diposit->afegir_cicle($this->cicle);
    }

    function test_afegir_cicle__inexistent() {
        $id = $this->cicle->id;
        $this->cicle->id = false;
        unset($this->record_cicle->id);

        $this->moodle->expects($this->at(0))->method('insert_record')
            ->with('fct_cicle', $this->record_cicle)
            ->will($this->returnValue($id));

        foreach ($this->records_activitat_cicle as $index => $record) {
            unset($record->id);
            $this->moodle->expects($this->at($index+1))->method('insert_record')
                ->with('fct_activitat_cicle', $record);
        }

        $this->diposit->afegir_cicle($this->cicle);

        $this->assertEquals($id, $this->cicle->id);
    }

    function test_afegir_fct__existent() {
        unset($this->record_centre->id);

        $this->moodle->expects($this->at(0))->method('update_record')
            ->with('fct', $this->record_fct);

        $this->moodle->expects($this->at(1))->method('delete_records')
            ->with('fct_dades_centre', 'fct', $this->fct->id);

        $this->moodle->expects($this->at(2))->method('insert_record')
            ->with('fct_dades_centre', $this->record_centre);

        $this->diposit->afegir_fct($this->fct);
    }

    function test_afegir_fct__inexistent() {
        $id = $this->fct->id;
        $this->fct->id = false;
        unset($this->record_fct->id);
        unset($this->record_centre->id);

        $this->moodle->expects($this->at(0))->method('insert_record')
            ->with('fct', $this->record_fct)
            ->will($this->returnValue($id));

        $this->moodle->expects($this->at(1))->method('insert_record')
            ->with('fct_dades_centre', $this->record_centre);

        $this->diposit->afegir_fct($this->fct);

        $this->assertEquals($id, $this->fct->id);
    }

    function test_afegir_quadern__existent() {
        $index = 0;
        $tables = array('fct_dades_empresa' => $this->record_empresa,
                        'fct_dades_relatives' => $this->record_dades_relatives,
                        'fct_dades_conveni' => $this->record_dades_conveni,
                        'fct_qualificacio_quadern' =>
                        $this->record_qualificacio_quadern);

        $this->moodle->expects($this->at($index++))->method('update_record')
            ->with('fct_quadern', $this->record_quadern);

        $this->moodle->expects($this->at($index++))
            ->method('get_field')
            ->with('fct_cicle', 'fct', 'id', $this->quadern->cicle)
            ->will($this->returnValue($this->record_dades_alumne->fct));
        $this->moodle->expects($this->at($index++))
            ->method('delete_records')
            ->with('fct_dades_alumne', 'fct', $this->record_dades_alumne->fct,
                   'alumne', $this->quadern->alumne);
        unset($this->record_dades_alumne->id);
        $this->moodle->expects($this->at($index++))
            ->method('insert_record')
            ->with('fct_dades_alumne', $this->record_dades_alumne);

        $this->moodle->expects($this->at($index++))
            ->method('delete_records')
            ->with('fct_qualificacio_global', 'cicle', $this->quadern->cicle,
                   'alumne', $this->quadern->alumne);
        unset($this->record_qualificacio_global->id);
        $this->moodle->expects($this->at($index++))
            ->method('insert_record')
            ->with('fct_qualificacio_global', $this->record_qualificacio_global);

        foreach ($tables as $table => $record) {
            $this->moodle->expects($this->at($index++))
                ->method('delete_records')
                ->with($table, 'quadern', $this->quadern->id);
        }

        $this->moodle->expects($this->at($index++))
            ->method('delete_records')
            ->with('fct_valoracio_actituds', 'quadern', $this->quadern->id);

        $this->moodle->expects($this->at($index++))
            ->method('get_records')
            ->with('fct_conveni', 'quadern', $this->quadern->id)
            ->will($this->returnValue(array(clone($this->record_conveni))));

        $this->moodle->expects($this->at($index++))
            ->method('delete_records')
            ->with('fct_horari', 'conveni', $this->record_conveni->id);

        $this->moodle->expects($this->at($index++))
            ->method('delete_records')
            ->with('fct_conveni', 'quadern', $this->quadern->id);

        foreach ($tables as $table => $record) {
            unset($record->id);
            $this->moodle->expects($this->at($index++))
                ->method('insert_record')->with($table, $record);
        }

        foreach ($this->records_valoracio_actituds as $record) {
            unset($record->id);
            $this->moodle->expects($this->at($index++))
                ->method('insert_record')
                ->with('fct_valoracio_actituds', $record);
        }

        $conveni_id = 5514;
        unset($this->record_conveni->id);
        $this->moodle->expects($this->at($index++))
            ->method('insert_record')
            ->with('fct_conveni', $this->record_conveni)
            ->will($this->returnValue($conveni_id));

        unset($this->record_horari->id);
        $this->record_horari->conveni = $conveni_id;
        $this->moodle->expects($this->at($index++))
            ->method('insert_record')
            ->with('fct_horari', $this->record_horari);

        $this->diposit->afegir_quadern($this->quadern);
    }

    function test_afegir_quadern__inexistent() {
        $quadern_id = $this->quadern->id;
        $this->quadern->id = false;

        $index = 0;
        $tables = array(
            'fct_dades_empresa' => $this->record_empresa,
            'fct_dades_relatives' => $this->record_dades_relatives,
            'fct_dades_conveni' => $this->record_dades_conveni,
            'fct_qualificacio_quadern' => $this->record_qualificacio_quadern,
        );

        unset($this->record_quadern->id);
        $this->moodle->expects($this->at($index++))->method('insert_record')
            ->with('fct_quadern', $this->record_quadern)
            ->will($this->returnValue($quadern_id));

        $this->moodle->expects($this->at($index++))->method('get_field')
            ->with('fct_cicle', 'fct', 'id', $this->quadern->cicle)
            ->will($this->returnValue($this->record_dades_alumne->fct));
        $where = "fct = {$this->record_dades_alumne->fct} AND alumne = {$this->quadern->alumne}";
        $this->moodle->expects($this->at($index++)) ->method('get_records_select')
            ->with('fct_dades_alumne', $where)
            ->will($this->returnValue(array($this->record_dades_alumne)));

        $where = "cicle = {$this->quadern->cicle} AND alumne = {$this->quadern->alumne}";
        $this->moodle->expects($this->at($index++)) ->method('get_records_select')
            ->with('fct_qualificacio_global', $where)
            ->will($this->returnValue(array($this->record_qualificacio_global)));

        foreach ($tables as $table => $record) {
            unset($record->id);
            $this->moodle->expects($this->at($index++))
                ->method('insert_record')->with($table, $record);
        }

        foreach ($this->records_valoracio_actituds as $record) {
            unset($record->id);
            $this->moodle->expects($this->at($index++))
                ->method('insert_record')
                ->with('fct_valoracio_actituds', $record);
        }

        $conveni_id = $this->record_conveni->id;
        unset($this->record_conveni->id);
        $this->moodle->expects($this->at($index++))->method('insert_record')
            ->with('fct_conveni', $this->record_conveni)
            ->will($this->returnValue($conveni_id));

        unset($this->record_horari->id);
        $this->moodle->expects($this->at($index++))
            ->method('insert_record')
            ->with('fct_horari', $this->record_horari);

        $this->diposit->afegir_quadern($this->quadern);

        $this->assertEquals($quadern_id, $this->quadern->id);
    }

    function test_afegir_quinzena__existent() {
        $index= 0;

        $this->moodle->expects($this->at($index++))->method('update_record')
            ->with('fct_quinzena', $this->record_quinzena);

        $this->moodle->expects($this->at($index++))->method('delete_records')
            ->with('fct_dia_quinzena', 'quinzena', $this->quinzena->id);

        $this->moodle->expects($this->at($index++))->method('delete_records')
            ->with('fct_activitat_quinzena', 'quinzena', $this->quinzena->id);

        foreach ($this->records_dia_quinzena as $record) {
            unset($record->id);
            $this->moodle->expects($this->at($index++))
                ->method('insert_record')
                ->with('fct_dia_quinzena', $record);
        }

        foreach ($this->records_activitat_quinzena as $record) {
            unset($record->id);
            $this->moodle->expects($this->at($index++))
                ->method('insert_record')
                ->with('fct_activitat_quinzena', $record);
        }

        $this->diposit->afegir_quinzena($this->quinzena);
    }

    function test_afegir_quinzena__inexistent() {
        $quinzena_id = $this->quinzena->id;
        $this->quinzena->id = false;

        $index= 0;

        unset($this->record_quinzena->id);
        $this->moodle->expects($this->at($index++))->method('insert_record')
            ->with('fct_quinzena', $this->record_quinzena)
            ->will($this->returnValue($quinzena_id));

        foreach ($this->records_dia_quinzena as $record) {
            unset($record->id);
            $this->moodle->expects($this->at($index++))
                ->method('insert_record')
                ->with('fct_dia_quinzena', $record);
        }

        foreach ($this->records_activitat_quinzena as $record) {
            unset($record->id);
            $this->moodle->expects($this->at($index++))
                ->method('insert_record')
                ->with('fct_activitat_quinzena', $record);
        }

        $this->diposit->afegir_quinzena($this->quinzena);

        $this->assertEquals($quinzena_id, $this->quinzena->id);
    }

    function test_cicle() {
        $this->moodle->expects($this->once())->method('get_record')
            ->with('fct_cicle', 'id', $this->cicle->id)
            ->will($this->returnValue($this->record_cicle));

        $this->moodle->expects($this->once())->method('get_records')
            ->with('fct_activitat_cicle', 'cicle', $this->cicle->id,
                   'descripcio')
            ->will($this->returnValue($this->records_activitat_cicle));

        $this->moodle->expects($this->once())->method('count_records')
            ->with('fct_quadern', 'cicle', $this->cicle->id)
            ->will($this->returnValue($this->cicle->n_quaderns));

        $cicle = $this->diposit->cicle($this->cicle->id);

        $this->assertEquals($this->cicle, $cicle);
    }

    function test_cicles() {
        $this->diposit = $this->getMock('fct_diposit', array('cicle'),
                                        array($this->moodle));

        $select = "fct = {$this->fct->id} AND nom = '"
            . addslashes($this->cicle->nom) . "'";
        $this->moodle->expects($this->once())->method('get_records_select')
            ->with('fct_cicle', $select, 'nom', 'id')
            ->will($this->returnValue(array($this->record_cicle)));

        $this->diposit->expects($this->once())->method('cicle')
            ->with($this->cicle->id)->will($this->returnValue($this->cicle));

        $cicles = $this->diposit->cicles($this->fct->id, $this->cicle->nom);

        $this->assertEquals(array($this->cicle), $cicles);
    }

    function test_fct() {
        $this->moodle->expects($this->at(0))->method('get_record')
            ->with('fct', 'id', $this->record_fct->id)
            ->will($this->returnValue($this->record_fct));

        $this->moodle->expects($this->at(1))
            ->method('get_coursemodule_from_instance')
            ->with('fct', $this->record_fct->id)
            ->will($this->returnValue($this->fct->cm));

        $this->moodle->expects($this->at(2))->method('get_record')
            ->with('course', 'id', $this->record_fct->course)
            ->will($this->returnValue($this->fct->course));

        $this->moodle->expects($this->at(3))->method('get_context_instance')
            ->with(CONTEXT_MODULE, $this->fct->cm->id)
            ->will($this->returnValue($this->fct->context));

        $this->moodle->expects($this->at(4))->method('get_record')
            ->with('fct_dades_centre', 'fct', $this->record_fct->id)
            ->will($this->returnValue($this->record_centre));

        $fct = $this->diposit->fct($this->record_fct->id);

        $this->assertEquals($this->fct, $fct);
    }

    function test_fct_cm() {
        $this->diposit = $this->getMock('fct_diposit', array('fct'),
                                        array($this->moodle));

        $this->moodle->expects($this->once())
            ->method('get_coursemodule_from_id')
            ->with('fct', $this->cm->id)
            ->will($this->returnValue($this->cm));

        $this->diposit->expects($this->once())->method('fct')
            ->with($this->cm->instance)
            ->will($this->returnValue($this->fct));

        $fct = $this->diposit->fct_cm($this->cm->id);

        $this->assertEquals($this->fct, $fct);
    }

    function test_quadern() {
        $index = 0;

        $this->moodle->expects($this->at($index++))->method('get_record')
            ->with('fct_quadern', 'id', $this->quadern->id)
            ->will($this->returnValue($this->record_quadern));

        $this->moodle->expects($this->at($index++))->method('get_record')
            ->with('fct_dades_empresa', 'quadern', $this->quadern->id)
            ->will($this->returnValue($this->record_empresa));

        $this->moodle->expects($this->at($index++))->method('get_record')
            ->with('fct_dades_relatives', 'quadern', $this->quadern->id)
            ->will($this->returnValue($this->record_dades_relatives));

        $this->moodle->expects($this->at($index++))->method('get_record')
            ->with('fct_dades_conveni', 'quadern', $this->quadern->id)
            ->will($this->returnValue($this->record_dades_conveni));

        $this->moodle->expects($this->at($index++))->method('get_record')
            ->with('fct_qualificacio_quadern', 'quadern', $this->quadern->id)
            ->will($this->returnValue($this->record_qualificacio_quadern));

        $this->moodle->expects($this->at($index++))->method('get_records')
            ->with('fct_conveni', 'quadern', $this->quadern->id, 'data_inici')
            ->will($this->returnValue(array($this->record_conveni)));

        $this->moodle->expects($this->at($index++))->method('get_record')
            ->with('fct_horari', 'conveni', $this->record_conveni->id)
            ->will($this->returnValue($this->record_horari));

        $this->moodle->expects($this->at($index++))->method('get_records')
            ->with('fct_valoracio_actituds', 'quadern',
                   $this->quadern->id, 'actitud')
            ->will($this->returnValue($this->records_valoracio_actituds));

        $this->moodle->expects($this->at($index++))->method('get_field')
            ->with('fct_cicle', 'fct', 'id', $this->quadern->cicle)
            ->will($this->returnValue($this->record_dades_alumne->fct));
        $where = "fct = {$this->record_dades_alumne->fct} AND alumne = {$this->quadern->alumne}";
        $this->moodle->expects($this->at($index++)) ->method('get_records_select')
            ->with('fct_dades_alumne', $where)
            ->will($this->returnValue(array($this->record_dades_alumne)));

        $where = "cicle = {$this->quadern->cicle} AND alumne = {$this->quadern->alumne}";
        $this->moodle->expects($this->at($index++)) ->method('get_records_select')
            ->with('fct_qualificacio_global', $where)
            ->will($this->returnValue(array($this->record_qualificacio_global)));

        $quadern = $this->diposit->quadern($this->quadern->id);

        $this->assertEquals($this->quadern, $quadern);
    }

    function test_quinzena() {
        $this->moodle->expects($this->at(0))->method('get_record')
            ->with('fct_quinzena', 'id', $this->quinzena->id)
            ->will($this->returnValue($this->record_quinzena));

        $this->moodle->expects($this->at(1))->method('get_records')
            ->with('fct_dia_quinzena', 'quinzena', $this->quinzena->id, 'dia')
            ->will($this->returnValue($this->records_dia_quinzena));

        $this->moodle->expects($this->at(2))->method('get_records')
            ->with('fct_activitat_quinzena', 'quinzena', $this->quinzena->id, 'activitat')
            ->will($this->returnValue($this->records_activitat_quinzena));

        $quinzena = $this->diposit->quinzena($this->quinzena->id);

        $this->assertEquals($this->quinzena, $quinzena);
    }

    function test_quinzenes() {
        $this->diposit = $this->getMock('fct_diposit', array('quinzena'),
                                        array($this->moodle));

        $where = 'quadern = ' . $this->quinzena->quadern;
        $this->moodle->expects($this->once())->method('get_records_select')
            ->with('fct_quinzena', $where)
            ->will($this->returnValue(array($this->record_quinzena)));

        $this->diposit->expects($this->once())->method('quinzena')
            ->with($this->quinzena->id)
            ->will($this->returnValue($this->quinzena));

        $quinzenes = $this->diposit->quinzenes($this->quinzena->quadern);

        $this->assertEquals(array($this->quinzena), $quinzenes);
    }

    function test_quinzenes__any_periode() {
        $this->diposit = $this->getMock('fct_diposit', array('quinzena'),
                                        array($this->moodle));

        $where = 'quadern = ' . $this->quinzena->quadern
            . ' AND any_ = ' . $this->quinzena->any
            . ' AND periode = '. $this->quinzena->periode;
        $this->moodle->expects($this->once())->method('get_records_select')
            ->with('fct_quinzena', $where)
            ->will($this->returnValue(array($this->record_quinzena)));

        $this->diposit->expects($this->once())->method('quinzena')
            ->with($this->quinzena->id)
            ->will($this->returnValue($this->quinzena));

        $quinzenes = $this->diposit->quinzenes($this->quinzena->quadern,
                                               $this->quinzena->any,
                                               $this->quinzena->periode);

        $this->assertEquals(array($this->quinzena), $quinzenes);
    }

    function test_suprimir_activitat() {
        $this->moodle->expects($this->once())->method('delete_records')
            ->with('fct_activitat_pla', 'id', $this->record_activitat->id);

        $this->diposit->suprimir_activitat($this->activitat);

        $this->assertFalse($this->activitat->id);
    }

    function test_suprimir_cicle() {
        $this->moodle->expects($this->at(0))->method('delete_records')
            ->with('fct_activitat_cicle', 'cicle', $this->cicle->id);

        $this->moodle->expects($this->at(1))->method('delete_records')
            ->with('fct_cicle', 'id', $this->cicle->id);

        $this->diposit->suprimir_cicle($this->cicle);

        $this->assertFalse($this->cicle->id);
    }

    function test_suprimir_fct() {
        $this->moodle->expects($this->once())->method('delete_records')
            ->with('fct', 'id', $this->fct->id);

        $this->diposit->suprimir_fct($this->fct);

        $this->assertFalse($this->fct->id);
    }

    function test_suprimir_quadern() {
        $index = 0;
        $tables = array('fct_dades_empresa', 'fct_dades_relatives',
                        'fct_dades_conveni', 'fct_qualificacio_quadern',
                        'fct_valoracio_actituds');

        foreach ($tables as $table) {
            $this->moodle->expects($this->at($index++))
                ->method('delete_records')
                ->with($table, 'quadern', $this->quadern->id);
        }

        $this->moodle->expects($this->at($index++))
            ->method('get_records')
            ->with('fct_conveni', 'quadern', $this->quadern->id)
            ->will($this->returnValue(array($this->record_conveni)));

        $this->moodle->expects($this->at($index++))
            ->method('delete_records')
            ->with('fct_horari', 'conveni', $this->record_conveni->id);

        $this->moodle->expects($this->at($index++))
            ->method('delete_records')
            ->with('fct_conveni', 'quadern', $this->quadern->id);

        $this->moodle->expects($this->at($index++))
            ->method('delete_records')
            ->with('fct_quadern', 'id', $this->quadern->id);

        $this->diposit->suprimir_quadern($this->quadern);

        $this->assertFalse($this->quadern->id);
    }

    function test_suprimir_quinzena() {
        $this->moodle->expects($this->at(0))->method('delete_records')
            ->with('fct_quinzena', 'id', $this->quinzena->id);

        $this->moodle->expects($this->at(1))->method('delete_records')
            ->with('fct_dia_quinzena', 'quinzena', $this->quinzena->id);

        $this->moodle->expects($this->at(2))->method('delete_records')
            ->with('fct_activitat_quinzena', 'quinzena', $this->quinzena->id);

        $this->diposit->suprimir_quinzena($this->quinzena);

        $this->assertFalse($this->quinzena->id);
    }

    function test_usuari() {
        $this->moodle->expects($this->at(0))->method('get_record')
            ->with('user', 'id', $this->usuari->id)
            ->will($this->returnValue($this->record_user));

        $caps = array(1 => 'admin', 2 => 'alumne',
                      3 => 'tutor_centre', 4 => 'tutor_empresa');
        foreach ($caps as $index => $cap) {
            $this->moodle->expects($this->at($index))->method('has_capability')
                ->with("mod/fct:$cap", $this->fct->context, $this->usuari->id)
                ->will($this->returnValue(True));
        }

        $usuari = $this->diposit->usuari($this->fct, $this->usuari->id);

        $this->assertEquals($this->usuari, $usuari);
    }


}
