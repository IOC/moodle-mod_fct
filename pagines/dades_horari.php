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

require_once($CFG->libdir . '/tablelib.php');
fct_require('pagines/base_dades_quadern', 'pagines/form_base');

class fct_form_element_afegir_franja_horari extends fct_form_base {

    function configurar($pagina) {
        $this->element('capcalera', "afegir_franja", 'nova_franja');
        $this->element('menu', 'conveni', 'conveni',
                       array('opcions' => $this->opcions_conveni($pagina)));
        $this->element('menu', 'dia', 'dia',
                       array('opcions' => $this->opcions_dia()));
        $this->element('hora', "hora_inici", 'de');
        $this->element('hora', "hora_final", 'a');
        $this->comprovacio($pagina, 'comprovar_hores');
        $this->element('boto', 'afegir', 'afegeix');
    }

    function opcions_dia() {
        return array('dilluns' => fct_string('dilluns'),
                     'dimarts' => fct_string('dimarts'),
                     'dimecres' => fct_string('dimecres'),
                     'dijous' => fct_string('dijous'),
                     'divendres' => fct_string('divendres'),
                     'dissabte' => fct_string('dissabte'),
                     'diumenge' => fct_string('diumenge'));
    }

    function opcions_conveni($pagina) {
        $opcions = array();
        foreach ($pagina->quadern->convenis as $conveni) {
            $opcions[$conveni->uuid] = $conveni->codi;
        }
        return $opcions;
    }
}

class fct_pagina_dades_horari extends fct_pagina_base_dades_quadern {

    var $form;

    function comprovar_hores($valors) {
        $errors = array();
        if ($valors->hora_inici == $valors->hora_final) {
            $errors['hora_inici'] = fct_string('franja_no_valida');
            $errors['hora_final'] = fct_string('franja_no_valida');
        }
        return $errors ? $errors : true;
    }

    function configurar() {
        parent::configurar(required_param('quadern', PARAM_INT));
        $this->configurar_accio(array('veure', 'afegir', 'suprimir', 'confirmar'), 'veure');

        if ($this->accio != 'veure') {
            $this->comprovar_permis($this->permis_editar);
        }
        $this->url = fct_url('dades_horari', array('quadern' => $this->quadern->id));
        $this->subpestanya = 'dades_horari';
        $this->form = new fct_form_element_afegir_franja_horari($this);
    }

    function mostrar() {
        $this->mostrar_capcalera();
        foreach ($this->quadern->convenis as $conveni) {
            echo '<h2>' .  fct_string('conveni') . ': ' . $conveni->codi . '</h2>';
            if ($conveni->horari) {
                $taula = new flexible_table('fct_activitats');
                $taula->define_columns(array('dia', 'hora_inici',
                                             'hora_final', 'accions'));
                $taula->column_class('accions', 'accions');
                $taula->define_headers(array(fct_string('dia'),
                                             fct_string('de'),
                                             fct_string('a'), ""));
                $taula->set_attribute('class', 'generaltable');
                $taula->setup();
                foreach ($conveni->horari as $franja) {
                    $accions = '';
                    if ($this->permis_editar) {
                        $url = fct_url('dades_horari',
                                       array('suprimir' => 1,
                                             'quadern' => $this->quadern->id,
                                             'conveni' => $conveni->uuid,
                                             'dia' => $franja->dia,
                                             'hora_inici' => $franja->hora_inici,
                                             'hora_final' => $franja->hora_final));
                        $accions = $this->icona_suprimir($url, fct_string('suprimeix_franja'));
                    }
                    $taula->add_data(array(fct_string($franja->dia),
                                           $franja->text_hora_inici(),
                                           $franja->text_hora_final(),
                                           $accions));
                }
                $taula->print_html();
            } else {
                echo '<p>' . fct_string('horari_no_definit') . '</p>';
            }
        }
        $this->form->valor('conveni', $this->quadern->ultim_conveni()->uuid);
        if ($this->permis_editar) {
            $this->form->mostrar();
        }
        $this->mostrar_peu();
    }

    function processar_afegir() {
        if ($this->form->validar()) {
            $franja = new fct_franja_horari($this->form->valor('dia'),
                                            $this->form->valor('hora_inici'),
                                            $this->form->valor('hora_final'));
            $conveni = $this->quadern->conveni($this->form->valor('conveni'));
            if ($conveni) {
                $conveni->afegir_franja_horari($franja);
                $this->diposit->afegir_quadern($this->quadern);
                $this->registrar('update dades_horari');
            }
            redirect($this->url);
        }
        $this->mostrar();
    }

    function processar_confirmar() {
        $this->comprovar_sessio();
        $conveni = $this->quadern->conveni(fct_param('conveni'));
        if ($conveni) {
            $franja = new fct_franja_horari(fct_param('dia'),
                                            fct_param('hora_inici'),
                                            fct_param('hora_final'));
            $conveni->suprimir_franja_horari($franja);
            $this->diposit->afegir_quadern($this->quadern);
            $this->registrar('update dades_horari');
        }
        redirect($this->url);
    }

    function processar_suprimir() {
        $conveni = $this->quadern->conveni(fct_param('conveni'));
        if ($conveni) {
            $this->mostrar_capcalera();
            notice_yesno(fct_string('segur_suprimir_franja'),
                         $this->url, $this->url,
                         array('sesskey' => sesskey(),
                               'confirmar' => 1,
                               'conveni' => $conveni->uuid,
                               'dia' => fct_param('dia'),
                               'hora_inici' => fct_param('hora_inici'),
                               'hora_final' => fct_param('hora_final')));
            $this->mostrar_peu();
        } else {
            redirect($this->url);
        }
    }

    function processar_veure() {
        $this->mostrar();
        $this->registrar('view dades_horari');
    }
}
