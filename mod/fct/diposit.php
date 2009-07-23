<?php

class fct_diposit {

    var $moodle;

    function __construct($moodle=false) {
        $this->moodle = $moodle ? $moodle : new fct_moodle;
    }

    function activitat($id) {
        $activitat = new fct_activitat;
        $record = $this->moodle->get_record('fct_activitat_pla', 'id', $id);
        fct_copy_vars($record, $activitat);
        return $activitat;
    }

    function activitats($quadern_id, $descripcio=false) {
        $activitats = array();
        $select = "quadern = {$quadern_id}";
        if ($descripcio) {
            $select .= " AND descripcio = '" . addslashes($descripcio) . "'";
        }

        $records = $this->moodle->get_records_select(
            'fct_activitat_pla', $select, 'descripcio', 'id');
        foreach ($records as $record) {
            $activitats[] = $this->activitat($record->id);
        }
        return $activitats;
    }

    function afegir_activitat($activitat) {
        $record = new stdClass;
        fct_copy_vars($activitat, $record,
                      array('quadern', 'descripcio', 'nota'));
        if ($activitat->id) {
            $record->id = $activitat->id;
            $this->moodle->update_record('fct_activitat_pla', $record);
        } else {
            $activitat->id = $this->moodle->insert_record('fct_activitat_pla',
                                                          $record);
        }
    }

    function afegir_cicle($cicle) {
        $record = (object) array('fct' => $cicle->fct,
                                 'nom' => $cicle->nom);
        if ($cicle->id) {
            $record->id = $cicle->id;
            $this->moodle->update_record('fct_cicle', $record);
            $this->moodle->delete_records('fct_activitat_cicle',
                                          'cicle', $cicle->id);
        } else {
            $cicle->id = $this->moodle->insert_record('fct_cicle', $record);
        }
        foreach ($cicle->activitats as $activitat) {
            $record = (object) array('cicle' => $cicle->id,
                                     'descripcio' => $activitat);
            $this->moodle->insert_record('fct_activitat_cicle', $record);
        }
    }

    function afegir_fct($fct) {
        $record = (object) array('course' => $fct->course->id);
        fct_copy_vars($fct, $record,
                      array('name', 'intro', 'timecreated', 'timemodified',
                            'frases_centre', 'frases_empresa'));
        if ($fct->id) {
            $record->id = $fct->id;
            $this->moodle->update_record('fct', $record);
            $this->moodle->delete_records('fct_dades_centre', 'fct', $fct->id);
        } else {
            $fct->id = $this->moodle->insert_record('fct', $record);
        }

        $record = (object) array('fct' => $fct->id);
        fct_copy_vars($fct->centre, $record,
                      array('nom', 'adreca', 'codi_postal', 'poblacio',
                            'telefon', 'fax', 'email'));
        $this->moodle->insert_record('fct_dades_centre', $record);
    }

