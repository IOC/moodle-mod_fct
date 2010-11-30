<?php
/* Quadern virtual d'FCT

   Copyright © 2008,2009,2010  Institut Obert de Catalunya

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

fct_require('diposit', 'domini', 'json', 'moodle');

class fct_pagina_base {

    var $accio = false;
    var $cm;
    var $course;
    var $fct;
    var $param = array();
    var $url;

    var $capcalera_mostrada = false;
    var $navegacio = array();
    var $pestanyes = array();
    var $pestanya = false;
    var $subpestanya = false;
    var $pestanyes_inactives = array();

    var $moodle;
    var $diposit;
    var $serveis;
    var $usuari;

    function __construct() {
        try {
            $this->moodle = new fct_moodle;
            $this->diposit = new fct_diposit($this->moodle);
            $this->serveis = new fct_serveis($this->diposit, $this->moodle);
            $this->configurar();
            $this->processar();
        } catch (fct_exception $e) {
            $this->error('pagina', $e->getMessage());
        }
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
        global $USER;

        if ($fct_id) {
            $this->cm = get_coursemodule_from_instance('fct', $fct_id);
        } else {
            $this->cm = get_coursemodule_from_id('fct', $cm_id);
        }

        $this->fct = $this->diposit->fct($this->cm->instance);
        $this->course = get_record('course', 'id', $this->fct->course);
        require_course_login($this->course, true, $this->cm);

        $this->usuari = $this->diposit->usuari($this->fct, $USER->id);
        if (!$this->usuari->es_administrador
            and! $this->usuari->es_alumne
            and !$this->usuari->es_tutor_centre
            and !$this->usuari->es_tutor_empresa) {
            $this->error('permis_activitat');
        }
    }

    function configurar_accio($accions, $predeterminada=false) {
        foreach ($accions as $accio) {
            if (optional_param($accio, 0, PARAM_BOOL)) {
                if ($this->accio) {
                    throw new fct_exception("més d'una acció indicada");
                }
                $this->accio = $accio;
            }
        }
        if (!$this->accio) {
            $this->accio = $predeterminada;
        }
    }

    function definir_pestanyes() {
        $pestanyes = array();
        if ($this->usuari->es_tutor_centre or $this->usuari->es_administrador) {
            $pestanyes[] = new tabobject('quaderns',
                                         fct_url('llista_quaderns', array('fct' => $this->fct->id)),
                                         fct_string('quaderns'));

        }
        if ($this->usuari->es_tutor_centre) {
            $pestanyes[] = new tabobject('avisos',
                                         fct_url('avisos', array('fct' => $this->fct->id)),
                                         fct_string('avisos'));
        }
        if ($this->usuari->es_administrador) {
            $pestanyes_admin = array(
                new tabobject('cicles',
                              fct_url('llista_cicles', array('fct' => $this->fct->id)),
                              fct_string('cicles_formatius')),
                new tabobject('dades_centre',
                              fct_url('dades_centre', array('fct' => $this->fct->id)),
                              fct_string('dades_centre')),
                new tabobject('frases_retroaccio',
                              fct_url('frases_retroaccio', array('fct' => $this->fct->id)),
                              fct_string('frases_retroaccio')),
                new tabobject('empreses',
                              fct_url('llista_empreses', array('fct' => $this->fct->id)),
                              fct_string('llista_empreses')),
                new tabobject('afegir_tutor_empresa',
                              fct_url('afegir_tutor_empresa', array('fct' => $this->fct->id)),
                              fct_string('afegeix_tutor_empresa')),
            );
            $pestanyes = array_merge($pestanyes, $pestanyes_admin);
        }
        $this->pestanyes = array($pestanyes);
    }

    function error($identifier, $info='') {
        $query = preg_replace('/^.*\/mod\/fct\//', '', me());
        $this->registrar('error ' .  $identifier, $query, $info);
        error(fct_string('error_' . $identifier));
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

    function mostrar_capcalera() {
        global $CFG;

        require_js("{$CFG->wwwroot}/mod/fct/client.js");

        $meta = '<script src="http://ajax.googleapis.com/ajax/libs/'
            . 'jquery/1.4.2/jquery.min.js" type="text/javascript"></script>';

        $navlinks = array();
        $navlinks[] = array('name' => format_string($this->fct->name),
                            'link' => "view.php?id={$this->cm->id}",
                            'type' => 'activityinstance');
        foreach ($this->navegacio as $enllac) {
            $navlinks[] = array('name' => $enllac->nom, 'link' => $enllac->url,
                                'type' => 'title');
        }
        $navigation = build_navigation($navlinks);

        $buttontext = update_module_button($this->cm->id,
                                           $this->fct->course,
                                           get_string('modulename', 'fct'));

        print_header_simple(format_string($this->fct->name), '', $navigation,
                            '', $meta, true, $buttontext,
                            navmenu($this->course, $this->cm));

        print_box_start('boxaligncenter', 'fct');

        if (!empty($this->fct->intro)) {
            print_box(format_text($this->fct->intro), 'generalbox', 'intro');
        }

        if ($this->pestanya) {
            $this->definir_pestanyes();
            if ($this->pestanyes) {
                $active = (!empty($this->subpestanya) ?
                           array($this->subpestanya) : null);
                print_tabs($this->pestanyes, $this->pestanya,
                           $this->pestanyes_inactives, $active);
            }
        }

        print_box_start('contingut');
        $this->capcalera_mostrada = true;
    }

    function mostrar_peu() {
        if ($this->capcalera_mostrada) {
            print_box_end();
            print_box_end();
        }
        print_footer($this->course);
    }

    static function nom_mes($mes) {
        $time = mktime(0, 0, 0, $mes+1, 1, 2000);
        return strftime('%B', $time);
    }

    static function nom_periode($periode, $any=2001) {
        $mes = floor((int) $periode / 2);
        $dies = ($periode % 2 == 0) ? '1-15' :
            '16-' . cal_days_in_month(CAL_GREGORIAN, $mes + 1, $any);
        return $dies . ' ' . self::nom_mes($mes);
    }

    function nom_usuari($user, $enllac=false, $correu=false) {
        global $CFG;

        if (!$user) {
            return '';
        }
        if (is_numeric($user)) {
          $user = get_record('user', 'id', $user);
        }
        if (!$user) {
            return '';
        }
        $html = $user->firstname.' '.$user->lastname;
        if ($enllac) {
            $html = '<a href="'.$CFG->wwwroot.'/user/view.php?id='.$user->id
                .'&amp;course='.$this->fct->course.'">'.$html.'</a>';
        }
        if ($correu) {
            $html .= ' (<a href="mailto:' . $user->email . '">'
                . $user->email . '</a>)';
        }
        return $html;
    }

    function processar() {
        if (!$this->accio) {
            throw new fct_exception("cap acció indicada");
        }

        $func = "processar_{$this->accio}";
        if (!method_exists($this, $func)) {
            throw new fct_exception("funció d'acció inexistent");
        }
        $this->$func();
    }

    function registrar($action, $url=null, $info='') {
        $url = is_null($url) ? $url : $this->url;
        $course = $this->fct ? $this->fct->course : 0;
        $cm = $this->cm ? $this->cm->id : 0;
        add_to_log($course, 'fct', $action, $url, $info, $cm);
    }
}
