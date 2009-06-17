<?php
/* Quadern virtual d'FCT

   Copyright © 2008,2009  Institut Obert de Catalunya

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

require_once($CFG->libdir . '/formslib.php');
require_once($CFG->libdir . '/pear/HTML/QuickForm/date.php');

class fct_form_base {

    var $comprovacions = array();
    var $data = array();
    var $elements = array();
    var $mform;
    var $pagina;

    function __construct($pagina) {
        $this->configurar($pagina);
        $this->mform = new fct_form_moodle(get_class($this), $pagina->url,
                                           $this->elements, $this->comprovacions,
                                           $this->data);
    }

    function barem_valoracio() {
        return array(
            0 => '-',
            1 => fct_string('barem_a'),
            2 => fct_string('barem_b'),
            3 => fct_string('barem_c'),
            4 => fct_string('barem_d'),
            5 => fct_string('barem_e'),
        );
    }

    function barem_qualificacio() {
        return array(
            0 => '-',
            1 => fct_string('apte'),
            2 => fct_string('noapte'),
        );
    }

    function comprovacio($objecte, $metode) {
        $comprovacio = new fct_form_comprovacio($this, $objecte, $metode);
        $this->comprovacions[] = $comprovacio;
    }

    function configurar() {
    }

    function congelar($noms=null) {
        if ($noms === null) {
            $this->congelar(array_keys($this->elements));
        } elseif (is_array($noms)) {
            foreach ($noms as $nom) {
                $this->congelar($nom);
            }
        } else {
            $this->elements[$noms]->congelar();
        }
    }

    function element($tipus, $nom, $etiqueta=false, $params=array()) {
        $element_class = 'fct_form_element_' . $tipus;
        $element = new $element_class($tipus, $nom, $etiqueta, $params);
        $this->elements[$nom] = $element;
    }

    function mostrar() {
        $this->mform->set_data((object) $this->data);
        $this->mform->display();
    }

    function validar() {
        if ($data = $this->mform->get_data()) {
            $this->data = (array) $data;
            return true;
        }
        return false;
    }

    function valor($nom, $valor=null) {
        if ($valor === null) {
            if (!$this->elements[$nom]->congelat) {
                return $this->elements[$nom]->get_data($this->data);
            }
        } else {
            if (isset($this->elements[$nom])) {
                $this->elements[$nom]->set_data($this->data, $valor);
            }
        }
    }

    function valors($valors=null) {
        if ($valors === null) {
            return $this->valors_data($this->data);
        } else {
            foreach ((array) $valors as $nom => $valor) {
                $this->valor($nom, $valor);
            }
        }
    }

    function valors_data($data) {
        $valors = array();
        foreach ($this->elements as $element) {
            if (!$element->congelat) {
                $valors[$element->nom] = $element->get_data($data);
            }
        }
        return (object) $valors;
    }

}

class fct_form_moodle extends moodleform {

    var $botons = array();
    var $data;
    var $elements;
    var $url;

    function __construct($class, $url, $elements, $comprovacions, &$data) {
        $this->url = $url;
        $this->elements = $elements;
        $this->comprovacions = $comprovacions;
        $this->data = &$data;
        parent::__construct($url, null, 'post', '', array('class' => $class));
    }

    function definition() {
        foreach ($this->elements as $element) {
            $element->definition($this, $this->data);
        }

        foreach ($this->comprovacions as $comprovacio) {
            $this->_form->addFormRule(array($comprovacio, 'comprovar'));
        }

        $this->_form->addGroup($this->botons, 'buttonar', '', array(' '), false);
        $this->_form->closeHeaderBefore('buttonar');
    }
}

class fct_form_comprovacio {

    var $form;
    var $objecte;
    var $metode;

    function __construct($form, $objecte, $metode) {
        $this->form = $form;
        $this->objecte = $objecte;
        $this->metode = $metode;
    }

    function comprovar($data) {
        $metode = $this->metode;
        $valors = $this->form->valors_data($data);
        return $this->objecte->$metode($valors);
    }
}

class fct_form_element_base {

    var $congelat;
    var $etqiueta;
    var $nom;
    var $params;
    var $tipus;

    function __construct($tipus, $nom, $etiqueta, $params) {
        $this->tipus = $tipus;
        $this->nom = $nom;
        $this->etiqueta = ($etiqueta ? fct_string($etiqueta) : false);
        $this->congelat = false;
        $this->params = (object) $params;
    }

    function congelar() {
        $this->congelat = true;
    }

    function definition($mform, &$data) {
    }

    function get_data(&$data) {
    }

    function set_data(&$data, $valor) {
    }
}

class fct_form_element_base_senzill extends fct_form_element_base {

    function definition($mform, &$data) {
        $this->definition_senzill($mform);
        if ($this->congelat) {
            $mform->_form->hardFreeze($this->nom);
        }
    }

    function get_data(&$data) {
        return $data[$this->nom];
    }

    function set_data(&$data, $valor) {
        $data[$this->nom] = $valor;
    }
}

class fct_form_element_areatext extends fct_form_element_base_senzill {

    function definition_senzill($mform) {
        if (!isset($this->params->cols)) {
            $this->params->cols = 50;
        }
        if (!isset($this->params->rows)) {
            $this->params->rows = 4;
        }

        $mform->_form->addElement('textarea', $this->nom, $this->etiqueta . ':',
                                  array('cols' => $this->params->cols,
                                        'rows' => $this->params->rows));

        $mform->_form->setType($this->nom, PARAM_TEXT);

        if (!empty($this->params->required)) {
            $mform->_form->addRule($this->nom, get_string('required'),
                                   'required', null, 'client');
        }
    }
}

class fct_form_element_boto extends fct_form_element_base {

    function definition($mform, &$data) {
        $form = $mform->_form;

        if ($this->nom == 'cancellar') {
            $boto = $form->createElement('cancel', $this->nom);
        } elseif ($this->congelat) {
            $url = new moodle_url($mform->url, array($this->nom => 1));
            $html = '<a class="botoenllac" href="' . $url->out() . '">'
                . $this->etiqueta . '</a>';
            $boto = $form->createElement('static', $this->nom, '', $html);
        } else {
            $boto = $form->createElement('submit', $this->nom, $this->etiqueta);
        }

        $mform->botons[] = $boto;
    }
}

class fct_form_element_capcalera extends fct_form_element_base {

    function definition($mform, &$data) {
        $mform->_form->addElement('header', $this->nom, $this->etiqueta);
    }
}

class fct_form_element_data extends fct_form_element_base_senzill {

    function definition_senzill($mform) {
        $mform->_form->addElement('date_selector', $this->nom, $this->etiqueta . ':',
                                  array('startyear' => 2000, 'optional' => false), null, '/');

    }

    function get_data(&$data) {
        $valor = $data[$this->nom];
        if (is_array($valor)) {
            $valor = mktime(0, 0, 0, $valor['month'],
                            $valor['day'], $valor['year']);
        }
        return $valor;
    }
}

class fct_form_element_estatic extends fct_form_element_base {

    function definition($mform, &$data) {
        $mform->_form->addElement('static', $this->nom, $this->etiqueta . ':');
    }

    function set_data(&$data, $valor) {
        $data[$this->nom] = $valor;
    }
}

class fct_form_element_hores extends fct_form_element_base {

    function definition($mform) {
        $elements = array();
        $elements[] =& $mform->_form->createElement('text', 'hores', '',
                                                    array('size' => 4));
        $mform->_form->setType("{$this->nom}[hores]", PARAM_INT);
        if (!empty($this->params->minuts)) {
            $elements[] =& $mform->_form->createElement('select', 'minuts', '',
                                                        $this->opcions());
            $mform->_form->setType("{$this->nom}[minuts]", PARAM_INT);
        }
        $mform->_form->addGroup($elements, $this->nom, $this->etiqueta . ':',
                                ' ' . fct_string('hores_i') . ' ');
        if ($this->congelat) {
            $mform->_form->hardFreeze($this->nom);
        }
    }

    function get_data(&$data) {
        $valor = (float) $data[$this->nom]['hores'];
        if (!empty($this->params->minuts)) {
            $valor += $data[$this->nom]['minuts'] / 60;
        }
        return $valor;
    }

    function opcions() {
        $opcions = array();
        for ($minuts = 0; $minuts < 60; $minuts += 15) {
            $opcions[$minuts] = $minuts . ' ' . fct_string('minuts');
        }
        return $opcions;
    }

    function set_data(&$data, $valor) {
        $hores = floor($valor);
        $minuts = round(($valor - $hores) * 60);
        $data[$this->nom]['hores'] = $hores;
        if (!empty($this->params->minuts))  {
            $data[$this->nom]['minuts'] = $minuts;
        }
    }

}

class fct_form_element_llista extends fct_form_element_base {

    function definition($mform, &$data) {
        $mform->_form->addElement('header', $this->nom, $this->etiqueta);
        foreach ($this->params->elements as $id => $etiqueta) {
            $mform->_form->addElement('checkbox', "{$this->nom}_$id",
                                      '', " $etiqueta");
            if ($this->congelat) {
                $mform->_form->hardFreeze("{$this->nom}_$id");
            }
        }
    }

    function get_data(&$data) {
        $valor = array();
        foreach (array_keys($this->params->elements) as $id) {
            if (!empty($data["{$this->nom}_$id"])) {
                $valor[] = $id;
            }
        }
        return $valor;
    }

    function set_data(&$data, $valor) {
        foreach (array_keys($this->params->elements) as $id) {
            unset($data["{$this->nom}_$id"]);
        }
        foreach ($valor as $id) {
            $data["{$this->nom}_$id"] = 1;
        }
    }
}

class fct_form_element_llista_menu extends fct_form_element_base {

    function definition($mform, &$data) {
        $mform->_form->addElement('header', $this->nom, $this->etiqueta);
        foreach ($this->params->elements as $id => $etiqueta) {
            $mform->_form->addElement('select', "{$this->nom}_$id",
                                      $etiqueta . ':', $this->params->opcions);
            if ($this->congelat) {
                $mform->_form->hardFreeze("{$this->nom}_$id");
            }
        }
    }

    function get_data(&$data) {
        $valor = array();
        foreach (array_keys($this->params->elements) as $id)  {
            $valor[$id] = $data["{$this->nom}_$id"];
        }
        return $valor;
    }

    function set_data(&$data, $valor) {
        foreach (array_keys($this->params->elements) as $id) {
            $data["{$this->nom}_$id"] = isset($valor[$id]) ?
                $valor[$id] : false;
        }
    }
}

class fct_form_element_menu extends fct_form_element_base_senzill {

    function definition_senzill($mform) {
        $mform->_form->addElement('select', $this->nom, $this->etiqueta . ':',
                                  $this->params->opcions);
        $mform->_form->setType($this->nom, PARAM_INT);
    }

}

class fct_form_element_nombres extends fct_form_element_base {

    var $element;

    function definition($mform, &$data)  {
        $mform->_form->addElement('text', $this->nom, $this->etiqueta . ':',
                                  array('size' => 32));
        $mform->_form->setType($this->nom, PARAM_TEXT);
        $mform->_form->addRule($this->nom, fct_string('nombres_separats_comes'),
                               'regex',"/^\s*([0-9]+(\s*,\s*[0-9]+)*)?\s*$/",
                               'client');
        if ($this->congelat) {
            $mform->_form->hardFreeze($this->nom);
        }
    }

    function get_data(&$data) {
        $nombres = array();
        foreach (explode(',', $data[$this->nom]) as $nombre) {
            $nombres[] = (int) $nombre;
        }
        return $nombres;
    }

    function set_data(&$data, $valor) {
        $data[$this->nom] = implode(', ', $valor);
    }
}

class fct_form_element_ocult extends fct_form_element_base {

    function definition($mform) {
        $mform->_form->addElement('hidden', $this->nom);
    }

    function set_data(&$data, $valor) {
        $data[$this->nom] = $valor;
    }
}

class fct_form_element_text extends fct_form_element_base_senzill {

    function definition_senzill($mform) {
        if (!isset($this->params->size)) {
            $this->params->size = 32;
        }

        $mform->_form->addElement('text', $this->nom, $this->etiqueta . ':',
                           array('size' => $this->params->size));

        $mform->_form->setType($this->nom, PARAM_TEXT);

        if (!empty($this->params->required)) {
            $mform->_form->addRule($this->nom, get_string('required'),
                                   'required', null, 'client');
        }
    }
}
