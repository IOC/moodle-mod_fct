<?php

class fct_db
{

// FCT

    function actualitzar_fct($fct) {
        $fct->timemodified = time();
        $fct->id = $fct->instance;

        return update_record('fct', $fct);
    }

    function afegir_fct($fct) {
        $ok = true;

        $fct->timecreated = $fct->timemodified = time();

        $ok = $ok && ($fct->id = insert_record('fct', $fct));
        $ok = $ok && self::afegir_dades_centre($fct->id);

        if (!$ok) {
            self::suprimir_fct($fct->id);
            return false;
        }

        return $fct->id;
    }

    function suprimir_fct($id) {
        if (!record_exists('fct', 'id', $id)) {
            return false;
        }

        $ok = true;

        $ok = delete_records('fct', 'id', $id) && $ok;
        $ok = self::suprimir_dades_centre($id) && $ok;
        $ok = self::suprimir_cicles($id) && $ok;
        $ok = self::suprimir_quaderns($id) && $ok;
        $ok = self::suprimir_dades_alumnes($id) && $ok;
        $ok = self::suprimir_dades_relatives($id) && $ok;
        $ok = self::suprimir_qualificacions_global($id) && $ok;

        return $ok;
    }

// Dades centre

    function actualitzar_dades_centre($data) {
        return update_record('fct_dades_centre', $data);
    }

    function afegir_dades_centre($fct_id) {
        $centre = (object) array('fct' => $fct_id);
        return insert_record('fct_dades_centre', $centre);
    }

    function dades_centre($fct_id) {
        return get_record('fct_dades_centre', 'fct', $fct_id);
    }

    function suprimir_dades_centre($fct_id) {
        return delete_records('fct_dades_centre', 'fct', $fct_id);
    }

// Cicles

    function activitat_cicle($activitat_id) {
        return get_record('fct_activitat_cicle', 'id', $activitat_id);
    }

    function activitat_cicle_duplicada($cicle_id, $descripcio, $activitat_id=false) {
        $select = "cicle = '$cicle_id' AND descripcio = '$descripcio'";
        if ($activitat_id) {
            $select .= " AND id != '$activitat_id'";
        }
        return record_exists_select('fct_activitat_cicle', $select);
    }

    function activitats_cicle($cicle_id) {
        return get_records('fct_activitat_cicle',
                           'cicle', $cicle_id, 'descripcio');
    }

    function actualitzar_activitat_cicle($activitat) {
        return update_record('fct_activitat_cicle', $activitat);
    }

    function actualitzar_cicle($cicle) {
        return update_record('fct_cicle', $cicle);
    }

    function afegir_activitat_cicle($cicle_id, $descripcio) {
        $activitat = (object) array(
            'cicle' => $cicle_id,
            'descripcio' => $descripcio);
        return insert_record('fct_activitat_cicle', $activitat);
    }

    function afegir_cicle($fct_id, $nom, $activitats='') {
        $ok = true;

        $cicle = (object) array('fct' => $fct_id, 'nom' => $nom);
        $ok = $ok && ($id = insert_record('fct_cicle', $cicle));

        $activitats = explode("\n", $activitats);
        foreach ($activitats as $activitat) {
            if (trim($activitat)) {
                $ok = $ok && self::afegir_activitat_cicle($id, $activitat);
            }
        }

        if ($ok) {
            return $id;
        } else {
            self::suprimir_cicle($id);
            return false;
        }
    }

    function cicle($cicle_id) {
        return get_record('fct_cicle', 'id', $cicle_id);
    }

    function cicle_duplicat($fct_id, $nom, $cicle_id=false) {
        $select = "fct = '$fct_id' AND nom = '$nom'";
        if ($cicle_id) {
            $select .= " AND id != '$cicle_id'";
        }
        return record_exists_select('fct_cicle', $select);
    }

    function cicles($fct_id) {
        return get_records('fct_cicle', 'fct', $fct_id, 'nom');
    }

    function nombre_cicles($fct_id=false) {
        if ($fct_id) {
            return count_records('fct_cicle', 'fct', $fct_id);
        } else {
            return count_records('fct_cicle');
        }
    }

    function suprimir_activitat_cicle($activitat_id){
        return delete_records('fct_activitat_cicle', 'id', $activitat_id);
    }

