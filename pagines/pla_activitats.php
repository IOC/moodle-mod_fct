<?php

require_once($CFG->libdir . '/tablelib.php');
fct_require('pagines/base_pla_activitats.php');

class fct_pagina_pla_activitats extends fct_pagina_base_pla_activitats {

    function configurar() {
        parent::configurar(required_param('quadern', PARAM_INT));
        $this->url = fct_url::pla_activitats($this->quadern->id);
        $this->subpestanya = 'activitats_pla';
    }

    function processar() {
        $this->mostrar_capcalera();

        $taula = new flexible_table('fct_activitats');
        $taula->define_columns(array('descripcio', 'accions'));
        $taula->column_class('accions', 'columna_accions');
        $taula->define_headers(array(fct_string('descripcio'), ""));
        $taula->set_attribute('class', 'generaltable');
        $taula->setup();

        $activitats = fct_db::activitats_pla($this->quadern->id);

        if (!$activitats) {
           echo '<p>' . fct_string('cap_activitat') . '</p>';
        } else {
            foreach ($activitats as $activitat) {
                $accions = '';
                if ($this->permis_editar) {
                    $url_editar = fct_url::editar_activitat_pla($activitat->id);
                    $url_suprimir = fct_url::suprimir_activitat_pla($activitat->id);
                    $icona_editar = $this->icona_editar($url_editar, fct_string('edita_activitat'));
                    $icona_suprimir = $this->icona_suprimir($url_suprimir, fct_string('suprimeix_activitat'));
                    $accions = "$icona_editar $icona_suprimir";
                }
                $taula->add_data(array($activitat->descripcio, $accions));
            }
            $taula->print_html();
        }

        $this->mostrar_peu();

        $this->registrar('view pla_activitats');
    }

}