    function afegir_quadern($quadern) {
        if ($quadern->id) {
            $record = (object) array('nom_empresa' => $quadern->empresa->nom);
            fct_copy_vars($quadern, $record,
                          array('id', 'cicle', 'alumne', 'tutor_centre',
                                'tutor_empresa', 'estat'));
            $this->moodle->update_record('fct_quadern', $record);

            $fct_id = $this->moodle->get_field('fct_cicle', 'fct',
                                               'id', $quadern->cicle);
            $this->moodle->delete_records('fct_dades_alumne', 'fct', $fct_id,
                                          'alumne', $quadern->alumne);
            $record = (object) array('fct' => $fct_id,
                                     'alumne' => $quadern->alumne);
            fct_copy_vars($quadern->dades_alumne, $record,
                          array('adreca', 'poblacio', 'codi_postal', 'telefon',
                                'email', 'dni', 'targeta_sanitaria'));
            $this->moodle->insert_record('fct_dades_alumne', $record);

            $this->moodle->delete_records('fct_qualificacio_global',
                                          'cicle', $quadern->cicle,
                                          'alumne', $quadern->alumne);
            $record = (object) array('cicle' => $quadern->cicle,
                                     'alumne' => $quadern->alumne,
                                     'qualificacio'=> $quadern->qualificacio_global->apte);
            fct_copy_vars($quadern->qualificacio_global, $record,
                          array('nota', 'data', 'observacions'));
            $this->moodle->insert_record('fct_qualificacio_global', $record);

            $tables = array('fct_dades_empresa', 'fct_dades_relatives',
                            'fct_dades_conveni', 'fct_qualificacio_quadern',
                            'fct_valoracio_actituds');
            foreach ($tables as $table) {
                $this->moodle->delete_records($table, 'quadern', $quadern->id);
            }

            $records = $this->moodle->get_records('fct_conveni', 'quadern',
                                                  $quadern->id);
            foreach ($records as $record) {
                $this->moodle->delete_records('fct_horari', 'conveni',
                                              $record->id);
            }

            $this->moodle->delete_records('fct_conveni', 'quadern',
                                          $quadern->id);
        } else {
            $record = (object) array('nom_empresa' => $quadern->empresa->nom);
            fct_copy_vars($quadern, $record,
                          array('cicle', 'alumne', 'tutor_centre',
                                'tutor_empresa', 'estat'));
            $quadern->id = $this->moodle->insert_record('fct_quadern', $record);

            $fct_id = $this->moodle->get_field('fct_cicle', 'fct',
                                               'id', $quadern->cicle);
            $where = "fct = {$fct_id} AND alumne = {$quadern->alumne}";
            $records = $this->moodle->get_records_select('fct_dades_alumne', $where);
            if ($records) {
                $record = array_pop($records);
                fct_copy_vars($record, $quadern->dades_alumne);
            }

            $where = "cicle = {$quadern->cicle} AND alumne = {$quadern->alumne}";
            $records = $this->moodle->get_records_select('fct_qualificacio_global', $where);
            if ($records) {
                $record = array_pop($records);
                fct_copy_vars($record, $quadern->qualificacio_global);
                $quadern->qualificacio_global->apte = $record->qualificacio;
            }
        }

        $record = (object) array('quadern' => $quadern->id);
        fct_copy_vars($quadern->empresa, $record,
                      array('adreca', 'poblacio', 'codi_postal',
                            'telefon', 'fax', 'email', 'nif'));
        $this->moodle->insert_record('fct_dades_empresa', $record);

        $record = (object) array('quadern' => $quadern->id);
        fct_copy_vars($quadern, $record,
                      array('hores_credit', 'exempcio', 'hores_anteriors'));
        $this->moodle->insert_record('fct_dades_relatives', $record);

        $record = (object) array('quadern' => $quadern->id);
        fct_copy_vars($quadern, $record,
                      array('prorrogues', 'hores_practiques'));
        $this->moodle->insert_record('fct_dades_conveni', $record);

        $record = (object) array('quadern' => $quadern->id);
        fct_copy_vars($quadern->qualificacio, $record,
                      array('nota', 'data', 'observacions'));
        $record->qualificacio = $quadern->qualificacio->apte;
        $this->moodle->insert_record('fct_qualificacio_quadern', $record);

        foreach ($quadern->valoracio_parcial as $actitud => $nota) {
            $record = (object) array('quadern' => $quadern->id, 'final' => 0,
                                     'actitud' => $actitud, 'nota' => $nota);
            $this->moodle->insert_record('fct_valoracio_actituds', $record);
        }
        foreach ($quadern->valoracio_final as $actitud => $nota) {
            $record = (object) array('quadern' => $quadern->id, 'final' => 1,
                                     'actitud' => $actitud, 'nota' => $nota);
            $this->moodle->insert_record('fct_valoracio_actituds', $record);
        }

        foreach ($quadern->convenis as $conveni) {
            $record = (object) array('quadern' => $quadern->id);
            fct_copy_vars($conveni, $record,
                          array('codi', 'data_inici', 'data_final'));
            $id = $this->moodle->insert_record('fct_conveni', $record);
            $record = (object) array('conveni' => $id);
            fct_copy_vars($conveni->horari, $record,
                          array('dilluns', 'dimarts', 'dimecres', 'dijous',
                                'divendres', 'dissabte', 'diumenge'));
            $this->moodle->insert_record('fct_horari', $record);
        }
    }

