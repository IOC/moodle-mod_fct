<?php

require_once $CFG->libdir . '/tablelib.php';
require_once 'base_quaderns.php';

class fct_pagina_llista_quaderns extends fct_pagina_base_quaderns {

    var $taula;
    var $quaderns;
    var $curs;
    var $cursos;

    function configurar() {
        parent::configurar(optional_param('fct', 0, PARAM_INT),
            optional_param('id', 0, PARAM_INT));
        $this->url = fct_url::llista_quaderns($this->fct->id);
        $this->curs = optional_param('curs', -1, PARAM_INT);
        $this->cursos = $this->valors_curs();
    }

    function processar() {
        global $CFG, $USER;

        $this->configurar_taula();

        $select = $this->select_quaderns();
        $redireccio = false;
        if (!$this->permis_admin) {
            $redireccio = (fct_db::nombre_quaderns($this->fct->id, $select) == 1);
        }

        if (!$redireccio) {
            $select = "($select) AND (" . $this->select_curs() . ')';
        }

        $this->quaderns = fct_db::quaderns($this->fct->id, $select,
                                           $this->taula->get_sql_sort());

        if ($redireccio) {
            $q = array_pop($this->quaderns);
            redirect(fct_url::quadern($q->id));
        }

        $this->mostrar_capcalera();

        print_heading(fct_string('quaderns'));

        if ($this->quaderns) {
            $this->mostrar_selectors();
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

    function select_quaderns() {
        global $USER;

        $select = 'FALSE';
        if ($this->permis_alumne) {
            $select .= ' OR q.alumne = '.$USER->id;
        }
        if ($this->permis_tutor_centre) {
            $select .= ' OR q.tutor_centre = '.$USER->id;
        }
        if ($this->permis_tutor_empresa) {
            $select .= ' OR q.tutor_empresa = '.$USER->id;
        }
        if ($this->permis_admin) {
            $select = 'TRUE';
        }
        return "$select";
    }

    function mostrar_selectors() {
        echo '<form action="view.php" method="get">';
        echo '<input type="hidden" name="pagina" value="llista_quaderns"/>';
        echo '<input type="hidden" name="fct" value="' . $this->fct->id . '" />';
        echo '<div id="fct_selectors_quaderns">';
        echo fct_string('curs') . ': ';
        echo choose_from_menu($this->cursos, 'curs', $this->curs,
                              '', 'this.form.submit()');
        echo '</div></form>';
    }

    function valors_curs() {
        list($min, $max) = fct_db::data_final_convenis_min_max($this->fct->id);

        if (!$min and !$max) {
            return array();
        }

        $min = ($min ? $min : $max);
        $max = ($max ? $max : $min);
        $min = getdate($min);
        $max = getdate($max);
        $any_min = ($min['mon'] >= 9 ? $min['year']  : $min['year'] - 1);
        $any_max = ($max['mon'] >= 9 ? $max['year']  : $max['year'] - 1);

        if ($this->curs < 0) {
            $this->curs = $any_max;
        }

        $cursos = array(0 => fct_string('tots'));
        for ($curs = $any_max; $curs >= $any_min;  $curs--) {
            $cursos[$curs] = $curs . '-' . ($curs + 1);
        }
        return $cursos;
    }

    function select_curs() {
        if (!$this->curs) {
            return '(TRUE)';
        }
        $data_min = mktime(0, 0, 0, 9, 1, $this->curs);
        $data_max = mktime(0, 0, 0, 9, 1, $this->curs + 1);
        return "data_final >= $data_min AND data_final < $data_max";
    }
}

