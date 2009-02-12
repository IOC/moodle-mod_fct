<?php

fct_require('pagines/base.php',
            'pagines/form_llista_empreses.php');

class fct_pagina_llista_empreses extends fct_pagina_base {

    const FORMAT_EXCEL = 1;
    const FORMAT_CSV = 2;

    var $cicles;

    function configurar() {
        parent::configurar(optional_param('fct', 0, PARAM_INT),
            optional_param('id', 0, PARAM_INT));
        $this->comprovar_permis($this->permis_admin);
        $this->url = fct_url::llista_empreses($this->fct->id);
        $this->pestanya = 'empreses';
        $this->afegir_navegacio(fct_string('llista_empreses'), $this->url);
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
                $this->enviar($empreses, $data->format);
            }
        }

        $this->mostrar_capcalera();
        if ($this->cicles) {
            $form->display();
        } else {
            echo '<p>' . fct_string('cap_cicle_formatiu') . '</p>';
        }
        $this->mostrar_peu();
        $this->registrar('view llista_empreses', $this->url);
    }

    function enviar($empreses, $format) {
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

        if ($format == self::FORMAT_EXCEL) {
            $this->enviar_excel($linies);
        } elseif ($format == self::FORMAT_CSV) {
            $this->enviar_csv($linies);
        }

        die;
    }

    function enviar_excel($linies) {
        global $CFG;
        require_once("{$CFG->dirroot}/lib/excellib.class.php");
        $workbook = new MoodleExcelWorkbook('-');
        $workbook->send(fct_string('llista_empreses') . '.xls');
        $worksheet = $workbook->add_worksheet(fct_string('llista_empreses'));
        foreach ($linies as $fila => $linia) {
            foreach ($linia as $columna => $camp) {
                $worksheet->write_string($fila, $columna, $camp);
            }
        }
        $workbook->close();
    }

    function enviar_csv($linies) {
        global $CFG;
        require_once("{$CFG->dirroot}/lib/filelib.php");
        $csv = array();
        foreach ($linies as $linia) {
            foreach ($linia as $camp) {
                $csv[] = '"' . addslashes($camp) .'",';
            }
            $csv[] = "\n";
        }
        $csv = implode($csv);
        send_file($csv, fct_string('llista_empreses') . '.csv',
                  'default', 0, true, true, '');
    }

}
