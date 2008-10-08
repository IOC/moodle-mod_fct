<?php

require_once dirname(__FILE__) . '/lib.php';

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

    function write_table_fct($table) {
        $records = get_records($table, 'fct', $this->fct->id);
        $this->write_table($table, $records);
    }

    function write_table_fct2($table, $field, $table2) {
        global $CFG;
        $select = "$field IN (SELECT id FROM {$CFG->prefix}$table2"
            . " WHERE fct = {$this->fct->id})";
        $records = get_records_select($table, $select);
        $this->write_table($table, $records);
    }

    function write_table_fct3($table, $field, $table2, $field2, $table3) {
        global $CFG;
        $select = "$field IN (SELECT id FROM {$CFG->prefix}$table2"
            . " WHERE $field2 IN (SELECT id FROM {$CFG->prefix}$table3"
            . " WHERE fct = {$this->fct->id}))";
        $records = get_records_select($table, $select);
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
    $userdata = backup_userdata_selected($preferences,'fct', $fct->id);
    
    $status = true;

    $backup = new fct_backup($bf, 3, $fct);
    $backup->write_start_tag('MOD');
    $backup->write_full_tag('ID', $fct->id);
    $backup->write_full_tag('MODTYPE', 'fct');
    $backup->write_full_tag('VERSION', get_field('modules', 'version' , 'name', 'fct'));
    $backup->write_full_tag('NAME', $fct->name);
    $backup->write_full_tag('INTRO', $fct->intro);
    $backup->write_full_tag('TIMECREATED', $fct->timecreated);
    $backup->write_full_tag('TIMEMODIFIED', $fct->timemodified);
    $backup->write_table_fct('fct_dades_centre');
    $backup->write_table_fct('fct_plantilla');
    $backup->write_table_fct2('fct_activitat_plantilla', 'plantilla', 'fct_plantilla');
    if ($userdata) {
        $backup->write_table_fct('fct_dades_alumne');
        $backup->write_table_fct('fct_dades_relatives');
        $backup->write_table_fct('fct_qualificacio_global');
        $backup->write_table_fct('fct_quadern');   
        $backup->write_table_fct2('fct_dades_centre_concertat', 'quadern', 'fct_quadern');
        $backup->write_table_fct2('fct_dades_empresa', 'quadern', 'fct_quadern');
        $backup->write_table_fct2('fct_dades_conveni', 'quadern', 'fct_quadern');
        $backup->write_table_fct2('fct_dades_horari', 'quadern', 'fct_quadern');
        $backup->write_table_fct2('fct_activitat_pla', 'quadern', 'fct_quadern');
        $backup->write_table_fct2('fct_valoracio_actituds', 'quadern', 'fct_quadern');
        $backup->write_table_fct2('fct_qualificacio_quadern', 'quadern', 'fct_quadern');
        $backup->write_table_fct2('fct_quinzena', 'quadern', 'fct_quadern');
        $backup->write_table_fct3('fct_activitat_quinzena', 'quinzena', 'fct_quinzena',
                                  'quadern', 'fct_quadern');
        $backup->write_table_fct3('fct_dia_quinzena', 'quinzena', 'fct_quinzena',
                                  'quadern', 'fct_quadern');
    }
    $backup->write_end_tag('MOD');

    return $status;
}
