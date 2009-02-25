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

function xmldb_fct_upgrade($oldversion=0) {
    global $CFG;

    $result = true;

    $xmldb_file = new XMLDBFile($CFG->dirroot . '/mod/fct/db/install.xml');
    $xmldb_file->loadXMLStructure();
    $structure = $xmldb_file->getStructure();

    if ($result && $oldversion < 2008100200) {
        $table = new XMLDBTable('fct_dades_relatives');
        $field = new XMLDBField('hores_anteriors');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '4', true, true, null, null, null, '0', 'exempcio');
        $result = $result && add_field($table, $field);   
    }

    if ($result && $oldversion < 2008100800) {
        $table = new XMLDBTable('fct_activitat_pla');

        $key = new XMLDBKey('pla');
        $key->setAttributes(XMLDB_KEY_FOREIGN, array('pla'), 'fct_pla', 'id');
        $result = $result && drop_key($table, $key);

        $field = new XMLDBField('pla');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '10', null, true, null, null, null, '0', 'id');
        $result = $result && rename_field($table, $field, 'quadern', false);

        $key = new XMLDBKey('quadern');
        $key->setAttributes(XMLDB_KEY_FOREIGN, array('quadern'), 'fct_quadern', 'id');
        $result = $result && add_key($table, $key, false);        

        $table = new XMLDBTable('fct_pla');
        $result = $result && drop_table($table, false);

        $table = new XMLDBTable('fct_quinzena');

        $key = new XMLDBKey('seguiment');
        $key->setAttributes(XMLDB_KEY_FOREIGN, array('seguiment'), 'fct_seguiment', 'id');
        $result = $result && drop_key($table, $key);

        $key = new XMLDBKey('seguiment_any_periode');
        $key->setAttributes(XMLDB_KEY_UNIQUE, array('seguiment', 'any_', 'periode'));
        $result = $result && drop_key($table, $key);

        $field = new XMLDBField('seguiment');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '10', null, true, null, null, null, '0', 'id');
        $result = $result && rename_field($table, $field, 'quadern', false);

        $key = new XMLDBKey('quadern');
        $key->setAttributes(XMLDB_KEY_FOREIGN, array('quadern'), 'fct_quadern', 'id');
        $result = $result && add_key($table, $key, false);        

        $key = new XMLDBKey('quadern_any_periode');
        $key->setAttributes(XMLDB_KEY_UNIQUE, array('quadern', 'any_', 'periode'));
        $result = $result && add_key($table, $key, false);

        $table = new XMLDBTable('fct_seguiment');
        $result = $result && drop_table($table, false);
    }

    if ($result && $oldversion < 2009012800) {
        $table_plantilla = new XMLDBTable('fct_plantilla');
        $table_activitat_plantilla = new XMLDBTable('fct_activitat_plantilla');
        $table_cicle = new XMLDBTable('fct_cicle');
        $table_activitat_cicle = new XMLDBTable('fct_activitat_cicle');

        $field_plantilla = new XMLDBField('plantilla');
        $field_plantilla->setAttributes(XMLDB_TYPE_INTEGER, '10', null, true, null, null, null, '0', 'id');

        $key_fct = new XMLDBKey('fct');
        $key_fct->setAttributes(XMLDB_KEY_FOREIGN, array('fct'), 'fct', 'id');
        $key_fct_nom = new XMLDBKey('fct_nom');
        $key_fct_nom->setAttributes(XMLDB_KEY_UNIQUE, array('fct', 'nom'));
        $key_plantilla = new XMLDBKey('plantilla');
        $key_plantilla->setAttributes(XMLDB_KEY_FOREIGN, array('plantilla'),
                                      'fct_plantilla', 'id');
        $key_cicle = new XMLDBKey('cicle');
        $key_cicle->setAttributes(XMLDB_KEY_FOREIGN, array('cicle'),
                                  'fct_cicle', 'id');

        $result = drop_key($table_plantilla, $key_fct, false)
            && drop_key($table_plantilla, $key_fct_nom, false)
            && drop_key($table_activitat_plantilla, $key_plantilla, false)
            && rename_table($table_plantilla, 'fct_cicle')
            && rename_field($table_activitat_plantilla,
                            $field_plantilla, 'cicle', false)
            && rename_table($table_activitat_plantilla,
                            'fct_activitat_cicle', false)
            && add_key($table_cicle, $key_fct, false)
            && add_key($table_cicle, $key_fct_nom, false)
            && add_key($table_activitat_cicle, $key_cicle, false);
    }

    if ($result && $oldversion < 2009012900) {
        $table = new XMLDBTable('fct_quadern');
        $field = new XMLDBField('cicle');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null, 'nom_empresa');
        $result = $result && add_field($table, $field, false);

        $key = new XMLDBKey('cicle');
        $key->setAttributes(XMLDB_KEY_FOREIGN, array('cicle'), 'fct_cicle', 'id');
        $result = $result && add_key($table, $key, false);
    }

    if ($result && $oldversion < 2009021200) {
        $table = new XMLDBTable('fct_dades_relatives');
        $table_temp = new XMLDBTable('fct_dades_relatives_temp');
        $result = $result && rename_table($table, $table_temp->getName(), false);

        $table = $structure->getTable('fct_dades_relatives');
        $result = $result && create_table($table, false);

        $quaderns = get_records('fct_quadern');

        if ($result && $quaderns) {
            foreach ($quaderns as $quadern) {
                $dades = array('quadern' => $quadern->id,
                               'hores_credit' => 0,
                               'exempcio' => 0,
                               'hores_anteriors' => 0);
                $record = get_record('fct_dades_relatives_temp',
                                     'fct', $quadern->fct,
                                     'alumne', $quadern->alumne);
                if ($record) {
                    $dades['hores_credit'] = $record->hores_credit;
                    $dades['exempcio'] = $record->exempcio;
                    $dades['hores_anteriors'] = $record->hores_anteriors;
                }
                $result = $result && insert_record('fct_dades_relatives',
                                                   (object) $dades);
            }
        }

        $result = $result && drop_table($table_temp);
    }

    return $result;
}

?>