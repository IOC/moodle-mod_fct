<?php

require_once 'base_seguiment.php';
require_once 'form_quinzena.php';

class fct_pagina_afegir_quinzena extends fct_pagina_base_seguiment {

    var $activitats;

    function comprovar_quinzena($data) {
        if (fct_db::quinzena_duplicada($this->seguiment->id,
                addslashes($data['periode'][0]),
                addslashes($data['periode'][1]))) {
            return array('periode' => fct_string('quinzena_duplicada'));
        }
        return true;
    }

    function configurar() {
        parent::configurar(false, required_param('seguiment', PARAM_INT));
        if (!$this->permis_alumne and !$this->permis_admin) {
            $this->error('permis_pagina');
        }
        $this->configurar_accio(array('afegir', 'cancellar'), 'afegir');
        $this->url = fct_url::afegir_quinzena($this->quadern->id);
        $this->activitats = fct_db::activitats_pla($this->pla->id);
        $this->comprovar_estat_obert();
    }

    function processar_afegir() {
        list($any, $periode) = self::quinzena_actual();
        $form = new fct_form_quinzena($this, $any, $periode);
        $data = $form->get_data();

        if ($data) {
            $quinzena = (object) array(
                'seguiment' => $this->seguiment->id,
                'any_' => $data->periode[0],
                'periode' => $data->periode[1],
                'hores' => $data->hores,
                'valoracions' => $data->valoracions,
                'observacions_alumne' => $data->observacions_alumne,
            );
             $dies = $form->get_data_calendari('dia',
                 $this->calendari_periode($quinzena->any_,
                 $quinzena->periode));
            $id = fct_db::afegir_quinzena($quinzena, $dies,
                $form->get_data_llista('activitat'));
            if ($id) {
                $this->registrar('add quinzena', fct_url::quinzena($id),
                    self::nom_periode($data->periode[1]) . ' '
                    . $data->periode[0]);
            } else {
                $this->error('afegir_quinzena');
            }
            redirect(fct_url::quinzena($id));
        }

        $form->set_data((object) array(
            'periode' => array(0 => $any, 1 => $periode)));
        $this->mostrar_capcalera();
        $form->display();
        $this->mostrar_peu();
    }

    function processar_cancellar() {
        redirect(fct_url::seguiment($this->quadern->id));
    }
}

