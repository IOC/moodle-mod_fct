<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Diposit fct class
 *
 * @package    mod
 * @subpackage fct
 * @copyright  2014 Institut Obert de Catalunya
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class fct_diposit {

    public function __construct() {
    }

    public function afegir_fct($fct) {
        global $DB;

        $record = new stdClass;
        $record->course = $fct->course;
        $record->name = $fct->name;
        $record->intro = $fct->intro;
        $record->timecreated = $fct->timecreated;
        $record->timemodified = $fct->timemodified;
        $record->objecte = '';

        if (!isset($fct->id) || !$fct->id) {
            $fct->id = $DB->insert_record('fct', $record);
        }

        $record->id = $fct->id;
        $record->objecte = fct_json::serialitzar_fct($fct);

        $DB->update_record('fct', $record);
    }

    public function activitat($id) {
        global $DB;

        $record = $DB->get_record('fct_activitat', array('id' => $id));
        return fct_json::deserialitzar_activitat($record->objecte);
    }

    public function activitats($quadernid, $descripcio=false) {
        global $DB;

        $activitats = array();
        $records = $DB->get_records('fct_activitat', 'quadern', array($quadernid => 'id'), 'id');

        foreach ($records as $record) {
            $activitat = $this->activitat($record->id);
            if (!$descripcio or $activitat->descripcio == $descripcio) {
                $activitats[] = $activitat;
            }
        }

        usort($activitats, array('fct_activitat', 'cmp'));
        return $activitats;
    }

    public function avisos_quadern($quadernid) {
        global $DB;

        $records = $DB('fct_avis', array('quadern' => $quadernid), 'data', 'id, objecte');
        return $this->avisos_records($records);
    }

    private function avisos_records($records) {
        global $DB;

        $avisos = array();
        foreach ($records as $record) {
            $avisos[] = fct_json::deserialitzar_avis($record->objecte);
        }
        return $avisos;
    }

    public function cicle($id) {
        global $DB;

        $record = $DB->get_record('fct_cicle', array('id' => $id));
        $cicle = fct_json::deserialitzar_cicle($record->objecte);

        $cicle->n_quaderns = $DB->count_records('fct_quadern', array('cicle' => $id));
        return $cicle;
    }

    public function cicles($id, $nom=false) {
        global $DB;

        $cicles = array();
        $select = "fct = :id";
        $params = array('id' => $id);
        if ($nom) {
            $select .= " AND nom = :nom ";
            $params['nom'] = $nom;
        }

        $records = $DB->get_records_select('fct_cicle', $select, $params,
                                                     'nom', 'id');
        foreach ($records as $record) {
            $cicles[] = $this->cicle($record->id);
        }
        return $cicles;
    }

    public function fct($id) {
        global $DB;

        $record = $DB->get_record('fct', array('id' => $id));
        $fct = fct_json::deserialitzar_fct($record->objecte);
        $fct->id = $record->id;
        $fct->course = $record->course;
        return $fct;
    }

    public function min_max_data_final_quaderns($fct_id) {
        global $CFG, $DB;

        $sql = "SELECT MIN(q.data_final) AS min_data_final,"
            . " MAX(q.data_final) AS max_data_final"
            . " FROM {$CFG->prefix}fct_quadern q"
            . " JOIN {$CFG->prefix}fct_cicle c ON c.id = q.cicle"
            . " WHERE c.fct = $fct_id AND q.data_final > 0";

        $record = $DB->get_record_sql($sql);

        return array($record->min_data_final, $record->max_data_final);
    }

    public function nombre_quaderns($especificacio=false) {
        global $CFG, $DB;

        if ($especificacio) {
            $sql = 'SELECT COUNT(*)'
                . ' FROM ' . $this->_tables_quaderns($especificacio)
                . ' WHERE ' . $this->_select_quaderns($especificacio);
            return $DB->count_records_sql($sql);
        } else {
            return $DB->count_records('fct_quadern');
        }
    }


    public function quadern($id) {
        global $DB;

        $record = $DB->get_record('fct_quadern', array('id' => $id));
        return fct_json::deserialitzar_quadern($record->objecte);
    }

    public function quaderns($especificacio, $ordenacio=false,
                      $limitfrom=false, $limitnum=false) {
        global $DB;

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
        $records = $DB->get_records_sql($sql, null, $limitfrom, $limitnum);
        foreach ($records as $record) {
            $quaderns[] = $this->quadern($record->id);
        }
        return $quaderns;
    }

    public function quinzena($id) {
        global $DB;

        $record = $DB->get_record('fct_quinzena', array('id' => $id));
        return fct_json::deserialitzar_quinzena($record->objecte);
    }

    public function quinzenes($quadernid, $any=null, $periode=null) {
        global $DB;

        $quinzenes = array();

        $where = "quadern = :quadernid";
        $params = array('quadernid' => $quadernid);

        if ($any !== null) {
            $where .= " AND any_ = :any ";
            $params['any'] = $any;
        }
        if ($periode !== null) {
            $where .= " AND periode = :periode ";
            $params['periode'] = $periode;
        }

        $records = $DB->get_records_select('fct_quinzena', $where, $params, 'any_, periode', 'id');

        foreach ($records as $record) {
            $quinzenes[] = $this->quinzena($record->id);
        }

        return $quinzenes;
    }

    public function suprimir_activitat($activitat) {
        global $DB;

        $DB->delete_records('fct_activitat', array('id' => $activitat->id));
        $activitat->id = false;
    }

    public function suprimir_avis($avis) {
        global $DB;

        $DB->delete_records('fct_avis', array('id' => $avis->id));
        $avis->id = false;
    }

    public function suprimir_cicle($cicle) {
        global $DB;

        $DB->delete_records('fct_cicle', array('id' => $cicle->id));
        $cicle->id = false;
    }

    public function suprimir_fct($fct) {
        global $DB;

        $DB->delete_records('fct', array('id' => $fct->id));
        $fct->id = false;
    }

    public function suprimir_quadern($quadern) {
        global $DB;

        $DB->delete_records('fct_quadern', array('id' => $quadern->id));
        $quadern->id = false;
    }

    public function suprimir_quinzena($quinzena) {
        global $DB;

        $DB->delete_records('fct_quinzena', 'id', $quinzena->id);
        $quinzena->id = false;
    }
    public function usuari($fct, $userid) {
        global $DB;

        $usuari = new fct_usuari;
        $record = $DB->get_record('user', array('id' => $userid));
        $usuari->id = $record->id;
        $usuari->fct = $fct->id;
        $usuari->nom = $record->firstname;
        $usuari->cognoms = $record->lastname;
        $usuari->email = $record->email;

        $cm = get_coursemodule_from_instance('fct', $fct->id);
        $context = get_context_instance(CONTEXT_MODULE, $cm->id);

        $usuari->es_administrador = (has_capability("mod/fct:admin", $context, $userid)
            or has_capability("moodle/site:config", $context, $userid));
        $usuari->es_alumne = has_capability(
            "mod/fct:alumne", $context, $userid);
        $usuari->es_tutor_centre = has_capability(
            "mod/fct:tutor_centre", $context, $userid);
        $usuari->es_tutor_empresa = has_capability(
            "mod/fct:tutor_empresa", $context, $userid);

        return $usuari;
    }

    public function _select_quaderns($especificacio) {
        $usuari = $especificacio->usuari;
        $select = array();
        if ($especificacio->fct !== false) {
            $select[] = 'c.fct = ' . $especificacio->fct;
        }
        if ($usuari !== false and !$usuari->es_administrador) {
            $selectusuari = array('FALSE');
            if ($usuari->es_alumne) {
                $selectusuari[] = 'q.alumne = ' . $usuari->id;
            }
            if ($usuari->es_tutor_centre) {
                $selectusuari[] = 'q.tutor_centre = ' .  $usuari->id;
            }
            if ($usuari->es_tutor_empresa) {
                $selectusuari[] = 'q.tutor_empresa = ' . $usuari->id;
            }
            $select[] = '(' . implode(' OR ', $selectusuari) . ')';
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
            $select[] = "q.estat = '" . addslashes($especificacio->estat) . "'";
        }
        if ($especificacio->cerca !== false) {
            $fields = array("CONCAT(ua.firstname, ' ', ua.lastname)",
                            "q.nom_empresa",
                            "CONCAT(uc.firstname, ' ', uc.lastname)",
                            "CONCAT(ue.firstname, ' ', ue.lastname)");
            $selectcerca = array();
            foreach ($fields as $field) {
                $selectcerca[] = "$field LIKE '%"
                    . addslashes($especificacio->cerca) . "%'";
            }
            $select[] = '(' . implode(' OR ', $selectcerca) . ')';
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

    public function _tables_quaderns($especificacio) {

        $tables = "{fct_quadern} q"
            . " JOIN {fct_cicle} c ON q.cicle = c.id"
            . " JOIN {user} ua ON q.alumne = ua.id"
            . " LEFT JOIN {user} uc ON q.tutor_centre = uc.id"
            . " LEFT JOIN {user} ue ON q.tutor_empresa = ue.id";
        return $tables;
    }


}
