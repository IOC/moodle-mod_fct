<?php
/* Quadern virtual d'FCT

   Copyright © 2009,2010  Institut Obert de Catalunya

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

class fct_moodle {

    function count_records($table, $field1='', $value1='',
                           $field2='', $value2='', $field3='', $value3='') {
        return count_records($table, $field1, addslashes($value1),
                             $field2, addslashes($value2),
                             $field3, addslashes($value3));
    }

    function count_records_select($table, $select='') {
        return count_records_select($table, $select);
    }

    function count_records_sql($sql) {
        return count_records_sql($sql);
    }

    function delete_records($table, $field1='', $value1='',
                            $field2='', $value2='', $field3='', $value3='') {
        $rs = delete_records($table, $field1, addslashes($value1),
                             $field2, addslashes($value2),
                             $field3, addslashes($value3));
        if (!$rs) {
            throw new fct_exception('moodle: delete_records');
        }
    }

    function delete_records_select($table, $select='') {
        delete_records_select($table, $select);
    }

    function get_context_instance($contextlevel, $instance=0) {
        return get_context_instance($contextlevel, $instance);
    }

    function get_coursemodule_from_id($modulename, $cmid) {
        if (!$cm = get_coursemodule_from_id($modulename, $cmid))  {
            throw new fct_exception('moodle: get_coursemodule_from_id');
        }
        return $cm;
    }

    function get_coursemodule_from_instance($modulename, $instance) {
        if (!$cm = get_coursemodule_from_instance($modulename, $instance)) {
            throw new fct_exception('moodle: get_coursemodule_from_instance');
        }
        return $cm;
    }

    function get_field($table, $return, $field1, $value1,
                       $field2='', $value2='', $field3='', $value3='') {
        $field = get_field($table, $return, $field1, addslashes($value1),
                           $field2, addslashes($value3),
                           $field3, addslashes($value3));
        if ($field === false) {
            throw new fct_exception('moodle: get_field');
        }
        return $field;
    }

    function get_record($table, $field1, $value1, $field2='', $value2='',
                        $field3='', $value3='', $fields='*') {
        $record = get_record($table, $field1, addslashes($value1),
                             $field2, addslashes($value2),
                             $field3, addslashes($value3), $fields);
        if (!$record) {
            throw new fct_exception('moodle: get_record');
        }
        return $record;
    }

    function get_record_select($table, $select='', $fields='*') {
        $record = get_record_select($table, $select, $fields);
        if (!$record) {
            throw new fct_exception('moodle: get_record');
        }
        return $record;
    }

    function get_record_sql($sql) {
        $record = get_record_sql($sql);
        if (!$record) {
            throw new fct_exception('moodle: get_record_sql');
        }
        return $record;
    }

    function get_records($table, $field='', $value='', $sort='',
                         $fields='*',$limitfrom='', $limitnum='') {
        $records = get_records($table, $field, addslashes($value), $sort,
                               $fields, $limitfrom, $limitnum);
        return $records ? $records : array();
    }

    function get_records_select($table, $select='', $sort='', $fields='*',
                                $limitfrom='', $limitnum='') {
        $records = get_records_select($table, $select, $sort, $fields,
                                      $limitfrom, $limitnum);
        return $records ? $records : array();
    }

    function get_records_sql($sql, $limitfrom='', $limitnum='') {
        $records = get_records_sql($sql, $limitfrom, $limitnum);
        return $records ? $records : array();
    }

    function has_capability($capability, $context, $userid, $doanything=true) {
        return has_capability($capability, $context, $userid, $doanything);
    }

    function insert_record($table, $dataobject) {
        if (!$id = insert_record($table, addslashes_recursive($dataobject))) {
            throw new fct_exception('moodle: insert_record');
        }
        return $id;
    }

    function time() {
        return time();
    }

    function update_record($table, $dataobject) {
        if (!update_record($table, addslashes_recursive($dataobject))) {
            throw new fct_exception('moodle: update_record');
        }
    }

}
