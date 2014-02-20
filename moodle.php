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

    function assign_role($userid, $courseid, $role) {
        $roleid = get_field('role', 'id', 'shortname', addslashes($role));
        $context = get_context_instance(CONTEXT_COURSE, $courseid);
        if (!$roleid or !role_assign($roleid, $userid, 0, $context->id, time())) {
            throw new fct_exception('moodle: assign_role');
        }
    }

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

    function create_user($username, $email, $firstname, $lastname) {
        global $CFG;
        $user = (object) array(
            'username' => $username,
            'firstname' => $firstname,
            'lastname' => $lastname,
            'email' => $email,
            'auth' => $CFG->local_secretaria_auth,
            'confirmed' => 1,
            'emailstop' => 0,
            'lang' => current_language(),
            'mnethostid' => $CFG->mnet_localhost_id,
            'secret' => random_string(15),
            'timemodified' => time(),
        );
        $params = array(
            'properties' => array (
                'username' => $username,
                'password' => $this->generate_valid_password(),
                'firstname' => $firstname,
                'lastname' => $lastname,
                'email' => $email,
            )
        );
        if ($this->call_moodle2($params)) {
            if ($id = insert_record('user', addslashes_recursive($user))) {
                $user = get_record('user', 'id', $id);
                events_trigger('user_created', $user);
                $this->mail_user($user, $params['properties']['password']);
                return $id;
            } else {
                throw new fct_exception('moodle: create_user');
            }
        } else {
            throw new fct_exception('moodle2: create_user');
        }
    }

    function delete_dir($courseid, $path) {
        global $CFG;
        require_once("{$CFG->dirroot}/lib/filelib.php");
        $fullpath = "{$CFG->dataroot}/$courseid/{$CFG->moddata}/fct/$path";
        if (is_dir($fullpath)) {
            fulldelete($fullpath);
        }
    }

    function delete_file($courseid, $path) {
        global $CFG;
        $base = "{$CFG->dataroot}/$courseid/{$CFG->moddata}/fct/";
        foreach (glob("$base$path~*") as $fullpath) {
            unlink($fullpath);
        }
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

    function file_url($courseid, $path) {
        global $CFG;
        $base = "{$CFG->dataroot}/$courseid/{$CFG->moddata}/fct/";
        if ($fullpaths = glob("$base$path~*")) {
            return ("{$CFG->wwwroot}/file.php/$courseid/moddata/fct/" .
                    substr($fullpaths[0], strlen($base)));
        }
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

    function upload_file($tmp_path, $courseid, $path) {
        global $CFG;
        $this->delete_file($courseid, $path);
        $hash = md5_file($tmp_path);
        make_upload_directory("$courseid/{$CFG->moddata}/fct/" . dirname($path));
        move_uploaded_file($tmp_path, "{$CFG->dataroot}/$courseid/{$CFG->moddata}/fct/$path~$hash");
    }

    private function generate_valid_password() {
        $letters = 'abcdefghijklmnopqrstvwxyz';
        $length = strlen($letters) - 1;
        $pass = $letters[mt_rand(0, $length)];
        $pass .= mt_rand(0, 9);
        $pass .= strtoupper($letters[mt_rand(0, $length)]);
        $pass .= $letters[mt_rand(0, $length)];
        $pass .= mt_rand(0, 9);
        $pass .= mt_rand(0, 1) ? mt_rand(0, 9) : $letters[mt_rand(0, $length)];
        $pass .= $letters[mt_rand(0, $length)];
        $pass .= mt_rand(0, 9);
        $pass .= $letters[mt_rand(0, $length)];
        $pass .= strtoupper($letters[mt_rand(0, $length)]);
        $pass = mt_rand(0, 1) ? strrev($pass) : $pass;
        return $pass;
    }

    private function mail_user($user, $newpassword) {

        global $CFG;

        $site  = get_site();

        $supportuser = generate_email_supportuser();

        $a = new object();
        $a->firstname   = fullname($user, true);
        $a->sitename    = format_string($site->fullname);
        $a->username    = $user->username;
        $a->newpassword = $newpassword;
        $a->link        = $CFG->wwwroot .'/login/';
        $a->signoff     = generate_email_signoff();

        $message = get_string('newusernewpasswordtext', '', $a);

        $subject  = format_string($site->fullname) .': '. get_string('newusernewpasswordsubj');
        return email_to_user($user, $supportuser, $subject, $message);
    }

    private function call_moodle2($params) {
        global $CFG;

        $url = "{$CFG->local_webservice_campus2_url}/webservice/rest/server.php"
            . "?wstoken={$CFG->local_webservice_campus2_token}&wsfunction=secretaria_create_user"
            . "&moodlewsrestformat=json";
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $this->format_params($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3600);
        $response = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);
        $response = json_decode($response, true);
        if (isset($response['exception'])) {
            throw new fct_exception($response['message']);
        }
        if ($error) {
            throw new fct_exception($error);
        }

        return true;
    }

    private function format_params(array $params, $prefix='') {
        $result = array();
        foreach ($params as $key => $value) {
            $key = $prefix ? $prefix.'['.urlencode($key).']' : urlencode($key);
            if (is_array($value)) {
                $result[] = $this->format_params($value, $key);
            } else if (is_bool($value)) {
                $result[] = $key.'='.((int) $value);
            } else {
                $result[] = $key.'='.urlencode($value);
            }
        }
        return implode('&', $result);
    }
}
