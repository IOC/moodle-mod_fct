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

class fct_restore {

    var $restore;
    var $info;
    var $userdata;
    var $diposit;

    function __construct($restore, $info, $userdata) {
        $this->restore = $restore;
        $this->info = $info;
        $this->userdata = $userdata;
        $this->diposit = new fct_diposit;
    }

    function get_id($table, $old_id) {
        if ($old_id == 0) {
            return 0;
        }
        $result = backup_getid($this->restore->backup_unique_code, $table, $old_id);
        return $result ? $result->new_id : 0;
    }

    function put_id($table, $old_id, $new_id) {
        backup_putid($this->restore->backup_unique_code, $table, $old_id, $new_id);
    }

    function restore_objecte_activitat($json) {
        $activitat = fct_json::deserialitzar_activitat($json);
        $activitat->quadern = $this->get_id('fct_quadern', $activitat->quadern);
        $id = $activitat->id;
        $activitat->id = false;
        $this->diposit->afegir_activitat($activitat);
        $this->put_id('fct_activitat_pla', $id, $activitat->id);
    }

    function restore_objecte_avis($json) {
        $avis = fct_json::deserialitzar_avis($json);
        $avis->quadern = $this->get_id('fct_quadern', $avis->quadern);
        $id = $avis->id;
        $avis->id = false;
        $this->diposit->afegir_avis($avis);
        $this->put_id('fct_avis', $id, $avis->id);
    }

    function restore_objecte_cicle($json) {
        $cicle = fct_json::deserialitzar_cicle($json);
        $cicle->fct = $this->get_id('fct', $cicle->fct);
        $id = $cicle->id;
        $cicle->id = false;
        $this->diposit->afegir_cicle($cicle);
        $this->put_id('fct_cicle', $id, $cicle->id);
    }

    function restore_objecte_fct($json) {
        $fct = fct_json::deserialitzar_fct($json);
        $id = $fct->id;
        $fct->id = false;
        $fct->course = $this->restore->course_id;
        $this->diposit->afegir_fct($fct);
        $this->put_id('fct', $id, $fct->id);
    }

    function restore_objecte_quadern($json) {
        $quadern = fct_json::deserialitzar_quadern($json);
        $quadern->cicle = $this->get_id('fct_cicle', $quadern->cicle);
        $quadern->alumne = $this->get_id('user', $quadern->alumne);
        $quadern->tutor_centre = $this->get_id('user', $quadern->tutor_centre);
        $quadern->tutor_empresa = $this->get_id('user', $quadern->tutor_empresa);
        $id = $quadern->id;
        $quadern->id = false;
        $this->diposit->afegir_quadern($quadern);
        $this->put_id('fct_quadern', $id, $quadern->id);
    }

    function restore_objecte_quinzena($json) {
        $quinzena = fct_json::deserialitzar_quinzena($json);
        $quinzena->quadern = $this->get_id('fct_quadern', $quinzena->quadern);
        $activitats = array();
        foreach ($quinzena->activitats as $activitat_id) {
            $activitat_id = $this->get_id('fct_activitat_pla', $activitat_id);
            if ($activitat_id) {
                $activitats[] = $activitat_id;
            }
        }
        $quinzena->activitats = $activitats;
        $id = $quinzena->id;
        $quinzena->id = false;
        $this->diposit->afegir_quinzena($quinzena);
        $this->put_id('fct_quinzena', $id, $quinzena->id);
    }

    function restore_objectes() {
        $node = $this->info['MOD']['#']['OBJECTES'][0]['#'];
        $tipus_restore = array(
            'FCT' => 'restore_objecte_fct',
            'CICLE' => 'restore_objecte_cicle',
            'QUADERN' => 'restore_objecte_quadern',
            'ACTIVITAT' => 'restore_objecte_activitat',
            'QUINZENA' => 'restore_objecte_quinzena',
            'AVIS' => 'restore_objecte_avis',
        );
        $tipus_userdata = array('QUADERN', 'ACTIVITAT', 'QUINZENA', 'AVIS');
        foreach ($tipus_restore as $tipus => $restore) {
            if ($this->userdata or !in_array($tipus, $tipus_userdata)) {
                foreach ($node[$tipus] as $node_objecte) {
                    $this->$restore($node_objecte['#']);
                }
            }
        }
    }

}

function fct_restore_mods($mod, $restore) {
    global $CFG, $db;

    $userdata = restore_userdata_selected($restore,'fct', $mod->id);
    $data = backup_getid($restore->backup_unique_code, $mod->modtype, $mod->id);
    if (!$data) {
        return false;
    }

    $restore = new fct_restore($restore, $data->info, $userdata);
    $restore->restore_objectes();

    return true;
}
