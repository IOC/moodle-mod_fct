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

require_once($CFG->dirroot . '/mod/fct/lib.php');
fct_require('domini');

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

    if ($result && $oldversion < 2009022700) {
        $quaderns = get_records_select('fct_quadern',
                                      "cicle IS NULL or cicle = 0");
        if ($quaderns) {
            foreach ($quaderns as $quadern) {
                $cicles = get_records('fct_cicle', 'fct', $quadern->fct);
                if ($cicles) {
                    $cicle = array_pop($cicles);
                    set_field('fct_quadern', 'cicle', $cicle->id,
                              'id', $quadern->id);
                }
            }
        }

        $table = $structure->getTable('fct_quadern');
        $field = $table->getField('cicle');
        $result = change_field_type($table, $field, false);
    }

    if ($result && $oldversion < 2009030200) {
        $table = $structure->getTable('fct_qualificacio_global');
        $field_fct = new XMLDBField('fct');
        $field_fct->setAttributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null, 'id');
        $key_cicle = $table->getKey('cicle');
        $key_cicle_alumne = $table->getKey('cicle_alumne');
        $key_fct = new XMLDBKey('fct');
        $key_fct->setAttributes(XMLDB_KEY_FOREIGN, array('fct'), 'fct', 'id');
        $key_fct_alumne = new XMLDBKey('fct_alumne');
        $key_fct_alumne->setAttributes(XMLDB_KEY_UNIQUE, array('fct', 'alumne'));

        $sql = "UPDATE {$CFG->prefix}fct_qualificacio_global qg,"
            . " {$CFG->prefix}fct_quadern q SET qg.cicle = q.cicle "
            . " WHERE qg.cicle = q.fct AND qg.alumne = q.alumne";

        $result = drop_key($table, $key_fct_alumne, false)
            && drop_key($table, $key_fct, false)
            && rename_field($table, $field_fct, 'cicle', false)
            && execute_sql($sql, false)
            && add_key($table, $key_cicle, false)
            && add_key($table, $key_cicle_alumne, false);
    }

    if ($result && $oldversion < 2009030201) {
        $table = new XMLDBTable('fct_quadern');
        $field = new XMLDBField('fct');
        $key = new XMLDBKey('fct');
        $key->setAttributes(XMLDB_KEY_FOREIGN, array('fct'), 'fct', 'id');
        $result = drop_key($table, $key, false)
            && drop_field($table, $field, false);
    }

    if ($result && $oldversion < 2009061700) {
        $table = new XMLDBTable('fct_quinzena');
        $field = new XMLDBField('hores');
        $field->setAttributes(XMLDB_TYPE_NUMBER, '4, 2', XMLDB_UNSIGNED,
                              XMLDB_NOTNULL, null, null, null, null, 'id');
        $result = change_field_type($table, $field, false);
    }

    if ($result && $oldversion < 2009061800) {
        $table_dades_conveni = new XMLDBTable('fct_dades_conveni');
        $table_dades_horari = new XMLDBTable('fct_dades_horari');
        $field_codi = new XMLDBField('codi');
        $field_data_inici = new XMLDBField('data_inici');
        $field_data_final = new XMLDBField('data_final');
        $table_conveni = $structure->getTable('fct_conveni');
        $table_horari = $structure->getTable('fct_horari');

        $sql_conveni = "INSERT INTO {$CFG->prefix}fct_conveni"
            . " (quadern, codi, data_inici, data_final)"
            . " SELECT quadern, codi, data_inici, data_final"
            . " FROM {$CFG->prefix}fct_dades_conveni";

        $sql_horari = "INSERT INTO {$CFG->prefix}fct_horari"
            . " (conveni, dilluns, dimarts, dimecres, dijous,"
            . " divendres, dissabte, diumenge)"
            . " SELECT c.id, h.dilluns, h.dimarts, h.dimecres,"
            . " h.dijous, h.divendres, h.dissabte, h.diumenge"
            . " FROM {$CFG->prefix}fct_conveni c,"
            . " {$CFG->prefix}fct_dades_horari h"
            . " WHERE c.quadern = h.quadern";

        $result = create_table($table_conveni, false)
            && create_table($table_horari, false)
            && execute_sql($sql_conveni)
            && execute_sql($sql_horari)
            && drop_field($table_dades_conveni, $field_codi, false)
            && drop_field($table_dades_conveni, $field_data_inici, false)
            && drop_field($table_dades_conveni, $field_data_final, false)
            && drop_table($table_dades_horari, false);
    }

    if ($result && $oldversion < 2009070600) {
        $table = $structure->getTable('fct');
        $field1 = $table->getField('frases_centre');
        $field2 = $table->getField('frases_empresa');
        $result = add_field($table, $field1, false)
            && add_field($table, $field2, false);
    }

    if ($result && $oldversion < 2009072700) {
        $table = $structure->getTable('fct_quinzena');
        $field = $table->getField('objecte');
        $result = add_field($table, $field, false);

        if ($result) {
            $records = get_records('fct_quinzena');
            $records = $records ? $records : array();
            foreach ($records as $record) {
                $objecte = (object) array(
                    'id' => $record->id,
                    'quadern' => $record->quadern,
                    'any' => $record->any_,
                    'periode' => $record->periode,
                    'hores' => $record->hores,
                    'dies' => array(),
                    'activitats' => array(),
                    'valoracions' => $record->valoracions,
                    'observacions_alumne' => $record->observacions_alumne,
                    'observacions_centre' => $record->observacions_centre,
                    'observacions_empresa' => $record->observacions_empresa,
                );

                $records_dies = get_records('fct_dia_quinzena',
                                            'quinzena', $record->id);
                if ($records_dies) {
                    foreach ($records_dies as $record_dia) {
                        $objecte->dies[] = $record_dia->dia;
                    }
                }

                $records_activitats = get_records('fct_activitat_quinzena',
                                                  'quinzena', $record->id);
                if ($records_activitats) {
                    foreach ($records_activitats as $record_activitat) {
                        $objecte->activitats[] = $record_activitat->activitat;
                    }
                }

                $record->objecte = json_encode($objecte);
                update_record('fct_quinzena', addslashes_recursive($record));
            }
        }

        $fields = array('hores', 'valoracions', 'observacions_alumne',
                        'observacions_centre', 'observacions_empresa');
        foreach ($fields as $name) {
            $result = $result && drop_field($table, new XMLDBField($name), false);
        }

        $tables = array('fct_dia_quinzena', 'fct_activitat_quinzena');
        foreach ($tables as $name) {
            $result = $result && drop_table(new XMLDBTable($name), false);
        }
    }

    if ($result && $oldversion < 2009072701) {
        $table = $structure->getTable('fct_activitat');
        $field = $table->getField('objecte');
        $result = rename_table(new XMLDBTable('fct_activitat_pla'),
                               'fct_activitat', false)
            && add_field($table, $field, false);

        if ($result) {
            $records = get_records('fct_activitat');
            $records = $records ? $records : array();
            foreach ($records as $record) {
                $objecte = (object) array(
                    'id' => $record->id,
                    'quadern' => $record->quadern,
                    'descripcio' => $record->descripcio,
                    'nota' => $record->nota,
                );
                $record->objecte = json_encode($objecte);
                update_record('fct_activitat', addslashes_recursive($record));
            }

            $result =  drop_field($table, new XMLDBField('descripcio'), false)
                && drop_field($table, new XMLDBField('nota'), false);
        }
    }

    if ($result && $oldversion < 2009072702) {
        $table = $structure->getTable('fct_quadern');
        $result = add_field($table, $table->getField('data_final'), false)
            && add_field($table, $table->getField('objecte'), false)
            && add_index($table, $table->getIndex('estat'), false)
            && add_index($table, $table->getIndex('data_final'), false);

        if ($result) {
            $records_quaderns = get_records('fct_quadern');
            $records_quaderns = $records_quaderns ? $records_quaderns : array();
            foreach ($records_quaderns as $record_quadern) {
                $objecte = (object) array(
                    'id' => $record_quadern->id,
                    'cicle' => $record_quadern->cicle,
                    'alumne' => $record_quadern->alumne,
                    'tutor_centre' => $record_quadern->tutor_centre,
                    'tutor_empresa' => $record_quadern->tutor_empresa,
                    'estat' => $record_quadern->estat,
                );

                $record = get_record('fct_dades_empresa',
                                     'quadern', $objecte->id);
                $objecte->empresa = (object) array(
                    'nom' => $record_quadern->nom_empresa,
                    'adreca' => $record->adreca,
                    'poblacio' => $record->poblacio,
                    'codi_postal' => $record->codi_postal,
                    'telefon' => $record->telefon,
                    'fax' => $record->fax,
                    'email' => $record->email,
                    'nif' => $record->nif,
                );

                $record = get_record('fct_dades_relatives', 'quadern', $objecte->id);
                $objecte->hores_credit = $record->hores_credit;
                $objecte->exempcio = $record->exempcio;
                $objecte->hores_anteriors = $record->hores_anteriors;

                $record = get_record('fct_dades_conveni', 'quadern', $objecte->id);
                $objecte->prorrogues = $record->prorrogues;
                $objecte->hores_practiques = $record->hores_practiques;

                $record = get_record('fct_qualificacio_quadern', 'quadern', $objecte->id);
                $objecte->qualificacio->apte = $record->qualificacio;
                $objecte->qualificacio->nota = $record->nota;
                $objecte->qualificacio->data = $record->data;
                $objecte->qualificacio->observacions = $record->observacions;

                $records = get_records('fct_conveni', 'quadern', $objecte->id, 'data_inici');
                $record_quadern->data_final = 0;
                if ($records) {
                    foreach ($records as $record) {
                        $conveni = (object) array(
                            'uuid' => fct_uuid(),
                            'codi' => $record->codi,
                            'data_inici' => $record->data_inici,
                            'data_final' => $record->data_final,
                        );
                        $record_quadern->data_final = max($record_quadern->data_final,
                                                          $record->data_final);
                        $record = get_record('fct_horari','conveni', $record->id);
                        $conveni->horari = (object) array(
                            'dilluns' => $record->dilluns,
                            'dimarts' => $record->dimarts,
                            'dimecres' => $record->dimecres,
                            'dijous' => $record->dijous,
                            'divendres' => $record->divendres,
                            'dissabte' => $record->dissabte,
                            'diumenge' => $record->diumenge,
                        );
                        $objecte->convenis[] = $conveni;
                    }
                }

                $records = get_records('fct_valoracio_actituds', 'quadern', $objecte->id, 'actitud');
                $objecte->valoracio_parcial = array();
                $objecte->valoracio_final = array();
                if ($records) {
                    foreach ($records as $record) {
                        if ($record->final) {
                            $objecte->valoracio_final[$record->actitud] = $record->nota;
                        } else {
                            $objecte->valoracio_parcial[$record->actitud] = $record->nota;
                        }
                    }
                }

                $fct_id = get_field('fct_cicle', 'fct', 'id', $objecte->cicle);
                $where = "fct = {$fct_id} AND alumne = {$objecte->alumne}";
                $records = get_records_select('fct_dades_alumne', $where);
                if ($records) {
                    $record = array_pop($records);
                    $objecte->dades_alumne->adreca = $record->adreca;
                    $objecte->dades_alumne->poblacio = $record->poblacio;
                    $objecte->dades_alumne->codi_postal = $record->codi_postal;
                    $objecte->dades_alumne->telefon = $record->telefon;
                    $objecte->dades_alumne->email = $record->email;
                    $objecte->dades_alumne->dni = $record->dni;
                    $objecte->dades_alumne->targeta_sanitaria = $record->targeta_sanitaria;
                } else {
                    $objecte->dades_alumne->adreca = '';
                    $objecte->dades_alumne->poblacio = '';
                    $objecte->dades_alumne->codi_postal = '';
                    $objecte->dades_alumne->telefon = '';
                    $objecte->dades_alumne->email = '';
                    $objecte->dades_alumne->dni = '';
                    $objecte->dades_alumne->targeta_sanitaria = '';
                }

                $where = "cicle = {$objecte->cicle} AND alumne = {$objecte->alumne}";
                $records = get_records_select('fct_qualificacio_global', $where);
                if ($records) {
                    $record = array_pop($records);
                    $objecte->qualificacio_global->apte = $record->qualificacio;
                    $objecte->qualificacio_global->nota = $record->nota;
                    $objecte->qualificacio_global->data = $record->data;
                    $objecte->qualificacio_global->observacions = $record->observacions;
                } else {
                    $objecte->qualificacio_global->apte = 0;
                    $objecte->qualificacio_global->nota = 0;
                    $objecte->qualificacio_global->data = 0;
                    $objecte->qualificacio_global->observacions = '';
                }

                $record_quadern->objecte = json_encode($objecte);
                update_record('fct_quadern', addslashes_recursive($record_quadern));
            }
        }

        $tables = array('fct_dades_alumne', 'fct_dades_centre_concertat',
                        'fct_dades_empresa', 'fct_dades_conveni',
                        'fct_conveni', 'fct_horari', 'fct_dades_relatives',
                        'fct_valoracio_actituds', 'fct_qualificacio_quadern',
                        'fct_qualificacio_global');
        foreach ($tables as $name) {
            $result = $result && drop_table(new XMLDBTable($name), false);
        }
    }

    if ($result && $oldversion < 2009072703) {
        $table = $structure->getTable('fct_cicle');
        $result = add_field($table, $table->getField('objecte'), false);

        if ($result) {
            $records = get_records('fct_cicle');
            $records = $records ? $records : array();
            foreach ($records as $record) {
                $objecte = (object) array(
                    'id' => $record->id,
                    'fct' => $record->fct,
                    'nom' => $record->nom,
                    'activitats' => array(),
                    'n_quaderns' => count_records('fct_quadern', 'cicle', $record->id),
                );
                $objecte->activitats = array();
                $activitats = get_records('fct_activitat_cicle', 'cicle', $record->id, 'descripcio');
                foreach ($activitats as $activitat) {
                    $objecte->activitats[] = $activitat->descripcio;
                }
                $record->objecte = json_encode($objecte);
                update_record('fct_cicle', addslashes_recursive($record));
            }

            $result = drop_table(new XMLDBTable('fct_activitat_cicle'), false);
        }
    }

    if ($result && $oldversion < 2009072704) {
        $table = $structure->getTable('fct');
        $result = add_field($table, $table->getField('objecte'), false);

        if ($result) {
            $records = get_records('fct');
            $records = $records ? $records : array();
            foreach ($records as $record) {
                $objecte = (object) array(
                    'id' => $record->id,
                    'course' => $record->course,
                    'name' => $record->name,
                    'intro' => $record->intro,
                    'timecreated' => $record->timecreated,
                    'timemodified' => $record->timemodified,
                    'frases_centre' => fct_linies_text($record->frases_centre),
                    'frases_empresa' => fct_linies_text($record->frases_empresa),
                );
                $record_centre = get_record('fct_dades_centre', 'fct', $record->id);
                $objecte->centre = (object) array(
                    'nom' => $record_centre->nom,
                    'adreca' => $record_centre->adreca,
                    'codi_postal' => $record_centre->codi_postal,
                    'poblacio' => $record_centre->poblacio,
                    'telefon' => $record_centre->telefon,
                    'fax' => $record_centre->fax,
                    'email' => $record_centre->email,
                );
                $record->objecte = json_encode($objecte);
                update_record('fct', addslashes_recursive($record));
            }

            $result = drop_field($table, new XMLDBField('frases_centre'), false)
                && drop_field($table, new XMLDBField('frases_empresa'), false)
                && drop_table(new XMLDBTable('fct_dades_centre'), false);
        }
    }

    if ($result && $oldversion < 2010030100) {
        $table = $structure->getTable('fct_avis');
        $result = create_table($table, false);
    }

    if ($result && $oldversion < 2010090900) {
        $table = $structure->getTable('fct_quadern');
        $index = $table->getIndex('estat');
        $field = $table->getField('estat');

        $result = drop_index($table, $index, false)
            && change_field_type($table, $field, false)
            && add_index($table, $index, false);

        $records = get_records('fct_quadern');
        if ($records) {
            foreach ($records as $record) {
                $objecte = json_decode($record->objecte, true);
                $objecte['estat'] = $objecte['estat'] ? 'obert' : 'tancat';
                $record->objecte = json_encode($objecte);
                $record->estat = $objecte['estat'];
                $record = addslashes_recursive($record);
                $result = $result && update_record('fct_quadern', $record);
            }
        }
    }

    if ($result && $oldversion < 2010091301) {
        $expr_hora = "(\d{1,2})(?:[\.,:;'´h](\d{1,2}))?";
        $expr_franja = "{$expr_hora}\D+{$expr_hora}";
        $expr_dia = "\D*{$expr_franja}(?:\D+{$expr_franja})?\D*";

        $records = get_records('fct_quadern');
        if ($records) {
            foreach ($records as $record) {
                $quadern = json_decode($record->objecte, true);
                foreach ($quadern['convenis'] as $index => $conveni) {
                    $horari = array();
                    foreach ($conveni['horari'] as $dia => $text) {
                        if (preg_match("/^{$expr_dia}$/", $text, $m)) {
                            if (isset($m[1])) {
                                $inici = (float) $m[1] + (isset($m[2]) ? (float) $m[2] : 0.0) / 60;
                                $final = (float) $m[3] + (isset($m[4]) ? (float) $m[4] : 0.0) / 60;
                                $horari[] = array('dia' => $dia,
                                                  'hora_inici' => $inici,
                                                  'hora_final' => $final);
                            }
                            if (isset($m[5])) {
                                $inici = (float) $m[5] + (isset($m[6]) ? (float) $m[6] : 0.0) / 60;
                                $final = (float) $m[7] + (isset($m[8]) ? (float) $m[8] : 0.0) / 60;
                                $horari[] = array('dia' => $dia,
                                                  'hora_inici' => $inici,
                                                  'hora_final' => $final);
                            }
                        }
                    }
                    $quadern['convenis'][$index]['horari'] = $horari;
                }
                $record->objecte = json_encode($quadern);
                $record = addslashes_recursive($record);
                $result = $result && update_record('fct_quadern', $record);
            }
        }
    }

    return $result;
}