    function afegir_quinzena($quinzena) {
        if ($quinzena->id) {
            $record = (object) array('any_' => $quinzena->any);
            fct_copy_vars($quinzena, $record,
                          array('id', 'quadern', 'periode', 'hores',
                                'valoracions', 'observacions_alumne',
                                'observacions_centre', 'observacions_empresa'));
            $this->moodle->update_record('fct_quinzena', $record);
            $this->moodle->delete_records('fct_dia_quinzena',
                                          'quinzena', $quinzena->id);
            $this->moodle->delete_records('fct_activitat_quinzena',
                                          'quinzena', $quinzena->id);
        } else {
            $record = (object) array('any_' => $quinzena->any);
            fct_copy_vars($quinzena, $record,
                          array('quadern', 'periode', 'hores',
                                'valoracions', 'observacions_alumne',
                                'observacions_centre', 'observacions_empresa'));
            $quinzena->id = $this->moodle->insert_record('fct_quinzena',
                                                         $record);
        }

        foreach ($quinzena->dies as $dia) {
            $record = (object) array('quinzena' => $quinzena->id,
                                     'dia' => $dia);
            $this->moodle->insert_record('fct_dia_quinzena', $record);
        }

        foreach ($quinzena->activitats as $activitat) {
            $record = (object) array('quinzena' => $quinzena->id,
                                     'activitat' => $activitat);
            $this->moodle->insert_record('fct_activitat_quinzena', $record);
        }
    }

    function cicle($id) {
        $cicle = new fct_cicle;
        $record = $this->moodle->get_record('fct_cicle', 'id', $id);
        fct_copy_vars($record, $cicle);
        $cicle->n_quaderns = $this->moodle->count_records('fct_quadern',
                                                          'cicle', $id);
        $records = $this->moodle->get_records('fct_activitat_cicle',
                                              'cicle', $id, 'descripcio');
        foreach ($records as $record) {
            $cicle->activitats[] = $record->descripcio;
        }
        return $cicle;
    }

    function cicles($fct_id, $nom=false) {
        $cicles = array();
        $select = "fct = {$fct_id}";
        if ($nom) {
            $select .= " AND nom = '" . addslashes($nom) . "'";
        }

        $records = $this->moodle->get_records_select('fct_cicle', $select,
                                                     'nom', 'id');
        foreach ($records as $record) {
            $cicles[] = $this->cicle($record->id);
        }
        return $cicles;
    }

    function empreses($cicles) {
        global $CFG;

        $empreses = array();

        if ($cicles) {
            $sql = "SELECT DISTINCT q.nom_empresa AS nom,"
                . " e.adreca, e.poblacio, e.codi_postal,"
                . " e.telefon, e.fax, e.email, e.nif"
                . " FROM {$CFG->prefix}fct_quadern q"
                . " JOIN {$CFG->prefix}fct_dades_empresa e ON q.id = e.quadern"
                . " WHERE q.cicle IN (" . implode(',', $cicles) . ")"
                . " ORDER BY q.nom_empresa";

            $records = $this->moodle->get_records_sql($sql);
            foreach ($records as $record) {
                $empresa = new fct_empresa;
                fct_copy_vars($record, $empresa);
                $empreses[] = $empresa;
            }
        }

        return $empreses;
    }

    function fct($id) {
        $fct = new fct;
        $record = $this->moodle->get_record('fct', 'id', $id);
        fct_copy_vars($record, $fct);
        $fct->cm = $this->moodle->get_coursemodule_from_instance('fct', $id);
        $fct->course = $this->moodle->get_record('course', 'id',
                                                 $record->course);
        $fct->context = $this->moodle->get_context_instance(CONTEXT_MODULE,
                                                            $fct->cm->id);
        $record = $this->moodle->get_record('fct_dades_centre', 'fct', $id);
        fct_copy_vars($record, $fct->centre);

        return $fct;
    }

