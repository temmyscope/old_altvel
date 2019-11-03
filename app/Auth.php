<?php
namespace app;

use app\model\{
	DB, Model, Cookie, Session, Strings
};

class Auth extends Model{ 
	public $id;

	public function __construct($user = ''){
		static::$instance = new Model();
		$this->_db = new DB();
		$this->_table = static::$_tbl = 'users';
		$this->_fulltextColumn = [];
		if($user != '' and is_int($user)){
			$this->id = $user; 
		}
	}

	public function FindByEmail($email){
		return $this->findFirst(['email' => $email]);
	}

	public static function thisUser(){
		if(Session::exists(CURRENT_USER_SESSION_NAME)){
			$u = new Auth(Session::get(CURRENT_USER_SESSION_NAME));
			return $u;
		}
	}

	public function login($rememberMe){
		Session::set(CURRENT_USER_SESSION_NAME, $this->id);
		if($rememberMe){
			$hash = Strings::secured(); 
			$user_agent = Session::uagent_no_version();
			Cookie::set(REMEMBER_ME_COOKIE_NAME, $hash);
			$fields = ['session' => $hash, 'user_agent' => $user_agent, 'user_id' => $this->id];
			$this->_db->softDeleteBY('user_sessions', ['user_id' => $this->id, 'user_agent' => $user_agent]);
			$this->_db->save('user_sessions', $fields);
		}
	}

	public static function loginUserFromCookie(){
		$userSession = (new Auth)->getFromCookie();
		if(isset($userSession->user_id) && (int)$userSession->user_id > 0){
			$user = new self((int) $userSession->user_id);
			$user->login(true);
		}
	}
 
	public function logout(){
	    Session::delete(CURRENT_USER_SESSION_NAME);
	    Session::destroy();
	    if(Cookie::exists(REMEMBER_ME_COOKIE_NAME)){
	    	Cookie::delete(REMEMBER_ME_COOKIE_NAME);
	    	$this->del();
	    }
	    return true;
  	}

  	public function getFromCookie(){
		if(COOKIE::exists(REMEMBER_ME_COOKIE_NAME)){
			return (object) $data = $this->_db->findBy('user_sessions', [
			 	'user_agent' => Session::uagent_no_version(),
			 	'session' => COOKIE::get(REMEMBER_ME_COOKIE_NAME)
			])[0];
		}
	}

	public function del(){
		(object)$data = $this->_db->findBy('user_sessions', [
			 'user_agent' => Session::uagent_no_version(),
			 'session' => COOKIE::get(REMEMBER_ME_COOKIE_NAME)
		])[0];
		return $this->_db->delete('user_sessions', $data->id);
	}
} 