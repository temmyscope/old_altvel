<?php
namespace app\lib;

class Curl{
	private $_curl, $_request, $_result, $_errors;

	function __construct($url=''){
		if(!extension_loaded("curl")){
			die("cURL extension not loaded! Quit Now.");
		}
		$this->_curl = (filter_var($url, FILTER_SANITIZE_URL)) ? curl_init($url) : curl_init();		
	}

	public function setUrl($url){
		$url = filter_var($url, FILTER_SANITIZE_URL);
		curl_setopt($this->_curl, CURLOPT_URL, $url);
		return $this;
	}

	public function addUrl($url){
		return $this->setUrl($url);	
	}

	public function setData(array $postdata){
		curl_setopt($this->_curl, CURLOPT_POSTFIELDS, json_encode($postdata));
		return $this;
	}

	public function addData(array $var){
		return $this->setData($var);
	}

	public function setSession($cookiefile = ""){
		curl_setopt($this->_curl, CURLOPT_COOKIEFILE, $cookiefile);
		return $this;
	}

	public function saveSession($cookiefile){
		curl_setopt($this->_curl, CURLOPT_COOKIEJAR, $cookiefile);
		return $this;
	}

	public function setCookie($cookiefile = ""){
		return $this->setSession($cookiefile);
	}

	public function saveCookie($cookiefile){
		return $this->saveSession($cookiefile);
	}

	public function setRequestFormat(string $method){
		curl_setopt($this->_curl, CURLOPT_CUSTOMREQUEST, $method);
		return $this;
	}

	public function isReturnable(){
		curl_setopt($this->_curl, CURLOPT_RETURNTRANSFER, true);
		return $this;
	}

	public function isGet(){
		curl_setopt($this->_curl, CURLOPT_HTTPGET, true);
		return $this;
	}

	public function isPost(){
		curl_setopt($this->_curl, CURLOPT_POST, true);
		return $this;
	}

	public function isPut(){
		curl_setopt($this->_curl, CURLOPT_CUSTOMREQUEST, 'PUT');
		return $this;
	}

	public function isDelete(){
		curl_setopt($this->_curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
		return $this;
	}

	public function isJson(array $var){
    	curl_setopt($this->_curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    	return $this;
	}	

	public function isXml(array $var){
		curl_setopt($this->_curl, CURLOPT_HTTPHEADER, array('Content-Type: application/xml'));
		return $this;
	}

	public function setTimeOut($time = 0){
		$time = ($time>0) ? $time : 200;
		curl_setopt($this->_curl, CURLOPT_CONNECTTIMEOUT, $time);
		curl_setopt($this->_curl, CURLOPT_TIMEOUT, $time);
		return $this;
	}

	public function TimeOut($time = 0){
		return $this->setTimeOut($time);
	}

	public function setHeader($headers){
		curl_setopt($this->_curl, CURLOPT_HTTPHEADER, $headers);
		return $this;
	}
	public function send($arg){
		return $this->addData($arg);
	}

	public function to($registered_service_name){
		$this->setUrl(api()->$registered_service_name)->isPost()->exec();
		return $this;
	}


	public function exec(){
		$this->_result = curl_exec($this->_curl);
		$this->_errors = curl_error($this->_curl);
		curl_close($this->_curl);
		return $this;
	}
}