    function fct_cm($cmid) {
        $cm = $this->moodle->get_coursemodule_from_id('fct', $cmid);
        return $this->fct($cm->instance);
    }

    function min_max_data_final_quaderns($fct_id) {
        global $CFG;

        $sql = "SELECT MIN(c.data_final) AS min_data_final,"
            . " MAX(c.data_final) AS max_data_final"
            . " FROM {$CFG->prefix}fct_conveni c"
            . " JOIN {$CFG->prefix}fct_quadern q ON c.quadern = q.id";

        $record = $this->moodle->get_record_sql($sql);

        return array($record->min_data_final, $record->max_data_final);
    }

    function nombre_cicles($fct_id=false) {
        if ($fct_id) {
            return $this->moodle->count_records('fct_cicle', 'fct', $fct_id);
        } else {
            return $this->moodle->count_records('fct_cicle');
        }
    }

    function nombre_quaderns($fct_id=false) {
        global $CFG;

        if ($fct_id) {
            $where = "cicle IN (SELECT id FROM {$CFG->prefix}fct_cicle"
                . " WHERE fct = $fct_id)";
            return $this->moodle->count_records_select('fct_quadern', $where);
        } else {
            return $this->moodle->count_records('fct_quadern');
        }
    }

    function nombre_quinzenes($fct_id=false) {
        global $CFG;

        if ($fct_id) {
            $where = "quadern IN (SELECT id FROM {$CFG->prefix}fct_quadern"
                . " WHERE cicle IN (SELECT id FROM {$CFG->prefix}fct_cicle"
                . " WHERE fct = $fct_id))";
            return $this->moodle->count_records_select('fct_quinzena', $where);
        } else {
            return $this->moodle->count_records('fct_quinzena');
        }
    }

    function quadern($id) {
        $quadern = new fct_quadern;

        $record = $this->moodle->get_record('fct_quadern', 'id', $id);
        fct_copy_vars($record, $quadern);

        $quadern->empresa->nom = $record->nom_empresa;

        $record = $this->moodle->get_record('fct_dades_empresa',
                                            'quadern', $id);
        fct_copy_vars($record, $quadern->empresa);

        $record = $this->moodle->get_record('fct_dades_relatives',
                                            'quadern', $id);
        fct_copy_vars($record, $quadern,
                      array('hores_credit', 'exempcio', 'hores_anteriors'));

        $record = $this->moodle->get_record('fct_dades_conveni',
                                            'quadern', $id);
        fct_copy_vars($record, $quadern,
                      array('prorrogues', 'hores_practiques'));

        $record = $this->moodle->get_record('fct_qualificacio_quadern',
                                            'quadern', $id);
        fct_copy_vars($record, $quadern->qualificacio);
        $quadern->qualificacio->apte = $record->qualificacio;

        $records = $this->moodle->get_records('fct_conveni', 'quadern',
                                              $id, 'data_inici');
        foreach ($records as $record) {
            $conveni = new fct_conveni;
            fct_copy_vars($record, $conveni);
            $record = $this->moodle->get_record('fct_horari', 'conveni',
                                                $record->id);
            fct_copy_vars($record, $conveni->horari);
            $quadern->convenis[] = $conveni;
        }

        $records = $this->moodle->get_records('fct_valoracio_actituds',
                                              'quadern', $id, 'actitud');
        foreach ($records as $record) {
            if ($record->final) {
                $quadern->valoracio_final[$record->actitud] = $record->nota;
            } else {
                $quadern->valoracio_parcial[$record->actitud] = $record->nota;
            }
        }

        $fct_id = $this->moodle->get_field('fct_cicle', 'fct',
                                           'id', $quadern->cicle);
        $where = "fct = {$fct_id} AND alumne = {$quadern->alumne}";
        $records = $this->moodle->get_records_select('fct_dades_alumne', $where);
        if ($records) {
            $record = array_pop($records);
            fct_copy_vars($record, $quadern->dades_alumne);
        }

        $where = "cicle = {$quadern->cicle} AND alumne = {$quadern->alumne}";
        $records = $this->moodle->get_records_select('fct_qualificacio_global', $where);
        if ($records) {
            $record = array_pop($records);
            fct_copy_vars($record, $quadern->qualificacio_global);
            $quadern->qualificacio_global->apte = $record->qualificacio;
        }

        return $quadern;
    }

