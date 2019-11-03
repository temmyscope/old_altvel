<?php
namespace app\model;

use \SplFixedArray;

class Request{
	
	public const OBJECT_MODE = 2;
	public const ARRAY_MODE = 1;
	public static $_file, $_instance;
	public $_files = [];
	private $_passed = false, $_errors, $_success, $_warnings= [];

	function __construct(){
		$this->_db = new DB();
	}

	public static function isValid(): bool{
		return (!is_null(self::post('csrf')) && (string)self::post('csrf') === (string)Session::get('csrf'))
		 ? true : false;
	}

	public static function isSecured(){
		return (self::isNotEmpty() && self::isValid()) ? true : false;
	}

	public static function get($var = ''){
	if(!empty($var)){
		return (isset($_GET[$var])) ? Strings::sanitize($_GET[$var]) : null;	
	}else{
		return (!empty($_GET)) ? (object) Strings::sanitize($_GET) : null;
	}
}

	public static function post($var = ''){
		if(!empty($var)){
			return (isset($_POST[$var])) ? Strings::sanitize($_POST[$var]) : null;	
		}else{
			return (!empty($_POST)) ? (object) Strings::sanitize($_POST) : null;
		}
	}

	public function status($type, $msg){
		if('success' === $type) {
			$this->_success[] = $msg;
			Session::set('success', $this->_success);
			return true;
		} elseif('error' === $type) {
			$this->_errors[] = $msg;
			Session::set('errors', $this->_errors);
			return true;
		}elseif ('warning' === $type) {
			$this->_warnings[] = $msg;
			Session::set('warnings', $this->_warnings);
			return true;
		}else{
			$this->_success[] = $msg;
			Session::set('success', $this->_success);
		}
		
	}

	public function passed(){
		return $this->_passed;
	}

	public function validate($items= [], $table=''){
		$this->_errors = [];
		foreach($items as $item => $rules) {
			$display = $rules['display'];
			(array)$source = Request::request();
			foreach ($rules as $rule => $rule_value) {
				$value = $source[$item];
				if ($rule === 'required' && empty($value)) {
					$this->status('error', ["{$display} is required", $item]);
				}elseif(!empty($value)){
					switch((string) $rule){
						case 'min':
							if (strlen($value) < $rule_value) {
								$this->status('error', ["{$display} must be a minimum of {$rule_value} characters.", $item]);
							}
							break;
						case 'max':
							if (strlen($value) > $rule_value) {
								$this->status('error', ["{$display} must be a maximum of {$rule_value} characters.", $item]);
							}
							break;
						case 'len':
							if (strlen($value) !== $rule_value) {
								$this->status('error', ["{$display} must be exactly {$rule_value} characters.", $item]);
							}
							break;
						case 'matches':
							if ($value != $source[$rule_value]){
								$matchDisplay = $items[$rule_value]['display'];
								$this->status('error', ["{$matchDisplay} and {$display} must match.", $item]);
							}
							break;
						case 'unique':
							if (!empty($table)){
								$check = $this->_db->findBy($table, [$item => $value]);
								if (!empty($check)) {
									$this->status('error', ["{$display} already exists. Please choose another {$display}", $item]);
								}
							}else{
								die("\$table is empty. The 'Unique' Validator requires a table to check.");
							}
							break;
						case 'is_numeric':
							if (!is_numeric($value)) {
								$this->status('error', ["{$display} has to be a number. Please use a numeric value.", $item]);
							}
							break;
						case 'valid_email':
							if(!filter_var($value, FILTER_VALIDATE_EMAIL)){
								$this->status('error', ["{$display} must be a valid email address.", $item]);
							}
							break;
						case 'alpha':
							if (!ctype_alpha($value)){
								$this->status('error', ["{$display} can only be alphabeths", $item]);
							}
							break;
						case 'alpha_num':
							if (!ctype_alnum($value)){
								$this->status('error', ["{$display} can only be alphabeths and or numbers.", $item]);
							}
							break;
						case 'is_one_of': 
							if(is_array($rule_value) && (!in_array($value, $rule_value)) ){
								$this->status('error', ["{$display} can only be one of the given options", $item]);
							}
							break;
						case 'equals':
							if( $value !== $rule_value['value'] ){
								$this->status('error', ["{$display} must be the same value as {$rule_value['display']}", $item]);
							}
							break;
						case 'is_same_as':
							if( $value !== $source[$rule_value] ){
								$this->status('error', ["{$display} must be the same value as {$rule_value}", $item]);
							}
							break;
						case 'is_file':
							if (!is_file($value)($value)){
								$this->status('error', ["{$display} must be a valid file type", $item]);
							}
							break;
					}
				}
			}
		}
		if(empty($this->_errors)){
			$this->_passed = true;
		}
		return $this;
	}

