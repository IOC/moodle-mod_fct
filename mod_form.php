<?php
require_once($CFG->dirroot . '/course/moodleform_mod.php');

class mod_fct_mod_form extends moodleform_mod {

    function definition() {

        global $CFG, $COURSE;
        $mform =& $this->_form;

        $mform->addElement('header', 'general', 'General');

        $mform->addElement('text', 'name', 'Nom', array('size'=>'32'));
        $mform->setType('name', PARAM_TEXT);
        $mform->setDefault('name', 'Quadern de pràctiques');
        $mform->addRule('name', get_string('required'), 'required', null, 'client');

        $mform->addElement('htmleditor', 'intro', 'Introducció');
        $mform->setType('intro', PARAM_CLEANHTML);
        $mform->setHelpButton('intro', array('writing', 'questions', 'richtext'), false, 'editorhelpbutton');

        $this->standard_coursemodule_elements(false);

        $this->add_action_buttons();
    }

}