    function quaderns($especificacio, $ordenacio=false) {
        global $CFG;
        $usuari = $especificacio->usuari;
        $select = array();
        if ($especificacio->fct !== false) {
            $select[] = 'c.fct = ' . $especificacio->fct;
        }
        if ($usuari !== false and !$usuari->es_administrador) {
            $select_usuari = array('FALSE');
            if ($usuari->es_alumne) {
                $select_usuari[] = 'q.alumne = ' . $usuari->id;
            }
            if ($usuari->es_tutor_centre) {
                $select_usuari[] = 'q.tutor_centre = ' .  $usuari->id;
            }
            if ($usuari->es_tutor_empresa) {
                $select_usuari[] = 'q.tutor_empresa = ' . $usuari->id;
            }
            $select[] = '(' . implode(' OR ', $select_usuari) . ')';
        }
        if ($especificacio->data_final_min !== false) {
            $select[] = "data_final >= $especificacio->data_final_min";
        }
        if ($especificacio->data_final_max !== false) {
            $select[] = "data_final <= $especificacio->data_final_max";
        }
        if ($especificacio->cicle !== false) {
            $select[] = "q.cicle = $especificacio->cicle";
        }
        if ($especificacio->estat !== false) {
            $select[] = "q.estat = $especificacio->estat";
        }
        if ($especificacio->cerca !== false) {
            $fields = array("CONCAT(ua.firstname, ' ', ua.lastname)",
                            "q.nom_empresa",
                            "CONCAT(uc.firstname, ' ', uc.lastname)",
                            "CONCAT(ue.firstname, ' ', ue.lastname)");
            $select_cerca = array();
            foreach ($fields as $field) {
                $select_cerca[] = "$field LIKE '%"
                    . addslashes($especificacio->cerca) . "%'";
            }
            $select[] = '(' . implode(' OR ', $select_cerca) . ')';
        }
        if ($especificacio->alumne !== false) {
            $select[] = "q.alumne = $especificacio->alumne";
        }
        if ($especificacio->empresa !== false) {
            $select[] = "q.nom_empresa = '$especificacio->empresa'";
        }
        $where = ' WHERE ' . implode(' AND ', $select);
        $sql = "SELECT q.id AS id,"
            . " CONCAT(ua.firstname, ' ', ua.lastname) AS alumne,"
            . " q.nom_empresa AS empresa,"
            . " c.nom AS cicle_formatiu,"
            . " CONCAT(uc.firstname, ' ', uc.lastname) AS tutor_centre,"
            . " CONCAT(ue.firstname, ' ', ue.lastname) AS tutor_empresa,"
            . " q.estat, MAX(dc.data_final) AS data_final"
            . " FROM {$CFG->prefix}fct_quadern q"
            . " JOIN {$CFG->prefix}fct_cicle c ON q.cicle = c.id"
            . " JOIN {$CFG->prefix}user ua ON q.alumne = ua.id"
            . " LEFT JOIN {$CFG->prefix}user uc ON q.tutor_centre = uc.id"
            . " LEFT JOIN {$CFG->prefix}user ue ON q.tutor_empresa = ue.id"
            . " LEFT JOIN {$CFG->prefix}fct_conveni dc ON q.id = dc.quadern"
            . $where
            . " GROUP BY q.id, alumne, empresa, cicle_formatiu,"
            . " tutor_centre, tutor_empresa, estat";

        if ($ordenacio) {
            $sql .= " ORDER BY $ordenacio";
        }
        $quaderns = array();
        $records = $this->moodle->get_records_sql($sql);
        foreach ($records as $record) {
            $quaderns[] = $this->quadern($record->id);
        }
        return $quaderns;
    }

