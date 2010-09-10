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

fct_require('pagines/base_quadern.php');

class fct_pagina_base_dades_quadern extends fct_pagina_base_quadern {

    var $permis_editar;

    function configurar($quadern_id) {
        parent::configurar($quadern_id);
        $this->pestanya = 'dades_generals';
        $this->permis_editar = ($this->usuari->es_administrador
                                or in_array($this->quadern->estat,
                                            array('proposat', 'obert'))
                                and ($this->usuari->es_tutor_centre
                                     or $this->usuari->es_alumne));
    }

    function definir_pestanyes() {
        parent::definir_pestanyes();
        $pestanyes = array(
            new tabobject('dades_centre_quadern',
                fct_url::dades_centre_quadern($this->quadern->id), fct_string('centre_docent')),
            new tabobject('dades_alumne',
                fct_url::dades_alumne($this->quadern->id), fct_string('alumne')),
            new tabobject('dades_empresa',
                fct_url::dades_empresa($this->quadern->id), fct_string('empresa')),
            new tabobject('dades_conveni',
                fct_url::dades_conveni($this->quadern->id), fct_string('conveni')),
            new tabobject('dades_horari',
                fct_url::dades_horari($this->quadern->id), fct_string('horari_practiques')),
            new tabobject('dades_relatives',
                fct_url::dades_relatives($this->quadern->id), fct_string('dades_relatives')),
        );
        $this->pestanyes[] = $pestanyes;
    }
}

