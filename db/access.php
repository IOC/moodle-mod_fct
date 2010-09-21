<?php

$mod_fct_capabilities = array(

    'mod/fct:admin' => array(
        'captype' => 'write',
        'contextlevel' => CONTEXT_MODULE,
        'legacy' => array(
            'editingteacher' => CAP_ALLOW,
            'admin' => CAP_ALLOW,
        )
    ),

    'mod/fct:alumne' => array(
        'captype' => 'write',
        'contextlevel' => CONTEXT_MODULE,
        'legacy' => array(
            'student' => CAP_ALLOW,
        )
    ),

    'mod/fct:tutor_centre' => array(
        'captype' => 'write',
        'contextlevel' => CONTEXT_MODULE,
        'legacy' => array(
            'teacher' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
        )
    ),

    'mod/fct:tutor_empresa' => array(
        'captype' => 'write',
        'contextlevel' => CONTEXT_MODULE,
    ),

);
