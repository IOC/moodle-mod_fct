<?php
/* Quadern virtual d'FCT

   Copyright © 2008,2009,2010  Institut Obert de Catalunya

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
fct_require('diposit', 'domini', 'json', 'moodle');

class fct_backup {

    function __construct($fct_id, $user_data, $course_id, $unique_code, $bf) {
        $this->fct_id = $fct_id;
        $this->user_data = $user_data;
        $this->course_id = $course_id;
        $this->unique_code = $unique_code;
        $this->bf = $bf;
        $this->level = 3;
        $this->diposit = new fct_diposit(new fct_moodle);
    }

    function check($status) {
        if (!$status) {
            throw fct_exception("backup error");
        }
    }

    function run() {
        $version = get_field('modules', 'version' , 'name', 'fct');
        $fct = $this->diposit->fct($this->fct_id);

        $this->write_start_tag('MOD');
        $this->write_full_tag('ID', $fct->id);
        $this->write_full_tag('MODTYPE', 'fct');
        $this->write_full_tag('VERSION', $version);
        $this->write_start_tag('OBJECTES');
        $this->write_objecte_fct($fct);
        $this->write_end_tag('OBJECTES');
        $this->write_end_tag('MOD');
    }

    function copy_files($path) {
        global $CFG;

        $this->check(check_and_create_moddata_dir($this->unique_code));
        $backup_dir = "{$CFG->dataroot}/temp/backup/{$this->unique_code}/moddata/fct";
        $this->check(check_dir_exists($backup_dir, true));

        $dir = "{$CFG->dataroot}/{$this->course_id}/{$CFG->moddata}/fct/$path";
        if (is_dir($dir)) {
            $this->check(backup_copy_file($dir, "$backup_dir/$path"));
        }
    }

    function write($text) {
        fwrite($this->bf, $text);
    }

    function write_end_tag($name) {
        $this->level--;
        $this->write(end_tag($name, $this->level, true));
    }

    function write_full_tag($name, $value) {
        $this->write(full_tag($name, $this->level, false, $value));
    }

    function write_objecte_activitat($activitat) {
        $json = fct_json::serialitzar_activitat($activitat);
        $this->write_full_tag('ACTIVITAT', $json);
    }

    function write_objecte_avis($avis) {
        $json = fct_json::serialitzar_avis($avis);
        $this->write_full_tag('AVIS', $json);
    }

    function write_objecte_cicle($cicle) {
        $json = fct_json::serialitzar_cicle($cicle);
        $this->write_full_tag('CICLE', $json);

        if ($this->user_data) {
            $especificacio = new fct_especificacio_quaderns;
            $especificacio->cicle = $cicle->id;
            $quaderns = $this->diposit->quaderns($especificacio);
            foreach ($quaderns as $quadern) {
                $this->write_objecte_quadern($quadern);
            }
        }
    }

    function write_objecte_fct($fct) {
        global $CFG;

        $json = fct_json::serialitzar_fct($fct);
        $this->write_full_tag('FCT', $json);

        $cicles = $this->diposit->cicles($fct->id);
        foreach ($cicles as $cicle) {
            $this->write_objecte_cicle($cicle);
        }
    }

    function write_objecte_quadern($quadern) {
        $json = fct_json::serialitzar_quadern($quadern);
        $this->write_full_tag('QUADERN', $json);

        $activitats = $this->diposit->activitats($quadern->id);
        foreach ($activitats as $activitat) {
            $this->write_objecte_activitat($activitat);
        }

        $quinzenes = $this->diposit->quinzenes($quadern->id);
        foreach ($quinzenes as $quinzena) {
            $this->write_objecte_quinzena($quinzena);
        }

        $avisos = $this->diposit->avisos_quadern($quadern->id);
        foreach ($avisos as $avis) {
            $this->write_objecte_avis($avis);
        }

        $this->copy_files("quadern-{$quadern->id}");
    }

    function write_objecte_quinzena($quinzena) {
        $json = fct_json::serialitzar_quinzena($quinzena);
        $this->write_full_tag('QUINZENA', $json);
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
            $diposit = new fct_diposit(new fct_moodle);
            $info[1][0] = fct_string('cicles_formatius');
            $info[1][1] = $diposit->nombre_cicles();
            $info[2][0] = fct_string('quaderns');
            $info[2][1] = $diposit->nombre_quaderns();
            $info[3][0] = fct_string('quinzenes');
            $info[3][1] = $diposit->nombre_quinzenes();
        }
    }

    return $info;
}

function fct_check_backup_mods_instances($instance, $backup_unique_code) {
    $info = array();
    $info[$instance->id.'0'][0] = '<b>'.$instance->name.'</b>';
    $info[$instance->id.'0'][1] = '';
    if ($instance->userdata) {
        $diposit = new fct_diposit(new fct_moodle);
        $info[$instance->id.'1'][0] = fct_string('cicles_formatius');
        $info[$instance->id.'1'][1] = $diposit->nombre_cicles($instance->id);
        $info[$instance->id.'2'][0] = fct_string('quaderns');
        $especificacio = new fct_especificacio_quaderns;
        $especificacio->fct = $instance->id;
        $info[$instance->id.'2'][1] = $diposit->nombre_quaderns($especificacio);
        $info[$instance->id.'3'][0] = fct_string('quinzenes');
        $info[$instance->id.'3'][1] = $diposit->nombre_quinzenes($instance->id);
    }

    return $info;
}

function fct_backup_mods($bf, $preferences) {
    $status = true;

    $fcts = get_records('fct', 'course', $preferences->backup_course, 'id');
    $fcts = is_array($fcts) ? $fcts : array();

    foreach ($fcts as $fct) {
        if (backup_mod_selected($preferences, 'fct', $fct->id)) {
            $status = $status && fct_backup_one_mod($bf, $preferences, $fct);
        }
    }

    return $status;
}

function fct_backup_one_mod($bf, $preferences, $fct) {
    $fct_id = is_numeric($fct) ? $fct : $fct->id;
    $user_data = backup_userdata_selected($preferences,'fct', $fct_id);

    $backup = new fct_backup($fct_id, $user_data, $preferences->backup_course,
                             $preferences->backup_unique_code, $bf);

    try {
        $backup->run();
    } catch (fct_exception $e) {
        return false;
    }

    return true;
}
