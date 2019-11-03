<?php
namespace app\model;

class Controller extends Application{
	protected $_controller, $_action, $request;

	public function __construct($controller, $action){
		parent::__construct();
		$this->_controller= $controller;
		$this->_action= $action;
		$this->request = new Request();
	}
}