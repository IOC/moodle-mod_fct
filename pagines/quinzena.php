<?php

require_once 'base_seguiment.php';
require_once 'form_quinzena.php';

class fct_pagina_quinzena extends fct_pagina_base_seguiment {

    var $quinzena;
    var $titol;
    var $form;
    var $activitats;
    var $activitats_quinzena;
    var $dies_quinzena;

    function comprovar_quinzena($data) {
        if (fct_db::quinzena_duplicada($this->seguiment->id,
            addslashes($data['periode'][0]), addslashes($data['periode'][1]),
            $this->quinzena->id)) {
            return array('periode' => fct_string('quinzena_duplicada'));
        }
        return true;
    }

    function configurar() {
        $this->quinzena = fct_db::quinzena(required_param('quinzena', PARAM_INT));
        if (!$this->quinzena) {
            $this->error('recuperar_quinzena');
        }

        parent::configurar(false, $this->quinzena->seguiment);

        $this->configurar_accio(array('veure', 'editar', 'desar', 'suprimir',
        	'confirmar','cancellar'), 'veure');

        if ($this->accio != 'veure') {
            $this->comprovar_estat_obert();
        }

        $this->activitats = fct_db::activitats_pla($this->pla->id);
        if (!$this->activitats) {
            $this->activitats = array();
        }

        $this->activitats_quinzena = fct_db::activitats_quinzena($this->quinzena->id);
        if ($this->accio == 'veure' or (!$this->permis_alumne and !$this->permis_admin)) {
            if ($this->activitats_quinzena) {
                foreach (array_keys($this->activitats) as $id) {
                    if (!isset($this->activitats_quinzena[$id])) {
                       unset($this->activitats[$id]);
                    }
                }
            } else {
                $this->activitats = array();
            }
        }

        $this->dies_quinzena = fct_db::dies_quinzena($this->quinzena->id);

        $this->titol = self::nom_periode($this->quinzena->periode).' de '.$this->quinzena->any_;
        $this->url = fct_url::quinzena($this->quinzena->id);
        $this->afegir_navegacio($this->titol, $this->url);
    }

    function configurar_formulari() {
        $this->form = new fct_form_quinzena($this, $this->quinzena->any_,
            $this->quinzena->periode, $this->dies_quinzena);
        return $this->form->get_data();
    }

    function mostrar() {
        $this->quinzena->periode = array(
            0 => $this->quinzena->any_,
            1 => $this->quinzena->periode);
        $this->form->set_data($this->quinzena);
        $this->form->set_data_llista('activitat', $this->activitats_quinzena);
        $this->mostrar_capcalera();
        $this->form->display();
        $this->mostrar_peu();
    }

    function processar_cancellar() {
        redirect($this->url);
    }

    function processar_confirmar() {
        $this->comprovar_sessio();
        $ok = fct_db::suprimir_quinzena($this->quinzena->id);
        if ($ok) {
            $this->registrar('delete quinzena', null, $this->titol);
        } else {
            $this->error('suprimir_quinzena');
        }
        redirect(fct_url::seguiment($this->seguiment->id));
    }

    function processar_desar() {
        $data = $this->configurar_formulari();

        if ($data) {
            $quinzena = (object) array('id' => $this->quinzena->id);
            $activitats = false;

            if ($this->permis_alumne or $this->permis_admin) {
                $quinzena->any_ = $data->periode[0];
                $quinzena->periode = $data->periode[1];
                $quinzena->hores = $data->hores;
                $quinzena->valoracions = $data->valoracions;
                $quinzena->observacions_alumne = $data->observacions_alumne;
                $dies = $this->form->get_data_calendari('dia',
                    $this->calendari_periode($quinzena->any_,
                    $quinzena->periode));
                $activitats = $this->form->get_data_llista('activitat');
            }
            if ($this->permis_tutor_centre or $this->permis_admin) {
                $quinzena->observacions_centre = $data->observacions_centre;
                $quinzena->observacions_empresa = $data->observacions_empresa;
            }
            if ($this->permis_tutor_empresa or $this->permis_admin) {
                $quinzena->observacions_empresa = $data->observacions_empresa;
            }

            $ok = fct_db::actualitzar_quinzena($quinzena, $dies, $activitats);
            if ($ok) {
                $this->registrar('update quinzena', null, $this->titol);
            } else {
               $this->error('desar_quinzena');
            }
            redirect($this->url);
        }

        $this->mostrar();
    }

    function processar_editar() {
        $this->configurar_formulari();
        $this->mostrar();
    }

    function processar_suprimir() {
        $this->mostrar_capcalera();
        notice_yesno(fct_string('segur_suprimir_quinzena', $this->titol),
            $this->url, $this->url, array('confirmar' => 1, 'sesskey' => sesskey()));
        $this->mostrar_peu();
    }

    function processar_veure() {
        $this->configurar_formulari();
        $this->mostrar();
        $this->registrar('view quinzena', null, $this->titol);
    }

}

