<?php

class fct_resum_hores_fct {
    var $credit;
    var $exempcio;
    var $anteriors;
    var $practiques;

    var $realitzades;
    var $pendents;


    function __construct($hores_credit, $hores_anteriors,
                         $exempcio, $hores_practiques) {
        $this->credit = $hores_credit;
        $this->anteriors = $hores_anteriors;
        $this->practiques = $hores_practiques;
        $this->exempcio = ceil((float) $exempcio / 100 * $hores_credit);

        $this->realitzades = $this->anteriors + $this->exempcio
            + $this->practiques;
        $this->pendents = max(0, $this->credit - $this->realitzades);
    }
}