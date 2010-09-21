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

fct_require('pagines/base.php');

class fct_pagina_base_quadern extends fct_pagina_base {

    var $quadern;
    var $cicle;
    var $titol;

    function configurar($quadern_id) {
        global $USER;

        $this->quadern = fct_db::quadern($quadern_id);
        if (!$this->quadern) {
            $this->error('recuperar_quadern');
        }

        $this->cicle = fct_db::cicle($this->quadern->cicle);
        if (!$this->cicle) {
            $this->error('recuperar_cicle');
        }

        parent::configurar($this->cicle->fct);

        $this->comprovar_permis($this->permis_admin
            or ($this->permis_alumne and $this->quadern->alumne == $USER->id)
            or ($this->permis_tutor_centre and $this->quadern->tutor_centre == $USER->id)
            or ($this->permis_tutor_empresa and $this->quadern->tutor_empresa == $USER->id));

        $this->titol = $this->nom_usuari($this->quadern->alumne)
            .' ('.$this->quadern->nom_empresa.')';
        $this->afegir_navegacio($this->titol,
            fct_url::quadern($this->quadern->id));
    }

    function definir_pestanyes() {
        $this->pestanyes = array(array(
            new tabobject('quadern',
                fct_url::quadern($this->quadern->id),
                fct_string('quadern')),
            new tabobject('dades_generals',
                fct_url::dades_centre_quadern($this->quadern->id),
                fct_string('dades_generals')),
            new tabobject('pla_activitats',
                fct_url::pla_activitats($this->quadern->id),
                fct_string('pla_activitats')),
            new tabobject('seguiment_quinzenal',
                fct_url::seguiment($this->quadern->id),
                fct_string('seguiment_quinzenal')),
            new tabobject('valoracio',
                fct_url::valoracio_actituds($this->quadern->id, 0),
                fct_string('valoracio')),
            new tabobject('qualificacio_global',
                fct_url::qualificacio_global($this->quadern->id),
                fct_string('qualificacio_global')),
        ));
    }

    function alumne() {
        return $this->quadern->alumne;
    }

    function tutor_centre() {
        return $this->quadern->tutor_centre;
    }

    function tutor_empresa() {
        return $this->quadern->tutor_empresa;
    }

}

