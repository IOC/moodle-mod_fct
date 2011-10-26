<?php
/* Quadern virtual d'FCT

   Copyright © 2008,2009,2010  Institut Obert de Catalunya

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
        $mform->setType('intro', PARAM_CLEAN);
        $mform->setHelpButton('intro', array('writing', 'questions', 'richtext'), false, 'editorhelpbutton');

        $this->standard_coursemodule_elements(false);

        $this->add_action_buttons();
    }

}

