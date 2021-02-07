<?php
	declare(strict_types=1);
	
	
	use PHPUnit\Framework\TestCase;
	
	
	class kuriParserTest extends TestCase {

		
		//test http
		function testKuriParserHttp(){
			
			$url = 'http://argumenti.ru/';
			$items = [
				'scheme' => 'http',
    			'host' => 'argumenti.ru',
    			'path' => '/',
    			'method' => ''
			];

			$result = kuri_parser($url);

			$this->assertEquals($items, $result);

		}

		//test https
		function testKuriParserHttps(){
			
			$url = 'https://argumenti.ru/';
			$items = [
				'scheme' => 'https',
    			'host' => 'argumenti.ru',
    			'path' => '/',
    			'method' => ''
			];

			$result = kuri_parser($url);

			$this->assertEquals($items, $result);

		}


		//test https
		function testKuriParserParam(){
			
			$url = 'https://test.ru/test_id/12/';
			$items = [
				'scheme' => 'https',
				'host' => 'test.ru',
				'path' => '/test_id/12/',
				'items' => [
					'0' => 'test_id',
					'1' => '12'
				],	
				'method' => ''
			];

			$result = kuri_parser($url);
			$this->assertEquals($items, $result);

		}
		
	

	}


	
