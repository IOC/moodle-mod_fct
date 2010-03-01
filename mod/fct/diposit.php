<?php

class fct_diposit {

    var $moodle;

    function __construct($moodle=false) {
        $this->moodle = $moodle ? $moodle : new fct_moodle;
    }

    function activitat($id) {
        $record = $this->moodle->get_record('fct_activitat', 'id', $id);
        return fct_json::deserialitzar_activitat($record->objecte);
    }

    function activitats($quadern_id, $descripcio=false) {
        $activitats = array();
        $records = $this->moodle->get_records('fct_activitat', 'quadern',
                                              $quadern_id, 'id', 'id');
        foreach ($records as $record) {
            $activitat = $this->activitat($record->id);
            if (!$descripcio or $activitat->descripcio == $descripcio) {
                $activitats[] = $activitat;
            }
        }

        usort($activitats, array('fct_activitat', 'cmp'));
        return $activitats;
    }

    function afegir_activitat($activitat) {
        $record = (object) array('quadern' => $activitat->quadern);
        if (!$activitat->id) {
            $activitat->id = $this->moodle->insert_record('fct_activitat',
                                                          $record);
        }
        $record->id = $activitat->id;
        $record->objecte = fct_json::serialitzar_activitat($activitat);
        $this->moodle->update_record('fct_activitat', $record);
    }

    function afegir_avis($avis) {
        $record = (object) array('quadern' => $avis->quadern,
                                 'data' => $avis->data);
        if (!$avis->id) {
            $avis->id = $this->moodle->insert_record('fct_avis', $record);
        }
        $record->id = $avis->id;
        $record->objecte = fct_json::serialitzar_cicle($avis);
        $this->moodle->update_record('fct_avis', $record);
    }

    function afegir_cicle($cicle) {
        $record = (object) array('fct' => $cicle->fct,
                                 'nom' => $cicle->nom);
        if (!$cicle->id) {
            $cicle->id = $this->moodle->insert_record('fct_cicle', $record);
        }

        $record->id = $cicle->id;
        $record->objecte = fct_json::serialitzar_cicle($cicle);
        $this->moodle->update_record('fct_cicle', $record);
    }

    function afegir_fct($fct) {
        $record = (object) array(
            'course' => $fct->course,
            'name' => $fct->name,
            'intro' => $fct->intro,
            'timecreated' => $fct->timecreated,
            'timemodified' => $fct->timemodified,
        );

        if (!$fct->id) {
            $fct->id = $this->moodle->insert_record('fct', $record);
        }

        $record->id = $fct->id;
        $record->objecte = fct_json::serialitzar_fct($fct);
        $this->moodle->update_record('fct', $record);
    }

    function afegir_quadern($quadern) {
        $record = (object) array(
            'alumne' => $quadern->alumne,
            'tutor_centre' => $quadern->tutor_centre,
            'tutor_empresa' => $quadern->tutor_empresa,
            'nom_empresa' => $quadern->empresa->nom,
            'cicle' => $quadern->cicle,
            'estat' => $quadern->estat,
            'data_final' => $quadern->data_final(),
        );

        if (!$quadern->id) {
            $quadern->id = $this->moodle->insert_record('fct_quadern',
                                                        $record);
        }
        $record->id = $quadern->id;
        $record->objecte = fct_json::serialitzar_quadern($quadern);
        $this->moodle->update_record('fct_quadern', $record);
    }

    function afegir_quinzena($quinzena) {
        $record = (object) array(
            'quadern' => $quinzena->quadern,
            'any_' => $quinzena->any,
            'periode' => $quinzena->periode,
        );

        if (!$quinzena->id) {
            $quinzena->id = $this->moodle->insert_record('fct_quinzena',
                                                         $record);
        }

        $record->id = $quinzena->id;
        $record->objecte = fct_json::serialitzar_quinzena($quinzena);
        $this->moodle->update_record('fct_quinzena', $record);
    }

    function avis($id) {
        $record = $this->moodle->get_record('fct_avis', 'id', $id);
        $avis = fct_json::deserialitzar_avis($record->objecte);
        return $avis;
    }

    function avisos_quadern($quadern_id, $limitfrom='', $limitnum='') {
        $records = $this->moodle->get_records('fct_avis',
                                              'quadern', $quadern_id,
                                              'data', 'id, objecte',
                                              $limitfrom, $limitnum);
        return $this->avisos_records($records);
    }

    function avisos_usuari($usuari, $limitfrom='', $limitnum='') {
        global $CFG;

        $sql = "SELECT a.id, a.objecte"
            . " FROM {$CFG->prefix}fct_cicle c"
            . " JOIN {$CFG->prefix}fct_quadern q ON q.cicle = c.id"
            . " JOIN {$CFG->prefix}fct_avis a ON a.quadern = q.id"
            . " WHERE c.fct = {$usuari->fct}"
            . " AND q.tutor_centre = {$usuari->id}"
            . " ORDER BY a.data";

        $records = $this->moodle->get_records_sql($sql, $limitfrom, $limitnum);
        return $this->avisos_records($records);
    }

