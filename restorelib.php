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

class fct_restore
{
    var $mod;
    var $restore;
    var $info;
    var $status;

    function __construct($restore, $info) {
        $this->restore = $restore;
        $this->info = $info;
        $this->status = true;
    }

    function get_id($table, $old_id) {
        if ($old_id == 0) {
            return 0;
        }
        $result = backup_getid($this->restore->backup_unique_code, $table, $old_id);
        return $result->new_id;
    }

    function get_node($name, $index=0) {
        $name = strtoupper($name);
        $node = $this->info['MOD']['#'][$name][$index]['#'];
        if (is_string($node)) {
            return backup_todb($node);
        } else {
            return $node;
        }
    }

    function get_records($table, $fkeys=array()) {
        $records = array();
        $table_node = $this->get_node($table);
        if (isset($table_node['RECORD'])) {
            foreach ($table_node['RECORD'] as $record_node) {
                $record_node = $record_node['#'];
                $record = array();
                foreach ($record_node as $field_name => $field_node) {
                    $field_name = strtolower($field_name);
                    $field_value = backup_todb($field_node['0']['#']);
                    if (isset($fkeys[$field_name])) {
                        $ftable = $fkeys[$field_name];
                        $field_value = $this->get_id($ftable, $field_value);
                    }
                    $record[$field_name] = $field_value;
                }
                $records[] = (object) $record;
            }
        }
        return $records;
    }

    function insert_record($table, $record) {
        if (!$this->status) {
            return;
        }
        $old_id = $record->id;
        unset($record->id);
        $new_id = insert_record($table, $record);
        if ($new_id) {
            $this->put_id($table, $old_id, $new_id);
        } else {
            $this->status = false;
        }
    }

    function restore_table($table, $fkeys=array()) {
        $records = $this->get_records($table, $fkeys);
        foreach ($records as $record) {
            $this->insert_record($table, $record);
        }
    }

    function put_id($table, $old_id, $new_id) {
        backup_putid($this->restore->backup_unique_code, $table, $old_id, $new_id);
    }

}

function fct_restore_mods($mod, $restore) {
    global $CFG, $db;
    
    $userdata = restore_userdata_selected($restore,'fct', $mod->id);
    $data = backup_getid($restore->backup_unique_code, $mod->modtype, $mod->id);
    if (!$data) {
        return false;
    }

    $r = new fct_restore($restore, $data->info);
    
    $fct = (object) array(
        'id' => $mod->id,
        'course' => $restore->course_id,
        'name' => $r->get_node('NAME'),
        'intro' => $r->get_node('INTRO'),
        'timecreated' => $r->get_node('TIMECREATED'),
        'timemodified' => $r->get_node('TIMEMODIFIED')
    );
    $r->insert_record('fct', $fct);

    $r->restore_table('fct_dades_centre', array('fct' => 'fct'));
    $r->restore_table('fct_cicle', array('fct' => 'fct'));
    $r->restore_table('fct_activitat_cicle', array('cicle' => 'fct_cicle'));

    if ($userdata) {
        $r->restore_table('fct_dades_alumne',
                          array('fct' => 'fct', 'alumne' => 'user'));
        $r->restore_table('fct_qualificacio_global',
                          array('fct' => 'fct', 'alumne' => 'user'));
        $r->restore_table( 'fct_quadern',
                           array('fct' => 'fct', 'alumne' => 'user',
                                 'tutor_centre' => 'user',
                                 'tutor_empresa' => 'user',
                                 'cicle' => 'fct_cicle'));
        $r->restore_table('fct_dades_centre_concertat',
                          array('quadern' => 'fct_quadern'));
        $r->restore_table('fct_dades_conveni',
                          array('quadern' => 'fct_quadern'));
        $r->restore_table('fct_dades_empresa',
                          array('quadern' => 'fct_quadern'));
        $r->restore_table('fct_dades_horari',
                          array('quadern' => 'fct_quadern'));
        $r->restore_table('fct_dades_relatives',
                          array('quadern' => 'fct_quadern'));
        $r->restore_table('fct_activitat_pla',
                          array('quadern' => 'fct_quadern'));
        $r->restore_table('fct_valoracio_actituds',
                          array('quadern' => 'fct_quadern'));
        $r->restore_table('fct_qualificacio_quadern',
                          array('quadern' => 'fct_quadern'));
        $r->restore_table('fct_quinzena',
                          array('quadern' => 'fct_quadern'));
        $r->restore_table('fct_activitat_quinzena',
                          array('quinzena' => 'fct_quinzena',
                                'activitat' => 'fct_activitat_pla'));
        $r->restore_table('fct_dia_quinzena',
                          array('quinzena' => 'fct_quinzena'));
    }

    return $r->status;
}
