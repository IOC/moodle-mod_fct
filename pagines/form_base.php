<?php
/* Quadern virtual d'FCT

   Copyright © 2008,2009,2010,2011  Institut Obert de Catalunya

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
                                           $this->elements,
                                           $this->comprovacions);
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

    function ocultar($noms) {
        if (is_array($noms)) {
            foreach ($noms as $nom) {
                $this->ocultar($nom);
            }
        } else {
            unset($this->elements[$noms]);
        }
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
            if (!isset($this->elements[$nom])) {
                return null;
            }
            if (!$this->elements[$nom]->congelat) {
                $valor = $this->elements[$nom]->get_data($this->data);
                return stripslashes_recursive($valor);
            }
        } else {
            if (isset($this->elements[$nom])) {
                $this->elements[$nom]->set_data($this->data, $valor);
            }
        }
    }

    function valors($valors=null) {
        if ($valors === null) {
            $valors = $this->valors_data($this->data);
            return stripslashes_recursive($valors);
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
    var $elements;
    var $url;

    function __construct($class, $url, $elements, $comprovacions) {
        global $COURSE;

        $this->url = $url;
        $this->elements = $elements;
        $this->comprovacions = $comprovacions;

        parent::__construct($url, null, 'post', '', array('class' => $class));

        $this->set_upload_manager(new upload_manager('', false, false, $COURSE, true, 0));
    }

    function definition() {
        foreach ($this->elements as $element) {
            $element->definition($this);
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

    function definition($mform) {
    }

    function get_data(&$data) {
    }

    function set_data(&$data, $valor) {
    }
}

class fct_form_element_base_grup extends fct_form_element_base {

    var $elements = array();

    function __construct($tipus, $nom, $etiqueta, $params) {
        parent::__construct($tipus, $nom, false, $params);
        $this->etiqueta = $etiqueta;
    }

    function configurar() {
    }

    function definition($mform) {
        $this->configurar();
        foreach ($this->elements as $element) {
            if ($this->congelat) {
                $element->congelar();
            }
            $element->definition($mform);
        }
    }

    function element($tipus, $nom, $etiqueta=false, $params=array()) {
        $element_class = 'fct_form_element_' . $tipus;
        $element = new $element_class($tipus, "{$this->nom}_{$nom}",
                                      $etiqueta, $params);
        $this->elements[$nom] = $element;
    }

    function get_data(&$data) {
        $valor = new object;
        foreach ($this->elements as $nom => $element) {
            $valor->$nom = $element->get_data($data);
        }
        return $valor;
    }

    function set_data(&$data, $valor) {
        foreach ($this->elements as $nom => $element) {
            if (isset($valor->$nom)) {
                $element->set_data($data, $valor->$nom);
            }
        }
    }

}

class fct_form_element_base_senzill extends fct_form_element_base {

    function __construct($tipus, $nom, $etiqueta, $params) {
        parent::__construct($tipus, $nom, $etiqueta, $params);
        if ($this->etiqueta) {
            $this->etiqueta .= ':';
        }
    }

    function definition($mform) {
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
        global $CFG;

        if (!isset($this->params->cols)) {
            $this->params->cols = 50;
        }
        if (!isset($this->params->rows)) {
            $this->params->rows = 4;
        }

        $mform->_form->addElement('textarea', $this->nom, $this->etiqueta,
                                  array('cols' => $this->params->cols,
                                        'rows' => $this->params->rows));

        $mform->_form->setType($this->nom, PARAM_TEXT);

        if (!empty($this->params->required)) {
            $mform->_form->addRule($this->nom, get_string('required'),
                                   'required', null, 'client');
        }
    }
}

class fct_form_element_areatext_linies extends fct_form_element_areatext {

    function get_data(&$data) {
        $valor = array();
        foreach (explode("\n", $data[$this->nom]) as $linia) {
            if (trim($linia)) {
                $valor[] = trim($linia);
            }
        }
        return $valor;
    }

    function set_data(&$data, $valor) {
        $data[$this->nom] = implode("\n", $valor);
    }
}

class fct_form_element_boto extends fct_form_element_base {

    function definition($mform) {
        $form = $mform->_form;

        if ($this->nom == 'cancellar') {
            $boto = $form->createElement('cancel', $this->nom);
        } elseif ($this->congelat) {
            $url = new moodle_url($mform->url, array($this->nom => 1));
            $html = '<a class="boto" href="' . $url->out() . '">'
                . $this->etiqueta . '</a>';
            $boto = $form->createElement('static', $this->nom, '', $html);
        } else {
            $boto = $form->createElement('submit', $this->nom, $this->etiqueta);
        }

        $mform->botons[] = $boto;
    }
}

class fct_form_element_capcalera extends fct_form_element_base {

    function definition($mform) {
        $mform->_form->addElement('header', null, $this->etiqueta);
    }
}

class fct_form_element_data extends fct_form_element_base_senzill {

    function definition_senzill($mform) {
        $dies = array();
        $mesos = array();
        $anys = array();
        if (!empty($this->params->opcional)) {
            $dies[0] = '';
            $mesos[0] = '';
            $anys[0] = '';
        }

        for ($dia = 1; $dia <= 31; $dia++) {
            $dies[$dia] = (string) $dia;
        }
        for ($mes = 1; $mes <= 12; $mes++) {
            $mesos[$mes] = strftime('%B', mktime(0, 0, 0, $mes, 1, 2000));
        }
        for ($any = 1900; $any <= 2020; $any++) {
            $anys[$any] = "$any";
        }

        if ($this->congelat) {
            $mform->_form->addElement('static', $this->nom, $this->etiqueta);
        } else {
            $elements = array();
            $elements[] = &$mform->_form->createElement('select', 'dia', '', $dies);
            $elements[] = &$mform->_form->createElement('select', 'mes', '', $mesos);
            $elements[] = &$mform->_form->createElement('select', 'any', '', $anys);
            $mform->_form->addGroup($elements, $this->nom, $this->etiqueta . ':', ' / ');
            $mform->_form->addRule($this->nom, fct_string('data_no_valida'),
                                   'callback', array($this, 'validar'));
            $mform->_form->setDefault($this->nom, array('dia' => (int) date('d'),
                                                        'mes' => (int) date('m'),
                                                        'any' => (int) date('Y')));
        }
    }

    function get_data(&$data) {
        $dia = $data[$this->nom]['dia'];
        $mes = $data[$this->nom]['mes'];
        $any = $data[$this->nom]['any'];
        return ($dia and $mes and $any) ? mktime(0, 0, 0, $mes, $dia, $any) : 0;
    }

    function set_data(&$data, $valor) {
        if ($this->congelat) {
            $data[$this->nom] = $valor ? strftime('%e / %B / %Y', $valor) : '-';
        } else {
            $data[$this->nom]['dia'] = $valor ? (int) date('d', $valor) : 0;
            $data[$this->nom]['mes'] = $valor ? (int) date('m', $valor) : 0;
            $data[$this->nom]['any'] = $valor ? (int) date('Y', $valor) : 0;
        }
    }

    function validar($data) {
        $dia = (int) $data['dia'];
        $mes = (int) $data['mes'];
        $any = (int) $data['any'];
        return ((!empty($this->params->opcional) or ($dia and $mes and $any)) and
                mktime(0, 0, 0, $mes, $dia, $any) !== false);
    }
}

class fct_form_element_fitxer extends fct_form_element_base {

    function definition($mform) {
        $mform->_form->addElement('file', $this->nom, $this->etiqueta);
        $mform->_form->addRule($this->nom, fct_string('tipus_fitxer_no_valid'),
                               'callback', array($this, 'validar'));
    }

    function get_data(&$data) {
        global $_FILES;
        return empty($_FILES[$this->nom]) ? null : $_FILES[$this->nom];
    }

    function validar($data) {
        global $_FILES;
        $types = (is_array($this->params->mimetype) ?
                  $this->params->mimetype : array($this->params->mimetype));
        return empty($_FILES[$this->nom]) or in_array($_FILES[$this->nom]['type'], $types);
    }
}

class fct_form_element_estatic extends fct_form_element_base {

    function definition($mform) {
        if ($this->etiqueta) {
            $this->etiqueta .= ':';
        }
        $mform->_form->addElement('static', $this->nom, $this->etiqueta);
    }


    function get_data(&$data) {
        return null;
    }

    function set_data(&$data, $valor) {
        $data[$this->nom] = $valor;
    }
}

class fct_form_element_hora extends fct_form_element_base {

    function definition($mform) {
        $elements = array();
        $elements[] =& $mform->_form->createElement('select', 'hora', '',
                                                    $this->opcions_hora());
        $mform->_form->setType("{$this->nom}[hora]", PARAM_INT);
        $elements[] =& $mform->_form->createElement('select', 'minuts', '',
                                                    $this->opcions_minuts());
        $mform->_form->setType("{$this->nom}[minuts]", PARAM_INT);
        $mform->_form->addGroup($elements, $this->nom,
                                $this->etiqueta . ':', ':');
        if ($this->congelat) {
            $mform->_form->hardFreeze($this->nom);
        }
    }

    function get_data(&$data) {
        $hora = ((float) $data[$this->nom]['hora'] +
                 (float) $data[$this->nom]['minuts'] / 60);
        return max(0, min(23.75, $hora));
    }

    function opcions_hora() {
        $opcions = array();
        for ($hora = 0; $hora < 24; $hora++) {
            $opcions[$hora] = (string) $hora;
        }
        return $opcions;
    }

    function opcions_minuts() {
        $opcions = array();
        for ($minuts = 0; $minuts < 60; $minuts += 15) {
            $opcions[$minuts] = sprintf("%02d", $minuts);
        }
        return $opcions;
    }

    function set_data(&$data, $valor) {
        $data[$this->nom]['hora'] = floor($valor);
        $data[$this->nom]['minuts'] = round(($valor - floor($valor)) * 4) * 15;
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

class fct_form_element_imatge extends fct_form_element_estatic {

    function set_data(&$data, $valor) {
        if ($valor) {
            $data[$this->nom] = '<img alt="' . s($this->etiqueta) . '" src="' . s($valor) . '"/>';
        }
    }
}

class fct_form_element_llista extends fct_form_element_base {

    function _name($id) {
        return $this->nom . '_' . md5($id);
    }

    function definition($mform) {
        $mform->_form->addElement('header', $this->nom, $this->etiqueta);
        foreach ($this->params->elements as $id => $etiqueta) {
            $mform->_form->addElement('checkbox', $this->_name($id),
                                      '', " $etiqueta");
            if ($this->congelat) {
                $mform->_form->hardFreeze($this->_name($id));
            }
        }
    }

    function get_data(&$data) {
        $valor = array();
        foreach (array_keys($this->params->elements) as $id) {
            if (!empty($data[$this->_name($id)])) {
                $valor[] = $id;
            }
        }
        return $valor;
    }

    function set_data(&$data, $valor) {
        foreach (array_keys($this->params->elements) as $id) {
            unset($data[$this->_name($id)]);
        }
        foreach ($valor as $id) {
            $data[$this->_name($id)] = 1;
        }
    }
}

class fct_form_element_llista_menu extends fct_form_element_base {

    function _name($id) {
        return $this->nom . '_' . md5($id);
    }

    function definition($mform) {
        $mform->_form->addElement('header', $this->nom, $this->etiqueta);
        foreach ($this->params->elements as $id => $etiqueta) {
            $mform->_form->addElement('select', $this->_name($id),
                                      $etiqueta, $this->params->opcions);
            if ($this->congelat) {
                $mform->_form->hardFreeze($this->_name($id));
            }
        }
    }

    function get_data(&$data) {
        $valor = array();
        foreach (array_keys($this->params->elements) as $id)  {
            $valor[$id] = $data[$this->_name($id)];
        }
        return $valor;
    }

    function set_data(&$data, $valor) {
        foreach (array_keys($this->params->elements) as $id) {
            $data[$this->_name($id)] = isset($valor[$id]) ?
                $valor[$id] : false;
        }
    }
}

class fct_form_element_menu extends fct_form_element_base_senzill {

    function definition_senzill($mform) {
        $mform->_form->addElement('select', $this->nom, $this->etiqueta,
                                  $this->params->opcions);
        $mform->_form->setType($this->nom, PARAM_RAW);
    }

}

class fct_form_element_nombres extends fct_form_element_base {

    var $element;

    function definition($mform) {
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

class fct_form_element_opcio extends fct_form_element_base_senzill {

    function definition_senzill($mform) {
        $mform->_form->addElement('checkbox', $this->nom, $this->etiqueta);
    }

    function get_data(&$data) {
        return !empty($data[$this->nom]);
    }
}

class fct_form_element_text extends fct_form_element_base_senzill {

    function definition_senzill($mform) {
        if (!isset($this->params->size)) {
            $this->params->size = 32;
        }

        $mform->_form->addElement('text', $this->nom, $this->etiqueta,
                           array('size' => $this->params->size));

        $mform->_form->setType($this->nom, PARAM_TEXT);

        if (!empty($this->params->required)) {
            $mform->_form->addRule($this->nom, get_string('required'),
                                   'required', null, 'client');
        }
    }
}