    function quinzena($id) {
        $quinzena = new fct_quinzena;

        $record = $this->moodle->get_record('fct_quinzena', 'id', $id);
        fct_copy_vars($record, $quinzena);
        $quinzena->any = $record->any_;

        $records = $this->moodle->get_records('fct_dia_quinzena',
                                              'quinzena', $id, 'dia');
        foreach ($records as $record) {
            $quinzena->dies[] = $record->dia;
        }

        $records = $this->moodle->get_records('fct_activitat_quinzena',
                                              'quinzena', $id, 'activitat');
        foreach ($records as $record) {
            $quinzena->activitats[] = $record->activitat;
        }

        return $quinzena;
    }

    function quinzenes($quadern_id, $any=false, $periode=false) {
        $quinzenes = array();

        $where = 'quadern = ' . $quadern_id;
        if ($any) {
            $where .= ' AND any_ = ' . $any;
        }
        if ($periode) {
            $where .= ' AND periode = ' . $periode;
        }

        $records = $this->moodle->get_records_select('fct_quinzena', $where);
        foreach ($records as $record) {
            $quinzenes[] = $this->quinzena($record->id);
        }

        return $quinzenes;
    }

    function suprimir_activitat($activitat) {
        $this->moodle->delete_records('fct_activitat_pla', 'id',
                                      $activitat->id);
        $activitat->id = false;
    }

    function suprimir_cicle($cicle) {
        $this->moodle->delete_records('fct_activitat_cicle',
                                      'cicle', $cicle->id);
        $this->moodle->delete_records('fct_cicle', 'id', $cicle->id);
        $cicle->id = false;
    }

    function suprimir_fct($fct) {
        $this->moodle->delete_records('fct', 'id', $fct->id);
        $fct->id = false;
    }

    function suprimir_quadern($quadern) {
        $tables = array('fct_dades_empresa', 'fct_dades_relatives',
                        'fct_dades_conveni', 'fct_qualificacio_quadern',
                        'fct_valoracio_actituds');
        foreach ($tables as $table) {
            $this->moodle->delete_records($table, 'quadern', $quadern->id);
        }

        $records = $this->moodle->get_records('fct_conveni', 'quadern',
                                              $quadern->id);
        foreach ($records as $record) {
            $this->moodle->delete_records('fct_horari', 'conveni', $record->id);
        }
        $this->moodle->delete_records('fct_conveni', 'quadern', $quadern->id);

        $this->moodle->delete_records('fct_quadern', 'id', $quadern->id);
        $quadern->id = false;
    }

    function suprimir_quinzena($quinzena) {
        $this->moodle->delete_records('fct_quinzena', 'id', $quinzena->id);
        $this->moodle->delete_records('fct_dia_quinzena',
                                      'quinzena', $quinzena->id);
        $this->moodle->delete_records('fct_activitat_quinzena',
                                      'quinzena', $quinzena->id);
        $quinzena->id = false;
    }

    function usuari($fct, $userid) {
        $usuari = new fct_usuari;
        $record = $this->moodle->get_record('user', 'id', $userid);
        $usuari->id = $record->id;
        $usuari->fct = $fct->id;
        $usuari->nom = $record->firstname;
        $usuari->cognoms = $record->lastname;
        $usuari->email = $record->email;
        $usuari->es_administrador = $this->moodle->has_capability(
            "mod/fct:admin", $fct->context, $userid);
        $usuari->es_alumne = $this->moodle->has_capability(
            "mod/fct:alumne", $fct->context, $userid);
        $usuari->es_tutor_centre = $this->moodle->has_capability(
            "mod/fct:tutor_centre", $fct->context, $userid);
        $usuari->es_tutor_empresa = $this->moodle->has_capability(
            "mod/fct:tutor_empresa", $fct->context, $userid);
        return $usuari;
    }
}
