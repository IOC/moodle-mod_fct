<?php
/* Quadern virtual d'FCT

   Copyright © 2009,2010  Institut Obert de Catalunya

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

require_once($CFG->libdir . '/tablelib.php');
fct_require('pagines/base');

class fct_pagina_avisos extends fct_pagina_base {

    var $index;

    function configurar() {
        parent::configurar(required_param('fct', PARAM_INT));
        $this->configurar_accio(array('veure', 'suprimir'), 'veure');
        $this->comprovar_permis($this->usuari->es_tutor_centre);
        $this->index = optional_param('index', 0, PARAM_INT);
        $this->url = fct_url('avisos', array('fct' => $this->fct->id));
        $this->pestanya = 'avisos';
    }

    function processar_veure() {
        $this->mostrar_capcalera();
        $taula = new flexible_table('fct_avisos');
        $taula->set_attribute('id', 'fct_avisos');
        $taula->define_columns(array('data', 'avis', 'quadern', 'accions'));
        $taula->define_headers(array(fct_string('data'), fct_string('avis'), fct_string('quadern'), ""));
        $taula->set_attribute('class', 'generaltable');
        $taula->setup();

        $nombre = $this->diposit->nombre_avisos($this->usuari);
        $avisos = $this->diposit->avisos($this->usuari, $this->index * 20, 20);
        if (!$avisos) {
            echo '<p>' . fct_string('cap_avis') . '</p>';
        } else {
            foreach ($avisos as $avis) {
                $str_data = userdate($avis->data, '%a %e %b %Y, %H:%M');
                $quadern = $this->diposit->quadern($avis->quadern);
                $str_quadern = '<a href="' . s(fct_url('quadern', array('quadern' => $quadern->id)))
                    . '">' . s($this->nom_usuari($quadern->alumne) . ' - ' .
                               $quadern->empresa->nom) . '</a>';
                $str_avis = $this->titol_avis($avis);
                if ($url = $this->url_avis($avis)) {
                    $str_avis = '<a href="' . s($url) . '">' . $str_avis . '</a>';
                }
                $url_suprimir = fct_url('suprimir_avis', array('avis' => $avis->id));
                $accions = $this->icona_suprimir($url_suprimir, fct_string('suprimeix_avis'));
                $taula->add_data(array($str_data, $str_avis, $str_quadern, $accions));
            }
            $taula->print_html();
            print_paging_bar($nombre, $this->index, 20, $this->url . '&', 'index');
        }

        $this->mostrar_peu();
        $this->registrar('view avisos_quadern');
    }

    function titol_avis($avis) {
        switch ($avis->tipus) {
        case 'quinzena_afegida':
        case 'quinzena_alumne':
        case 'quinzena_empresa':
        case 'quinzena_tutor':
            $quinzena = $this->diposit->quinzena($avis->quinzena);
            return fct_string('avis_' . $avis->tipus,
                              self::nom_periode($quinzena->periode) . " {$quinzena->any}");
        default:
            return fct_string('avis_' . $avis->tipus);
        }
    }

    function url_avis($avis) {
        switch ($avis->tipus) {
        case 'dades_alumne':
            return fct_url('dades_alumne', array('quadern' => $avis->quadern));
        case 'dades_conveni':
            return fct_url('dades_conveni', array('quadern' => $avis->quadern));
        case 'dades_empresa':
            return fct_url('dades_empresa', array('quadern' => $avis->quadern));
        case 'dades_horari':
            return fct_url('dades_horari', array('quadern' => $avis->quadern));
        case 'dades_relatives':
            return fct_url('dades_relatives', array('quadern' => $avis->quadern));
        case 'pla_activitats':
            return fct_url('pla_activitats', array('quadern' => $avis->quadern));
        case 'qualificacio_quadern':
            return fct_url('qualificacio_quadern', array('quadern' => $avis->quadern));
        case 'quinzena_afegida':
        case 'quinzena_alumne':
        case 'quinzena_empresa':
        case 'quinzena_tutor':
            return fct_url('quinzena', array('quinzena' => $avis->quinzena));
        case 'quinzena_suprimida':
            return fct_url('seguiment', array('quadern' => $avis->quadern));
        case 'valoracio_actituds_final':
            return fct_url('valoracio_actituds', array('quadern' => $avis->quadern, 'final' => '1'));
        case 'valoracio_actituds_parcial':
            return fct_url('valoracio_actituds', array('quadern' => $avis->quadern, 'final' => '0'));
        case 'valoracio_activitats':
            return fct_url('valoracio_activitats', array('quadern' => $avis->quadern));
        }
    }

}

