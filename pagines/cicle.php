<?php

require_once $CFG->libdir . '/tablelib.php';
require_once 'base_cicle.php';

class fct_pagina_cicle extends fct_pagina_base_cicle {

    function configurar() {
        parent::configurar(required_param('id', PARAM_INT));
        $this->url = fct_url::cicle($this->cicle->id);
        $this->pestanya = 'activitats';
    }

    function processar() {
        $this->mostrar_capcalera();

        print_heading(fct_string('activitats'));

        $taula = new flexible_table('fct_activitats');
        $taula->define_columns(array('descripcio', 'accions'));
        $taula->column_class('accions', 'columna_accions');
        $taula->define_headers(array(fct_string('descripcio'), ""));
        $taula->set_attribute('class', 'generaltable');
        $taula->setup();

        $activitats = fct_db::activitats_plantilla($this->cicle->id);

        if (!$activitats) {
           echo '<p>' . fct_string('cap_activitat') . '</p>';
        } else {
            foreach ($activitats as $activitat) {
                $url_editar = fct_url::editar_activitat_cicle($activitat->id);
                $url_suprimir = fct_url::suprimir_activitat_cicle($activitat->id);
                $icona_editar = $this->icona_editar($url_editar, fct_string('edita_activitat'));
                $icona_suprimir = $this->icona_suprimir($url_suprimir, fct_string('suprimeix_activitat'));
                $taula->add_data(array($activitat->descripcio, "$icona_editar $icona_suprimir"));
            }
            $taula->print_html();
        }

        $this->mostrar_peu();

        $this->registrar('view cicle');
    }
}

