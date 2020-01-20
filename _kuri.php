<?php declare(strict_types=1);

		/**
		* kURI: return scheme, host, path, controller, action, items, http method 
		* @license http://www.opensource.org/licenses/mit-license.html  MIT License
		* @author АК Delfin <masterforweb@hotmail.com>
		*/
		
		function kuri_parser($uri = '') {

		    $result = array();

			$result = parse_url(urldecode($uri));

			if ($_SERVER['PHP_SELF'] !== '')
			   $spath = $_SERVER['PHP_SELF'];
			elseif ($_SERVER['SCRIPT_NAME'] !== '')
				$spath = $_SERVER['SCRIPT_NAME'];
			
			
			/* корень пути с учетом подпапки */
			$dirname = dirname($spath);
			if ($dirname !== '/') {
				$ldir = strlen($dirname);
				$result['path'] = '/'.substr($result['path'], $ldir);
			}
			
			/* определяем путь относительно url */
			if ($result['path'] !== '/') {
					$result['items'] = explode('/', trim($result['path'], '/'));
			}
			
			
			$result['method'] = $_SERVER['REQUEST_METHOD'];
			
			return $result;
		}


		function kuri_argv($argv){

		    if (isset($argv)) {

		        $items = $_SERVER['argv'];

		        $result['script'] = array_shift($items);
		        $result['method'] = 'command';
		        $result['items'] = $items;

		        return $result;

            }

		    return null;

        }





		/**
		* current url (k - current url)
		*/
		function kurl() {
			
			
			if (isset($_SERVER['REQUEST_URI']) and $_SERVER['REQUEST_URI'] !== '')
				$uri = $_SERVER['REQUEST_URI'];
			elseif(isset($_SERVER['PATH_INFO']) and $_SERVER['PATH_INFO'] !== '')
				$uri = $_SERVER['PATH_INFO'];
			else
			    return False;

			
			/*get query*/
			if (isset($_SERVER['QUERY_STRING']) and $_SERVER['QUERY_STRING'] !== ''){
				if ($uri)	
					$uri = str_replace($_SERVER['QUERY_STRING'], '',$uri);
				else
					$uri = $_SERVER['REQUEST_URI'];
			}
				
			$uri = trim($uri, '/');
			
			if (!isset($_SERVER['REQUEST_SCHEME']) or $_SERVER['REQUEST_SCHEME'] == '')
				$sheme = 'http';
			else {
				$sheme = $_SERVER['REQUEST_SCHEME'];
			}
			$result = $sheme.'://'.$_SERVER['SERVER_NAME'].'/';
			if ($uri !== '')
				$result .= $uri;
			return $result;
		}
		
		/**
		* find controller (k - controller)
		*/
		function kufind($items = array(), $method = 'get', $prefix = ''){
			$size = sizeof($items);
			$action = 'index';
			
			if ($size == 0) {// mainpage
				$cname = 'main';
			}	
			else {
				$cname = array_shift($items); //title action
				if ($size > 1)
					$action = $items[0];
			}

			$cname .= $prefix;


			/**
             * if ($control = kuload($cname)){ //autoload class
				
				if (method_exists($control, $action)){
					if ($size > 2)
						$action = array_shift($items);
					$func = $action;
					$args = $items;
				}
				elseif (method_exists($control, $method)){ //REST API post, get ... 
					$func = $method;
					$args =  $items;
				}	
			
				if ($func)
					return array('class'=>$control, 'func'=>$func, 'args'=>$args);
			}
			
			define('KURI_CNAME', $cname); **/


			
			$func_temp = str_replace('-', '_', $cname);
			
			if (function_exists($func = $func_temp.'_'.$action)){
				$action = array_shift($items);
				$args = $items;
			}
			elseif (function_exists($func = $func_temp.'_'.$method)){
				$args = $items;
			}
			elseif (function_exists($func = $func_temp)){
				$args = $items;
			}	
			else
				return False;
					
			return array('class'=>False, 'func'=>$func, 'args'=>$args, 'cname'=>$cname);
		}

		function kuload($cname, $p = ''){

            $class = kuri_load_class($cname);

            if (isset($class))
                return $class;


            if (defined(APPPATH))
                $path_load = APPPATH;
            else
                $path_load = 'app/';

            $cfile = $path_load.'controllers'.DIRECTORY_SEPARATOR.$cname.DIRECTORY_SEPARATOR.$cname.'.php';


            if (file_exists($cfile)) {
                require($cfile);
                return kuri_load_class($cname);
            }

            return null;


		}

		function kuri_load_class($cname){

            if (class_exists($cname))
                return new $cname();

            return null;

        }
		
		/**
		* Base load controller class in 
		*/
		function kucontroller($cname, $path){

		    if ($path == null)
				$path = 'app'.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR;
			
			if (!class_exists($cname)) {
				$cfile = $path.'.php';
				if (file_exists($cfile)) 
					require ($cfile);
				else
					return False;
			}
			return new $cname();
		}
		
		
		function kufindfunc($items = array(), $method = 'GET') {
			$size = sizeof($items);
			$action = 'index';
			
			if ($size == 0) {// mainpage
				$cname = 'main';
			}	
			else {
				$cname = array_shift($items);
				if ($size > 2)
					$action = $items[0];
			}
			if ($control = $this->loadclass($cname)){ //autoload class
				
				if (method_exists($control, $action)){
					if ($size > 2)
						$action = array_shift($items);
					$func = $action;
					$args = $items;
				}
				elseif (method_exists($control, $method)){ //REST API post, get ... 
					$func = $method;
					$args =  $items;
				}	
			
				if ($func)
					return array('class'=>$cname, 'func'=>$func, 'args'=>$args);
			}
				
			if (function_exists($func = $cname.'_'.$action)){
				$action = array_shift($items);
				$args = $items;
			}
			elseif (function_exists($func = $cname.'_'.$method)){
				$args = $items;
			}
			elseif (function_exists($func = $cname)){
				$args = $items;
			}	
			else
				return kuri_http_error(404);
					
			return array('class'=>False, 'func'=>$func, 'args'=>$arguments);
					
		}
		
		function kuloadfunc($func, $class = False, $args = array()) {

			if ($class == False) {

			    $arg_count = sizeof($args);

			    if (is_array($args) and $arg_count > 0) {
                    $realparams = kuri_real_params($func);

                    if ($arg_count > sizeof($realparams))
                        return kuri_http_error(404);


                    for ($i = 0; $i < $arg_count; $i++) {

                        if ($realparams[$i] == 'int') {
                            $valid = filter_var($args[$i], FILTER_VALIDATE_INT);
                        }
                        elseif ($realparams[$i] == 'boolean') {
                            $valid = filter_var($args[$i], FILTER_VALIDATE_BOOLEAN);
                        }
                        elseif ($realparams[$i] == 'float') {
                            $valid = filter_var($args[$i], FILTER_VALIDATE_FLOAT);
                        }
                        else
                            $valid = $args[$i];

                        if ($valid){
                            $params[$i] = $valid;
                        }
                        else {
                            return kuri_http_error(404);
                        }

                    }

                    try {
                        return call_user_func_array($func, $params);
                    }
                    catch (Error $e) {
                        return kuri_http_error(404);
                    }

                }
				else
					return call_user_func($func);
			}
			else {

			    if (is_array($args) and sizeof($args) > 0)
					return call_user_func_array(array($class, $func), $args);
				else
					return call_user_func(array($class, $func));
			}	
		}


        function kuri_real_params($func){

            $reflectionFunc = new ReflectionFunction($func);
            $reflectionParams = $reflectionFunc->getParameters();

            foreach ($reflectionParams as $key=>$refparam) {

                $name = (string)$refparam->getName();

                if ($refparam->hasType())
                    $type = (string)$refparam->getType();
                else
                    $type = 'string';

                $params[$key] = $type;
            }

            return $params;

        }
		
				
		
		function view ($view, $data = array(), $layer = null){
			
			ob_start();
        		
        	if (is_array($data))
            	extract($data);
            if ($layer !== null){
            	$content = view($view, $data);
            	require $layer;
            }
            else
            	require $view;
   	       	       	
        	return trim(ob_get_clean());
        	
       	}
       	function set($name = null, $value = null) {
       		static $vars = array();
       		if ($name == null)
       			return $vars;	
       		if ($value == null){
       			if(array_key_exists($name, $vars)) 
       				return $vars[$name];
       		}
       		else
       			$vars[$name] = $value; 
			return null;
       	
       	}



       	/**
       	*  http error
       	*/
       	function kuri_http_error($code){

       	    $errfunc = "kuri_$code";

       	    if (function_exists($errfunc))
       				return $errfunc();
       	    else
       	        http_response_code($code);

       	}


       	//add define SITE, SITEPATH and APPPATH
       	function kuri_define(){

            if (isset($_SERVER['SERVER_PORT']) and isset($_SERVER['HTTP_HOST'])) {

                if ($_SERVER['SERVER_PORT'] == 443 OR $_SERVER['HTTPS'] == 'on')
                    $protocol = 'https://';
                else
                    $protocol = 'http://';

                define('SITE', $protocol.$_SERVER['HTTP_HOST'].'/');
                define('SITEPATH', $_SERVER['DOCUMENT_ROOT'].'/');

            }
            else {
                define('SITEPATH', $_SERVER['PWD'].'/');
            }

            define('APPPATH', SITEPATH.'app/');

            return True;

        }
       	
			
			function _kuri($currurl = null, $prefix = '_kuri', $autotype = 'html'){

                if ($currurl == null) {
                    $url = kurl();
                }
                else
                    $url = $currurl;


                if ($url !== FALSE) {
                    $params = kuri_parser($url);
                }
                else {
                    $params = kuri_argv($_SERVER['argv']);
                }


                if (!is_array($params))
                    return kuri_http_error(404);

				$result = kufind($params['items'], $params['method'], $prefix);


				if ($currurl == null) {
				    define('KURI_CNAME', $result['cname']);
                }

				if (is_array($result)) {
				
					if ($result['func'] !== '') { 
						$data =  kuloadfunc($result['func'], $result['class'], $result['args']);
						if (is_array($data)){
							header('Content-Type: application/json; charset=utf-8');
							echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
						}
						else	
							return $data;		
					}
					else
						return false;
				}
				else {
					kuri_http_error(404);
				}
				
			}


			if (!function_exists('action')) {
		        function action($url = null, $prefix = '', $autotype = 'html') {
		            return _kuri($url, $prefix, $autotype);
                }
		    }

		
		
		
		
