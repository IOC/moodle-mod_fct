<?php
/* Quadern virtual d'FCT

   Copyright © 2008,2009  Institut Obert de Catalunya

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
        $ok = self::suprimir_dades_alumnes($id) && $ok;
        $ok = self::suprimir_cicles($id) && $ok;

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

    function activitats_cicle($cicle_id) {
        return get_records_menu('fct_activitat_cicle', 'cicle', $cicle_id,
                                'descripcio', 'id, descripcio');
    }

    function actualitzar_cicle($cicle) {
        $ok = update_record('fct_cicle', $cicle);
        $ok = $ok && delete_records('fct_activitat_cicle', 'cicle', $cicle->id);
        $ok = $ok && self::afegir_activitats_cicle($cicle->id,
                                                   $cicle->activitats);
        return $ok;
    }

    function afegir_activitats_cicle($cicle_id, $activitats) {
        $ok = true;
        $activitats = explode("\n", $activitats);
        foreach ($activitats as $activitat) {
            if (trim($activitat)) {
                $record = (object) array('cicle' => $cicle_id,
                                         'descripcio' => trim($activitat));
                $ok = $ok && insert_record('fct_activitat_cicle', $record);
            }
        }
        return $ok;
    }

    function afegir_cicle($fct_id, $nom, $activitats='') {
        $ok = true;

        $cicle = (object) array('fct' => $fct_id, 'nom' => $nom);
        $ok = $ok && ($id = insert_record('fct_cicle', $cicle));
        $ok = $ok && self::afegir_activitats_cicle($id, $activitats);

        if ($ok) {
            return $id;
        } else {
            self::suprimir_cicle($id);
            return false;
        }
    }

    function cicle($cicle_id) {
        $cicle = get_record('fct_cicle', 'id', $cicle_id);
        if (!$cicle) {
            return false;
        }

        $activitats = self::activitats_cicle($cicle_id);
        $cicle->activitats = implode("\n", $activitats);

        return $cicle;
    }

    function cicle_duplicat($fct_id, $nom, $cicle_id=false) {
        $select = "fct = '$fct_id' AND nom = '$nom'";
        if ($cicle_id) {
            $select .= " AND id != '$cicle_id'";
        }
        return record_exists_select('fct_cicle', $select);
    }

    function cicles($fct_id) {
        $records = get_records_menu('fct_cicle', 'fct', $fct_id,
                                    'nom', 'id, nom');
        return $records ? $records: array();
    }

    function nombre_cicles($fct_id=false) {
        if ($fct_id) {
            return count_records('fct_cicle', 'fct', $fct_id);
        } else {
            return count_records('fct_cicle');
        }
    }

    function suprimir_cicle($cicle_id) {
        $ok = true;
        $ok = delete_records('fct_cicle', 'id', $cicle_id) && $ok;
        $ok = delete_records('fct_activitat_cicle', 'cicle', $cicle_id) && $ok;
        $ok = self::suprimir_quaderns($cicle_id) && $ok;
        $ok = self::suprimir_qualificacions_globals($cicle_id) && $ok;
        return $ok;
    }

    function suprimir_cicles($fct_id) {
        $cicles = self::cicles($fct_id);
        $ok = true;
        if ($cicles) {
            foreach (array_keys($cicles) as $id) {
                $ok = self::suprimir_cicle($id) && $ok;
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
        $ok = $ok && self::afegir_dades_relatives($id);
        $ok = $ok && self::afegir_qualificacio_quadern($id);

        if (!$ok) {
            self::suprimir_quadern($id);
            return false;
        }

        return $id;
    }

    function data_final_quadern($quadern_id) {
        return get_field('fct_dades_conveni', 'data_final', 'quadern', $quadern_id);
    }

    function nombre_quaderns($fct_id=false, $params=false) {
        global $CFG;

        $sql = "SELECT COUNT(*)"
            . " FROM {$CFG->prefix}fct_quadern q"
            . " JOIN {$CFG->prefix}fct_cicle c ON q.cicle = c.id"
            . self::quaderns_where_sql($fct_id, $params);

       return count_records_sql($sql);
    }

    function nombre_quaderns_cicle($cicle_id) {
        return count_records('fct_quadern', 'cicle', $cicle_id);
    }

    function quadern_duplicat($fct_id, $alumne_id, $nom_empresa, $quadern_id=false) {
        global $CFG;
        $sql = "SELECT COUNT(*)"
            . " FROM {$CFG->prefix}fct_quadern q"
            . " JOIN {$CFG->prefix}fct_cicle c ON q.cicle = c.id"
            . " WHERE c.fct = $fct_id"
            . " AND q.alumne = $alumne_id"
            . " AND q.nom_empresa = '$nom_empresa'";
        if ($quadern_id) {
            $sql .= " AND q.id != '$quadern_id'";
        }
        return count_records_sql($sql) > 0;
    }

    function quadern($quadern_id) {
        return get_record('fct_quadern', 'id', $quadern_id);
    }

    function quaderns($fct_id, $params=false, $order=false) {
        global $CFG;

        $sql = "SELECT q.id,"
            . " CONCAT(ua.firstname, ' ', ua.lastname) AS alumne,"
            . " q.nom_empresa AS empresa,"
            . " c.nom AS cicle_formatiu,"
            . " CONCAT(uc.firstname, ' ', uc.lastname) AS tutor_centre,"
            . " CONCAT(ue.firstname, ' ', ue.lastname) AS tutor_empresa,"
            . " q.estat, dc.data_final"
            . " FROM {$CFG->prefix}fct_quadern q"
            . " JOIN {$CFG->prefix}user ua ON q.alumne = ua.id"
            . " LEFT JOIN {$CFG->prefix}user uc ON q.tutor_centre = uc.id"
            . " LEFT JOIN {$CFG->prefix}user ue ON q.tutor_empresa = ue.id"
            . " JOIN {$CFG->prefix}fct_cicle c ON q.cicle = c.id"
            . " JOIN {$CFG->prefix}fct_dades_conveni dc ON q.id = dc.quadern"
            . self::quaderns_where_sql($fct_id, $params);

        if ($order) {
            $sql .= " ORDER BY $order";
        }

        return get_records_sql($sql);
    }

    function quaderns_where_sql($fct_id=false, $params=false) {
        $select = array('TRUE');

        if ($fct_id) {
            $select[] = "c.fct = $fct_id";
        }

        if ($params) {
            if (!$params->permis_admin) {
                $select_usuari = array('FALSE');
                if ($params->permis_alumne) {
                    $select_usuari[] = "q.alumne = $params->usuari";
                }
                if ($params->permis_tutor_centre) {
                    $select_usuari[] = "q.tutor_centre = $params->usuari";
                }
                if ($params->permis_tutor_empresa) {
                    $select_usuari[] = "q.tutor_empresa = $params->usuari";
                }
                $select[] = '(' . implode(' OR ', $select_usuari) . ')';
            }

            if (isset($params->data_final_min)) {
                $select[] = "dc.data_final >= $params->data_final_min";
            }

            if (isset($params->data_final_max)) {
                $select[] = "dc.data_final < $params->data_final_max";
            }

            if (isset($params->cicle)) {
                $select[] = "q.cicle = $params->cicle";
            }

            if (isset($params->estat)) {
                $select[] = "q.estat = $params->estat";
            }

            if (isset($params->cerca)) {
                $fields = array("CONCAT(ua.firstname, ' ', ua.lastname)",
                                "q.nom_empresa",
                                "CONCAT(uc.firstname, ' ', uc.lastname)",
                                "CONCAT(ue.firstname, ' ', ue.lastname)");
                $select_cerca = array();
                foreach ($fields as $field) {
                    $select_cerca[] = "$field LIKE '%$params->cerca%'";
                }
                $select[] = '(' . implode(' OR ', $select_cerca) . ')';
            }
        }

        return ' WHERE ' . implode(' AND ', $select);
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
        $ok = self::suprimir_dades_relatives($id) && $ok;
        $ok = self::suprimir_qualificacio_quadern($id) && $ok;

        return $ok;
    }

    function suprimir_quaderns($cicle_id) {
        $quaderns = get_records('fct_quadern', 'cicle', $cicle_id);
        $ok = true;
        if ($quaderns) {
            foreach ($quaderns as $quadern) {
                $ok = self::suprimir_quadern($quadern->id) && $ok;
            }
        }
        return $ok;
    }

    function ultim_quadern($quadern_id, $exclou=false) {
        global $CFG;

        $quadern = get_record('fct_quadern', 'id', $quadern_id);
        if (!$quadern) {
            return false;
        }

        $sql = "SELECT q.*"
            . " FROM {$CFG->prefix}fct_quadern q"
            . " JOIN {$CFG->prefix}fct_dades_conveni c ON q.id = c.quadern"
            . " WHERE q.cicle = {$quadern->cicle}"
            . " AND q.alumne = {$quadern->alumne}"
            . ($exclou ? " AND q.id != $quadern_id" : "")
            . " ORDER BY c.data_final DESC LIMIT 1";

        $records = get_records_sql($sql);
        return $records ? array_pop($records) : false;
    }

// Pla d'activitats

    function activitat_pla($activitat_id) {
        return get_record('fct_activitat_pla', 'id', $activitat_id);
    }

    function activitats_pla($quadern_id) {
        $records = get_records_menu('fct_activitat_pla', 'quadern', $quadern_id,
                                    'descripcio', 'id, descripcio');
        return $records ? $records: array();
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
        foreach ($activitats as $id) {
            $descripcio = get_field('fct_activitat_cicle', 'descripcio', 'id', $id);
            $ok = $ok && !empty($descripcio);
            $ok = $ok && self::afegir_activitat_pla($quadern_id,$descripcio);
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
        foreach (array_keys(self::activitats_pla($quadern_id)) as $id) {
            $ok = self::suprimir_activitat_pla($id) && $ok;
        }
        return $ok;
    }

// Seguiment quinzenal

    function activitats_quinzena($quinzena_id) {
        $activitats = array();
        $records = get_records('fct_activitat_quinzena', 'quinzena', $quinzena_id);
        if ($records) {
            foreach ($records as $record) {
                $activitats[] = $record->activitat;
            }
        }
        return $activitats;
    }

    function actualitzar_quinzena($quinzena, $dies=false, $activitats=false) {
        if (isset($quinzena->any)) {
            $quinzena->any_ = $quinzena->any;
        }
        $ok = update_record('fct_quinzena', $quinzena);
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

        foreach ($activitats as $activitat) {
            $record = (object) array('quinzena' => $quinzena_id,
                                     'activitat' => $activitat);
            $ok = insert_record('fct_activitat_quinzena', $record) && $ok;
        }

        return $ok;
    }

    function afegir_dies_quinzena($quinzena_id, $dies) {
        $ok = true;

        foreach ($dies as $dia) {
            $record = (object) array('quinzena' => $quinzena_id,
                                     'dia' => $dia);
            $ok = insert_record('fct_dia_quinzena', $record) && $ok;
        }

        return $ok;
    }

    function afegir_quinzena($quinzena, $dies=false, $activitats=false) {
        if (isset($quinzena->any)) {
            $quinzena->any_ = $quinzena->any;
        }
        $id = insert_record('fct_quinzena', $quinzena);
        if ($dies !== false) {
            if (!self::afegir_dies_quinzena($id, $dies)) {
                return false;
            }
        }
        if ($activitats !== false) {
            if (!self::afegir_activitats_quinzena($id, $activitats)) {
                return false;
            }
        }
        return $id;
    }

    function dies_quinzena($quinzena_id) {
        $dies = array();
        $records = get_records('fct_dia_quinzena', 'quinzena', $quinzena_id);
        if ($records) {
            foreach ($records as $record) {
                $dies[] = $record->dia;
            }
        }
        return $dies;
    }

    function nombre_quinzenes($fct_id=false) {
        global $CFG;
        if ($fct_id) {
            $sql = "SELECT COUNT(*) FROM {$CFG->prefix}fct_quinzena qi"
                . " JOIN {$CFG->prefix}fct_quadern qa ON qa.id = qi.quadern"
                . " JOIN {$CFG->prefix}fct_cicle c ON qa.cicle = c.id"
                . " WHERE c.fct = $fct_id";
            return count_records_sql($sql);
        } else {
            return count_records('fct_quinzena');
        }
    }

    function quinzena($quinzena_id) {
        $record = get_record('fct_quinzena', 'id', $quinzena_id);
        $record->any = $record->any_;
        if ($record) {
            //$record->dies = count_records('fct_dia_quinzena', 'quinzena',
            //    $quinzena_id);
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

    function data_final_convenis_min_max($fct_id, $params=false) {
        global $CFG;
        $sql = "SELECT MIN(dc.data_final) AS data_min,"
            . " MAX(dc.data_final) AS data_max"
            . " FROM {$CFG->prefix}fct_dades_conveni dc"
            . " JOIN {$CFG->prefix}fct_quadern q ON dc.quadern = q.id"
            . " JOIN {$CFG->prefix}fct_cicle c ON q.cicle = c.id"
            . self::quaderns_where_sql($fct_id, $params);
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

    function afegir_dades_relatives($quadern_id) {
        $record = (object) array(
            'quadern' => $quadern_id,
            'hores_credit' => '0',
            'exempcio' => '0',
            'hores_anteriors' => '0',
        );

        $ultim_quadern = self::ultim_quadern($quadern_id, true);
        if ($ultim_quadern) {
            $dades = self::dades_relatives($ultim_quadern->id);
            $record->hores_credit = $dades->hores_credit;
            $record->exempcio = $dades->exempcio;
            $record->hores_anteriors = $dades->hores_anteriors;
        }

        return insert_record('fct_dades_relatives', $record);
    }

    function dades_relatives($quadern_id) {
        $dades = get_record('fct_dades_relatives', 'quadern', $quadern_id);
        $quadern = get_record('fct_quadern', 'id', $quadern_id);

        if (!$dades or !$quadern) {
            return false;
        }

        $data_final = fct_db::data_final_quadern($quadern->id);
        $dades->hores_practiques = fct_db::hores_realitzades_cicle(
            $quadern->cicle, $quadern->alumne, $data_final);
        $dades->hores_exempcio = ceil((float) $dades->exempcio / 100
                                      * $dades->hores_credit);
        $dades->hores_realitzades = $dades->hores_practiques
            + $dades->hores_exempcio + $dades->hores_anteriors;
        $dades->hores_pendents = max(0, $dades->hores_credit
                                     - $dades->hores_realitzades);
        return $dades;
    }

    function suprimir_dades_relatives($quadern_id) {
        return delete_records('fct_dades_relatives', 'quadern', $quadern_id);
    }

// Hores realitzdes

    function hores_realitzades_cicle($cicle_id, $alumne, $data_final) {
        global $CFG;
        $sql = 'SELECT SUM(qi.hores) AS hores '
            . "FROM {$CFG->prefix}fct_quadern qa "
            . "JOIN {$CFG->prefix}fct_quinzena qi ON qa.id = qi.quadern "
            . "JOIN {$CFG->prefix}fct_dades_conveni c ON qa.id = c.quadern "
            . "WHERE qa.cicle = $cicle_id AND qa.alumne = $alumne"
            . " AND c.data_final <= $data_final";
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

    function qualificacio_global($cicle_id, $alumne) {
        $record = get_record('fct_qualificacio_global',
            'cicle', $cicle_id, 'alumne', $alumne);
        if (!$record) {
            $record = (object) array('cicle' => $cicle_id, 'alumne' => $alumne);
            if (!insert_record('fct_qualificacio_global', $record)) {
                return false;
            }
        }
        return $record;
    }

    function suprimir_qualificacions_globals($cicle_id) {
        return delete_records('fct_qualificacio_global', 'cicle', $cicle_id);
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

    function empreses($cicles) {
        global $CFG;

        if (!$cicles) {
            return array();
        }

        $sql = "SELECT DISTINCT q.nom_empresa AS nom,"
            . " e.adreca, e.poblacio, e.codi_postal,"
            . " e.telefon, e.fax, e.email, e.nif"
            . " FROM {$CFG->prefix}fct_quadern q"
            . " JOIN {$CFG->prefix}fct_dades_empresa e ON q.id = e.quadern"
            . " WHERE q.cicle IN (" . implode(',', $cicles) . ")"
            . " ORDER BY q.nom_empresa";

        return get_records_sql($sql);
    }

}
