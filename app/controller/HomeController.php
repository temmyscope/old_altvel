<?php
namespace app\controller;

use app\model\{Controller, Request, Strings};

class HomeController extends Controller{

	public function __construct($controller, $action){
		parent::__construct($controller, $action);
	}
	
	public function IndexEndPoint(){
		view('home.index');
	}	
}