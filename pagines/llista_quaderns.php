<?php

require_once $CFG->libdir . '/tablelib.php';
require_once 'base_quaderns.php';

class fct_pagina_llista_quaderns extends fct_pagina_base_quaderns {

    var $taula;
    var $quaderns;

    function configurar() {
        parent::configurar(optional_param('fct', 0, PARAM_INT),
            optional_param('id', 0, PARAM_INT));
        $this->url = fct_url::llista_quaderns($this->fct->id);
    }

    function processar() {
        global $CFG;

        $this->configurar_taula();

        $this->quaderns = fct_db::quaderns($this->fct->id,
                                           $this->select_quaderns(),
                                           $this->taula->get_sql_sort());

        if ($this->quaderns and !$this->permis_admin
            and count($this->quaderns) == 1) {
            $q = array_pop($this->quaderns);
            redirect(fct_url::quadern($q->id));
        }

        $this->mostrar_capcalera();

        print_heading(fct_string('quaderns'));

        if ($this->quaderns) {
            $this->mostrar_taula();
        } else {
            echo '<p>' . fct_string('cap_quadern') . '</p>';
        }

        $this->mostrar_peu();

        $this->registrar('view llista_quaderns');
    }

    function configurar_taula() {
        $this->taula = new flexible_table('fct_quaderns');
        $this->taula->set_attribute('id', 'fct_quaderns');
        $columnes = array('alumne', 'empresa', 'tutor_centre',
                          'tutor_empresa', 'estat', 'data_final');
        $this->taula->define_columns($columnes);
        $this->taula->define_headers(array_map('fct_string', $columnes));
        $this->taula->sortable(true, 'data_final');
        $this->taula->set_attribute('class', 'generaltable');
        $this->taula->setup();
    }

    function mostrar_taula() {
        foreach ($this->quaderns as $q) {
            $url = fct_url::quadern($q->id);
            $estat = ($q->estat ? 'estat_obert' : 'estat_tancat');
            $str_estat = fct_string($estat);
            $tutor_centre = ($q->tutor_centre ? $q->tutor_centre : '-');
            $tutor_empresa = ($q->tutor_empresa ? $q->tutor_empresa : '-');
            $data_final = ($q->data_final ?
                           userdate($q->data_final, "%d/%m/%Y") : '-');
            $this->taula->add_data(
                array("<a href=\"$url\">{$q->alumne}</a>",
                      "<a href=\"$url\">{$q->empresa}</a>",
                      "<a href=\"$url\">$tutor_centre</a>",
                      "<a href=\"$url\">$tutor_empresa</a>",
                      "<a href=\"$url\" class=\"$estat\">$str_estat</a>",
                      "<a class=\"link_taula\"  href=\"$url\">$data_final</a>")
            );
        }
        $this->taula->print_html();
    }

}

