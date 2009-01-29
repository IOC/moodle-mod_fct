<?php

require_once $CFG->libdir . '/formslib.php';
require_once $CFG->libdir . '/pear/HTML/QuickForm/date.php';

class fct_form_base extends moodleform {

    var $pagina;
    private $botons;
    private $comprovacions;
    private $elements;
    private $congelat = false;
    private $elements_congelats = array();

    static function options_barem() {
        return array(
            0 => '',
            1 => fct_string('barem_a'),
            2 => fct_string('barem_b'),
            3 => fct_string('barem_c'),
            4 => fct_string('barem_d'),
            5 => fct_string('barem_e'),
        );
    }

    static function options_barem_qualificacio() {
        return array(
            0 => '',
            1 => fct_string('apte'),
            2 => fct_string('noapte'),
        );
    }

    function __construct($pagina) {
        $this->pagina = $pagina;
        $this->botons = array();
        $this->comprovacions = array();
        $this->elements = array();
        $this->elements_congelats = array();
        $this->configurar();
        parent::moodleform($pagina->url);
    }

    function afegir_boto($name, $label) {
        $this->botons[] = (object) array(
            'type' => 'submit',
            'name' => $name,
            'label' => $label,
        );
    }

    function afegir_boto_cancellar($name='cancellar') {
        $this->botons[] = (object) array(
            'type' => 'cancel',
            'name' => $name,
        );
    }

    function afegir_boto_enllac($name, $label) {
        $this->botons[] = (object) array(
            'type' => 'enllac',
            'name' => $name,
            'label' => $label,
        );
    }

    function afegir_boto_reset($name='desfer', $label=null) {
        if (is_null($label)) {
            $label = fct_string("desfes");
        }
        $this->botons[] = (object) array(
            'type' => 'reset',
            'name' => $name,
            'label' => $label,
        );
    }

    function afegir_calendari($name, $label, $calendaris,
            $any, $periode, $dies=array(), $congelat, $sep=':') {
        $this->elements[] = (object) array(
            'type' => 'calendari',
            'name' => $name,
            'label' => $label.$sep,
            'calendaris' => $calendaris,
            'any' => $any,
            'periode' => $periode,
            'dies' => array_keys($dies),
            'congelat' => $congelat,
        );
    }

    function afegir_checkbox($name, $label) {
        $this->elements[] = (object) array(
            'type' => 'checkbox',
            'name' => $name,
            'label' => $label,
        );
    }

    function afegir_comprovacio($method, $instance=false) {
        if (!$instance) {
            $instance = $this->pagina;
        }
        $this->comprovacions[] = array($instance, $method);
    }

    function afegir_date($name, $label, $sep=':') {
        $this->elements[] = (object) array(
            'type' => 'date',
            'name' => $name,
            'label'=> $label.$sep,
        );
    }

    function afegir_header($name, $label) {
        $this->elements[] = (object) array(
            'type' => 'header',
            'name' => $name,
            'label'=> $label,
        );
    }

    function afegir_hierselect($name, $label, $options, $sep=':',
            $onchange='') {
        $this->elements[] = (object) array(
            'type' => 'hierselect',
            'name' => $name,
            'label' => $label.$sep,
            'options' => $options,
            'onchange' => $onchange,
        );
    }

    function afegir_llista_checkbox($name, $labels, $field=false) {
        foreach ($labels as $id => $label) {
            $label_id = $field ? $label->id : $id;
            $label_text = $field ? $label->$field : $label;
            $this->afegir_checkbox($name.$label_id, $label_text);
        }
    }

    function afegir_llista_select($name, $labels, $options, $field=false, $sep=':') {
        foreach ($labels as $id => $label) {
            $label_id = $field ? $label->id : $id;
            $label_text = $field ? $label->$field : $label;
            $this->afegir_select($name.$label_id, $label_text, $options, false, $sep);
        }
    }

    function afegir_select($name, $label, $options, $field=false, $sep=':') {
        if ($field) {
            foreach ($options as $index => $value) {
                $options[$index] = $value->$field;
            }
        }
        $this->elements[] = (object) array(
            'type' => 'select',
            'name' => $name,
            'label' => $label.$sep,
            'options' => $options,
        );
    }

   function afegir_static($name, $label, $text, $sep=':') {
        $this->elements[] = (object) array(
            'type' => 'static',
            'name' => $name,
            'label' => $label.$sep,
            'text' => $text,
        );
    }

    function afegir_text($name, $label, $size, $required=false, $numeric=false, $sep=':') {
        $this->elements[] = (object) array(
            'type' => 'text',
            'name' => $name,
            'label' => $label.$sep,
            'size' => $size,
            'required' => $required,
            'numeric' => $numeric,
        );
    }

