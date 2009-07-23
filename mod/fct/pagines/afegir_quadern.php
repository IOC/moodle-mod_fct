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

fct_require('pagines/base_quaderns.php',
            'pagines/form_quadern.php');

class fct_pagina_afegir_quadern extends fct_pagina_base_quaderns {

    function comprovar_nom_empresa($valors) {
        $especificacio = new fct_especificacio_quaderns;
        $especificacio->fct = $this->fct->id;
        $especificacio->alumne = $valors->alumne;
        $especificacio->empresa = $valors->nom_empresa;
        if ($this->diposit->quaderns($especificacio)) {
            return array('nom_empresa' => fct_string('quadern_duplicat'));
        }
        return true;
    }

    function configurar() {
        $this->configurar_accio(array('afegir', 'cancellar'), 'afegir');
        parent::configurar(required_param('fct', PARAM_INT));
        $this->comprovar_permis($this->permis_admin);
        $this->url = fct_url::afegir_quadern($this->fct->id);
        $this->subpestanya = 'afegir_quadern';
    }

    function processar_afegir() {
        $form = new fct_form_quadern($this);
        if ($form->validar()) {
            $quadern = new fct_quadern;
            fct_copy_vars($form->valors(), $quadern);
            $quadern->empresa->nom = $form->valor('nom_empresa');
            $quadern->afegir_conveni(new fct_conveni);
            $this->diposit->afegir_quadern($quadern);
            $this->registrar('add quadern', fct_url::quadern($quadern->id),
                             $this->nom_usuari($quadern->alumne)
                             . ' ('. $quadern->empresa->nom . ')');
            redirect(fct_url::quadern($quadern->id));
        }

        $this->mostrar_capcalera();
        if (!$this->diposit->cicles($this->fct->id)){
            $missatge = fct_string('cicle_necessari_per_afegir_quaderns');
            echo "<p>$missatge</p>";
        } else {
            $form->mostrar();
        }
        $this->mostrar_peu();
    }

    function processar_cancellar() {
        redirect(fct_url::llista_quaderns($this->fct->id));
    }

    function alumne() {
        return false;
    }

    function tutor_centre() {
        return false;
    }

    function tutor_empresa() {
        return false;
    }
}