    function suprimir_cicle($cicle_id) {
        $ok = true;
        $ok = delete_records('fct_cicle', 'id', $cicle_id) && $ok;
        $ok = delete_records('fct_activitat_cicle', 'cicle', $cicle_id) && $ok;
        return $ok;
    }

    function suprimir_cicles($fct_id) {
        $cicles = self::cicles($fct_id);
        $ok = true;
        if ($cicles) {
            foreach ($cicles as $cicle) {
                $ok = self::suprimir_cicle($cicle->id) && $ok;
            }
        }
        return $ok;
    }

// Quaderns

    function actualitzar_quadern($quadern) {
        return update_record('fct_quadern', $quadern);
    }

    function afegir_quadern($quadern) {
        $ok = true;

        $ok = $ok && ($id = insert_record('fct_quadern', $quadern));
        $ok = $ok && self::afegir_dades_centre_concertat($id);
        $ok = $ok && self::afegir_dades_empresa($id);
        $ok = $ok && self::afegir_dades_conveni($id);
        $ok = $ok && self::afegir_dades_horari($id);
        $ok = $ok && self::afegir_qualificacio_quadern($id);

        if (!$ok) {
            self::suprimir_quadern($id);
            return false;
        }

        return $id;
    }

    function nombre_quaderns($fct_id=false, $select=false) {
        global $CFG;

        $sql = "SELECT COUNT(*)"
            . " FROM {$CFG->prefix}fct_quadern q";

        $where = array();
        if ($fct_id) {
            $where[] = "q.fct = $fct_id";
        }
        if ($select) {
            $where[] =  "$select";
        }
        if ($where)  {
            $sql .= " WHERE " . implode(" AND ", $where);
        }

       return count_records_sql($sql);
    }

    function quadern_duplicat($fct_id, $alumne_id, $nom_empresa, $quadern_id=false) {
        $select = "fct = '$fct_id' AND alumne = '$alumne_id' AND nom_empresa = '$nom_empresa'";
        if ($quadern_id) {
            $select .= " AND id != '$quadern_id'";
        }
        return record_exists_select('fct_quadern', $select);
    }

    function quadern($quadern_id) {
        return get_record('fct_quadern', 'id', $quadern_id);
    }

    function quaderns($fct_id, $select=false, $order=false) {
        global $CFG;

        $sql = "SELECT q.id,"
            . " CONCAT(ua.firstname, ' ', ua.lastname) AS alumne,"
            . " q.nom_empresa AS empresa,"
            . " CONCAT(uc.firstname, ' ', uc.lastname) AS tutor_centre,"
            . " CONCAT(ue.firstname, ' ', ue.lastname) AS tutor_empresa,"
            . " q.estat, c.data_final"
            . " FROM {$CFG->prefix}fct_quadern q"
            . " JOIN {$CFG->prefix}user ua ON q.alumne = ua.id"
            . " LEFT JOIN {$CFG->prefix}user uc ON q.tutor_centre = uc.id"
            . " LEFT JOIN {$CFG->prefix}user ue ON q.tutor_empresa = ue.id"
            . " JOIN {$CFG->prefix}fct_dades_conveni c ON q.id = c.quadern"
            . " WHERE q.fct = '$fct_id'";
        if ($select) {
            $sql .= " AND ($select)";
        }
        if ($order) {
            $sql .= " ORDER BY $order";
        }

        return get_records_sql($sql);
    }

    function suprimir_quadern($id) {
        if (!$id or !record_exists('fct_quadern', 'id', $id)) {
            return false;
        }

        $ok = true;

        $ok = delete_records('fct_quadern', 'id', $id) && $ok;
        $ok = self::suprimir_activitats_pla($id) && $ok;
        $ok = self::suprimir_quinzenes($id) && $ok;
        $ok = self::suprimir_valoracions_actituds($id) && $ok;
        $ok = self::suprimir_dades_centre_concertat($id) && $ok;
        $ok = self::suprimir_dades_empresa($id) && $ok;
        $ok = self::suprimir_dades_conveni($id) && $ok;
        $ok = self::suprimir_dades_horari($id) && $ok;
        $ok = self::suprimir_qualificacio_quadern($id) && $ok;

        return $ok;
    }

    function suprimir_quaderns($id) {
        $quaderns = self::quaderns($id, false, false);
        $ok = true;
        if ($quaderns) {
            foreach ($quaderns as $quadern) {
                $ok = self::suprimir_quadern($quadern->id) && $ok;
            }
        }
        return $ok;
    }


// Pla d'activitats