    function afegir_textarea($name, $label, $rows, $cols, $required=false, $sep=':') {
        $this->elements[] = (object) array(
            'type' => 'textarea',
            'name' => $name,
            'label' => $label.$sep,
            'rows' => $rows,
            'cols' => $cols,
            'required' => $required,
        );
    }

    function congelar() {
        $this->congelat = true;
    }

    function congelar_element($elements) {
        $this->elements_congelats = array_merge($this->elements_congelats, $elements);
    }

    function congelar_llista($name) {
        foreach ($this->elements as $element) {
             preg_match("/^$name([0-9]+)$/", $element->name, &$match);
             if ($match) {
                 $this->elements_congelats[] = $element->name;
             }
        }
    }

    function definition() {
        $this->definition_elements();
        $this->definition_comprovacions();
        $this->definition_botons();

        if ($this->congelat) {
            foreach ($this->elements as $element) {
                $this->elements_congelats[] = $element->name;
            }
        }
        if ($this->elements_congelats) {
            $this->_form->hardFreeze($this->elements_congelats);
        }
    }

    function definition_botons() {
        $form = $this->_form;
        $buttonarray = array();
        foreach ($this->botons as $boto) {
            if ($boto->type == 'submit') {
                $buttonarray[] = &$form->createElement('submit', $boto->name, $boto->label);
            } else if ($boto->type == 'cancel') {
                $buttonarray[] = &$form->createElement('cancel', $boto->name);
             } else if ($boto->type == 'reset') {
                $buttonarray[] = &$form->createElement('reset', $boto->name, $boto->label);
            } else if ($boto->type == 'enllac') {
                $html = '<a class="botoenllac" href="' . $this->pagina->url
                    . '&' . $boto->name . '=1">' . $boto->label . '</a>';
                $buttonarray[] = &$form->createElement('static', $boto->name, '', $html);
            }
        }
        $form->addGroup($buttonarray, 'buttonar', '', array(' '), false);
        $form->closeHeaderBefore('buttonar');
    }

    function definition_calendari($element) {
        global $CFG;

        $id = 'calendari_' . $element->name;
        $capcalera = array('dl', 'dt', 'dc', 'dj', 'dv', 'ds', 'dg');
        $calendari = $element->calendaris[$element->any][$element->periode];
        $congelat = ($this->congelat or $element->congelat);

        require_once($CFG->libdir.'/pear/HTML/AJAX/JSON.php');
        $json_capcalera = json_encode($capcalera);
        $json_calendaris = json_encode($element->calendaris);

        $html = "<script type=\"text/javascript\" defer=\"defer\">
            //<![CDATA[
            capcalera_$id = $json_capcalera;
            calendaris_$id = $json_calendaris;
            function actualitzar_$id(form) {
                any = form['periode[0]'].value;
                periode = form['periode[1]'].value;
                calendari = calendaris_{$id}[any][periode]
                table = document.getElementById('$id');

                html = '<table><tr><th>' + capcalera_$id.join('</th><th>')
                    + '</th></tr><tr>';

                for (n=0; n < calendari.dia_setmana; n++) {
                    html += '<td></td>';
                }

                for (dia = calendari.dia_inici;
                        dia <= calendari.dia_final;
                        dia++) {
                    if (n == 7) {
                        n = 0;
                        html += '</tr><tr>';
                    }
                    checked = '';
                    if ('{$element->name}' + dia in form) {
                        if (form['{$element->name}' + dia].checked) {
                            checked = ' checked=\"checked\" ';
                        }
                    }
                    html += '<td>' + dia
                        + ' <input type=\"checkbox\" name=\"{$element->name}'
                        + dia + '\"' + checked + '/></td>';
                    n++;
                }

                for (; n < 7; n++) {
                    html += '<td></td>';
                }

                html += '</tr></table>';
                table.innerHTML = html;
            }
            //]]>
            </script>
                ";

        $html .= '<div id="' . $id . '"><table><tr><th>'
            . implode('</th><th>', $capcalera) . '</th></tr><tr>';

        for ($n = 0; $n < $calendari->dia_setmana; $n++) {
            $html .= '<td></td>';
        }

        $disabled = $congelat ? ' disabled="disabled" ' : '';
        for ($dia = $calendari->dia_inici;
                $dia <= $calendari->dia_final;
                $dia++) {
            if ($n == 7) {
                $n = 0;
                $html .= '</tr><tr>';
            }
            $checked = (in_array($dia, $element->dies) 
                        or optional_param($element->name . $dia, 0, PARAM_BOOL)) ?
                ' checked="checked" ' : '';
            $html .= '<td>' . $dia
                . ' <input type="checkbox" name="'. $element->name
                . $dia .  '"' . $disabled . $checked .'/></td>';
            $n++;
        }

        for (; $n < 7; $n++) {
            $html .= '<td></td>';
        }

        $html .= '</tr></table></div>';

        $this->_form->addElement('static', $element->name,
            $element->label, $html);
    }

