<?php

require_once 'PHPUnit/Framework.php';

class fct_tests {

    public static function suite() {
        $tests = array('diposit');

        $suite = new PHPUnit_Framework_TestSuite('FCT');
        foreach ($tests as $test) {
            $file = "test_$test.php";
            $class = "fct_test_$test";
            require_once($file);
            $suite->addTestSuite($class);
        }
        return $suite;
    }
}
