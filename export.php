<?php


require_once('../../config.php');
require_once('lib.php');
require_once($CFG->dirroot . '/mod/fct/lib.php');
require_once('classes/fct_quadern_base.php');

global $DB;

$quadernstemp = $DB->get_records('fct_quadern_temp', array());

$quadernscount = 0;
foreach ($quadernstemp as $quaderntemp) {
    $quadern = new fct_quadern_base((int)$quaderntemp->id);

    if ($quaderntemp->alumne != $quadern->alumne) {
        $quadern->alumne = $quaderntemp->alumne;
    }
    if ($quaderntemp->tutor_centre != $quadern->tutor_centre) {
        $quadern->tutor_centre = $quaderntemp->tutor_centre;
    }
    if ($quaderntemp->tutor_empresa != $quadern->tutor_empresa) {
        $quadern->tutor_empresa = $quaderntemp->tutor_empresa;
    }
    $quadern->create_object();
    $quadern->update();

 }