    function definition_checkbox($element) {
        $this->_form->addElement('checkbox', $element->name, '', ' ' . $element->label);
    }

    function definition_comprovacions() {
        foreach ($this->comprovacions as $comprovacio) {
            list($instance, $method) = $comprovacio;
            $this->_form->addFormRule(array($instance, $method));
        }
    }

    function definition_date($element) {
        $this->_form->addElement(new fct_HTML_QuickForm_date(
            $element->name, $element->label, array('maxYear' => date('Y') + 3)));
    }

    function definition_elements() {
        $form = $this->_form;
        foreach ($this->elements as $element) {
            $function = 'definition_'.$element->type;
            $this->$function($element);
            if (!empty($element->required)) {
                $form->addRule($element->name, get_string('required'),
                    'required', null, 'client');
            }
        }
    }

    function definition_header($element) {
        $this->_form->addElement('header', $element->name,
            '<h2 class="main">' . $element->label . '</h2>');
    }

    function definition_hierselect($element) {
        $attributes = $element->onchange ?
            array('onchange' => $element->onchange) : null;
        $select =& $this->_form->addElement('hierselect', $element->name,
            $element->label, $attributes, ' / ');
        $select->setOptions($element->options);
        $this->_form->setType($element->name, PARAM_INT);
    }

    function definition_select($element) {
        $this->_form->addElement('select', $element->name, $element->label, $element->options);
        $this->_form->setType($element->name, PARAM_INT);
    }

    function definition_static($element) {
        $this->_form->addElement('static', $element->name, $element->label,
            $element->text);
    }

    function definition_text($element) {
        $this->_form->addElement('text', $element->name, $element->label,
            array('size'=>$element->size));
        $this->_form->setType($element->name, PARAM_TEXT);
        if ($element->numeric) {
            $this->_form->addRule($element->name, "Numèric", 'regex', "/^[0-9]+$/", 'client');
        }
    }

    function definition_textarea($element) {
        $this->_form->addElement('textarea', $element->name, $element->label,
            array('cols'=>$element->cols, 'rows'=>$element->rows));
        $this->_form->setType($element->name, PARAM_TEXT);
    }

    function date2unix($date) {
        return mktime(0, 0, 0, (int) $date['F'], (int) $date['d'], (int) $date['Y']);
    }

    function get_data_calendari($name, $calendari) {
        $values = array();

        for ($dia = $calendari->dia_inici;
                $dia <= $calendari->dia_final;
                $dia++) {
            $value = optional_param($name.$dia, false, PARAM_BOOL);
            if ($value) {
                $values[$dia] = $value;
            }
        }

        return $values;
    }

    function get_data_llista($name) {
        $data = $this->get_data();
        if (!$data) {
            return false;
        }

        $values = array();
        foreach ((array) $data as $param => $value) {
            preg_match("/^$name([0-9]+)$/", $param, &$match);
            if ($match) {
                $values[$match[1]] = $value;
            }
        }
        return $values;
    }

    function set_data_llista($name, $values) {
        $data = array();
        foreach ($values as $id => $value) {
            $data[$name.$id] = $value;
        }
        $this->set_data($data);
    }

}

class fct_HTML_QuickForm_date extends HTML_QuickForm_date
{

    var $_options = array(
        'language'         => 'ca',
        'format'           => 'd F Y',
        'minYear'          => 2000,
        'maxYear'          => 2020,
        'addEmptyOption'   => false,
        'emptyOptionValue' => '',
        'emptyOptionText'  => '&nbsp;',
        'optionIncrement'  => array('i' => 1, 's' => 1)
    );


    var $_locale = array(
        'ca' => array (
            'weekdays_short'=> array ('dg', 'dl', 'dt', 'dc', 'dj', 'dv', 'ds'),
            'weekdays_long' => array ('diumenge', 'dilluns', 'dimarts', 'dimecres', 'dijous', 'divendres', 'dissabte'),
            'months_short'  => array ('gen', 'feb', 'mar', 'abr', 'mai', 'jun', 'jul', 'ago', 'set', 'oct', 'nov', 'des'),
            'months_long'   => array ('gener', 'febrer', 'març', 'abril', 'maig', 'juny', 'juliol', 'agost', 'setembre', 'octubre', 'novembre', 'desembre')
        ),
    );

}
