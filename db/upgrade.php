<?php

function xmldb_fct_upgrade($oldversion=0) {
    $result = true;

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

        $key = new XMLDBKey('seguiment_any_dia');
        $key->setAttributes(XMLDB_KEY_UNIQUE, array('seguiment', 'any_', 'dia'));
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

    return $result;
}

?>