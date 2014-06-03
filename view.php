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
 * This file is the entry point to the fct module. All pages are rendered from here
 *
 * @package    mod
 * @subpackage fct
 * @copyright  2014 Institut Obert de Catalunya
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define("PAGENUM", "10");

require_once('../../config.php');
require_once('lib.php');
require_once($CFG->dirroot . '/mod/fct/lib.php');
require_once('classes/fct_base.php');

require_login();

$id = required_param('id', PARAM_INT);    // Course Module ID
$page = optional_param('page', 'quadern', PARAM_RAW);
$index = optional_param('index', 0, PARAM_INT);
$action = optional_param('action', 'view', PARAM_RAW);
$quadern = optional_param('quadern', false, PARAM_RAW);
$subpage = optional_param('subpage', false, PARAM_RAW);
$valoracio = optional_param('valoracio', false, PARAM_RAW);
$curs = optional_param('searchcurs', false, PARAM_INT);
$cicle = optional_param('searchcicle', false, PARAM_INT);
$estat = optional_param('searchestat', false, PARAM_RAW);
$cerca = optional_param('cerca', false, PARAM_RAW);
$qualificaciotype = optional_param('qualificaciotype', false, PARAM_RAW);

if (!$cm = get_coursemodule_from_id('fct', $id)) {
    print_error('Course Module ID was incorrect');
}
if (!$course = $DB->get_record('course', array('id' => $cm->course))) {
    print_error('course is misconfigured');
}
if (!$fct = $DB->get_record('fct', array('id' => $cm->instance))) {
    print_error('course module is incorrect');
}

global $USER, $DB;

if ($subpage) {
    $class = 'fct_'.$subpage;
} else {
    $class = 'fct_'.$page;
}

require_once('classes/'.$class.'.php');

$record = new stdClass;
$record->fct = $fct->id;

if ($quadern) {
    $record->quadern = $quadern;
}

if ($valoracio) {
    $record->valoracio = $valoracio;
}

if ($qualificaciotype) {
    $record->qualificaciotype = $qualificaciotype;
}

$class = new $class($record);

$class->checkpermissions();

if ($action == 'export') {
    $class->$action();
}

$searchparams = new stdClass;
$searchparams->curs = $curs;
$searchparams->cicle = $cicle;
$searchparams->estat = $estat;
$searchparams->cerca = $cerca;

$url = new moodle_url('/mod/fct/view.php', array('id' => $id));
$context = context_module::instance($cm->id);

$PAGE->set_cm($cm, $course, $fct);
$PAGE->set_context($context);
$PAGE->set_url($url);
$PAGE->set_title(format_string($fct->name));
$PAGE->set_heading(format_string($fct->name));

if ($quadern) {
    if ($alumne = $DB->get_record('user', array('id' => $class->alumne))) {
        $PAGE->navbar->add(fullname($alumne));
  }
}

$PAGE->set_pagelayout('standard');
$PAGE->requires->jquery();
$PAGE->requires->css('/mod/fct/styles.css');
$PAGE->requires->js('/mod/fct/client.js');

echo $OUTPUT->header();

$tab = $class->tabs($id);

$output = $PAGE->get_renderer('mod_fct');
$output->print_tabs($tab);

if ($action == 'view') {
    $class->view(false, $index, $searchparams);
} else {
    $class->$action();
}

echo $OUTPUT->footer();
