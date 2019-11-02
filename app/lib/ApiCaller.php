<?php
class ApiCaller{
    
    private $_app_id;
    private $_app_key;
    private $_api_url;
     
    /**
    * @param $app_id<int> is the public unique id of this app
    * @param $app_key<string> is the private key used to secure api requests
    * @param $api_url<string> is the url of the api to process the request
    */
    public function __construct($app_id, $app_key, $api_url)
    {
        $this->_app_id = $app_id;
        $this->_app_key = $app_key;
        $this->_api_url = $api_url;
    }
     
    /**
    * @param $request_params<array> contains the controller, action/endpoint, & optional queryParams
    */
    public function sendRequest($request_params)
    {
        //encrypt the request parameters
        $enc_request = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, 
            $this->_app_key, json_encode($request_params), MCRYPT_MODE_ECB)
        );
        $params = [];
        $params['enc_request'] = $enc_request;
        $params['app_id'] = $this->_app_id;

        //initialize and setup the curl handler
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->_api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, count($params));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        $result = @json_decode(curl_exec($ch));
         
        //check if we're able to json_decode the result correctly or if there was an error in the request
        if($result == false || !isset($result['success']) || $result['success'] == false ){
            throw new Exception($result['errormsg'] ?? 'Request was not correct');
        }
        return $result['data'];
    }
}