<?php
/* Quadern virtual d'FCT

   Copyright © 2008,2009,2010  Institut Obert de Catalunya

   This program is free software: you can redistribute it and/or modify
   it under the terms of the GNU General Public License as published by
   the Free Software Foundation, either version 3 of the License, or
   (at your option) any later version.

   This program is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

   You should have received a copy of the GNU General Public License
   along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

fct_require('pagines/base', 'pagines/form_base');

class fct_form_llista_empreses extends fct_form_base {

    function configurar($pagina) {
        $elements = array();
        foreach ($pagina->cicles as $cicle) {
            $elements[$cicle->id] = $cicle->nom;
        }
        $this->element('llista', 'cicles', 'llista_empreses',
                       array('elements' => $elements));
        $this->element('capcalera', 'configuracio', 'configuracio');
        $opcions = array(1 => 'Excel', 2 => 'CSV');
        $this->element('menu', 'format', 'format',
                       array('opcions' => $opcions));
        $this->element('boto', 'baixar', 'baixa');
    }
}

class fct_pagina_llista_empreses extends fct_pagina_base {

    const FORMAT_EXCEL = 1;
    const FORMAT_CSV = 2;

    var $cicles;

    function configurar() {
        parent::configurar(optional_param('fct', 0, PARAM_INT),
            optional_param('id', 0, PARAM_INT));
        $this->comprovar_permis($this->usuari->es_administrador);
        $this->cicles = $this->diposit->cicles($this->fct->id);
        $this->url = fct_url('llista_empreses', array('fct' => $this->fct->id));
        $this->pestanya = 'empreses';
        $this->afegir_navegacio(fct_string('llista_empreses'), $this->url);
    }

    function processar() {
        if (!$this->cicles) {
            $this->mostrar_capcalera();
            echo '<p>' . fct_string('cap_cicle_formatiu') . '</p>';
            $this->mostrar_peu();
            return;
        }

        $form = new fct_form_llista_empreses($this);
        if ($form->validar()) {
            $quaderns = array();
            foreach ($form->valor('cicles') as $cicle) {
                $especificacio = new fct_especificacio_quaderns;
                $especificacio->cicle = $cicle;
                foreach ($this->diposit->quaderns($especificacio) as $quadern) {
                    $quaderns[] = $quadern;
                }
            }
            $this->registrar('view baixa_empreses', $this->url);
            $this->enviar($quaderns, $form->valor('format'));
        }

        $this->mostrar_capcalera();
        $form->mostrar();
        $this->mostrar_peu();
        $this->registrar('view llista_empreses', $this->url);
    }

    function enviar($quaderns, $format) {
        $camps = array(
            'nom',
            'adreca',
            'poblacio',
            'codi_postal',
            'telefon',
            'fax',
            'email',
            'nif',
            'cicle_formatiu',
            'tutor_empresa',
            'tutor_centre',
            'estat',
            'data_inici',
            'data_final',
        );
        $files = array(array_map('fct_string', $camps));

        foreach ($quaderns as $q) {
            $files[] = array(
                $q->empresa->nom,
                $q->empresa->adreca,
                $q->empresa->poblacio,
                $q->empresa->codi_postal,
                $q->empresa->telefon,
                $q->empresa->fax,
                $q->empresa->email,
                $q->empresa->nif,
                $this->cicles[$q->cicle]->nom,
                $this->nom_usuari($q->tutor_empresa),
                $this->nom_usuari($q->tutor_centre),
                fct_string('estat_' . $q->estat),
                $q->data_inici() ? userdate($q->data_final(), "%d/%m/%Y") : '-',
                $q->data_final() ? userdate($q->data_final(), "%d/%m/%Y") : '-',
            );
        }

        if ($format == self::FORMAT_EXCEL) {
            $this->enviar_excel($files);
        } elseif ($format == self::FORMAT_CSV) {
            $this->enviar_csv($files);
        }

        die;
    }

    function enviar_excel($files) {
        global $CFG;
        require_once("{$CFG->dirroot}/lib/excellib.class.php");
        $workbook = new MoodleExcelWorkbook('-');
        $workbook->send(fct_string('llista_empreses') . '.xls');
        $worksheet = $workbook->add_worksheet(fct_string('llista_empreses'));
        foreach ($files as $fila => $columnes) {
            foreach ($columnes as $columna => $camp) {
                $worksheet->write_string($fila, $columna, $camp);
            }
        }
        $workbook->close();
    }

    function enviar_csv($files) {
        global $CFG;
        require_once("{$CFG->dirroot}/lib/filelib.php");
        $csv = array();
        foreach ($files as $columnes) {
            foreach ($columnes as $camp) {
                $csv[] = '"' . addslashes($camp) .'",';
            }
            $csv[] = "\n";
        }
        $csv = implode($csv);
        send_file($csv, fct_string('llista_empreses') . '.csv',
                  'default', 0, true, true, '');
    }

}
