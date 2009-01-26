<?php

require_once $CFG->libdir . '/tablelib.php';
require_once 'base_plantilles.php';

class fct_pagina_llista_plantilles extends fct_pagina_base_plantilles {

    function configurar() {
        parent::configurar(required_param('fct', PARAM_INT));
        $this->url = fct_url::llista_plantilles($this->fct->id);
    }

    function processar() {

        $this->mostrar_capcalera();

        print_heading(fct_string('plantilles_activitats'));

        $taula = new flexible_table('fct_quaderns');
        $taula->define_columns(array('plantilla'));
        $taula->define_headers(array(fct_string('nom')));
        $taula->set_attribute('class', 'generaltable');
        $taula->setup();

        $plantilles = fct_db::plantilles($this->fct->id);

        if (!$plantilles) {
           echo '<p>' . fct_string('cap_plantilla') . '</p>';
        } else {
            foreach ($plantilles as $plantilla) {
                $taula->add_data(array('<a href="'
                    .fct_url::plantilla($plantilla->id).'">'
                   ."{$plantilla->nom}</a>"));
            }
            $taula->print_html();
        }

        $this->mostrar_peu();

        $this->registrar('view llista_plantilles');
    }

}

