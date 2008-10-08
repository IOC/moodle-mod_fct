<?php

require_once dirname(__FILE__) . '/lib.php';

class fct_backup
{
    var $bf;
    var $level;

    function __construct($bf, $level) {
        $this->bf = $bf;
        $this->level = $level;
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
        if (!is_array($records)) {
            $records = array($records);
        }
        $this->write_start_tag(strtoupper($table));
        foreach ($records as $record) {
            $this->write_record($record);
        }
        $this->write_end_tag(strtoupper($table));
    }

    function write_table_field($table, $field, $value) {
        $records = get_records($table, $field, $value);
        $this->write_table($table, $records);
    }

    function write_table_where($table, $where) {
        $records = get_records_select($table, $where);
        $this->write_table($table, $records);
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
            $info[1][0] = fct_string('plantilles_activitats');
            $info[1][1] = fct_db::nombre_plantilles();
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
        $info[$instance->id.'1'][0] = fct_string('plantilles_activitats');
        $info[$instance->id.'1'][1] = fct_db::nombre_plantilles($instance->id);
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
    $userdata = backup_userdata_selected($preferences,'fct', $fcy->id);
    
    $status = true;

    $backup = new fct_backup($bf, 3);
    $backup->write_start_tag('MOD');
    $backup->write_table('fct', $fct);
    $backup->write_table_field('fct_dades_centre', 'fct', $fct->id);
    $backup->write_table_field('fct_plantilla', 'fct', $fct->id);
    if ($userdata) {
        $backup->write_table_field('fct_dades_alumne', 'fct', $fct->id);
        $backup->write_table_field('fct_dades_relatives', 'fct', $fct->id);
        $backup->write_table_field('fct_qualificacio_global', 'fct', $fct->id);
        $backup->write_table_field('fct_quadern', 'fct', $fct->id);   
    }
    $backup->write_end_tag('MOD');

    return $status;
}