    function activitat_pla($activitat_id) {
        return get_record('fct_activitat_pla', 'id', $activitat_id);
    }

    function activitats_pla($quadern_id) {
        return get_records('fct_activitat_pla', 'quadern', $quadern_id, 'descripcio');
    }

    function activitat_pla_duplicada($quadern_id, $descripcio, $activitat_id=false) {
        $select = "quadern = '$quadern_id' AND descripcio = '$descripcio'";
        if ($activitat_id) {
            $select .= " AND id != '$activitat_id'";
        }
        return record_exists_select('fct_activitat_pla', $select);
    }

    function actualitzar_activitat_pla($activitat) {
        return update_record('fct_activitat_pla', $activitat);
    }

    function actualitzar_notes_activitats_pla($notes) {
        $ok = true;
        if ($notes) {
            foreach ($notes as $id => $nota) {
                $ok = set_field('fct_activitat_pla', 'nota', $nota, 'id', $id) && $ok;
            }
        }
        return $ok;
    }

    function afegir_activitat_pla($quadern_id, $descripcio) {
        $activitat = (object) array('quadern' => $quadern_id, 'descripcio' => $descripcio);
        return insert_record('fct_activitat_pla', $activitat);
    }

    function afegir_activitats_cicle_pla($quadern_id, $activitats) {
        $ok = true;
        foreach ($activitats as $id => $afegir) {
            if ($afegir) {
                $descripcio = get_field('fct_activitat_cicle', 'descripcio', 'id', $id);
                $ok = $ok && !empty($descripcio);
                $ok = $ok && self::afegir_activitat_pla($quadern_id,$descripcio);
            }
        }
        return $ok;
    }

    function notes_activitats_pla($quadern_id) {
        $notes = array();
        $activitats = get_records('fct_activitat_pla', 'quadern', $quadern_id);
        if ($activitats) {
            foreach ($activitats as $activitat) {
                $notes[$activitat->id] = $activitat->nota;
            }
        }
        return $notes;
    }

    function suprimir_activitat_pla($activitat_id){
        $ok = true;
        $ok = delete_records('fct_activitat_pla', 'id', $activitat_id) && $ok;
        $ok = delete_records('fct_activitat_quinzena', 'activitat', $activitat_id) && $ok;
        return $ok;
    }

    function suprimir_activitats_pla($quadern_id){
        $ok = true;
        $activitats = self::activitats_pla($quadern_id);
        if ($activitats) {
            foreach ($activitats as $activitat) {
                $ok = self::suprimir_activitat_pla($activitat->id) && $ok;
            }
        }
        return $ok;
    }

// Seguiment quinzenal

    function activitats_quinzena($quinzena_id) {
        $activitats = array();
        $records = get_records('fct_activitat_quinzena', 'quinzena', $quinzena_id);
        if ($records) {
            foreach ($records as $record) {
                $activitats[$record->activitat] = 1;
            }
        }
        return $activitats;
    }

    function actualitzar_quinzena($quinzena, $dies=false, $activitats=false) {
        $ok = true;
        $ok = update_record('fct_quinzena', $quinzena) && $ok;
        if ($dies !== false) {
            $ok = delete_records('fct_dia_quinzena', 'quinzena', $quinzena->id) && $ok;
            $ok = self::afegir_dies_quinzena($quinzena->id, $dies) && $ok;
        }
        if ($activitats !== false) {
            $ok = delete_records('fct_activitat_quinzena', 'quinzena', $quinzena->id) && $ok;
            $ok = self::afegir_activitats_quinzena($quinzena->id, $activitats) && $ok;
        }
        return $ok;
    }

    function afegir_activitats_quinzena($quinzena_id, $activitats) {
        $ok = true;

        if ($activitats) {
            foreach ($activitats as $activitat => $seleccionada) {
                if ($seleccionada) {
                    $record = (object) array(
                    	'quinzena' => $quinzena_id,
                    	'activitat' => $activitat,
                    );
                    $ok = insert_record('fct_activitat_quinzena', $record) && $ok;
                }
            }
        }

        return $ok;
    }

