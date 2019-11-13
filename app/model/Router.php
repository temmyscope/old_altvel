<?php
namespace app\model;

use app\Auth;

class Router{

	public static function route(){
		$url = (isset($_SERVER['PATH_INFO'])) ? explode('/', $_SERVER['PATH_INFO']) : [];
		array_shift($url);
		if(isset($url[0]) && $url[0] === 'api' && isset($url[1]) && array_key_exists($url[1], $GLOBALS['api'])){
			array_shift($url);
			$version = $url[0];
			
		    $enc_request = $_REQUEST['enc_request']; //get the encrypted request
		    $app_id = $_REQUEST['app_id']; //get the provided app id
		    $api = new Api();
		    //check first if the app id exists in the list of applications
		    if( $api->client_exists($app_id)) {
		        throw new Exception('Application does not exist!');
		    }
		    //request must contain the controller, action/endpoint, & optional queryParams
		    $params = json_decode(trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $app_key, base64_decode($enc_request), MCRYPT_MODE_ECB)));
		    
			if(array_key_exists($version, $GLOBALS['api']) && array_key_exists($params["controller"], $GLOBALS['api'][$version])){
			 	$controller = $params["controller"];
			 	array_shift($params); 
			 	$action = $params["action"] ?? $params["endpoint"];
			 	array_shift($params);
			 	if (array_key_exists($action, $GLOBALS['api'][$version]["controller"])) {
			 		$api->request($controller, $action, $params);
			 	} else {
			 		$api->error(405);
			 	}
			}else{
				$api->error(405);
			}
		}else{
			$controller = (isset($url[0])) ? ucfirst(substr_replace($url[0], '', strcspn($url[0], '.'))).'Controller' : DEFAULT_CONTROLLER;
			if(isset($url[0]) && in_array(strtolower($url[0]), $GLOBALS['controllers'][DEFAULT_CONTROLLER])){
				$controller = DEFAULT_CONTROLLER;
				$_endpoint = $url[0].'EndPoint';
			}elseif(array_key_exists($controller, $GLOBALS['controllers'][RESTRICTED])){
				$_endpoint = (isset($url[1]) && $url[1] != '' && in_array($url[1], $GLOBALS['controllers'][RESTRICTED][$controller])) ? 
					$url[1].'EndPoint' : 'indexEndPoint';
				if(!Session::exists(CURRENT_USER_SESSION_NAME) && !Cookie::exists(REMEMBER_ME_COOKIE_NAME)){
					$param = "";
					$c_url = count($url);
					if($c_url > 2){
						for($i=2; $i < $c_url; $i++){ 
							$param .= '/'.$url[$i];
						}
					}
					Session::set(REDIRECT, ''.$url[0].'/'.$url[1].$param);
					self::redirect('login');
				}
			}elseif(array_key_exists($controller, $GLOBALS['controllers'])){
				$_endpoint = (isset($url[1]) && $url[1] != '' && in_array($url[1], $GLOBALS['controllers'][$controller])) ? 
					$url[1].'EndPoint' : 'indexEndPoint';
			}else{
				$controller = 'ErrorsController';
				$_endpoint = 'indexEndPoint';
			}

			if(!Session::exists(CURRENT_USER_SESSION_NAME) && Cookie::exists(REMEMBER_ME_COOKIE_NAME)){
			    Auth::loginUserFromCookie();
			}

			if(Session::exists(CURRENT_USER_SESSION_NAME) && 
				$controller== 'AuthController' && ($_endpoint != 'indexEndPoint' && $_endpoint != 'logoutEndPoint' && $_endpoint != 'aboutEndPoint')){
				self::redirect('home');
			}
			
			array_shift($url);
			if($_endpoint !== 'indexEndPoint' && $_endpoint !== 'activateEndPoint'){
				array_shift($url);
			}

			$queryParams = Strings::sanitize($url);
			$controller = 'app\controller\\' . $controller;

			if(method_exists($controller, $_endpoint)){
				$dispatch = new $controller($controller, $_endpoint);
				call_user_func_array([$dispatch, $_endpoint], $queryParams);
			}else{
				self::redirect('errors');
			}
		}
	}
	
	public static function redirect($location){
		$location = PROOT. "{$location}";
		if(!headers_sent()){ header("location: $location"); exit();
		}else{
			echo "<script type='text/javascript'> window.location.href= '{$location}';</script>";
			echo '<noscript> <meta http-equiv="refresh" content="0;url='.$location.'"/></noscript>'; exit();
		}
	}

	public static function getRedirect(){
		if (Session::exists(REDIRECT)) {
			$route = Session::get(REDIRECT);
			Session::delete(REDIRECT);
			self::redirect($route);
		}else{
			self::redirect('home');
		}
	}
}
?>