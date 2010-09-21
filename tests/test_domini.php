<?php
/* Quadern virtual d'FCT

   Copyright © 2009,2010  Institut Obert de Catalunya

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

require_once 'PHPUnit/Framework.php';
require_once 'domini.php';

class fct_test_domini extends PHPUnit_Framework_TestCase {

    function test_nova_data() {
        $data = new fct_data(31, 12, 2010);
        $this->assertEquals(31, $data->dia);
        $this->assertEquals(12, $data->mes);
        $this->assertEquals(2010, $data->any);
    }

    function test_dia_setmana_data() {
        $tests = array('dilluns' => new fct_data(20, 9, 2010),
                       'dimarts' => new fct_data(21, 9, 2010),
                       'dimecres' => new fct_data(22, 9, 2010),
                       'dijous' => new fct_data(23, 9, 2010),
                       'divendres' => new fct_data(24, 9, 2010),
                       'dissabte' => new fct_data(25, 9, 2010),
                       'diumenge' => new fct_data(26, 9, 2010));
        foreach ($tests as $dia => $data) {
            $this->assertEquals($dia, $data->dia_setmana());
        }
    }

    function test_data_valida() {
        $valides = array(new fct_data(1, 1, 2010),
                         new fct_data(31, 1, 2010),
                         new fct_data(28, 2, 2010),
                         new fct_data(29, 2, 2012),
                         new fct_data(31, 3, 2010),
                         new fct_data(30, 4, 2010),
                         new fct_data(31, 5, 2010),
                         new fct_data(30, 6, 2010),
                         new fct_data(31, 7, 2010),
                         new fct_data(31, 8, 2010),
                         new fct_data(30, 9, 2010),
                         new fct_data(31, 10, 2010),
                         new fct_data(30, 11, 2010),
                         new fct_data(31, 12, 2010));

        $no_valides = array(new fct_data(1, 0, 2010),
                            new fct_data(1, 13, 2010),
                            new fct_data(32, 1, 2010),
                            new fct_data(29, 2, 2010),
                            new fct_data(30, 2, 2012),
                            new fct_data(32, 3, 2010),
                            new fct_data(31, 4, 2010),
                            new fct_data(32, 5, 2010),
                            new fct_data(31, 6, 2010),
                            new fct_data(32, 7, 2010),
                            new fct_data(32, 8, 2010),
                            new fct_data(31, 9, 2010),
                            new fct_data(32, 10, 2010),
                            new fct_data(31, 11, 2010),
                            new fct_data(32, 12, 2010));

        foreach ($valides as $data) {
            $this->assertTrue($data->valida());
        }

        foreach ($no_valides as $data) {
            $this->assertFalse($data->valida());
        }
    }
}
