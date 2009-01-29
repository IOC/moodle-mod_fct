<?php

require_once 'base_quaderns.php';
require_once 'form_quadern.php';

class fct_pagina_afegir_quadern extends fct_pagina_base_quaderns {

    function comprovar_nom_empresa($data) {
        if (fct_db::quadern_duplicat($this->fct->id, addslashes($data['alumne']),
                addslashes($data['nom_empresa']))) {
            return array('nom_empresa' => fct_string('quadern_duplicat'));
        }
        return true;
    }

    function configurar() {
        $this->configurar_accio(array('afegir', 'cancellar'), 'afegir');
        parent::configurar(required_param('fct', PARAM_INT));
        $this->comprovar_permis($this->permis_admin);
        $this->url = fct_url::afegir_quadern($this->fct->id);
    }

    function processar_afegir() {
        $form = new fct_form_quadern($this);
        $data = $form->get_data();
        if ($data) {
           $quadern = (object) array(
                'fct' => $this->fct->id,
                'alumne' => $data->alumne,
                'nom_empresa' => $data->nom_empresa,
                'tutor_centre' => $data->tutor_centre,
                'tutor_empresa' => $data->tutor_empresa,
                'cicle' => $data->cicle,
                'estat' => $data->estat);
            $id = fct_db::afegir_quadern($quadern);
            if ($id) {
                $this->registrar('add quadern', fct_url::quadern($id),
                    $this->nom_usuari($data->alumne) . " ({$data->nom_empresa})");
            } else {
                $this->error('afegir_quadern');
            }
            redirect(fct_url::quadern($id));
        }

        $this->mostrar_capcalera();
        $form->display();
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

