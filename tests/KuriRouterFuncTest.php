<?php
    
    require (dirname(__FILE__)."/routes_func.php");

	use PHPUnit\Framework\TestCase;
    
    
    class KuriRouterFuncTest extends TestCase {


        function testFindIndex(){

            $res = array(
                'class' => '',
                'func' => 'index_kuri',
                'args' => array(),
                'cname' => 'index_kuri'
            );     
            
            $result = kufind();
            $this->assertEquals($res, $result);


        }   


        function testFindFunc(){

            $items = Array
            (
                '0' => 'test',
                '1' => '12'
            );

            $res = Array(
                'class' => '',
                'func' => 'test_kuri',
                'args' => Array
                    (
                        '0' => 12
                    ),
            
                'cname' => 'test_kuri'
            );

        
            $result = kufind($items);
            
            $this->assertEquals($res, $result);


        }

        
    }    