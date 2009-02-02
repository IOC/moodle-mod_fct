<?php

require_once 'base.php';
require_once 'form_cicles_empreses.php';

class fct_pagina_llista_empreses extends fct_pagina_base {

    var $cicles;

    function configurar() {
        parent::configurar(optional_param('fct', 0, PARAM_INT),
            optional_param('id', 0, PARAM_INT));
        $this->comprovar_permis($this->permis_admin);
        $this->url = fct_url::llista_empreses($this->fct->id);
        $this->pestanya = 'empreses';
        $this->afegir_navegacio(fct_string('empreses'), $this->url);
    }

    function processar() {
        $this->cicles = fct_db::cicles($this->fct->id);
        if ($this->cicles) {
            $form = new fct_form_cicles_empreses($this);
            $data = $form->get_data();
            if ($data) {
                $cicles = $form->get_data_llista('cicle');
                $empreses = fct_db::empreses($this->fct->id, $cicles);
                $this->registrar('view baixa_empreses', $this->url);
                $this->enviar_csv($empreses);
                die;
            }
        }

        $this->mostrar_capcalera();
        if ($this->cicles) {
            $form->display();
        } else {
            print_heading(fct_string('empreses'));
            echo '<p>' . fct_string('cap_cicle_formatiu') . '</p>';
        }
        $this->mostrar_peu();
        $this->registrar('view llista_empreses', $this->url);
    }

    function enviar_csv($empreses) {
        global $CFG;
        require_once("{$CFG->dirroot}/lib/filelib.php");

        $camps = array('nom', 'adreca', 'poblacio', 'codi_postal',
                       'telefon', 'fax', 'email', 'nif');
        $linies = array(array_map('fct_string', $camps));
        foreach ($empreses as $empresa) {
            $linia = array();
            foreach ($camps as $camp) {
                $linia[] = $empresa->$camp;
            }
            $linies[] = $linia;
        }
        $csv = array();
        foreach ($linies as $linia) {
            foreach ($linia as $camp) {
                $csv[] = '"' . addslashes($camp) .'",';
            }
            $csv[] = "\n";
        }
        $csv = implode($csv);
        send_file($csv, fct_string('empreses') . '.csv',
                  'default', 0, true, true, '');
    }

}

