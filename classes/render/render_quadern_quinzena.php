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
 * Renderers for outputting fct quinzenes.
 *
 * @package    mod
 * @subpackage fct
 * @copyright  2014 Institut Obert de Catalunya
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

class mod_fct_quinzena_renderer extends plugin_renderer_base {

    public function quinzenes_table($quinzenes) {
        $data = array();

        foreach ($quinzenes as $quinzena) {
                $data[] = $this->make_table_line($quinzena);
        }

        $table = new html_table();
        $table->head = array(get_string('any', 'mod_fct'), get_string('periode', 'mod_fct'),
                             get_string('dies', 'mod_fct'), get_string('hores', 'mod_fct'),
                             get_string('edit'));
        $table->data = $data;
        $table->id = 'quinzenes';
        $table->attributes['class'] = 'quinzenes generaltable';
        $table->colclasses = array('', '', '', '', 'edit');

        $output = html_writer::table($table);
        return $output;

    }

    private function make_table_line($quinzena) {

        global $PAGE, $OUTPUT;

        $line = array();

        $line['any'] = $quinzena->any;
        $line['periode'] = format_string($quinzena->nom_periode($quinzena->periode));
        $line['dies'] = count($quinzena->dies);
        $line['hores'] = $quinzena->hores;

        $buttons = array();

        $params = array(
            'cmid' => $PAGE->cm->id,
            'id' => $quinzena->id,
            'quadern' => $quinzena->quadern,
            'page' => 'quadern_quinzena',
        );
        $editlink = new moodle_url('./edit.php', $params);
        $params = array(
            'src' => $OUTPUT->pix_url('t/edit'),
            'alt' => get_string('edit'),
            'class' => 'iconsmall',
        );
        $editicon = html_writer::empty_tag('img', $params);
        $params = array(
            'id' => $quinzena->id,
            'cmid' => $PAGE->cm->id,
            'delete' => 1,
            'page' => 'quadern_quinzena',
            'quadern' => $quinzena->quadern,
        );
        $deletelink = new moodle_url('./edit.php', $params);
        $deleteicon = html_writer::empty_tag('img',
            array('src' => $OUTPUT->pix_url('t/delete'), 'alt' => get_string('delete'), 'class' => 'iconsmall'));

        $buttons[] = html_writer::link($editlink, $editicon);
        $buttons[] = html_writer::link($deletelink, $deleteicon);
        $line[] = implode(' ', $buttons);
        return $line;
    }

    public function resum_table($title, $lines) {
        $table = new html_table();
        $table->head = array($title,
                             get_string('dies', 'mod_fct'),
                             get_string('hores', 'mod_fct'));
        $table->data = $lines;
        $table->id = 'quinzenes';
        $table->attributes['class'] = 'admintable generaltable';

        $output = html_writer::table($table);

        echo $output;

    }

    public function data_prevista($dataprevista) {
        $text = get_string('data_prevista_valoracio_parcial', 'fct', userdate($dataprevista, get_string('strftimedate')));
        return html_writer::tag('div', $text, array('class' => 'fct_data_prevista'));
    }

}
