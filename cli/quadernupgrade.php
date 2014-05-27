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
 * @package    mod
 * @subpackage fct
 * @copyright  2014 Institut Obert de Catalunya
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define('CLI_SCRIPT', true);

require_once(dirname(dirname(dirname(dirname(__FILE__)))) . '/config.php');
require_once($CFG->libdir.'/clilib.php');
require_once('lib.php');
require_once($CFG->dirroot . '/mod/fct/lib.php');
require_once('classes/fct_quadern_base.php');

global $DB;

$quadernstemp = $DB->get_records('fct_quadern_temp', array());

$quadernscount = 0;
foreach ($quadernstemp as $quaderntemp) {
    $quadern = new fct_quadern_base((int)$quaderntemp->id);

    if ($userid = $DB->get_record('user', array('username' => $quaderntemp->alumne))) {
        $quadern->alumne = $userid;
    }
    if ($userid = $DB->get_record('user', array('username' => $quaderntemp->tutor_centre))) {
            $quadern->tutor_centre = $userid;
    }
    if ($userid = $DB->get_record('user', array('username' => $quaderntemp->tutor_empresa))) {
        $quadern->tutor_empresa = $userid;
    }
    $quadern->create_object();
    $quadern->update();
    $quadernscount++;
 }

 echo "Se han actualizado $quadernscount quaderns";

