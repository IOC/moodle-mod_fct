<?php

require_once $CFG->libdir . '/tablelib.php';
require_once 'base.php';

class fct_pagina_llista_empreses extends fct_pagina_base {

    function configurar() {
        parent::configurar(optional_param('fct', 0, PARAM_INT),
            optional_param('id', 0, PARAM_INT));
        $this->url = fct_url::llista_empreses($this->fct->id);
        $this->pestanya = 'empreses';
        $this->comprovar_permis($this->permis_admin);
    }

    function processar() {
        global $CFG;

        $taula = new flexible_table('fct_empreses');
        $taula->set_attribute('id', 'fct_empreses');
        $columnes = array('nom', 'adreca', 'poblacio', 'codi_postal',
                          'telefon', 'fax', 'email', 'nif');
        $taula->define_columns($columnes);
        $taula->define_headers(array_map('fct_string', $columnes));
        $taula->sortable(true, 'nom');
        $taula->set_attribute('class', 'generaltable');
        $taula->setup();

        $empreses = fct_db::empreses($this->fct->id, $taula->get_sql_sort());

        $this->mostrar_capcalera();

        print_heading(fct_string('empreses'));

        if ($empreses) {
            foreach ($empreses as $e) {
                $url = fct_url::quadern($e->id);
                $fila = array();
                foreach ($columnes as $columna) {
                    $fila[] = $e->$columna;
                }
                $taula->add_data($fila);
            }
            $taula->print_html();
        } else {
            echo '<p>' . fct_string('cap_empresa') . '</p>';
        }

        $this->mostrar_peu();

        $this->registrar('view llista_empreses');
    }

}