	public static function file($var){
		return (isset($_FILES[$var]) && !empty($_FILES[$var])) ? $_FILES[$var] : null;
	}

	public static function request($var = ''){
		if(!empty($var)){
			return (isset($_REQUEST[$var]) && !empty($_REQUEST[$var])) ? Strings::sanitize($_REQUEST[$var]) : null;
		}
		return (!empty($_REQUEST)) ? Strings::sanitize($_REQUEST) : null;
	}

	public static function has($var){
		return (isset($_REQUEST[$var]) && !empty($_REQUEST[$var])) ? true : false;
	}

	/*---------------------------------------------------------------------
		Recieves no parameter
		@return an array containing all the addresses of the uploaded files
	----------------------------------------------------------------------*/
	public static function uploadAll(): array{
		$list = [];
		foreach($_FILES as $key => $value){
			array_push($list, self::Upload($_FILES[$key]));
		}
		return splFixedArray::fromArray($list);
	}

	public static function Upload($file){
		$name = basename($file['name']);
		$nm  = Strings::UniqueName( $name );
	    $tar = local_cdn().$nm;
	    $FileType = pathinfo($name, PATHINFO_EXTENSION);
	    $target = $tar.'.'.$FileType;
	    $uploadOk = 1;
	    /*---------------------------------------------------------------------------------------
	    | check file size,																		|
	    | check if filetype is allowed 															|
	    | check if no other errors exist 														|
	    ---------------------------------------------------------------------------------------*/
	    $request = new Request();
	    if($file["size"] > FILE_UPLOAD_LIMIT){
	    	$size_in_mb = FILE_UPLOAD_LIMIT/1048576;
	        $request->status('warning', "The File upload size limit is {$size_in_mb} Mb");
		    $uploadOk = 0;
	    }
	    if($uploadOk !== 0){
	        if(move_uploaded_file($file["tmp_name"], $target)){
	        	$mimeType = mime_content_type($target);
	        	if(!array_key_exists($FileType, FILE_ALLOWED_TYPES) || FILE_ALLOWED_TYPES[$FileType] != $mimeType){
			    	$allowed = implode(', ', FILE_ALLOWED_TYPES);
			    	$request->status('warning', "Oops!! only {$allowed} formats are allowed");
			    	return null;
				}else{
	            	return app_url().'cdn/'.$nm.'.'.$FileType;
				}
	        }else{
	        	$request->status('error', 'An unknown error occurred. Try again later.');
	        	return null;
	        }
	    }else{ return null; }
	}

	public static function hasFile($var): bool{
		return (isset($_FILES[$var]) && !empty($_FILES[$var])) ? true : false;
	}

	public static function hasFiles(): bool{
		return (isset($_FILES) && !empty($_FILES)) ? true : false;
	}	

	public static function isEmpty(): bool{
		return (empty($_GET) && empty($_POST)) ? true : false ;
	}

	public static function isNotEmpty(): bool{
		return (!empty($_REQUEST)) ? true : false ;
	}

	public static function destroy(): bool{
		$_GET = $_POST = $_REQUEST = $_FILES = [];
		return true;
	}

	public static function sanitize($var){
		return Strings::sanitize($var);
	}
}