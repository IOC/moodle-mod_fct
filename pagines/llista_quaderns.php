<?php

require_once $CFG->libdir . '/tablelib.php';
require_once 'base.php';

class fct_pagina_llista_quaderns extends fct_pagina_base {

    function configurar() {
        parent::configurar(optional_param('fct', 0, PARAM_INT),
            optional_param('id', 0, PARAM_INT));
        $this->url = fct_url::llista_quaderns($this->fct->id);
        $this->pestanya = 'quaderns';
    }

    function processar() {
        global $CFG;

        $taula = new flexible_table('fct_quaderns');
        $taula->set_attribute('id', 'fct_quaderns');
        $taula->define_columns(array('alumne', 'empresa',
            'tutor_centre', 'tutor_empresa', 'estat', 'data_final'));
        $taula->define_headers(array(fct_string('alumne'),
            fct_string('empresa'), fct_string('tutor_centre'),
            fct_string('tutor_empresa'), fct_string('estat'),
            fct_string('data_final')));
        $taula->sortable(true, 'data_final');
        $taula->set_attribute('class', 'generaltable');
        $taula->setup();

        $quaderns = fct_db::quaderns($this->fct->id, $this->select_quaderns(),
            $taula->get_sql_sort());

        if ($quaderns and !$this->permis_admin and count($quaderns) == 1) {
            $q = array_pop($quaderns);
            redirect(fct_url::quadern($q->id));
        }

        $buttontext = update_module_button($this->cm->id, $this->course->id,
            get_string('modulename', 'fct'));
        $this->mostrar_capcalera($buttontext);

        if (!empty($this->fct->intro)) {
            print_box(format_text($this->fct->intro), 'generalbox', 'intro');
        }

        print_heading(fct_string('quaderns'));

        if ($quaderns) {
            foreach ($quaderns as $q) {
                $url = fct_url::quadern($q->id);
                $estat = $q->estat ? 'estat_obert' : 'estat_tancat';
                $taula->add_data(array(
                    '<a href="'.$url.'">'.$q->alumne.' </a>',
                    '<a href="'.$url.'">'.$q->empresa.' </a>',
                    '<a href="'.$url.'">'. ($q->tutor_centre ?
                    $q->tutor_centre : '-') . '</a>',
                    '<a href="'.$url.'">' . ($q->tutor_empresa ?
                    $q->tutor_empresa : '-') . '</a>',
                    '<a href="'.$url.'" class="'. $estat . '">'
                    . fct_string($estat) . '</a>',
                    '<a class="link_taula"  href="'.$url.'">'
                    . ($q->data_final ? userdate($q->data_final, "%d/%m/%Y")
                    : '-') . '</a>'));
            }
            $taula->print_html();
        } else {
            echo '<p>' . fct_string('cap_quadern') . '</p>';
        }

        $this->mostrar_peu();

        $this->registrar('view llista_quaderns');
    }

}

