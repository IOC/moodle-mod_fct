<?php

require_once 'form_base.php';

class fct_form_quadern extends fct_form_base {

    function configurar() {
        $this->afegir_header('quadern', $this->pagina->accio == 'afegir' ?
        	fct_string('nou_quadern') : fct_string('quadern'));

        $this->afegir_select('alumne', fct_string('alumne'),
            $this->usuaris_amb_permis('mod/fct:alumne', false));

        $this->afegir_text( 'nom_empresa', fct_string('empresa'), 48, true);

        $this->afegir_select('tutor_centre', fct_string('tutor_centre'),
            $this->usuaris_amb_permis('mod/fct:tutor_centre'));

        $this->afegir_select('tutor_empresa', fct_string('tutor_empresa'),
            $this->usuaris_amb_permis('mod/fct:tutor_empresa'));

        if ($this->pagina->accio == 'afegir') {
            $this->afegir_select('plantilla', fct_string('plantilla'),
                $this->pagina->plantilles, 'nom');
        } else {
            $this->afegir_select('estat', fct_string('estat'),
                array(1 => '<span class="estat_obert">' . fct_string('estat_obert') . '</span>',
                      0 => '<span class="estat_tancat">' . fct_string('estat_tancat') . '</span>'));
        }

        $this->afegir_comprovacio('comprovar_nom_empresa');

        if ($this->pagina->accio == 'afegir') {
            $this->afegir_boto('afegir', fct_string('afegeix'));
            $this->afegir_boto_cancellar();
        } else if ($this->pagina->accio == 'veure') {
            if ($this->pagina->permis_admin) {
                $this->afegir_boto_enllac('editar', fct_string('edita'));
                $this->afegir_boto_enllac('suprimir', fct_string('suprimeix'));
            }
            $this->congelar();
        } else {
            $this->afegir_boto('desar', fct_string('desa'));
            $this->afegir_boto_cancellar();
        }

    }

    function usuaris_amb_permis($capability, $usuari_nul=true) {
        $usuaris = array();
        if ($usuari_nul) {
            $usuaris[0] = '';
        }
        $records = get_users_by_capability($this->pagina->context, $capability,
                                           'u.id', 'u.lastname, u.firstname',
                                           '', '', '', '', false);
        if ($records) {
            foreach ($records as $record) {
                $enllac = $this->pagina->nom_usuari($record->id, true,
                    ($this->pagina->accio == 'veure'));
                $usuaris[$record->id] = $enllac;
            }
        }
        return $usuaris;
    }

}

