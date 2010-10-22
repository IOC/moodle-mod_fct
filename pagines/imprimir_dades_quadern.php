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

fct_require('pagines/base_dades_quadern', 'pagines/form_base');

class fct_pagina_imprimir_dades_quadern extends fct_pagina_base_quadern {

    function configurar() {
        parent::configurar(required_param('quadern', PARAM_INT));
        $this->url = fct_url('imprimir_dades_quadern', array('quadern' => $this->quadern->id));
    }

    function data($time) {
        return $time ? strftime('%d / %B / %Y', $time) : '-';
    }

    function mostrar_camps($camps) {
        echo '<table class="camps">';
        foreach ($camps as $nom => $valor) {
            echo '<tr><td class="nom">' . fct_string($nom) . ':</td>'
                . '<td class="valor">' . s($valor) . '</td></tr>';
        }
        echo '</table>';
    }

    function mostrar_capcalera($titol) {
        echo '<!doctype html>' . "\n"
            . '<html><head><meta charset="utf-8" />'
            . '<title>' . s($titol) . '</title><style>' . "\n";
        include 'imprimir_dades_quadern.css';
        echo "\n" . '</style></head><body><h1>' . s($titol) . '</h1>';
    }

    function mostrar_dades_alumne() {
        $this->mostrar_titol(fct_string('alumne'));
        $procedencia = $this->quadern->dades_alumne->procedencia;
        $this->mostrar_camps(
            array('nom' => $this->nom_usuari($this->quadern->alumne),
                  'dni' => $this->quadern->dades_alumne->dni,
                  'data_naixement' =>  $this->data($this->quadern->dades_alumne->data_naixement),
                  'adreca' => $this->quadern->dades_alumne->adreca,
                  'codi_postal' => $this->quadern->dades_alumne->codi_postal,
                  'poblacio' => $this->quadern->dades_alumne->poblacio,
                  'telefon' => $this->quadern->dades_alumne->telefon,
                  'email' => $this->quadern->dades_alumne->email,
                  'inss' => $this->quadern->dades_alumne->inss,
                  'targeta_sanitaria' => $this->quadern->dades_alumne->targeta_sanitaria,
                  'procedencia' => $procedencia ? fct_string($procedencia) : '')
        );
    }

    function mostrar_dades_conveni() {
        $this->mostrar_titol(fct_string('conveni'));
        foreach ($this->quadern->convenis as $conveni) {
            $this->mostrar_camps(
                array('codi' => $conveni->codi,
                      'data_inici' => $this->data($conveni->data_inici),
                      'data_final' => $this->data($conveni->data_final))
            );
        }

        $hores = $this->serveis->hores_realitzades_quadern($this->quadern);
        $this->mostrar_camps(
            array('prorrogues' => $this->quadern->prorrogues,
                  'hores_practiques' => $this->quadern->hores_practiques,
                  'hores_realitzades' => $hores,
                  'hores_pendents' => $this->quadern->hores_practiques - (float) $hores)
        );
    }

    function mostrar_dades_quadern() {
        $this->mostrar_camps(
            array('alumne' => $this->nom_usuari($this->quadern->alumne),
                  'empresa' => $this->quadern->empresa->nom,
                  'tutor_centre' => $this->nom_usuari($this->quadern->tutor_centre),
                  'tutor_empresa' => $this->nom_usuari($this->quadern->tutor_empresa),
                  'cicle_formatiu' => $this->cicle->nom,
                  'estat' => fct_string("estat_{$this->quadern->estat}"))
        );
    }

    function mostrar_dades_empresa() {
        $this->mostrar_titol(fct_string('empresa'));
        $this->mostrar_camps(
            array('nom' => $this->quadern->empresa->nom,
                  'adreca' => $this->quadern->empresa->adreca,
                  'codi_postal' => $this->quadern->empresa->codi_postal,
                  'poblacio' => $this->quadern->empresa->poblacio,
                  'telefon' => $this->quadern->empresa->telefon,
                  'fax' => $this->quadern->empresa->fax,
                  'email' => $this->quadern->empresa->email,
                  'nif' => $this->quadern->empresa->nif,
                  'codi_agrupacio' => $this->quadern->empresa->codi_agrupacio,
                  'sic' => $this->quadern->empresa->sic)
        );

        $this->mostrar_subtitol(fct_string('responsable_conveni'));
        $this->mostrar_camps(
            array('nom' => $this->quadern->empresa->nom_responsable,
                  'cognoms' => $this->quadern->empresa->cognoms_responsable,
                  'dni' => $this->quadern->empresa->dni_responsable,
                  'carrec' => $this->quadern->empresa->carrec_responsable)
        );

        $this->mostrar_subtitol(fct_string('tutor_empresa'));
        $this->mostrar_camps(
            array('nom' => $this->quadern->empresa->nom_tutor,
                  'cognoms' => $this->quadern->empresa->cognoms_tutor,
                  'dni' => $this->quadern->empresa->dni_tutor,
                  'email' => $this->quadern->empresa->email_tutor)
        );
    }

    function mostrar_dades_horari() {
        $this->mostrar_titol(fct_string('horari_practiques'));
        foreach ($this->quadern->convenis as $conveni) {
            if (count($this->quadern->convenis) > 1) {
                $this->mostrar_subtitol(fct_string('conveni') . ': ' . $conveni->codi);
            }
            if ($conveni->horari) {
                echo '<table class="horari">'
                    . '<tr><th class="dia">' . fct_string('dia') . '</th>'
                    . '<th class="hora">' . fct_string('de') . '</th>'
                    . '<th class="hora">' . fct_string('a') . '</th></tr>';
                foreach ($conveni->horari as $franja) {
                    echo '<tr><td class="dia">' . fct_string($franja->dia) . '</td>'
                        . '<td class="hora">' . $franja->text_hora_inici() . '</td>'
                        . '<td class="hora">' . $franja->text_hora_final() . '</td></tr>';
                }
                echo '</table>';
            } else {
                echo '<p>' . fct_string('horari_no_definit') . '</p>';
            }
        }
    }

    function mostrar_dades_relatives() {
        $this->mostrar_titol(fct_string('dades_relatives'));
        $hores = $this->serveis->resum_hores_fct($this->quadern);
        $this->mostrar_camps(
            array('hores_credit' => $this->quadern->hores_credit,
                  'exempcio' => $this->quadern->exempcio,
                  'hores_anteriors' => $this->quadern->hores_anteriors,
                  'hores_realitzades' => $hores->realitzades,
                  'hores_pendents' => $hores->pendents)
        );
    }

    function mostrar_peu() {
        echo '</body></html>';
    }

    function mostrar_subtitol($titol) {
        echo '<h3>' . s($titol) . '</h3>';
    }

    function mostrar_titol($titol) {
        echo '<h2>' . s($titol) . '</h2>';
    }

    function processar() {
        $this->mostrar_capcalera(fct_string('quadern'));
        $this->mostrar_dades_quadern();
        $this->mostrar_dades_alumne();
        $this->mostrar_dades_empresa();
        $this->mostrar_dades_conveni();
        $this->mostrar_dades_horari();
        $this->mostrar_dades_relatives();
        $this->mostrar_peu();
    }
}
