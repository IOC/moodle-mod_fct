<?php

require_once $CFG->libdir . '/tablelib.php';
require_once 'base_seguiment.php';

class fct_pagina_resum_seguiment extends fct_pagina_base_seguiment {

    var $resum = array(); // resum[any][trimestre][mes]
    var $quinzenes;
    var $total_dies = 0;
    var $total_hores = 0;

    function calcular_resum() {
        $resum = array();
        $records = fct_db::quinzenes($this->quadern->id);
        if ($records) {
            foreach ($records as $record) {
                $any = $record->any_;
                $periode = $record->periode;
                $mes = (int) ($periode / 2);
                $trimestre = (int) ($mes / 3);

                if (!isset($resum[$any])) {
                    $resum[$any] = array();
                }

                if (!isset($resum[$any][$trimestre])) {
                    $resum[$any][$trimestre] = array();
                }

                if (!isset($resum[$any][$trimestre][$mes])) {
                    $resum[$any][$trimestre][$mes] = (object) array(
                        'dies' => 0, 'hores' => 0);
                }

                $resum[$any][$trimestre][$mes]->dies += $record->dies;
                $resum[$any][$trimestre][$mes]->hores += $record->hores;
                $this->total_hores += $record->hores;
                $this->total_dies += $record->dies;
            }
        }
        $this->resum = $resum;
    }

    function configurar() {
        parent::configurar(required_param('quadern', PARAM_INT));
        $this->url = fct_url::resum_seguiment($this->quadern->id);
    }

    function mostrar_resum_trimestre($any, $trimestre) {
        $taula = new flexible_table("fct_resum_seguiment_{$any}_{$trimestre}");
        $taula->define_columns(array('mes', 'dies', 'hores'));
        $titol = '<strong>'. self::nom_trimestre($trimestre) . ' '. $any . '</strong>';
        $taula->define_headers(array($titol, fct_string('dies'), fct_string('hores')));
        $taula->set_attribute('class', 'generaltable');
        $taula->setup();

        $dies = 0;
        $hores = 0;
        for ($mes = $trimestre * 3; $mes < $trimestre * 3 + 3; $mes++) {
            if (isset($this->resum[$any][$trimestre][$mes])) {
                $record = $this->resum[$any][$trimestre][$mes];
                $taula->add_data(array(self::nom_mes($mes), $record->dies, $record->hores));
                $dies += $record->dies;
                $hores += $record->hores;
            } else {
                $taula->add_data(array(self::nom_mes($mes), 0, 0));
            }
        }

        $taula->add_data(array('<strong>' . fct_string('total') . '</strong>',
        	"<strong>$dies</strong>", "<strong>$hores</strong>"));

        $taula->print_html();

        echo '<br/>';
    }

    function processar() {
        global $CFG;

        $this->calcular_resum();

        $this->mostrar_capcalera();
        print_heading(fct_string('resum_seguiment'));

        if ($this->resum) {
            foreach ($this->resum as $any => $resum_any) {
                foreach ($resum_any as $trimestre => $resum_trimestre) {
                    $this->mostrar_resum_trimestre($any, $trimestre);
                }
            }
            echo '<p>' . fct_string('durada_total_practiques', array(
            	'dies' => $this->total_dies, 'hores' => $this->total_hores)) . '</p>';
        } else {
            echo '<p>' . fct_string('cap_quinzena') . '</p>';
        }
        $this->mostrar_peu();

        $this->registrar('view resum_seguiment');
    }

}