    function afegir_dies_quinzena($quinzena_id, $dies) {
        $ok = true;

        if ($dies) {
            foreach ($dies as $dia => $seleccionat) {
                if ($seleccionat) {
                    $record = (object) array(
                        'quinzena' => $quinzena_id,
                        'dia' => $dia,
                    );
                    $ok = insert_record('fct_dia_quinzena', $record) && $ok;
                }
            }
        }

        return $ok;
    }

    function afegir_quinzena($quinzena, $dies, $activitats) {
        $id = insert_record('fct_quinzena', $quinzena);
        if (!self::afegir_dies_quinzena($id, $dies)) {
            return false;
        }
        if (!self::afegir_activitats_quinzena($id, $activitats)) {
            return false;
        }
        return $id;
    }

    function dies_quinzena($quinzena_id) {
        $dies = array();
        $records = get_records('fct_dia_quinzena', 'quinzena', $quinzena_id);
        if ($records) {
            foreach ($records as $record) {
                $dies[$record->dia] = 1;
            }
        }
        return $dies;
    }

    function nombre_quinzenes($fct_id=false) {
        global $CFG;
        if ($fct_id) {
            $sql = "SELECT COUNT(*) FROM {$CFG->prefix}fct_quinzena qi"
                . " JOIN {$CFG->prefix}fct_quadern qa ON qa.id = qi.quadern"
                . " WHERE qa.fct = $fct_id";
            return count_records_sql($sql);
        } else {
            return count_records('fct_quinzena');
        }
    }

    function quinzena($quinzena_id) {
        $record = get_record('fct_quinzena', 'id', $quinzena_id);
        if ($record) {
            $record->dies = count_records('fct_dia_quinzena', 'quinzena',
                $quinzena_id);
        }
        return $record;
    }

    function quinzena_duplicada($quadern_id, $any, $periode, $quinzena_id=false) {
        $select = "quadern = '$quadern_id' AND any_ = '$any' AND periode = '$periode'";
        if ($quinzena_id) {
            $select .= " AND id != '$quinzena_id'";
        }
        return record_exists_select('fct_quinzena', $select);
    }

    function quinzenes($quadern_id) {
        global $CFG;
        $sql = 'SELECT q.*, COUNT(d.id) AS dies '
             . "FROM {$CFG->prefix}fct_quinzena q "
             . "LEFT JOIN {$CFG->prefix}fct_dia_quinzena d "
             . '          ON q.id = d.quinzena '
             . "WHERE q.quadern = '$quadern_id' "
             . 'GROUP BY q.id '
             . 'ORDER BY q.any_, q.periode ';
        return get_records_sql($sql);
    }

    function primera_quinzena($quadern_id) {
        global $CFG;
        $sql = "SELECT * FROM {$CFG->prefix}fct_quinzena q1 "
             . "WHERE q1.quadern = $quadern_id AND "
             . '(q1.any_ * 100 + q1.periode) = '
             . '(SELECT MAX(q2.any_ * 100 + q2.periode) '
             . "FROM {$CFG->prefix}fct_quinzena q2 "
             . "WHERE q2.quadern = $quadern_id) ";
        return get_record_sql($sql);
    }

    function suprimir_quinzena($quinzena_id) {
        $ok = true;
        $ok = delete_records('fct_dia_quinzena', 'quinzena', $quinzena_id) && $ok;
        $ok = delete_records('fct_activitat_quinzena', 'quinzena', $quinzena_id) && $ok;
        $ok = delete_records('fct_quinzena', 'id', $quinzena_id) && $ok;
        return $ok;
    }

    function suprimir_quinzenes($quadern_id) {
        $ok = true;

        $quinzenes = self::quinzenes($quadern_id);
        if ($quinzenes) {
            foreach ($quinzenes as $quinzena) {
                $ok = self::suprimir_quinzena($quinzena->id) && $ok;
            }
        }

        return $ok;
    }

// Valoració actituds

    function actualitzar_valoracio_actituds($quadern_id, $final, $notes) {
        $ok = true;

        $ok = self::suprimir_valoracio_actituds($quadern_id, $final) && $ok;

        foreach ($notes as $actitud => $nota) {
            $record = (object) array(
            	'quadern' => $quadern_id,
                'final' => $final,
                'actitud' => $actitud,
                'nota' => $nota,
            );
            $ok = insert_record('fct_valoracio_actituds', $record) && $ok;
        }

        return $ok;
    }

    function suprimir_valoracio_actituds($quadern_id, $final) {
        return delete_records('fct_valoracio_actituds', 'quadern', $quadern_id, 'final', $final);
    }

