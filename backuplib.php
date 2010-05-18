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

require_once($CFG->dirroot . '/mod/fct//lib.php');

class fct_backup
{
    var $bf;
    var $level;
    var $fct;

    function __construct($bf, $level, $fct) {
        $this->bf = $bf;
        $this->level = $level;
        $this->fct = $fct;
    }

    function write($text) {
        fwrite ($this->bf, $text);
    }

    function write_end_tag($name) {
        $this->level--;
        $this->write(end_tag($name, $this->level, true));
    }

    function write_full_tag($name, $value) {
        $this->write(full_tag($name, $this->level, false, $value));
    }

    function write_record($record) {
        $this->write_start_tag('RECORD');
        foreach ((array) $record as $name => $value) {
            $this->write_full_tag(strtoupper($name), $value);
        }
        $this->write_end_tag('RECORD');
    }

    function write_table($table, $records) {
        if (!empty($records) and !is_array($records)) {
            $records = array($records);
        }
        $this->write_start_tag(strtoupper($table));
        if ($records) {
            foreach ($records as $record) {
                $this->write_record($record);
            }
        }
        $this->write_end_tag(strtoupper($table));
    }

    function write_table_fct($table, $ftables=false) {
        global $CFG;

        $select = "fct = {$this->fct->id}";

        if ($ftables) {
            $fkeys = array_keys($ftables);
            while ($fkeys) {
                $fkey = array_pop($fkeys);
                $ftable = $ftables[$fkey];
                $select = "$fkey IN (SELECT id FROM {$CFG->prefix}$ftable"
                    . " WHERE $select)";
            }
        }

        $records = get_records_select($table, $select);
        $this->write_table($table, $records);
    }

    function write_tables_fct($tables, $ftables=false) {
        foreach ($tables as $table) {
            $this->write_table_fct($table, $ftables);
        }
    }

    function write_start_tag($name) {
        $this->write(start_tag($name, $this->level, true));
        $this->level++;
    }

}

function fct_check_backup_mods($course, $user_data=false, $backup_unique_code, $instances=null) {
    $info = array();

    if (!empty($instances) && is_array($instances) && count($instances)) {
        $info = array();
        foreach ($instances as $id => $instance) {
            $info += fct_check_backup_mods_instances($instance, $backup_unique_code);
        }
    } else {
        $info[0][0] = get_string('modulenameplural', 'fct');
        $info[0][1] = count_records('fct', 'course', $course);
        if ($user_data) {
            $info[1][0] = fct_string('cicles_formatius');
            $info[1][1] = fct_db::nombre_cicles();
            $info[2][0] = fct_string('quaderns');
            $info[2][1] = fct_db::nombre_quaderns();
            $info[3][0] = fct_string('quinzenes');
            $info[3][1] = fct_db::nombre_quinzenes();
        }
    }

    return $info;
}

function fct_check_backup_mods_instances($instance, $backup_unique_code) {
    $info = array();
    $info[$instance->id.'0'][0] = '<b>'.$instance->name.'</b>';
    $info[$instance->id.'0'][1] = '';
    if ($instance->userdata) {
        $info[$instance->id.'1'][0] = fct_string('cicles_formatius');
        $info[$instance->id.'1'][1] = fct_db::nombre_cicles($instance->id);
        $info[$instance->id.'2'][0] = fct_string('quaderns');
        $info[$instance->id.'2'][1] = fct_db::nombre_quaderns($instance->id);
        $info[$instance->id.'3'][0] = fct_string('quinzenes');
        $info[$instance->id.'3'][1] = fct_db::nombre_quinzenes($instance->id);
    }

    return $info;
}

function fct_backup_mods($bf, $preferences) {
    $status = true;
    $fcts = get_records ('fct', 'course', $preferences->backup_course,'id');
    if ($fcts) {
        foreach ($fcts as $fct) {
            if (backup_mod_selected($preferences, 'fct', $fct->id)) {
                $status = fct_backup_one_mod($bf, $preferences, $fct);
            }
        }
    }
    return $status;
}

function fct_backup_one_mod($bf, $preferences, $fct) {
    if (is_numeric($fct)) {
        $fct = get_record('fct', 'id', $fct);
    }
    $instanceid = $fct->id;
    $userdata = backup_userdata_selected($preferences,'fct', $fct->id);
    
    $status = true;

    $backup = new fct_backup($bf, 3, $fct);
    $backup->write_start_tag('MOD');
    $backup->write_full_tag('ID', $fct->id);
    $backup->write_full_tag('MODTYPE', 'fct');
    $backup->write_full_tag('VERSION',
                            get_field('modules', 'version' , 'name', 'fct'));
    $backup->write_full_tag('NAME', $fct->name);
    $backup->write_full_tag('INTRO', $fct->intro);
    $backup->write_full_tag('TIMECREATED', $fct->timecreated);
    $backup->write_full_tag('TIMEMODIFIED', $fct->timemodified);
    $backup->write_full_tag('FRASES_CENTRE', $fct->frases_centre);
    $backup->write_full_tag('FRASES_EMPRESA', $fct->frases_empresa);
    $backup->write_tables_fct(array('fct_dades_centre', 'fct_cicle'));
    $backup->write_table_fct('fct_activitat_cicle',
                             array('cicle' => 'fct_cicle'));
    if ($userdata) {
        $backup->write_table_fct('fct_dades_alumne');
        $backup->write_tables_fct(array('fct_quadern',
                                        'fct_qualificacio_global'),
                                  array('cicle' => 'fct_cicle'));
        $backup->write_tables_fct(array('fct_dades_centre_concertat',
                                        'fct_dades_empresa',
                                        'fct_dades_conveni',
                                        'fct_conveni',
                                        'fct_dades_relatives',
                                        'fct_activitat_pla',
                                        'fct_valoracio_actituds',
                                        'fct_qualificacio_quadern',
                                        'fct_quinzena'),
                                  array('quadern' => 'fct_quadern',
                                        'cicle' => 'fct_cicle'));
        $backup->write_table_fct('fct_horari',
                                 array('conveni' => 'fct_conveni',
                                       'quadern' => 'fct_quadern',
                                       'cicle' => 'fct_cicle'));
        $backup->write_tables_fct(array('fct_activitat_quinzena',
                                        'fct_dia_quinzena'),
                                  array('quinzena' => 'fct_quinzena',
                                        'quadern' => 'fct_quadern',
                                        'cicle' => 'fct_cicle'));
    }
    $backup->write_end_tag('MOD');

    return $status;
}
