<?php

fct_require('pagines/form_base.php');

class fct_form_quadern extends fct_form_base {

    function configurar() {
        $this->afegir_header('quadern', $this->pagina->accio == 'afegir' ?
        	fct_string('nou_quadern') : fct_string('quadern'));

        $this->afegir_select(
            'alumne', fct_string('alumne'),
            $this->usuaris_amb_permis('mod/fct:alumne',
                                      $this->pagina->alumne(), false));

        $this->afegir_text( 'nom_empresa', fct_string('empresa'), 48, true);

        $this->afegir_select(
            'tutor_centre', fct_string('tutor_centre'),
            $this->usuaris_amb_permis('mod/fct:tutor_centre',
                                      $this->pagina->tutor_centre()));

        $this->afegir_select(
            'tutor_empresa', fct_string('tutor_empresa'),
            $this->usuaris_amb_permis('mod/fct:tutor_empresa',
                                      $this->pagina->tutor_empresa()));

        $this->afegir_select('cicle', fct_string('cicle_formatiu'), $this->cicles());

        $this->afegir_select('estat', fct_string('estat'),
                             array(1 => '<span class="estat_obert">' . fct_string('estat_obert') . '</span>',
                                   0 => '<span class="estat_tancat">' . fct_string('estat_tancat') . '</span>'));

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

    function cicles() {
        $cicles = array(0 => '');
        $cicles_fct = fct_db::cicles($this->pagina->fct->id);
        if ($cicles_fct) {
            foreach ($cicles_fct as $id => $cicle) {
                $cicles[$id] = $cicle->nom;
            }
        }
        return $cicles;
    }

    function usuaris_amb_permis($capability, $usuari, $usuari_nul=true) {
        $usuaris = array();
        if ($usuari_nul) {
            $usuaris[0] = '';
        }
        if ($usuari) {
            $usuaris[$usuari] = true;
        }

        $records = get_users_by_capability($this->pagina->context, $capability,
                                           'u.id', 'u.lastname, u.firstname',
                                           '', '', '', '', false);
        if ($records) {
            foreach ($records as $record) {
                $usuaris[$record->id] = true;
            }
        }

        foreach (array_keys($usuaris) as $id) {
            $usuaris[$id] = $this->pagina->nom_usuari(
                $id, true, ($this->pagina->accio == 'veure'));
        }
        return $usuaris;
    }
}

