<?php 
namespace app;

use app\model\{DB, Model};

class App extends Model{
	/*
	* This class simplifies the creation of api for developers, provviding functions that create apps
	*/

	public $id; 

	public function __construct($id = null){
		static::$instance = new Model();
		$this->_db = new DB();
		$this->_table = static::$_tbl = 'app';
		$this->_fulltextColumn = [];
		$this->id = $id;
	}

	public function exists($app_id){
		return (count($this->findby(['app_id' => (int)$app_id])) > 0 ) ? true : false;
	}

	private function generateKey(){
		//return $key;
	}

	public function createApp($params){
		//params include credentials of the app owner including the site-domain or url;
		if (!$this->DataExists('app_key', $params['app_key']) {
			$this->insert($params);
			return (object) ['app_id' => $params['app_id'], 'app_key' => $params['app_key'] ];
		}
	}
}