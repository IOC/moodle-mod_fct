<?php
/* Quadern virtual d'FCT

   Copyright © 2008,2009  Institut Obert de Catalunya

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
fct_require('pagines/base_quaderns.php');

class fct_pagina_llista_quaderns extends fct_pagina_base_quaderns {

    var $taula;
    var $quaderns;
    var $curs;
    var $cicle;
    var $estat;
    var $cerca;
    var $nombre;
    var $index;

    function configurar() {
        parent::configurar(optional_param('fct', 0, PARAM_INT),
            optional_param('id', 0, PARAM_INT));
        $this->url = fct_url::llista_quaderns($this->fct->id);

        $this->curs = optional_param('curs', -1, PARAM_INT);
        $this->cicle = optional_param('cicle', 0, PARAM_INT);
        $this->estat = optional_param('estat', 1, PARAM_INT);
        $this->cerca = optional_param('cerca', '', PARAM_TEXT);
        $this->index = optional_param('index', 0, PARAM_INT);

        $this->subpestanya = 'llista_quaderns';
    }

    function processar() {
        $especificacio = new fct_especificacio_quaderns;
        $especificacio->fct = $this->fct->id;
        $especificacio->usuari = $this->usuari;

        $mode_selectors = ($this->usuari->es_administrador or
                           $this->usuari->es_tutor_centre);

        if ($mode_selectors) {
            $valors_curs = $this->valors_curs();
            $valors_cicle = $this->valors_cicle();
            $valors_estat = $this->valors_estat();
            if ($this->curs > 0) {
                $especificacio->data_final_min = mktime(0, 0, 0, 9, 1,
                                                        $this->curs);
                $especificacio->data_final_max = mktime(0, 0, 0, 9, 1,
                                                        $this->curs + 1);
            }
            if ($this->cicle > 0) {
                $especificacio->cicle = $this->cicle;
            }
            if ($this->estat >= 0) {
                $especificacio->estat = $this->estat;
            }
            if ($this->cerca) {
                $especificacio->cerca = $this->cerca;
            }
        }

        $this->configurar_taula();

        $this->nombre = $this->diposit->nombre_quaderns($especificacio);

        if (!$mode_selectors and $this->nombre == 1) {
            $quaderns = $this->diposit->quaderns($especificacio);
            $quadern = array_pop($quaderns);
            redirect(fct_url::quadern($quadern->id));
        }

        $this->quaderns = $this->diposit->quaderns($especificacio,
                                                   $this->taula->get_sql_sort(),
                                                   $this->index * 10, 10);

        $this->mostrar_capcalera();
        if ($mode_selectors) {
            $this->mostrar_selectors($valors_curs, $valors_cicle, $valors_estat);
        }

        if ($this->quaderns) {
            $this->mostrar_paginacio();
            $this->mostrar_taula();
        } else {
            echo '<p>' . fct_string('cap_quadern') . '</p>';
        }

        $this->mostrar_peu();

        $this->registrar('view llista_quaderns');
    }

    function configurar_taula() {
        $this->taula = new flexible_table('fct_quaderns');
        $this->taula->set_attribute('id', 'fct_quaderns');
        $columnes = array('alumne', 'empresa', 'cicle_formatiu',
                          'tutor_centre', 'tutor_empresa',
                          'estat', 'data_final');
        $this->taula->define_columns($columnes);
        $this->taula->define_headers(array_map('fct_string', $columnes));
        $this->taula->sortable(true, 'data_final');
        $this->taula->set_attribute('class', 'generaltable');
        $this->taula->setup();
    }

    function mostrar_paginacio() {
        $url = $this->url();
        print_paging_bar($this->nombre, $this->index, 10,
                         $url->out() . '&', 'index');
    }

    function mostrar_taula() {
        foreach ($this->quaderns as $q) {
            $url = fct_url::quadern($q->id);
            $alumne = $this->diposit->usuari($this->fct, $q->alumne)->nom_sencer();
            $tutor_centre = $q->tutor_centre ?
                $this->diposit->usuari($this->fct, $q->tutor_centre)->nom_sencer() : '-';
            $tutor_empresa = $q->tutor_empresa ?
                $this->diposit->usuari($this->fct, $q->tutor_empresa)->nom_sencer() : '-';
            $cicle = $this->diposit->cicle($q->cicle)->nom;
            $estat = ($q->estat ? 'estat_obert' : 'estat_tancat');
            $str_estat = fct_string($estat);
            $data_final = ($q->data_final() ?
                           userdate($q->data_final(), "%d/%m/%Y") : '-');
            $this->taula->add_data(
                array("<a href=\"$url\">$alumne</a>",
                      "<a href=\"$url\">{$q->empresa->nom}</a>",
                      "<a href=\"$url\">$cicle</a>",
                      "<a href=\"$url\">$tutor_centre</a>",
                      "<a href=\"$url\">$tutor_empresa</a>",
                      "<a href=\"$url\" class=\"$estat\">$str_estat</a>",
                      "<a class=\"link_taula\"  href=\"$url\">$data_final</a>")
            );
        }
        $this->taula->print_html();
    }

    function mostrar_selectors($cursos, $cicles, $estats) {
        $selectors = array(
            array('curs',
                  choose_from_menu($cursos, 'curs', $this->curs,
                                   '', 'this.form.submit()', '', true,
                                   false, false, 'id_curs')),
            array('cicle',
                  choose_from_menu($cicles, 'cicle', $this->cicle,
                                   '', 'this.form.submit()', '', true,
                                   false, false, 'id_cicle')),
            array('estat',
                  choose_from_menu($estats, 'estat', $this->estat,
                                   '', 'this.form.submit()', '', true,
                                   false, false, 'id_estat')),
            array('cerca',
                  '<input type="text" id="id_cerca" name="cerca"'
                  . ' value="' . s($this->cerca)
                  . '" onchange="this.form.submit()" />'),
        );

        echo '<form id="selectors_quaderns" action="view.php" method="get">'
            . '<input type="hidden" name="pagina" value="llista_quaderns"/>'
            . '<input type="hidden" name="fct" value="' . $this->fct->id . '"/>';
        foreach ($selectors as $selector) {
            echo '<div><label for="id_' . $selector[0] . '">'
                . fct_string($selector[0]) . ':</label>' . $selector[1] . '</div>';
        }
        echo '</form>';
    }

    function url() {
        $params = array('pagina' => 'llista_quaderns',
                        'fct' => $this->fct->id,
                        'curs'=> $this->curs,
                        'cicle' => $this->cicle,
                        'estat' => $this->estat,
                        'cerca' => $this->cerca);
        return new moodle_url(null, $params);
    }

    function valors_cicle() {
        $valors = array(0 => fct_string('tots'));
        $cicles = $this->diposit->cicles($this->fct->id);
        foreach ($cicles as $cicle) {
            $valors[$cicle->id] = $cicle->nom;
        }
        return $valors;
    }

    function valors_curs() {
        $cursos = array(0 => fct_string('tots'));

        $params = (object) array(
            'usuari' => $this->usuari->id,
            'permis_admin' => $this->usuari->es_administrador,
            'permis_alumne' => $this->usuari->es_alumne,
            'permis_tutor_centre' => $this->usuari->es_tutor_centre,
            'permis_tutor_empresa' => $this->usuari->es_tutor_empresa,
        );
        list($min, $max) =
            $this->diposit->min_max_data_final_quaderns($this->fct->id);

        if (!$min or !$max) {
            $this->curs = false;
            return $cursos;
        }

        $min = getdate($min);
        $max = getdate($max);
        $any_min = ($min['mon'] >= 9 ? $min['year']  : $min['year'] - 1);
        $any_max = ($max['mon'] >= 9 ? $max['year']  : $max['year'] - 1);

        if ($this->curs < 0) {
            $this->curs = $any_max;
        }

        for ($curs = $any_max; $curs >= $any_min;  $curs--) {
            $cursos[$curs] = $curs . '-' . ($curs + 1);
        }
        return $cursos;
    }

    function valors_estat() {
        $estats = array(-1 => fct_string('tots'),
                        1 => fct_string('estat_obert'),
                        0 => fct_string('estat_tancat'));
        return $estats;
    }

}
