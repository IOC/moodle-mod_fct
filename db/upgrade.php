<?php

function xmldb_fct_upgrade($oldversion=0) {
    $result = true;

    if ($result && $oldversion < 2008100200) {
        $table = new XMLDBTable('fct_dades_relatives');
        $field = new XMLDBField('hores_anteriors');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '4', true, true, null, null, null, '0', 'exempcio');
        $result = $result && add_field($table, $field);   
    }

    return $result;
}

?>