<?php

    require_once '../../config.php';
    require_once 'lib.php';

    $id = optional_param('id', 0, PARAM_INT); // Course id

    if ($id) {
        if (!$course = get_record("course", "id", $id)) {
            error("Course ID is incorrect");
        }
    } else {
        if (!$course = get_site()) {
            error("Could not find a top-level course!");
        }
    }

    require_course_login($course);
    $coursecontext = get_context_instance(CONTEXT_COURSE, $course->id);

    $strfcts = get_string('modulenameplural', 'fct');
    $strfct = get_string('modulename', 'fct');

    // Print header

    $navlinks = array(array('name' => $strfcts,
                            'link' => "indexw.php?id=$id",
                            'type' => 'activity'));

    if ($course->id != SITEID) {
        print_header($strfcts, $course->fullname, build_navigation($navlinks),
                     "", "", true, false, navmenu($course));
    } else {
        print_header($strfcts, $course->fullname, build_navigation($navlinks),
                      "", "", true, "", navmenu($course));
    }

    // Print table

    $generaltable->head  = array ($strfct, get_string('description'));

    $fcts = get_all_instances_in_course('fct', $course);
    foreach ($fcts as $fct) {
        $cm = get_coursemodule_from_instance('fct', $fct->id,
            $course->id);
        $context = get_context_instance(CONTEXT_MODULE, $cm->id);
        $linkclass = $fct->visible ? '' :'class="dimmed"';
        $link = "<a $linkclass href=\"view.php?fct={$fct->id}\">"
             . format_string($fct->name, true)."</a>";
        $generaltable->data[] = array($link, $fct->intro);
    }

    print_table($generaltable);

    print_footer($course);

