<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Renderers for outputting avisos main view.
 *
 * @package    mod
 * @subpackage fct
 * @copyright  2014 Institut Obert de Catalunya
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

class mod_fct_avisos_renderer extends plugin_renderer_base {

    public function avisos_table($avisos) {

        $data = array();

        if ($avisos) {
            foreach ($avisos as $avis) {
                $data[] = $this->make_table_line($avis);
            }
        }

        $table = new html_table();
        $table->head = array(get_string('data', 'mod_fct'),
                             get_string('avis', 'mod_fct'),
                             get_string('quadern', 'mod_fct'),
                             get_string('edit'));
        $table->data = $data;
        $table->id = 'avisos';
        $table->attributes['class'] = 'admintable generaltable';

        $output = html_writer::table($table);
        return $output;

    }

    private function make_table_line($avis) {
        global $DB, $PAGE, $OUTPUT;

        if (!$fct = $DB->get_record('fct', array('id' => $PAGE->cm->instance))) {
           print_error('course module is incorrect');
        }

        $line = array();

        $line[] = userdate($avis->data, get_string('strftimedatetimeshort'));
        $line[] = $avis->titol_avis();

        $quadern = $avis->quadern();

        $user = $DB->get_record('user', array('id' => $quadern->alumne));
        $fullname = fullname($user);
        $line[] = $fullname. ' ' . $quadern->nom_empresa;

        $deletelink = new moodle_url('./edit.php', array('cmid' => $PAGE->cm->id, 'id' => $avis->id, 'page' => 'avisos', 'fct' => $fct->id, 'delete' => 1));
        $deleteicon = html_writer::empty_tag('img',
                    array('src' => $OUTPUT->pix_url('t/delete'), 'alt' => get_string('delete'), 'class' => 'iconsmall'));
        $deletebutton = html_writer::link($deletelink, $deleteicon);

        $line[] = $deletebutton;

        return $line;
    }

}
