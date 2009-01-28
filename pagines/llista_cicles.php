<?php

require_once $CFG->libdir . '/tablelib.php';
require_once 'base_cicles.php';

class fct_pagina_llista_cicles extends fct_pagina_base_cicles {

    function configurar() {
        parent::configurar(required_param('fct', PARAM_INT));
        $this->url = fct_url::llista_cicles($this->fct->id);
    }

    function processar() {

        $this->mostrar_capcalera();

        print_heading(fct_string('cicles_formatius'));

        $taula = new flexible_table('fct_quaderns');
        $taula->define_columns(array('nom'));
        $taula->define_headers(array(fct_string('nom')));
        $taula->set_attribute('class', 'generaltable');
        $taula->setup();

        $cicles = fct_db::cicles($this->fct->id);

        if (!$cicles) {
           echo '<p>' . fct_string('cap_cicle_formatiu') . '</p>';
        } else {
            foreach ($cicles as $cicle) {
                $taula->add_data(array('<a href="'
                    .fct_url::cicle($cicle->id).'">'
                   ."{$cicle->nom}</a>"));
            }
            $taula->print_html();
        }

        $this->mostrar_peu();

        $this->registrar('view llista_cicles');
    }

}