    function cicle($id) {
        $record = $this->moodle->get_record('fct_cicle', 'id', $id);
        $cicle = fct_json::deserialitzar_cicle($record->objecte);
        $cicle->n_quaderns = $this->moodle->count_records('fct_quadern',
                                                          'cicle', $id);
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

    function fct($id) {
        $record = $this->moodle->get_record('fct', 'id', $id);
        $fct = fct_json::deserialitzar_fct($record->objecte);
        $fct->id = $record->id;
        $fct->course = $record->course;
        return $fct;
    }

    function min_max_data_final_quaderns($fct_id) {
        global $CFG;

        $sql = "SELECT MIN(q.data_final) AS min_data_final,"
            . " MAX(q.data_final) AS max_data_final"
            . " FROM {$CFG->prefix}fct_quadern q"
            . " JOIN {$CFG->prefix}fct_cicle c ON c.id = q.cicle"
            . " WHERE c.fct = $fct_id AND q.data_final > 0";

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

    function nombre_quaderns($especificacio=false) {
        global $CFG;

        if ($especificacio) {
            $sql = 'SELECT COUNT(*)'
                . ' FROM ' . $this->_tables_quaderns($especificacio)
                . ' WHERE ' . $this->_select_quaderns($especificacio);
            return $this->moodle->count_records_sql($sql);
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

    function noms_empreses($cicle) {
        $noms = array();
        $records = $this->moodle->get_records('fct_quadern', 'cicle', $cicle,
                                              'nom_empresa', 'DISTINCT nom_empresa');
        foreach ($records as $record) {
            $noms[] = $record->nom_empresa;
        }
        return $noms;
    }

    function quadern($id) {
        $record = $this->moodle->get_record('fct_quadern', 'id', $id);
        return fct_json::deserialitzar_quadern($record->objecte);
    }

    function quaderns($especificacio, $ordenacio=false,
                      $limitfrom=false, $limitnum=false) {
        global $CFG;
        $sql = "SELECT q.id AS id,"
            . " CONCAT(ua.firstname, ' ', ua.lastname) AS alumne,"
            . " q.nom_empresa AS empresa,"
            . " c.nom AS cicle_formatiu,"
            . " CONCAT(uc.firstname, ' ', uc.lastname) AS tutor_centre,"
            . " CONCAT(ue.firstname, ' ', ue.lastname) AS tutor_empresa,"
            . " q.estat AS estat,"
            . " q.data_final AS data_final"
            . ' FROM ' . $this->_tables_quaderns($especificacio)
            . ' WHERE ' . $this->_select_quaderns($especificacio);
        if ($ordenacio) {
            $sql .= " ORDER BY $ordenacio";
        }
        $quaderns = array();
        $records = $this->moodle->get_records_sql($sql, $limitfrom, $limitnum);
        foreach ($records as $record) {
            $quaderns[] = $this->quadern($record->id);
        }
        return $quaderns;
    }

    function quinzena($id) {
        $record = $this->moodle->get_record('fct_quinzena', 'id', $id);
        return fct_json::deserialitzar_quinzena($record->objecte);
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

        $records = $this->moodle->get_records_select('fct_quinzena', $where,
                                                     'any_, periode', 'id');
        foreach ($records as $record) {
            $quinzenes[] = $this->quinzena($record->id);
        }

        return $quinzenes;
    }

    function suprimir_activitat($activitat) {
        $this->moodle->delete_records('fct_activitat', 'id', $activitat->id);
        $activitat->id = false;
    }

    function suprimir_avis($avis) {
        $this->moodle->delete_records('fct_avis', 'id', $avis->id);
        $avis->id = false;
    }

    function suprimir_cicle($cicle) {
        $this->moodle->delete_records('fct_cicle', 'id', $cicle->id);
        $cicle->id = false;
    }

    function suprimir_fct($fct) {
        $this->moodle->delete_records('fct', 'id', $fct->id);
        $fct->id = false;
    }

    function suprimir_quadern($quadern) {
        $this->moodle->delete_records('fct_quadern', 'id', $quadern->id);
        $quadern->id = false;
    }

    function suprimir_quinzena($quinzena) {
        $this->moodle->delete_records('fct_quinzena', 'id', $quinzena->id);
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

        $cm = $this->moodle->get_coursemodule_from_instance('fct', $fct->id);
        $context = $this->moodle->get_context_instance(CONTEXT_MODULE, $cm->id);

        $usuari->es_administrador = $this->moodle->has_capability(
            "mod/fct:admin", $context, $userid);
        $usuari->es_alumne = $this->moodle->has_capability(
            "mod/fct:alumne", $context, $userid);
        $usuari->es_tutor_centre = $this->moodle->has_capability(
            "mod/fct:tutor_centre", $context, $userid);
        $usuari->es_tutor_empresa = $this->moodle->has_capability(
            "mod/fct:tutor_empresa", $context, $userid);

        return $usuari;
    }

    function _select_quaderns($especificacio) {
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
            $select[] = "q.data_final >= $especificacio->data_final_min";
        }
        if ($especificacio->data_final_max !== false) {
            $select[] = "q.data_final <= $especificacio->data_final_max";
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
            $select[] = "q.nom_empresa = '"
                . addslashes($especificacio->empresa) . "'";
        }
        return implode(' AND ', $select);
    }

    function _tables_quaderns($especificacio) {
        global $CFG;
        $tables = "{$CFG->prefix}fct_quadern q"
            . " JOIN {$CFG->prefix}fct_cicle c ON q.cicle = c.id"
            . " JOIN {$CFG->prefix}user ua ON q.alumne = ua.id"
            . " LEFT JOIN {$CFG->prefix}user uc ON q.tutor_centre = uc.id"
            . " LEFT JOIN {$CFG->prefix}user ue ON q.tutor_empresa = ue.id";
        return $tables;
    }

    private function avisos_records($records) {
        $avisos = array();
        foreach ($records as $record) {
            $avisos[] = fct_json::deserialitzar_avis($record->objecte);
        }
        return $avisos;
    }

}
