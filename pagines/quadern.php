<?php

fct_require('pagines/base_quadern.php',
            'pagines/form_quadern.php');

class fct_pagina_quadern extends fct_pagina_base_quadern {

    function comprovar_nom_empresa($data) {
        if (fct_db::quadern_duplicat($this->fct->id, addslashes($data['alumne']),
                addslashes($data['nom_empresa']), $this->quadern->id)) {
            return array('nom_empresa' => fct_string('quadern_duplicat'));
        }
        return true;
    }

    function configurar() {
        parent::configurar(required_param('quadern', PARAM_INT));
        $this->configurar_accio(array('veure', 'editar', 'desar',
            'cancellar', 'suprimir', 'confirmar'), 'veure');

        if ($this->accio != 'veure') {
            $this->comprovar_permis($this->permis_admin);
        }

        $this->url = fct_url::quadern($this->quadern->id);
        $this->pestanya = 'quadern';
        $this->form = new fct_form_quadern($this);
    }

    function mostrar() {
        $this->form->set_data($this->quadern);
        $this->mostrar_capcalera();
        $this->form->display();
        $this->mostrar_peu();
    }

    function processar_cancellar() {
        redirect($this->url);
    }

    function processar_confirmar() {
        $this->comprovar_sessio();
        if (!fct_db::suprimir_quadern($this->quadern->id)) {
            $this->error('suprimir_quadern');
        }
        redirect(fct_url::llista_quaderns($this->fct->id));
    }

    function processar_desar() {
        $data = $this->form->get_data();
        if ($data) {
            $quadern = (object) array(
                'id' => $this->quadern->id,
                'alumne' => $data->alumne,
                'nom_empresa' => $data->nom_empresa,
                'tutor_centre' => $data->tutor_centre,
                'tutor_empresa' => $data->tutor_empresa,
                'cicle' => $data->cicle,
                'estat' => $data->estat);
            $ok = fct_db::actualitzar_quadern($quadern);
            if ($ok) {
                $this->registrar('update quadern');
            } else {
                $this->error('desar_quadern');
            }
            redirect(fct_url::quadern($this->quadern->id));
        }
        $this->mostrar();
    }

    function processar_editar() {
        $this->mostrar();
    }

    function processar_suprimir() {
        $this->mostrar_capcalera();
        notice_yesno(fct_string('segur_suprimir_quadern', $this->titol),
            $this->url, fct_url::quadern($this->quadern->id),
            array('confirmar' => 1, 'sesskey' => sesskey()));
        $this->mostrar_peu();
    }

    function processar_veure() {
        $this->mostrar();
        $this->registrar('view quadern');
    }
}

