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
 * Renderers for outputting fct quadern horari.
 *
 * @package    mod
 * @subpackage fct
 * @copyright  2014 Institut Obert de Catalunya
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

class mod_fct_quadern_horari_renderer extends plugin_renderer_base {

    public function view($quadern) {

        $output = '';

        $cm = get_coursemodule_from_instance('fct', $quadern->fct);
        $link = new moodle_url('./edit.php', array('cmid' => $cm->id, 'quadern' => $quadern->id, 'page' => 'quadern_dades', 'subpage' => 'quadern_horari'));

        if (isset($quadern->convenis)) {

            foreach ((array)$quadern->convenis as $conveni) {

                $output .= html_writer::start_div('datagroup');
                $output .= html_writer::tag('span', get_string('conveni', 'fct').':', array('class' => 'datatitle'));
                $output .= html_writer::tag('span', $conveni->codi, array('class' => 'datacontent'));
                $output .= html_writer::end_div();

                if (isset($conveni->horari)) {
                    $output .= $this->horari_table($conveni, $quadern->id);
                }
            }
        }

        $output .= html_writer::link($link, get_string('edit'));

        echo $output;

    }

    public function horari_table($conveni, $quadern) {

        $output = '';

        $data = array();

        foreach ($conveni->horari as $key => $value) {
            $data[] = $this->make_horari_line($value, $conveni->uuid, $quadern);
        }

        $table = new html_table();
        $table->head = array(get_string('dia', 'mod_fct'), get_string('de', 'mod_fct'), get_string('a', 'mod_fct'), get_string('edit'));
        $table->data = $data;
        $table->id = 'quadern_horari';
        $table->attributes['class'] = 'admintable generaltable';

        $output .= html_writer::table($table);

        return $output;

    }

    public function make_horari_line($horari, $uuid, $quadern) {
        global $OUTPUT, $PAGE;

        $line[] = $horari->dia;
        $line[] = str_replace('.', ':', $horari->hora_inici);
        $line[] = str_replace('.', ':', $horari->hora_final);

        $deletelink = new moodle_url('./edit.php', array('cmid' => $PAGE->cm->id,
                                                         'quadern' => $quadern,
                                                         'uuid' => $uuid,
                                                         'dia' => $horari->dia,
                                                         'hora_inici' => $horari->hora_inici,
                                                         'hora_final' => $horari->hora_final,
                                                         'page' => 'quadern_horari',
                                                         'delete' => 1));
        $deleteicon = html_writer::empty_tag('img',
            array('src' => $OUTPUT->pix_url('t/delete'), 'alt' => get_string('delete'), 'class' => 'iconsmall'));
        $button = html_writer::link($deletelink, $deleteicon);

        $line[] = $button;


        return $line;

    }

}
