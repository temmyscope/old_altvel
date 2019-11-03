<?php
namespace app\model;

class Cookie{
	
	public static function exists($name){
		return (isset($_COOKIE[$name])) ? true : false ;
	}

	public static function get($name){
		return $_COOKIE[$name];
	}

	public static function set($name, $value){
		if(setcookie($name, $value, time()+REMEMBER_ME_COOKIE_EXPIRY, '/')){
			return true;
		}
		return false;
	}

	public static function delete($name){
		self::set($name, '', time()-3600);
	}
}