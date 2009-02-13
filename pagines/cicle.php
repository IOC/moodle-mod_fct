<?php

fct_require('pagines/base_cicles.php');
fct_require('pagines/form_cicle.php');

class fct_pagina_cicle extends fct_pagina_base_cicles {

    var $cicle;

    function comprovar_nom($data) {
        if (fct_db::cicle_duplicat($this->fct->id, addslashes($data['nom']),
                                   $this->cicle->id)) {
            return array('nom' => fct_string('cicle_formatiu_duplicat'));
        }

        return true;
    }

    function configurar() {
        $this->configurar_accio(array('veure', 'editar', 'desar', 'cancellar',
                                      'suprimir', 'confirmar'), 'veure');
        $this->cicle = fct_db::cicle(required_param('id', PARAM_INT));
        if (!$this->cicle) {
            $this->error('recuperar_cicle');
        }

        parent::configurar($this->cicle->fct);

        $this->url = fct_url::cicle($this->cicle->id);
        $this->form = new fct_form_cicle($this);
    }

    function mostrar() {
        $this->form->set_data($this->cicle);
        $this->mostrar_capcalera();
        $this->form->display();
        $this->mostrar_peu();
    }

    function processar_cancellar() {
        redirect(fct_url::cicle($this->cicle->id));
    }

    function processar_confirmar() {
        $this->comprovar_sessio();
        $ok = fct_db::suprimir_cicle($this->cicle->id);
        if ($ok) {
            $this->registrar('delete cicle',
                fct_url::llista_cicles($this->fct->id),
                $this->cicle->nom);
        } else {
            $this->error('suprimir_cicle');
        }
        redirect(fct_url::llista_cicles($this->fct->id));
    }

    function processar_desar() {
        $data = $this->form->get_data();
        if ($data) {
            $cicle = (object) array('id' => $this->cicle->id,
                                    'nom' => $data->nom,
                                    'activitats' => $data->activitats);
            $ok = fct_db::actualitzar_cicle($cicle);
            if ($ok) {
                $this->registrar('update cicle',
                    fct_url::cicle($this->cicle->id), $data->nom);
            } else {
                $this->error('desar_cicle');
            }
            redirect(fct_url::cicle($this->cicle->id));
        }

        $this->mostrar();
     }

    function processar_editar() {
        $this->mostrar();
    }

    function processar_suprimir() {
        $this->mostrar_capcalera();

        notice_yesno(fct_string('segur_suprimir_cicle_formatiu', $this->cicle->nom),
            $this->url, fct_url::cicle($this->cicle->id),
            array('confirmar' => 1, 'sesskey' => sesskey()));

        $this->mostrar_peu();
    }

    function processar_veure() {
        $this->mostrar();
        $this->registrar('view cicle');
    }

}

