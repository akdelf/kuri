<?php

require (dirname(__FILE__)."/routes/router_types.php");

use PHPUnit\Framework\TestCase;


class KuriRouterTypeTest extends TestCase {
    
    function testParamTrueInt(){

        $val = 12;

        $result =  kuloadfunc('test_int', null, ['0'=> $val]);
        $this->assertEquals($val, $result);
        
    }

    function testParamFalseInt(){

        $result =  kuloadfunc('test_int', null, ['0'=> 'sss']);
        $this->assertEquals(False, $result);

    }


    function testParamTrueBool(){

        $result =  kuloadfunc('test_bool', null, ['0'=> True]);
        $this->assertEquals(True, $result);

    }

    function testParamFalseBool(){

        $result =  kuloadfunc('test_bool', null, ['0'=> 12]);
        $this->assertEquals(False, $result);

    }

    function testParamTrueFloat(){

        $val = 1.234;

        $result =  kuloadfunc('test_float', null, ['0'=> $val]);
        $this->assertEquals($val, $result);

    }

    function testParamFalseFloat(){

        $result =  kuloadfunc('test_float', null, ['0'=> 'sss']);
        $this->assertEquals(False, $result);

    }




    
}