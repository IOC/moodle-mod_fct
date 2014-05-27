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
 * Quadern alumne FCT class
 *
 * @package    mod
 * @subpackage fct
 * @copyright  2014 Institut Obert de Catalunya
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once('form/quadern_alumne_edit_form.php');
require_once('fct_quadern_base.php');
require_once('fct_cicle.php');


class fct_quadern_alumne extends fct_quadern_base {

    protected static $dataobject = 'dades_alumne';

    protected $editform = 'fct_quadern_alumne_edit_form';

    protected static $dataobjectkeys = array('dni',
                                        'data_naixement',
                                        'adreca',
                                        'poblacio',
                                        'codi_postal',
                                        'telefon',
                                        'email',
                                        'procedencia',
                                        'inss',
                                        'targeta_sanitaria');

    public function tabs($id, $type = 'view') {

        $tab = parent::tabs_quadern($id, $this->id);
        $subtree = parent::subtree($id, $this->id);

        $row = $tab['row'];
        $row['quadern_dades']->subtree = $subtree;
        $tab['row'] = $row;
        $tab['currentab'] = 'quadern_alumne';

        return $tab;
    }

    public function view() {
        global $PAGE;

        $output = $PAGE->get_renderer('mod_fct',
            'quadern_alumne');

        $output->view($this);

        return true;

    }

    public function prepare_form_data($data) {
        $data->procedencies = $this->procedencies();
        $data = parent::prepare_form_data($data);
        return $data;
    }

    public function procedencies() {
        return array('' => '',
             'batxillerat' => fct_string('batxillerat'),
             'curs_acces' => fct_string('curs_acces'),
             'cicles' => fct_string('cicles'),
             'eso' => fct_string('eso'),
             'ges' => fct_string('ges'));
    }

}
