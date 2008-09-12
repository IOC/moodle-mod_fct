<?php

require_once 'form_base.php';

class fct_form_quinzena extends fct_form_base {

    var $options;
    var $calendaris;
    var $any;
    var $periode;
    var $dies;

    function __construct($pagina, $any, $periode, $dies=array()) {
        $this->any = $any;
        $this->periode = $periode;
        $this->dies = $dies;
        parent::__construct($pagina);
    }

    function configurar() {
        $this->configurar_periodes();

        $this->afegir_header('quinzena', $this->pagina->accio == 'afegir'
            ? fct_string('nova_quinzena') : fct_string('quinzena'));

        $this->afegir_hierselect('periode', fct_string('periode'),
            $this->options, ':',
            'actualitzar_calendari_dia(this.form)');

        $this->afegir_calendari('dia', fct_string('dies'), $this->calendaris,
                                $this->any, $this->periode, $this->dies,
                                !$this->pagina->permis_editar_alumne);

        $this->afegir_text('hores', fct_string('hores'), 4, true, true);

        if ($this->pagina->activitats) {
            $this->afegir_header('activitats_realitzades', fct_string('activitats_realitzades'));
            $this->afegir_llista_checkbox('activitat', $this->pagina->activitats, 'descripcio');
        }

        $this->afegir_header('observacions', fct_string('valoracions_observacions'));
        $this->afegir_textarea('valoracions', fct_string('valoracions'), 4, 50);
        $this->afegir_textarea('observacions_alumne', fct_string('observacions'), 4, 50);

        if ($this->pagina->accio != 'afegir' or $this->pagina->permis_editar_centre) {
            $this->afegir_header('retroaccio', fct_string('retroaccio'));
            $this->afegir_textarea('observacions_centre', fct_string('tutor_centre'), 4, 50);
            $this->afegir_textarea('observacions_empresa', fct_string('tutor_empresa'), 4, 50);
        }

        if ($this->pagina->permis_editar_alumne) {
            $this->afegir_comprovacio('comprovar_quinzena');
        }

        if ($this->pagina->accio == 'veure') {
            if ($this->pagina->permis_editar) {
                $this->afegir_boto_enllac('editar', fct_string('edita'));
                if ($this->pagina->permis_editar_alumne) {
                    $this->afegir_boto_enllac('suprimir', fct_string('suprimeix'));
                }
            }
        } else if ($this->pagina->accio == 'afegir') {
            $this->afegir_boto('afegir', fct_string('afegeix'));
            $this->afegir_boto_cancellar();
        } else {
            $this->afegir_boto('desar', fct_string('desa'));
            $this->afegir_boto_cancellar();
        }

        if (!$this->pagina->permis_editar_alumne) {
            $this->congelar_element(array('periode', 'hores',
                'valoracions', 'observacions_alumne'));
            $this->congelar_llista('activitat');
        }
        if ($this->pagina->accio != 'afegir' and !$this->pagina->permis_editar_centre) {
            $this->congelar_element(array('observacions_centre'));
        }
        if ($this->pagina->accio != 'afegir' and !$this->pagina->permis_editar_empresa) {
            $this->congelar_element(array('observacions_empresa'));
        }
        if ($this->pagina->accio == 'veure') {
            $this->congelar();
        }
    }

    function configurar_periodes() {
        $this->clalendaris = array();
        $options_anys = array();
        $options_periodes = array();

        $any0 = $this->pagina->any_data($this->pagina->conveni->data_inici);
        $any1 = $this->pagina->any_data($this->pagina->conveni->data_final);

        for ($any = $any0; $any <= $any1; $any++) {
            $options_anys[$any] = $any;
            $options_periodes[$any] = array();

            $periode0 = ($any == $any0) ? $this->pagina->periode_data(
                $this->pagina->conveni->data_inici) : 0;
            $periode1 = ($any == $any1) ? $this->pagina->periode_data(
                $this->pagina->conveni->data_final) : 23;

            for ($periode = $periode0; $periode <= $periode1; $periode++) {
                $options_periodes[$any][$periode] =
                    $this->pagina->nom_periode($periode, $any);
                $calendari = $this->pagina->calendari_periode($any, $periode);
                if (!isset($this->calendaris[$any])) {
                    $this->calendaris[$any] = array();
                }
                $this->calendaris[$any][$periode] = $calendari;
            }
        }

        $this->options = array($options_anys, $options_periodes);
    }

}

