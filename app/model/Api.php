<?php
namespace  app\model;

use app\model\{Application, Strings};
use Closure;

class Api extends Application{
	protected $_controller, $_action;
	public const BAD_REQUEST = "Bad Request";
	//error codes ['ok' => 200, '...' => 404, '...' => 404, '...' => 405, ]
	private $status = [
		200 => 'OK',
		201,
	    404 => 'EndPoint Not Found',   
	    405 => 'Method Not Allowed',
	    422,
	    500 => 'Internal Server Error',
	];

	public function __construct(){
		parent::__construct();
	}

  	public function client_exists($app_id){
  		$this->load_model('App');
	  	return ($this->AppModel->exists(Strings::sanitize($app_id)) ) ?  true: false;
  	}

  	public function Request($controller, $action, $params){
  		$_controller = 'app\api\\' . $controller;
  		if(method_exists($_controller, $action)){
			$dispatch = new $_controller();
			call_user_func_array([$dispatch, $action], Strings::sanitize($params));
		}else{
			$this->error(405);
		}
  	}

    public function error($code $error_msg= null){
  		$this->jsonResponse($code,  function($error_msg){
  			return (!is_null($error_msg)) ? $error_msg : $this->status[$code]
  		});
  	}
}