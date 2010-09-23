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

fct_require('pagines/base');

class fct_pagina_base_quadern extends fct_pagina_base {

    var $quadern;
    var $cicle;
    var $titol;

    function configurar($quadern_id) {
        $this->quadern = $this->diposit->quadern($quadern_id);
        $this->cicle = $this->diposit->cicle($this->quadern->cicle);

        parent::configurar($this->cicle->fct);

        $this->comprovar_permis($this->usuari->es_administrador
            or ($this->usuari->es_alumne and
                $this->quadern->alumne == $this->usuari->id)
            or ($this->usuari->es_tutor_centre and
                $this->quadern->tutor_centre == $this->usuari->id)
            or ($this->usuari->es_tutor_empresa and
                $this->quadern->tutor_empresa == $this->usuari->id));

        $this->titol = $this->nom_usuari($this->quadern->alumne)
            .' ('.$this->quadern->empresa->nom.')';
        $this->afegir_navegacio($this->titol,
            fct_url('quadern', array('quadern' => $this->quadern->id)));
    }

    function definir_pestanyes() {
        $this->pestanyes = array(array(
            new tabobject('quadern',
                          fct_url('quadern', array('quadern' => $this->quadern->id)),
                          fct_string('quadern')),
            new tabobject('dades_generals',
                          fct_url('dades_centre_quadern', array('quadern' => $this->quadern->id)),
                          fct_string('dades_generals')),
            new tabobject('pla_activitats',
                          fct_url('pla_activitats', array('quadern' => $this->quadern->id)),
                          fct_string('pla_activitats')),
            new tabobject('seguiment_quinzenal',
                          fct_url('seguiment', array('quadern' => $this->quadern->id)),
                          fct_string('seguiment_quinzenal')),
            new tabobject('valoracio',
                          fct_url('valoracio_actituds', array('quadern' => $this->quadern->id, 'final' => 0)),
                          fct_string('valoracio')),
            new tabobject('qualificacio_global',
                          fct_url('qualificacio_global', array('quadern' => $this->quadern->id)),
                          fct_string('qualificacio_global')),
        ));
        if ($this->quadern->estat == 'proposat' and !$this->usuari->es_administrador) {
            $this->pestanyes_inactives = array('seguiment_quinzenal',
                                               'valoracio',
                                               'qualificacio_global');
        }
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

