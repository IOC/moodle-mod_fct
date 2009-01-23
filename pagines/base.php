<?php

class fct_pagina_base {

    var $accio = false;
    var $cm;
    var $course;
    var $context;
    var $fct;
    var $param = array();
    var $permis_admin;
    var $permis_alumne;
    var $permis_tutor_centre;
    var $permis_tutor_empresa;
    var $url;

    var $capcalera_mostrada = false;
    var $navegacio = array();
    var $pestanyes = array();
    var $pestanya = false;

    function __construct() {
        $this->configurar();
        $this->processar();
    }

    function afegir_navegacio($nom, $url=false) {
        $link = new object();
        $link->nom = $nom;
        $link->url = $url;
        $this->navegacio[] = $link;
    }

    function comprovar_permis($permis) {
        if (!$permis) {
            $this->error('permis_pagina');
        }
    }

    function comprovar_sessio() {
        if (!confirm_sesskey()) {
            $this->error('sessio');
        }
    }

    function configurar($fct_id=false, $cm_id=false) {

        if ($cm_id) {

            if (!$this->cm = get_coursemodule_from_id('fct', $cm_id)) {
                error("Course Module ID was incorrect");
            }
            if (!$this->course = get_record('course', 'id', $this->cm->course)) {
                error("Course is misconfigured");
            }
            if (!$this->fct = get_record('fct', 'id', $this->cm->instance)) {
                error("quadernfct ID was incorrect");
            }

        } else if ($fct_id) {

            if (!$this->fct = get_record('fct', 'id', $fct_id)) {
                error("fct ID was incorrect or no longer exists");
            }
            if (!$this->course = get_record('course', 'id', $this->fct->course)) {
                error("fct is misconfigured - don't know what course it's from");
            }
            if (!$this->cm = get_coursemodule_from_instance('fct',
                    $this->fct->id, $this->course->id)) {
                error("Course Module ID was incorrect");
            }

        } else {
            error('Must specify a course module or a qudernfct ID');
        }

        require_course_login($this->course, true, $this->cm);
        $this->context = get_context_instance(CONTEXT_MODULE, $this->cm->id);

        $this->permis_admin = has_capability('mod/fct:admin', $this->context);
        $this->permis_alumne = has_capability('mod/fct:alumne', $this->context);
        $this->permis_tutor_centre = has_capability('mod/fct:tutor_centre', $this->context);
        $this->permis_tutor_empresa = has_capability('mod/fct:tutor_empresa', $this->context);

        if (!$this->permis_admin and !$this->permis_alumne
            and !$this->permis_tutor_centre and !$this->permis_tutor_empresa) {
            $this->error('permis_activitat');
        }
    }

    function configurar_accio($accions, $predeterminada=false) {
        foreach ($accions as $accio) {
            if (optional_param($accio, 0, PARAM_BOOL)) {
                if ($this->accio) {
                    $this->error('accio_unica');
                } else {
                    $this->accio = $accio;
                }
            }
        }
        if (!$this->accio) {
            $this->accio = $predeterminada;
        }
    }

    function definir_pestanyes() {
        if ($this->permis_admin) {
            $this->pestanyes = array(array(
            new tabobject('quaderns', fct_url::llista_quaderns($this->fct->id), fct_string('quaderns')),
                new tabobject('afegir_quadern', fct_url::afegir_quadern($this->fct->id), fct_string('afegeix_quadern')),
                new tabobject('plantilles', fct_url::llista_plantilles($this->fct->id), fct_string('plantilles_activitats')),
                new tabobject('afegir_plantilla', fct_url::afegir_plantilla($this->fct->id), fct_string('afegeix_plantilla')),
                new tabobject('dades_centre', fct_url::dades_centre($this->fct->id), fct_string('dades_centre')),
                new tabobject('afegir_tutor_empresa', fct_url::afegir_tutor_empresa($this->fct->id), fct_string('afegeix_tutor_empresa')),
            ));
        }
    }

    function error($identifier, $a=null, $link='') {
        $url = preg_replace('/^.*\/mod\/fct\//', '', me());
        $info = print_r(data_submitted(), true);
        $this->registrar('error ' .  $identifier, $url, $info);
        error(fct_string('error_' . $identifier), $link);
    }

    function icona($cami, $url, $text) {
        global $CFG;
        return '<a title="'.$text.'" href="'.$url.'">'
        	. '<img src="'.$CFG->pixpath. $cami.'" '
        	.'class="iconsmall edit" alt="'.$text.'" /></a>';
    }

    function icona_editar($url, $text) {
        return $this->icona('/t/edit.gif', $url, $text);
    }

    function icona_suprimir($url, $text) {
        return $this->icona('/t/delete.gif', $url, $text);
    }

    function mostrar_capcalera($buttontext='') {
        $navlinks = array();
        $navlinks[] = array('name' => format_string($this->fct->name),
                            'link' => "view.php?id={$this->cm->id}",
                            'type' => 'activityinstance');
        foreach ($this->navegacio as $enllac) {
            $navlinks[] = array('name' => $enllac->nom, 'link' => $enllac->url,
                                'type' => 'title');
        }
        $navigation = build_navigation($navlinks);

        print_header_simple(format_string($this->fct->name), '',
            $navigation, '', '', true, $buttontext, navmenu($this->course, $this->cm));

        print_box_start('paginafct boxaligncenter');

        if ($this->pestanya) {
            $this->definir_pestanyes();
            if ($this->pestanyes) {
                print_tabs($this->pestanyes, $this->pestanya);
            }
        }

        print_box_start('contingutfct');
        $this->capcalera_mostrada = true;
    }

    function mostrar_peu() {
        if ($this->capcalera_mostrada) {
            print_box_end();
            print_box_end();
        }
        print_footer($this->course);
    }

    function nom_usuari($userid, $enllac=false, $correu=false) {
        global $CFG;

        if (!$userid) {
            return '';
        }
        $user = get_record('user', 'id', $userid);
        if (!$user) {
            return '';
        }
        $html = $user->firstname.' '.$user->lastname;
        if ($enllac) {
        	$html = '<a href="'.$CFG->wwwroot.'/user/view.php?id='.$userid
                .'&amp;course='.$this->course->id.'">'.$html.'</a>';
        }
        if ($correu) {
            $html .= ' (<a href="mailto:' . $user->email . '">'
                . $user->email . '</a>)';
        }
        return $html;
    }

    function processar() {
        if ($this->accio) {
            $func = "processar_{$this->accio}";
            if (!method_exists($this, $func)) {
                $this->error('processar_pagina');
            }
            $this->$func();
        } else {
            $this->error('cap_accio');
        }
    }

    function registrar($action, $url=null, $info='') {
        if (is_null($url)) {
            $url = $this->url;
        }
        add_to_log($this->course->id, 'fct', $action, $url, $info, $this->cm->id);
    }

    function select_quaderns() {
        global $USER;

        $select = 'FALSE';
        if ($this->permis_alumne) {
            $select .= ' OR q.alumne = '.$USER->id;
        }
        if ($this->permis_tutor_centre) {
            $select .= ' OR q.tutor_centre = '.$USER->id;
        }
        if ($this->permis_tutor_empresa) {
            $select .= ' OR q.tutor_empresa = '.$USER->id;
        }
        if ($this->permis_admin) {
            $select = '';
        }
        return $select;
    }
}

