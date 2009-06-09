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

fct_require('pagines/form_base.php');

class fct_form_quinzena extends fct_form_base {

    var $options;
    var $calendaris;
    var $any;
    var $periode;
    var $dies;

    function __construct($pagina, $any, $periode, $dies=array()) {
        $this->any = $any;
        $this->periode = $periode;
        $this->dies = $dies;
        parent::__construct($pagina);
    }

    function configurar() {
        $this->configurar_periodes();

        $this->afegir_header('quinzena', $this->pagina->accio == 'afegir'
            ? fct_string('nova_quinzena') : fct_string('quinzena'));

        $this->afegir_hierselect('periode', fct_string('periode'),
            $this->options, ':',
            'actualitzar_calendari_dia(this.form)');

        $this->afegir_calendari('dia', fct_string('dies'), $this->calendaris,
                                $this->any, $this->periode, $this->dies,
                                !$this->pagina->permis_editar_alumne);

        $this->afegir_text('hores', fct_string('hores'), 4, true, true);

        if ($this->pagina->activitats) {
            $this->afegir_header('activitats_realitzades', fct_string('activitats_realitzades'));
            $this->afegir_llista_checkbox('activitat', $this->pagina->activitats, 'descripcio');
        }

        $this->afegir_header('observacions', fct_string('valoracions_observacions'));
        $this->afegir_textarea('valoracions', fct_string('valoracions'), 4, 50);
        $this->afegir_textarea('observacions_alumne', fct_string('observacions'), 4, 50);

        if ($this->pagina->accio != 'afegir' or $this->pagina->permis_editar_centre) {
            $this->afegir_header('retroaccio', fct_string('retroaccio'));
            $this->afegir_textarea('observacions_centre', fct_string('tutor_centre'), 4, 50);
            $this->afegir_textarea('observacions_empresa', fct_string('tutor_empresa'), 4, 50);
        }

        if ($this->pagina->permis_editar_alumne) {
            $this->afegir_comprovacio('comprovar_quinzena');
        }

        if ($this->pagina->accio == 'veure') {
            if ($this->pagina->permis_editar) {
                $this->afegir_boto_enllac('editar', fct_string('edita'));
                if ($this->pagina->permis_editar_alumne) {
                    $this->afegir_boto_enllac('suprimir', fct_string('suprimeix'));
                }
            }
        } else if ($this->pagina->accio == 'afegir') {
            $this->afegir_boto('afegir', fct_string('afegeix'));
            $this->afegir_boto_cancellar();
        } else {
            $this->afegir_boto('desar', fct_string('desa'));
            $this->afegir_boto_cancellar();
        }

        if (!$this->pagina->permis_editar_alumne) {
            $this->congelar_element(array('periode', 'hores',
                'valoracions', 'observacions_alumne'));
            $this->congelar_llista('activitat');
        }
        if ($this->pagina->accio != 'afegir' and !$this->pagina->permis_editar_centre) {
            $this->congelar_element(array('observacions_centre'));
        }
        if ($this->pagina->accio != 'afegir' and !$this->pagina->permis_editar_empresa) {
            $this->congelar_element(array('observacions_empresa'));
        }
        if ($this->pagina->accio == 'veure') {
            $this->congelar();
        }
    }

    function configurar_periodes() {
        $this->clalendaris = array();
        $options_anys = array();
        $options_periodes = array();

        $any0 = $this->pagina->any_data($this->pagina->conveni->data_inici);
        $any1 = $this->pagina->any_data($this->pagina->conveni->data_final);
        $periode0 = $this->pagina->periode_data($this->pagina->conveni->data_inici);
        $periode1 = $this->pagina->periode_data($this->pagina->conveni->data_final);

        for ($any = $any0; $any <= $any1; $any++) {
            $options_anys[$any] = $any;
            $options_periodes[$any] = array();

            for ($periode = ($any == $any0 ? $periode0 : 0);
                 $periode <= ($any == $any1 ? $periode1 : 23);
                 $periode++) {
                $options_periodes[$any][$periode] =
                    $this->pagina->nom_periode($periode, $any);
                $calendari = $this->pagina->calendari_periode($any, $periode);
                if (!isset($this->calendaris[$any])) {
                    $this->calendaris[$any] = array();
                }
                $this->calendaris[$any][$periode] = $calendari;
            }
        }

        $this->options = array($options_anys, $options_periodes);

        if ($this->any <= $any0) {
            $this->any = $any0;
            if ($this->periode < $periode0) {
                $this->peridoe = $periode0;
            }
        }
        if ($this->any >= $any1) {
            $this->any = $any1;
            if ($this->periode > $periode1) {
                $this->periode = $periode1;
            }
        }
    }

}