    function suprimir_valoracions_actituds($quadern_id) {
        return delete_records('fct_valoracio_actituds', 'quadern', $quadern_id);
    }

    function valoracio_actituds($quadern_id, $final) {
        $notes = array();

        $records = get_records_select('fct_valoracio_actituds',
            "quadern = '$quadern_id' AND final = '$final'");
        if ($records) {
            foreach ($records as $record) {
                $notes[$record->actitud] = $record->nota;
            }
        }

        return $notes;
    }

// Dades alumne

    function actualitzar_dades_alumne($data) {
        return update_record('fct_dades_alumne', $data);
    }

    function dades_alumne($fct_id, $alumne) {
        $record = get_record('fct_dades_alumne', 'fct', $fct_id, 'alumne', $alumne);
        if (!$record) {
            $record = (object) array('fct' => $fct_id, 'alumne' => $alumne);
            if (!insert_record('fct_dades_alumne', $record)) {
                return false;
            }
        }
        return $record;
    }

    function suprimir_dades_alumnes($fct_id) {
        return delete_records('fct_dades_alumne', 'fct', $fct_id);
    }

// Dades centre concertat

    function actualitzar_dades_centre_concertat($data) {
        return update_record('fct_dades_centre_concertat', $data);
    }

    function afegir_dades_centre_concertat($quadern_id) {
        $record = (object) array('quadern' => $quadern_id);
        return insert_record('fct_dades_centre_concertat', $record);
    }

    function dades_centre_concertat($quadern_id) {
        return get_record('fct_dades_centre_concertat', 'quadern', $quadern_id);
    }

    function suprimir_dades_centre_concertat($quadern_id) {
        return delete_records('fct_dades_centre_concertat', 'quadern', $quadern_id);
    }

// Dades empresa

    function actualitzar_dades_empresa($data) {
        return update_record('fct_dades_empresa', $data);
    }

    function afegir_dades_empresa($quadern_id) {
        $record = (object) array('quadern' => $quadern_id);
        return insert_record('fct_dades_empresa', $record);
    }

    function dades_empresa($quadern_id) {
        return get_record('fct_dades_empresa', 'quadern', $quadern_id);
    }

    function suprimir_dades_empresa($quadern_id) {
        return delete_records('fct_dades_empresa', 'quadern', $quadern_id);
    }

// Dades conveni

    function actualitzar_dades_conveni($data) {
        return update_record('fct_dades_conveni', $data);
    }

    function afegir_dades_conveni($quadern_id) {
        $record = (object) array('quadern' => $quadern_id,
            'prorrogues' => '-',
            'data_inici' => time(),
            'data_final' => time() + 365 * 24 * 60 * 60);
        return insert_record('fct_dades_conveni', $record);
    }

    function dades_conveni($quadern_id) {
        return get_record('fct_dades_conveni', 'quadern', $quadern_id);
    }

    function data_final_convenis_min_max($fct_id) {
        global $CFG;
        $sql = "SELECT MIN(c.data_final) AS data_min, MAX(c.data_final) AS data_max"
            . " FROM {$CFG->prefix}fct_dades_conveni c"
            . " JOIN {$CFG->prefix}fct_quadern q ON c.quadern = q.id"
            . " WHERE q.fct = $fct_id";
        $record = get_record_sql($sql);
        return array($record->data_min, $record->data_max);
    }

    function suprimir_dades_conveni($quadern_id) {
        return delete_records('fct_dades_conveni', 'quadern', $quadern_id);
    }

// Dades horari

    function actualitzar_dades_horari($data) {
        return update_record('fct_dades_horari', $data);
    }

    function afegir_dades_horari($quadern_id) {
        $record = (object) array('quadern' => $quadern_id);
        return insert_record('fct_dades_horari', $record);
    }

    function dades_horari($quadern_id) {
        return get_record('fct_dades_horari', 'quadern', $quadern_id);
    }

    function suprimir_dades_horari($quadern_id) {
        return delete_records('fct_dades_horari', 'quadern', $quadern_id);
    }

// Dades relatives

    function actualitzar_dades_relatives($data) {
        return update_record('fct_dades_relatives', $data);
    }

