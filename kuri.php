<?php 
		/**
		* kURI: return scheme, host, path, controller, action, items, http method 
		* @license http://www.opensource.org/licenses/mit-license.html  MIT License
		* @author АК Delfin <masterforweb@hotmail.com>
		*/
		
		function kuparser($uri = '') {
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
		
		/**
		* current url (k - current url)
		*/
		function kuri() {
			
			
			if (isset($_SERVER['REQUEST_URI']) and $_SERVER['REQUEST_URI'] !== '')
				$uri = $_SERVER['REQUEST_URI'];
			elseif(isset($_SERVER['PATH_INFO']) and $_SERVER['PATH_INFO'] !== '')
				$uri = $_SERVER['PATH_INFO'];

			
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


			if ($control = kuload($cname)){ //autoload class
				
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
			
			define('KURI_CNAME', $cname);
			
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
					
			return array('class'=>False, 'func'=>$func, 'args'=>$args);	
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
		
		
		function kufindfunc($items = array(), $method = "get") {
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
				return $this->er404();
					
			return array('class'=>False, 'func'=>$func, 'args'=>$arguments);	
					
		}
		
		function kuloadfunc($func, $class = False, $args = array()) {

			if ($class == False) {
				if (is_array($args) and sizeof($args) > 0)
					return call_user_func_array($func, $args);
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
		

		if (!function_exists('action')) {
			
			function action($url = null, $prefix = '', $autotype = 'html'){
			
				if ($url == null)
					$url = kuri();
				$params = kuparser($url);
				
				$result = kufind($params['items'], $params['method'], $prefix);

				if (is_array($result)) {
				
					if ($result['func'] !== '') { 
						$data =  kuloadfunc($result['func'], $result['class'], $result['args']);
						if ($autotype == 'json' AND is_array($data)){
							header("Content-type:application/json");
							echo json_encode($data);
						}
						else	
							return $data;		
					}
					else
						return false;
				}
				else {
					if (!function_exists('err404')) {
						echo '404 no find page';
					}
					else
						return call_user_func('err404');	
				}
				
			}	
		}		
		
		
		
		
