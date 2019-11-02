<?php
function api()
{
	$services = (object) $GLOBALS['services'];
	return $services;
}

function using($header)
{
	return (new app\lib\Curl())->isReturnable()->setHeader($header);
}

function resume(){
	return app\model\Router::getRedirect();
}

function redirect($var){
	return app\model\Router::redirect($var);
}

function status(){
	if(app\model\Session::exists('errors')){
	    $html = "<div><ul class='alert alert-danger'>";
	    $errors = app\model\Session::get('errors');
	    foreach($errors as $error){
	    	if(is_array($error)){
	    		$html .= '<li style="list-style: none;text-align: center; color: white;">'.$error[0].'</li><br/>';	
	    	}else{
	    		$html .= '<li style="list-style: none;text-align: center; color: white;">'.$error.'</li><br/>';	
	    	}
	    }
	    $html .= '</ul></div>';
	    app\model\Session::delete('errors');
	    return $html;	
	}
	if(app\model\Session::exists('warnings')){
		$html = "<div><ul class='alert alert-warning'>";
	    $errors = app\model\Session::get('warnings');
	    foreach($errors as $error) {
	    	if (is_array($error)) {
	    		$html .= '<li style="list-style: none;text-align: center; color: white;">'.$error[0].'</li><br/>';	
	    	}else{
	    		$html .= '<li style="list-style: none;text-align: center; color: white;">'.$error.'</li><br/>';	
	    	}
	    }
	    $html .= '</ul></div>';
	    app\model\Session::delete('warnings');
	    return $html;
	}
	if(app\model\Session::exists('success')){
		$html = "<div><ul class='alert alert-success'>";
	    $errors = app\model\Session::get('success');
	    foreach($errors as $error){
	    	if(is_array($error)){
	    		$html .= '<li style="list-style: none;text-align: center; color: white;">'.$error[0].'</li><br/>';	
	    	}else{
	    		$html .= '<li style="list-style: none;text-align: center; color: white;">'.$error.'</li><br/>';	
	    	}
	    }
	    $html .= '</ul></div>';
	    app\model\Session::delete('success');
	    return $html;
	}
}

function errors(){
	if(app\model\Session::exists('errors')){
	    $html = "<div><ul class='alert alert-danger'>";
	    $errors = app\model\Session::get('_errors');
	    foreach($errors as $error){
	    	if(is_array($error)){
	    		$html .= '<li style="list-style: none;text-align: center; color: white;">'.$error[0].'</li><br/>';	
	    	}else{
	    		$html .= '<li style="list-style: none;text-align: center; color: white;">'.$error.'</li><br/>';	
	    	}
	    }
	    $html .= '</ul></div>';
	    app\model\Session::delete('errors');
	    return $html;	
	}
}


/**
*	@param formats may vary e.g. controllerName@endpoint; controllerName.endpoint; controllerName/endpoint; 
*/
function route($var): string{
	$var = str_replace('@', '/', $var);
	$var = str_replace('.', '/', $var);
	$var = str_ireplace('controller', '', $var);
	return app_url().$var.'/';
}

function app_url(): string{
	return PROOT;
}

function app_path(): string{
	return PROOT;
}

function local_cdn(): string{
	if(is_dir(FILE_UPLOAD_CDN)){
	}else{
		mkdir(FILE_UPLOAD_CDN);
	}
	return FILE_UPLOAD_CDN;
}

function salt(){
	return SALT;
}

function view_exists($viewString){
	return file_exists(ROOT.DS.'app'.DS.'view'.DS.$viewString.'.blade.php');
}

function view(...$var){
	return (empty($var)) ? new app\model\View() : (new app\model\View())->render($var[0], $var[1] ?? '');
}

function get($var = ''){
	if(!empty($var)){
		return (isset($_GET[$var])) ? app\model\Strings::sanitize($_GET[$var]) : null;	
	}else{
		return (!empty($_GET)) ? (object) app\model\Strings::sanitize($_GET) : null;
	}
}

function post($var = ''){
	if(!empty($var)){
		return (isset($_POST[$var])) ? app\model\Strings::sanitize($_POST[$var]) : null;	
	}else{
		return (!empty($_POST)) ? (object) app\model\Strings::sanitize($_POST) : null;
	}
}

function request($var = ''){
	if(!empty($var)){
		return (isset($_REQUEST[$var]) && !empty($_REQUEST[$var])) ? app\model\Strings::sanitize($_REQUEST[$var]) : null;
	}
	return (!empty($_REQUEST)) ? app\model\Strings::sanitize($_REQUEST) : null;
}

function is_restricted($route){
	$route = explode('/', $route);
	$controller = ucfirst($route[0]).'Controller';
	$endpoint = $route[1];
	if(array_key_exists($controller, $GLOBALS['controllers'][RESTRICTED]) && 
		in_array($endpoint, $GLOBALS['controllers'][RESTRICTED][$controller])){
		return true;
	}else{
		return false;
	}	
}


function dnd($var){
	echo "<pre>";
		var_dump($var);
	echo "<pre>";
	die();
}

/*--------------------------------------------------------------
|	$arr1 = [
|			'function'=> 'strpos',
|			'parameters'=> ['home.php', '.']
|	]; 
	$arr2 = [
|			'function'=> 'strstr',
|			'parameters'=> ['home.php', '.']
|	];
|	speed_cmp($arr1, $arr2);
----------------------------------------------------------------*/
function speed_cmp(...$args){
	if(count($args) > 1){
		foreach($args as $key => $value){
			$time_start= microtime(true);
			$mem_start = memory_get_usage(true);
			for ($i=0; $i <= 10000; $i++) { 
				call_user_func_array($args[$key]['function'], $args[$key]['parameters']);
			}
			$mem_end = memory_get_usage(true);
			$time_end= microtime(true);
			$time_elapsed= $time_end - $time_start;
			$memory_used = $mem_end - $mem_start;
			echo "<pre>";
			echo "Time elapsed for testcase <b>{$key}</b> is {$time_elapsed}";
			echo "Memory used for testcase <b>{$key}</b> is {$memory_used}";
			echo "<pre>";
		}
	}else{
		throw new Exception("Testcases must be atleast 2", 1);
	}
}