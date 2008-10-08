<?php

require_once $CFG->libdir . '/tablelib.php';
require_once 'base_seguiment.php';

class fct_pagina_seguiment extends fct_pagina_base_seguiment {

    function configurar() {
        parent::configurar(required_param('quadern', PARAM_INT));
        $this->url = fct_url::seguiment($this->quadern->id);
    }

    function processar() {
        $this->mostrar_capcalera();
        print_heading(fct_string('seguiment_quinzenal'));

        $taula = new flexible_table('fct_seguiment');
        $taula->set_attribute('id', 'fct_seguiment');
        $taula->define_columns(array('any_', 'periode', 'dies', 'hores'));
        $taula->define_headers(array(fct_string('any'), fct_string('periode'),
            fct_string('dies'), fct_string('hores')));
        $taula->set_attribute('class', 'generaltable');
        $taula->setup();

        $quinzenes = fct_db::quinzenes($this->quadern->id);

        if (!$quinzenes) {
           echo '<p>' . fct_string('cap_quinzena') . '</p>';
        } else {
            foreach ($quinzenes as $quinzena) {
                $url = fct_url::quinzena($quinzena->id);
                $any = '<a href="'.$url.'">'.$quinzena->any_.'</a>';
                $periode = '<a href="'.$url.'">'.self::nom_periode($quinzena->periode).'</a>';
                $dies = '<a href="'.$url.'">'.$quinzena->dies.'</a>';
                $hores = '<a href="'.$url.'">'.$quinzena->hores.'</a>';
                $taula->add_data(array($any, $periode, $dies, $hores));
            }
            $taula->print_html();
        }

        $this->mostrar_peu();

        $this->registrar('view seguiment');
    }

}