    function dades_relatives($fct_id, $alumne) {
        $record = get_record('fct_dades_relatives', 'fct', $fct_id, 'alumne', $alumne);
        if (!$record) {
            $record = (object) array(
            	'fct' => $fct_id,
            	'alumne' => $alumne,
                'hores_credit' => '0',
                'exempcio' => '0',
                'hores_anteriors' => '0',
            );
            if (!insert_record('fct_dades_relatives', $record)) {
                return false;
            }
        }
        return $record;
    }

    function suprimir_dades_relatives($fct_id) {
        return delete_records('fct_dades_relatives', 'fct', $fct_id);
    }

// Hores realitzdes

    function hores_realitzades_fct($fct_id, $alumne) {
        global $CFG;
        $sql = 'SELECT SUM(qi.hores) AS hores '
            . "FROM {$CFG->prefix}fct_quadern qa "
            . "JOIN {$CFG->prefix}fct_quinzena qi ON qa.id = qi.quadern "
            . "WHERE qa.fct = '$fct_id' AND qa.alumne = '$alumne'";
        $record = get_record_sql($sql);
        return ($record and $record->hores) ? $record->hores : 0;
    }

    function hores_realitzades_quadern($quadern_id) {
        $hores = get_field('fct_quinzena', 'SUM(hores)', 'quadern', $quadern_id);
        return $hores ? $hores : 0;
    }


// Qualificació quadern

    function actualitzar_qualificacio_quadern($data) {
        return update_record('fct_qualificacio_quadern', $data);
    }

    function afegir_qualificacio_quadern($quadern_id) {
        $record = (object) array('quadern' => $quadern_id);
        return insert_record('fct_qualificacio_quadern', $record);
    }

    function qualificacio_quadern($quadern_id) {
        return get_record('fct_qualificacio_quadern', 'quadern', $quadern_id);
    }

    function suprimir_qualificacio_quadern($quadern_id) {
        return delete_records('fct_qualificacio_quadern',
            'quadern', $quadern_id);
    }

// Qualificació global

    function actualitzar_qualificacio_global($data) {
        return update_record('fct_qualificacio_global', $data);
    }

    function qualificacio_global($fct_id, $alumne) {
        $record = get_record('fct_qualificacio_global',
            'fct', $fct_id, 'alumne', $alumne);
        if (!$record) {
            $record = (object) array('fct' => $fct_id, 'alumne' => $alumne);
            if (!insert_record('fct_qualificacio_global', $record)) {
                return false;
            }
        }
        return $record;
    }

    function suprimir_qualificacions_global($fct_id) {
        return delete_records('fct_qualificacio_global', 'fct', $fct_id);
    }

// Tutor d'empresa

    function afegir_tutor_empresa($courseid, $dni, $contrasenya, $nom, $cognoms, $email) {
        global $USER;

        $roleid = get_field('role', 'id', 'shortname', 'tutorempresa');
        if (!$roleid) {
            return false;
        }

        $record = array('username' => strtolower($dni),
                        'password' => hash_internal_user_password($contrasenya),
                        'firstname' => $nom,
                        'lastname' => $cognoms,
                        'email' => $email,
                        'auth' => 'manual',
                        'confirmed' => 1,
                        'deleted' => 0,
                        'mnethostid' => 1,
                        'country'  => 'CT',
                        'lang' => 'ca_utf8',
                        'maildigest' => 1,
                        'autosubscribe' => 0,
                        'ajax' => 0,
                        'timemodified' => time());
        $id = insert_record('user', (object) $record);
        if (!$id) {
            return false;
        }

        $context = get_context_instance(CONTEXT_COURSE, $courseid);
        $record = array('roleid' => $roleid,
                        'userid' => $id,
                        'contextid' => $context->id,
                        'timestart' => time(),
                        'timemodified' => time(),
                        'modifierid' => $USER->id,
                        'enrol' => 'manual');
        insert_record('role_assignments', (object) $record);

        return $id;
    }

// Llista d'empreses

    function empreses($fct_id, $order=false) {
        global $CFG;

        $sql = "SELECT q.id, q.nom_empresa AS nom,
                    e.adreca, e.poblacio, e.codi_postal,
                    e.telefon, e.fax, e.email, e.nif
                FROM {$CFG->prefix}fct_quadern q
                    JOIN {$CFG->prefix}fct_dades_empresa e ON q.id = e.quadern
                    WHERE q.fct = '$fct_id'";
        if ($order) {
            $sql .= " ORDER BY $order";
        }

        return get_records_sql($sql);
    }

}
