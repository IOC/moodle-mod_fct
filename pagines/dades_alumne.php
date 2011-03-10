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

class fct_form_dades_alumne extends fct_form_base {

    function configurar($pagina) {
        $this->element('capcalera', 'dades_alumne', 'alumne');
        $this->element('estatic', 'nom', 'nom');
        $this->element('text', 'dni', 'dni', array('size' => 16));
        $this->element('data', 'data_naixement', 'data_naixement',
                       array('opcional' => true));
        $this->element('text', 'adreca', 'adreca');
        $this->element('text', 'codi_postal', 'codi_postal',
                       array('size' => 8));
        $this->element('text', 'poblacio', 'poblacio');
        $this->element('text', 'telefon', 'telefon');
        $this->element('text', 'email', 'email');

        $this->element('menu', 'procedencia', 'procedencia',
                       array('opcions' => $this->opcions_procedencia()));

        $this->element('capcalera', 'targeta_sanitaria', 'targeta_sanitaria');
        $this->element('text', 'codi_targeta_sanitaria', 'codi');
        $this->element('imatge', 'imatge_targeta_sanitaria', 'imatge_targeta');
        if ($pagina->accio != 'veure') {
            $this->element('fitxer', 'fitxer_targeta_sanitaria', 'nova_imatge',
                           array('mimetype' => array('image/jpeg', 'image/png')));
            $this->element('opcio', 'suprimir_targeta_sanitaria', 'suprimeix_imatge');
        }

        $this->element('capcalera', 'inss', 'inss');
        $this->element('text', 'codi_inss', 'codi');
        $this->element('imatge', 'imatge_inss', 'imatge_targeta');
        if ($pagina->accio != 'veure') {
            $this->element('fitxer', 'fitxer_inss', 'nova_imatge',
                           array('mimetype' => array('image/jpeg', 'image/png')));
            $this->element('opcio', 'suprimir_inss', 'suprimeix_imatge');
        }

        if ($pagina->accio == 'veure') {
            if ($pagina->permis_editar) {
                $this->element('boto', 'editar', 'edita');
            }
            $this->congelar();
        } else {
            $this->element('boto', 'desar', 'desa');
            $this->element('boto', 'cancellar');
        }
    }

    function opcions_procedencia() {
        return array('' => '',
                     'batxillerat' => fct_string('batxillerat'),
                     'curs_acces' => fct_string('curs_acces'),
                     'cicles' => fct_string('cicles'),
                     'eso' => fct_string('eso'),
                     'ges' => fct_string('ges'));
    }
}

class fct_pagina_dades_alumne extends fct_pagina_base_dades_quadern {

    var $form;

    function configurar() {
        parent::configurar(required_param('quadern', PARAM_INT));
        $this->configurar_accio(array('veure', 'editar', 'desar', 'cancellar'), 'veure');

        if ($this->accio != 'imatge_catsalut') {
            $this->comprovar_permis($this->permis_editar);
        }

        $this->url = fct_url('dades_alumne', array('quadern' => $this->quadern->id));
        $this->subpestanya = 'dades_alumne';
        $this->form = new fct_form_dades_alumne($this);
    }

    function desar_imatge($file, $nom) {
        if ($this->redimensionar_imatge($file, 500, 500)) {
            $this->moodle->upload_file($file['tmp_name'], $this->fct->course,
                                       "quadern-{$this->quadern->id}/$nom.jpg");
        }
    }

    function mostrar() {
        $this->form->valors($this->quadern->dades_alumne);
        $this->form->valor('nom', $this->nom_usuari($this->quadern->alumne, true));
        $this->form->valor('codi_targeta_sanitaria', $this->quadern->dades_alumne->targeta_sanitaria);
        $this->form->valor('imatge_targeta_sanitaria', $this->url_imatge('catsalut'));
        $this->form->valor('codi_inss', $this->quadern->dades_alumne->inss);
        $this->form->valor('imatge_inss', $this->url_imatge('inss'));
        $this->mostrar_capcalera();
        $this->form->mostrar();
        $this->mostrar_peu();
    }

    function processar_cancellar() {
        redirect($this->url);
    }

    function processar_desar() {
        if ($this->form->validar()) {
            $this->quadern->dades_alumne->dni = $this->form->valor('dni');
            $this->quadern->dades_alumne->data_naixement = $this->form->valor('data_naixement');
            $this->quadern->dades_alumne->adreca = $this->form->valor('adreca');
            $this->quadern->dades_alumne->codi_postal = $this->form->valor('codi_postal');
            $this->quadern->dades_alumne->poblacio = $this->form->valor('poblacio');
            $this->quadern->dades_alumne->telefon = $this->form->valor('telefon');
            $this->quadern->dades_alumne->email = $this->form->valor('email');
            $this->quadern->dades_alumne->procedencia = $this->form->valor('procedencia');
            $this->quadern->dades_alumne->targeta_sanitaria = $this->form->valor('codi_targeta_sanitaria');
            if ($this->form->valor('suprimir_targeta_sanitaria')) {
                $this->suprimir_imatge('catsalut');
            } else if ($file = $this->form->valor('fitxer_targeta_sanitaria')) {
                $this->desar_imatge($file, 'catsalut');
            }
            $this->quadern->dades_alumne->inss = $this->form->valor('codi_inss');
            if ($this->form->valor('suprimir_inss')) {
                $this->suprimir_imatge('inss');
            } else if ($file = $this->form->valor('fitxer_inss')) {
                $this->desar_imatge($file, 'inss');
            }

            $this->diposit->afegir_quadern($this->quadern);
            if ($this->quadern->alumne == $this->usuari->id) {
                $this->serveis->registrar_avis($this->quadern, 'dades_alumne');
            }
            $this->registrar('update dades_alumne');
            redirect($this->url);
        }
        $this->mostrar();
    }

    function processar_editar() {
        $this->mostrar();
    }

    function processar_veure() {
        $this->mostrar();
        $this->registrar('view dades_alumne');
    }

    function redimensionar_imatge($file, $max_width, $max_height) {
        if ($file['type'] == 'image/jpeg') {
            $image = imagecreatefromjpeg($file['tmp_name']);
        } elseif ($file['type'] == 'image/png') {
            $image = imagecreatefrompng($file['tmp_name']);
        } elseif ($file['type'] == 'image/gif') {
            $image = imagecreatefromgif($file['tmp_name']);
        } else {
            return false;
        }

        if ($image === false) {
            return false;
        }

        if (imagesx($image) > $max_width) {
            $width = $max_width;
            $height = imagesy($image) * $max_width / imagesx($image);
        }
        if (imagesy($image) > $max_height) {
            $height = $max_height;
            $width = imagesx($image) * $max_height / imagesy($image);
        }

        $resized = imagecreatetruecolor($width, $height);
        imagecopyresampled($resized, $image, 0, 0, 0, 0, $width, $height, imagesx($image), imagesy($image));
        imagedestroy($image);
        imagejpeg($resized, $file['tmp_name'], 85);
        imagedestroy($resized);

        return true;
    }

    function suprimir_imatge($nom) {
        $this->moodle->delete_file($this->fct->course,
                                   "quadern-{$this->quadern->id}/$nom.jpg");
    }

    function url_imatge($nom) {
        return $this->moodle->file_url($this->fct->course,
                                       "quadern-{$this->quadern->id}/$nom.jpg");
    }
}
