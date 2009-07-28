<?php

require_once 'PHPUnit/Framework.php';
require_once 'mod/fct/diposit.php';
require_once 'mod/fct/domini.php';
require_once 'mod/fct/moodle.php';

class fct_test_diposit extends PHPUnit_Framework_TestCase {

    var $activitat;
    var $cicle;
    var $cm;
    var $context;
    var $diposit;
    var $fct;
    var $moodle;
    var $quadern;
    var $quinzena;
    var $record_activitat;
    var $record_cicle;
    var $record_fct;
    var $record_quadern;
    var $record_quinzena;
    var $record_user;
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
        $this->activitat = new fct_activitat;
        $this->activitat->id = 3945;
        $this->activitat->quadern = $this->record_quadern->id;
        $this->activitat->descripcio = "Activitat";
        $this->activitat->nota = 2;

        $this->record_activitat = (object) array(
            'id' => $this->activitat->id,
            'quadern' => $this->activitat->quadern,
            'objecte' => fct_json::serialitzar_activitat($this->activitat),
        );
    }

    function setup_cicle() {
        $this->cicle = new fct_cicle;
        $this->cicle->id = 8402;
        $this->cicle->fct = $this->fct->id;
        $this->cicle->nom = 'nom cicle';
        $this->cicle->activitats = array('activitat 1', 'activitat 2',
                                         'activitat 3', 'activitat 4');
        $this->cicle->n_quaderns = 13;

        $this->record_cicle = (object) array(
            'id' => $this->cicle->id,
            'fct' => $this->cicle->fct,
            'nom' => $this->cicle->nom,
            'objecte' => fct_json::serialitzar_cicle($this->cicle),
        );
    }

    function setup_fct() {
        $this->fct = new fct;
        $this->fct->id = 5446;
        $this->fct->course = 4165;
        $this->fct->name = 'fct name';
        $this->fct->intro = 'fct intro';
        $this->fct->timecreated = 384729;
        $this->fct->timemodified = 372939;
        $this->fct->centre->nom = 'nom centre';
        $this->fct->frases_centre = array('a', 'b');
        $this->fct->frases_empresa = array('c', 'd');

        $this->record_fct = (object) array(
            'id' => $this->fct->id,
            'course' => $this->fct->course,
            'name' => $this->fct->name,
            'intro' => $this->fct->intro,
            'timecreated' => $this->fct->timecreated,
            'timemodified' => $this->fct->timemodified,
            'objecte' => fct_json::serialitzar_fct($this->fct),
        );
    }

    function setup_quadern() {
        $this->quadern = new fct_quadern;
        $this->quadern->id = 3249;
        $this->quadern->cicle = 1369;
        $this->quadern->alumne = 3179;
        $this->quadern->tutor_centre = 7192;
        $this->quadern->tutor_empresa = 0394;
        $this->quadern->estat = 1;

        $this->quadern->dades_alumne->adreca = 'adreça alumne';
        $this->quadern->empresa->nom = 'nom empresa';
        $this->quadern->qualificacio->apte = 1;
        $this->quadern->qualificacio_global->nota = 2;
        $this->quadern->valoracio_parcial[1] = 2;

        $conveni = new fct_conveni;
        $conveni->data_final = 8342791;
        $conveni->horari->dilluns = 'dl';
        $this->quadern->afegir_conveni($conveni);

        $this->record_quadern = (object) array(
            'id' => $this->quadern->id,
            'alumne' => $this->quadern->alumne,
            'tutor_centre' => $this->quadern->tutor_centre,
            'tutor_empresa' => $this->quadern->tutor_empresa,
            'nom_empresa' => $this->quadern->empresa->nom,
            'cicle' => $this->quadern->cicle,
            'estat' => $this->quadern->estat,
            'data_final' => $this->quadern->data_final(),
            'objecte' => fct_json::serialitzar_quadern($this->quadern),
        );
    }

    function setup_quinzena() {
        $this->quinzena = new fct_quinzena;
        $this->quinzena->id = 2086;
        $this->quinzena->quadern = 3940;
        $this->quinzena->any = 2009;
        $this->quinzena->periode = 14;
        $this->quinzena->hores = 36;
        $this->quinzena->dies = array(16, 17, 18, 20, 21, 22);
        $this->quinzena->activitats = array(3927, 3827, 2839, 0327);
        $this->quinzena->valoracions = 'valoracions alumne';
        $this->quinzena->observacions_alumne = 'observacions alumne';
        $this->quinzena->observacions_centre = 'observacions tutor centre';
        $this->quinzena->observacions_empresa = 'observacions tutor empresa';

        $this->record_quinzena = (object) array(
            'id' => $this->quinzena->id,
            'quadern' => $this->quinzena->quadern,
            'any_' => $this->quinzena->any,
            'periode' => $this->quinzena->periode,
            'objecte' => fct_json::serialitzar_quinzena($this->quinzena),
        );
    }

    function setup_usuari() {
        $this->cm = (object) array(
            'id' => 2123,
            'instance' => $this->record_fct->id,
        );
        $this->context = (object) array(
            'id' => 2301,
            'contextlevel' => CONTEXT_MODULE,
            'instanceid' => $this->cm->id,
        );

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
            ->with('fct_activitat', 'id', $this->activitat->id)
            ->will($this->returnValue($this->record_activitat));

        $activitat = $this->diposit->activitat($this->activitat->id);

        $this->assertEquals($this->activitat, $activitat);
    }

    function test_activitats() {
        $this->diposit = $this->getMock('fct_diposit', array('activitat'),
                                        array($this->moodle));

        $activitat2 = new fct_activitat;
        $activitat2->id = 3849;
        $activitat2->quadern = $this->activitat->quadern;
        $activitat2->descripcio = "Activitat 2";

        $records = array((object) array('id' => $this->activitat->id),
                         (object) array('id' => $activitat2->id));
        $this->moodle->expects($this->once())->method('get_records')
            ->with('fct_activitat', 'quadern', $this->quadern->id, 'id', 'id')
            ->will($this->returnValue($records));

        $this->diposit->expects($this->at(0))->method('activitat')
            ->with($this->activitat->id)
            ->will($this->returnValue($this->activitat));

        $this->diposit->expects($this->at(1))->method('activitat')
            ->with($activitat2->id)
            ->will($this->returnValue($activitat2));

        $activitats = $this->diposit->activitats($this->quadern->id,
                                                 $this->activitat->descripcio);

        $this->assertEquals(array($this->activitat), $activitats);
    }

    function test_afegir_activitat__existent() {
        $this->moodle->expects($this->once())->method('update_record')
            ->with('fct_activitat', $this->record_activitat);

        $this->diposit->afegir_activitat($this->activitat);
    }

    function test_afegir_activitat__inexistent() {
        $id = $this->activitat->id;
        $this->activitat->id = false;

        $record = (object) array('quadern' => $this->activitat->quadern);
        $this->moodle->expects($this->once())->method('insert_record')
            ->with('fct_activitat', $record)
            ->will($this->returnValue($id));

        $this->moodle->expects($this->once())->method('update_record')
            ->with('fct_activitat', $this->record_activitat);

        $this->diposit->afegir_activitat($this->activitat);

        $this->assertEquals($id, $this->activitat->id);
    }

    function test_afegir_cicle__existent() {
        $this->moodle->expects($this->once())->method('update_record')
            ->with('fct_cicle', $this->record_cicle);

        $this->diposit->afegir_cicle($this->cicle);
    }

    function test_afegir_cicle__inexistent() {
        $id = $this->cicle->id;
        $this->cicle->id = false;

        $record = (object) array('fct' => $this->cicle->fct,
                                 'nom' => $this->cicle->nom);
        $this->moodle->expects($this->once())->method('insert_record')
            ->with('fct_cicle', $record)->will($this->returnValue($id));

        $this->moodle->expects($this->once())->method('update_record')
            ->with('fct_cicle', $this->record_cicle);

        $this->diposit->afegir_cicle($this->cicle);

        $this->assertEquals($id, $this->cicle->id);
    }

    function test_afegir_fct__existent() {
        $this->moodle->expects($this->once())->method('update_record')
            ->with('fct', $this->record_fct);

        $this->diposit->afegir_fct($this->fct);
    }

    function test_afegir_fct__inexistent() {
        $id = $this->fct->id;
        $this->fct->id = false;

        $record = clone($this->record_fct);
        unset($record->id);
        unset($record->objecte);

        $this->moodle->expects($this->once())->method('insert_record')
            ->with('fct', $record)->will($this->returnValue($id));

        $this->moodle->expects($this->once())->method('update_record')
            ->with('fct', $this->record_fct);

        $this->diposit->afegir_fct($this->fct);

        $this->assertEquals($id, $this->fct->id);
    }

    function test_afegir_quadern__existent() {
        $this->moodle->expects($this->once())->method('update_record')
            ->with('fct_quadern', $this->record_quadern);

        $this->diposit->afegir_quadern($this->quadern);
    }

    function test_afegir_quadern__inexistent() {
        $quadern_id = $this->quadern->id;
        $this->quadern->id = false;

        $record = clone($this->record_quadern);
        unset($record->id);
        unset($record->objecte);

        $this->moodle->expects($this->once())->method('insert_record')
            ->with('fct_quadern', $record)
            ->will($this->returnValue($quadern_id));

        $this->moodle->expects($this->once())->method('update_record')
            ->with('fct_quadern', $this->record_quadern);

        $this->diposit->afegir_quadern($this->quadern);

        $this->assertEquals($quadern_id, $this->quadern->id);
    }

    function test_afegir_quinzena__existent() {
        $this->moodle->expects($this->once())->method('update_record')
            ->with('fct_quinzena', $this->record_quinzena);

        $this->diposit->afegir_quinzena($this->quinzena);
    }

    function test_afegir_quinzena__inexistent() {
        $quinzena_id = $this->quinzena->id;
        $this->quinzena->id = false;

        $record = clone($this->record_quinzena);
        unset($record->id);
        unset($record->objecte);

        $this->moodle->expects($this->once())->method('insert_record')
            ->with('fct_quinzena', $record)
            ->will($this->returnValue($quinzena_id));

        $this->moodle->expects($this->once())->method('update_record')
            ->with('fct_quinzena', $this->record_quinzena);

        $this->diposit->afegir_quinzena($this->quinzena);

        $this->assertEquals($quinzena_id, $this->quinzena->id);
    }

    function test_cicle() {
        $this->moodle->expects($this->once())->method('get_record')
            ->with('fct_cicle', 'id', $this->cicle->id)
            ->will($this->returnValue($this->record_cicle));

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
        $this->moodle->expects($this->once())->method('get_record')
            ->with('fct', 'id', $this->fct->id)
            ->will($this->returnValue($this->record_fct));

        $fct = $this->diposit->fct($this->fct->id);

        $this->assertEquals($this->fct, $fct);
    }

    function test_min_max_data_final_quaderns() {
        global $CFG;
        $fct_id = 4801;

        $sql = "SELECT MIN(q.data_final) AS min_data_final,"
            . " MAX(q.data_final) AS max_data_final"
            . " FROM {$CFG->prefix}fct_quadern q"
            . " JOIN {$CFG->prefix}fct_cicle c ON c.id = q.cicle"
            . " WHERE c.fct = $fct_id AND q.data_final > 0";
        $record = (object) array('min_data_final' => 3874292,
                                 'max_data_final' => 3819388);

        $this->moodle->expects($this->once())->method('get_record_sql')
            ->with($sql)->will($this->returnValue($record));

        list($min, $max) = $this->diposit->min_max_data_final_quaderns($fct_id);

        $this->assertEquals($record->min_data_final, $min);
        $this->assertEquals($record->max_data_final, $max);
    }

    function test_nombre_cicles() {
        $nombre = 132;

        $this->moodle->expects($this->once())->method('count_records')
            ->with('fct_cicle')->will($this->returnValue($nombre));

        $resultat = $this->diposit->nombre_cicles();

        $this->assertEquals($nombre, $resultat);
    }

    function test_nombre_cicles__fct() {
        $nombre = 32;
        $fct_id = 2938;

        $this->moodle->expects($this->once())
            ->method('count_records')->with('fct_cicle', 'fct', $fct_id)
            ->will($this->returnValue($nombre));

        $resultat = $this->diposit->nombre_cicles($fct_id);

        $this->assertEquals($nombre, $resultat);
    }

    function test_nombre_quaderns() {
        $nombre = 482;

        $this->moodle->expects($this->once())->method('count_records')
            ->with('fct_quadern')->will($this->returnValue($nombre));

        $resultat = $this->diposit->nombre_quaderns();

        $this->assertEquals($nombre, $resultat);
    }

    function test_nombre_quaderns__fct() {
        global $CFG;

        $nombre = 239;
        $fct_id = 3847;

        $where = "cicle IN (SELECT id FROM {$CFG->prefix}fct_cicle"
            . " WHERE fct = $fct_id)";
        $this->moodle->expects($this->once())->method('count_records_select')
            ->with('fct_quadern', $where)->will($this->returnValue($nombre));

        $resultat = $this->diposit->nombre_quaderns($fct_id);

        $this->assertEquals($nombre, $resultat);
    }

    function test_nombre_quinzenes() {
        $nombre = 1832;

        $this->moodle->expects($this->once())->method('count_records')
            ->with('fct_quinzena')->will($this->returnValue($nombre));

        $resultat = $this->diposit->nombre_quinzenes();

        $this->assertEquals($nombre, $resultat);
    }

    function test_nombre_quinzenes__fct() {
        global $CFG;

        $nombre = 983;
        $fct_id = 3842;

        $where = "quadern IN (SELECT id FROM {$CFG->prefix}fct_quadern"
            . " WHERE cicle IN (SELECT id FROM {$CFG->prefix}fct_cicle"
            . " WHERE fct = $fct_id))";
        $this->moodle->expects($this->once())->method('count_records_select')
            ->with('fct_quinzena', $where)->will($this->returnValue($nombre));

        $resultat = $this->diposit->nombre_quinzenes($fct_id);

        $this->assertEquals($nombre, $resultat);
    }

    function test_quadern() {
        $this->moodle->expects($this->once())->method('get_record')
            ->with('fct_quadern', 'id', $this->quadern->id)
            ->will($this->returnValue($this->record_quadern));

        $quadern = $this->diposit->quadern($this->quadern->id);

        $this->assertEquals($this->quadern, $quadern);
    }

    function test_quinzena() {
        $this->moodle->expects($this->once())->method('get_record')
            ->with('fct_quinzena', 'id', $this->quinzena->id)
            ->will($this->returnValue($this->record_quinzena));

        $quinzena = $this->diposit->quinzena($this->quinzena->id);

        $this->assertEquals($this->quinzena, $quinzena);
    }

    function test_quinzenes() {
        $this->diposit = $this->getMock('fct_diposit', array('quinzena'),
                                        array($this->moodle));

        $where = 'quadern = ' . $this->quinzena->quadern;
        $record = (object) array('id' => $this->quinzena->id);
        $this->moodle->expects($this->once())->method('get_records_select')
            ->with('fct_quinzena', $where, 'any_, periode', 'id')
            ->will($this->returnValue(array($record)));

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
        $record = (object) array('id' => $this->quinzena->id);
        $this->moodle->expects($this->once())->method('get_records_select')
            ->with('fct_quinzena', $where, 'any_, periode', 'id')
            ->will($this->returnValue(array($record)));

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
            ->with('fct_activitat', 'id', $this->activitat->id);

        $this->diposit->suprimir_activitat($this->activitat);

        $this->assertFalse($this->activitat->id);
    }

    function test_suprimir_cicle() {
        $this->moodle->expects($this->once())->method('delete_records')
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
        $this->moodle->expects($this->once())
            ->method('delete_records')
            ->with('fct_quadern', 'id', $this->quadern->id);

        $this->diposit->suprimir_quadern($this->quadern);

        $this->assertFalse($this->quadern->id);
    }

    function test_suprimir_quinzena() {
        $this->moodle->expects($this->once())->method('delete_records')
            ->with('fct_quinzena', 'id', $this->quinzena->id);

        $this->diposit->suprimir_quinzena($this->quinzena);

        $this->assertFalse($this->quinzena->id);
    }

    function test_usuari() {
        $index = 0;

        $this->moodle->expects($this->at($index++))->method('get_record')
            ->with('user', 'id', $this->usuari->id)
            ->will($this->returnValue($this->record_user));

        $this->moodle->expects($this->at($index++))
            ->method('get_coursemodule_from_instance')
            ->with('fct', $this->record_fct->id)
            ->will($this->returnValue($this->cm));

        $this->moodle->expects($this->at($index++))
            ->method('get_context_instance')
            ->with(CONTEXT_MODULE, $this->cm->id)
            ->will($this->returnValue($this->context));

        $caps = array(1 => 'admin', 2 => 'alumne',
                      3 => 'tutor_centre', 4 => 'tutor_empresa');
        foreach ($caps as $cap) {
            $this->moodle->expects($this->at($index++))->method('has_capability')
                ->with("mod/fct:$cap", $this->context, $this->usuari->id)
                ->will($this->returnValue(True));
        }

        $usuari = $this->diposit->usuari($this->fct, $this->usuari->id);

        $this->assertEquals($this->usuari, $usuari);
    }


}
