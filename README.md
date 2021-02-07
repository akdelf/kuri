# _kuri



## Installation

It's recommended that you use [Composer](https://getcomposer.org/) to install Kuri.

```bash
$ composer require masterforweb/kuri
```

add postfix kuri for your function

http://{your function}/param1/param2

kuri_ [prefix system functions and plugins]

_kuri [postfix user functions]


PHP >= 5.2.0

1. Autorouting without regular. MVC standart role: controller/action/params
2. Minimum code - maximum speed 


RETURN:
* shema (http, https)
* domain (default _$_SERVER['QUERY_STRING']_)
* method (GET, POST ...)
* permanent MVC rule: control/action/params

[Auto Search function]

 - CLASSIC: find class $control and method $action
 - REST: find class $control and method (get, post)
 - SMALL APP: find function $control_$action
 - SHORT FUNC: find function $control 


## Hello World

```php

require 'vendor/autoload.php';
$app = new kURI();
$app->action();

function index() {
	echo 'Hello World! Is index page';	
}
```

[EXAMPLE MVC CLASS]
path: domain.my/news/id/$id

require 'vendor/akdelf/kuri/kuri.php';
$app = new kURI();


class news_kuri {
	
	function id($id){
		echo 'ID ='.$id;
	}	

}


[EXAMPLE AUTO RESTfull CLASS]

require 'vendor/akdelf/kuri/kuri.php';
$result = action();

class news_kuri {
	
	function get($id){
		echo 'ID ='.$id;
	}

	function post($title, $text) {
		$sql = "INSERT INTO `news` (`title`, `name`) VALUES($title, $text);";

	}


}
