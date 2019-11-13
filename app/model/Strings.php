<?php
namespace app\model;

use \DateTime;
use \DateTimeZone;

class Strings{

	/*--------------------------------------------------------------------------|
	|	This is a string manipulation class that handles all manners of string	|
	|	conversion,formatting,validation,generation,sanitization and encrytion	|
	---------------------------------------------------------------------------*/

	const ENCODE_STYLE_HTML = 0;
	const ENCODE_STYLE_JAVASCRIPT = 1;
	const ENCODE_STYLE_CSS = 2;
	const ENCODE_STYLE_URL = 3;
	const ENCODE_STYLE_URL_SPECIAL = 4;
	const DEFAULT_SALT = SALT;
	private static $salt = SALT;
	private static $URL_UNRESERVED_CHARS ='ABCDEFGHIJKLMNOPQRSTUVWXYZabcedfghijklmnopqrstuvwxyz-_.~';

	final public static function EncryptText($var): string{
		return hash('MD5', crypt($var, self::$salt));
	}

	final public static function isValidUrl($var): bool{
		return filter_var($var, FILTER_VALIDATE_URL) ? true : false;
	}

	final public static function isUrl($var): bool{
		return self::isValidUrl($var);
	}

	final public static function isEmail($var): bool{
		return filter_var($var, FILTER_VALIDATE_EMAIL) ? true : false;
	}

	final public static function isAlphaNum($var): bool{
		return ctype_alnum($var);
	}

	final public static function isAlphaNumeric($var): bool{
		return self::isAlphaNum($var);
	}

	final public static function toUTF8($str): bool{
		return (!mb_check_encoding($str, 'UTF-8')) ? mb_convert_encoding($str, 'UTF-8', mb_detect_encoding($str)) : $str;
	}

	final public function rand(){
		return substr(str_shuffle('qwertyuiopasdfghjklzxcvbnm1234567890QWERTYUIOPZXCVBNMLKJHGFDSA'), 0, 32);
	}

	final public static function Encrypt($var): string{
		return password_hash($var, PASSWORD_DEFAULT);
	}

	final public static function en_mcrypt($data){
		return base64_encode(openssl_encrypt($data, 'AES-256-CFB', salt()));
	}

	final public static function de_mcrypt($encrypted){
		return openssl_decrypt(base64_decode($encrypted), 'AES-256-CFB', salt());
	}

	final public static function Verify($var, $hash): bool{
		return password_verify($var, $hash);
	}

	final static public function Random(){
		$chars = 'qwertyuiopasdfghjklzxcvbnm1234567890QWERTYUIOPZXCVBNMLKJHGFDSA';
		return substr(str_shuffle(($chars)), 0, random_int(32, 64));
	}

	final public static function generateToken(){
    	return base64_encode(openssl_random_pseudo_bytes(32));
    }

    final public static function CleanUp($dirty){
		return htmlentities(strip_tags(stripslashes($dirty)));
  	}

  	final static public function Hash($str){
		return hash('SHA256', $str);
	}

	final public static function sanitize($dirty){
    	$clean_input = [];
        if(is_array($dirty)){
            foreach ($dirty as $k => $v) {
                $clean_input[$k] = htmlentities($v, ENT_QUOTES, 'UTF-8');
            }
        } else {
            $clean_input = htmlentities($dirty, ENT_QUOTES, 'UTF-8');
        }
        return $clean_input;
  	}

	final static public function Secured(){ //generates random values, hence can not be used for password
		$str = bin2hex(random_bytes(random_int(16, 48)));
		return hash('SHA256', $str);
	}

	final static public function UniqueName($str = ''){
		return hash('MD5', uniqid().$str).hash('MD5', microtime(true));
	}

	final static public function HashWithSalt($salt){ //can't be used to hash passwords
		$str = bin2hex(random_bytes(random_int(16, 48)));
		return hash('MD5', $str).hash('MD5', $salt); // return 64 character length string
	}

	final static public function Linkify(string $str): string{
		$url = '@(http)?(s)?(://)?(([a-zA-Z])([-\w]+\.)+([^\s\.]+[^\s]*)+[^,.\s])@';
		$string = preg_replace($url, "<a href='http$2://$4' target='_blank' rel='noopener' title='$0'>$0</a>", $str);
		return $string;
	}

	final static public function removeHTML($str){
		$string = preg_replace ('/<[^>]*>/', ' ', $str);     
	    return $string; 
	}

	final static public function val2string($count){
		if($count > 1000){
			$count = round($count/1000, 1);
			return "over " .$count."K";
		}elseif($count > 1000000){
			$count= round($count/1000000, 2);
			return "over ". $count."M";
		}else{
			return $count;
		}
	}

	public static function JsTimeToPhp($str){ //formats a javascript date string to php date string
		$str= substr_replace($str, '', strcspn($str, '('));
		return $str;
	}

	public static function strToTime($str){
		return strtotime($str);
	}

	public static function generateTime(): string{
		$var = new DateTime('now', new DateTimeZone('UTC'));
		return $var->format('Y-m-d H:i:s');
	}

	public static function Now($timezone): string{
		$var = new DateTime('now', new DateTimeZone($timezone));
		return $var->format('Y-m-d H:i:s');
	}

	public static function ReadableTime($time): string{
		$current_time = time();
		if (!is_int($time))
			$time = strtotime($time);	

		if($current_time < ( $time + 60)){
			// the update was in the past minute
			return "just now"; //"less than a minute ago";
		}elseif( $current_time < ( $time + 120 ) ){
			// it was less than 2 minutes ago, more than 1, but we don't want to say 1 minute ago do we?
			return "just over a minute ago";
		}elseif( $current_time < ( $time + ( 60*60 ) ) ){
			// it was less than 60 minutes ago: so say X minutes ago
			return round( ( $current_time - $time ) / 60 ) . " minutes ago";
		}elseif( $current_time < ( $time + ( 60*120 ) ) ){
			// it was more than 1 hour
			return "just over an hour ago";
		}elseif( $current_time < ( $time + ( 60*60*24 ) ) ){
			//it was in the last day:X hours
			return round( ( $current_time - $time ) / (60*60) ) . " hours ago";
		}elseif( $current_time > ( $time + ( 60*60*24) ) && $current_time < ( $time + ( 60*60*24*2) )){
			//it was in the last month: X days
			return " about a day ago";
		}elseif( $current_time < ( $time + ( 60*60*24*28 ) )){
			//it was in the last month: X days
			return round( ( $current_time - $time ) / (60*60*24) ) . " days ago";
		}elseif ($current_time > ( $time + ( 60*60*24*56 ) ) && $current_time < ( $time + ( 60*60*24*365 ) )) {
			//over 2 months at least
			return 'about '.round( ( $current_time - $time ) / (60*60*24*28) ) . " months ago";
		}else{
			// longer than a day ago: give up, and display the date
			return "" . date('jS \o\f M, Y', $time);
		}

	}
	
	public function encodeForHTML($value){
		$value = str_replace('&', '&amp;', $value);
		$value = str_replace('<', '&lt;', $value);
		$value = str_replace('>', '&gt;', $value);
		$value = str_replace('"', '&quot;', $value);
		$value = str_replace('\'', '&#x27;', $value); // &apos; is not recommended
		$value = str_replace('/', '&#x2F;', $value); // forward slash can help end HTML entity
		return $value;
	}

	public function encodeForHTMLAttribute($value){
		return $this->_encodeString($value);
	}

	public function encodeForJavascript($value){
		return $this->_encodeString($value, self::ENCODE_STYLE_JAVASCRIPT);
	}

	public function encodeForURL($value){
		return $this->_encodeString($value, self::ENCODE_STYLE_URL_SPECIAL);
	}

	public function encodeForCSS($value){
		return $this->_encodeString($value, self::ENCODE_STYLE_CSS);
	}
	
	/**
	* Encodes any special characters in the path portion of the URL. Does not
	* modify the forward slash used to denote directories. If your directory
	* names contain slashes (rare), use the plain urlencode on each directory
	* component and then join them together with a forward slash.
	*
	* Based on http://en.wikipedia.org/wiki/Percent-encoding and http://tools.ietf.org/html/rfc3986
	*/
	public function encodeURLPath($value){
		$length = mb_strlen($value);
		if ($length == 0) {
			return $value;
		}
		$output = '';
		for ($i = 0; $i < $length; $i++) {
			$char = mb_substr($value, $i, 1);
			if ($char == '/') {
			// Slashes are allowed in paths.
			$output .= $char;
			}else if (mb_strpos(self::$URL_UNRESERVED_CHARS, $char) == false) {
				// It's not in the unreserved list so it needs to be encoded.
				$output .= $this->_encodeCharacter($char, self::ENCODE_STYLE_URL);
			}else {
				// It's in the unreserved list so let it through.
				$output .= $char;
			}
		}
		return $output;
	}

	private function _encodeString($value, $style = self::ENCODE_STYLE_HTML){
		if (mb_strlen($value) == 0) {
			return $value;
		}
		$characters = preg_split('/(?<!^)(?!$)/u', $value);
		$output = '';
		foreach ($characters as $c) {
			$output .= $this->_encodeCharacter($c, $style);
		}
		return $output;
	}

	private function _encodeCharacter($c, $style = self::ENCODE_STYLE_HTML){
		if (ctype_alnum($c)){
			return $c;
		}
		if(($style === self::ENCODE_STYLE_URL_SPECIAL) && ($c == '/' || $c == ':')) {
			return $c;
		}
		$charCode = $this->_unicodeOrdinal($c);
		$prefixes = array(
			self::ENCODE_STYLE_HTML => array('&#x', '&#x'),
			self::ENCODE_STYLE_JAVASCRIPT => array('\\x', '\\u'),
			self::ENCODE_STYLE_CSS => array('\\', '\\'),
			self::ENCODE_STYLE_URL => array('%', '%'),
			self::ENCODE_STYLE_URL_SPECIAL => array('%', '%'),
		);
		$suffixes = array(
			self::ENCODE_STYLE_HTML => ';',
			self::ENCODE_STYLE_JAVASCRIPT => '',
			self::ENCODE_STYLE_CSS => '',
			self::ENCODE_STYLE_URL => '',
			self::ENCODE_STYLE_URL_SPECIAL => '',
		);
		// if ASCII, encode with \\xHH
		if ($charCode < 256) {
			$prefix = $prefixes[$style][0];
			$suffix = $suffixes[$style];
			return $prefix . str_pad(strtoupper(dechex($charCode)), 2, '0') . $suffix;
		}
		// otherwise encode with \\uHHHH
		$prefix = $prefixes[$style][1];
		$suffix = $suffixes[$style];
		return $prefix . str_pad(strtoupper(dechex($charCode)), 4, '0') . $suffix;
	}

	private function _unicodeOrdinal($u){
		$c = mb_convert_encoding($u, 'UCS-2LE', 'UTF-8');
		$c1 = ord(substr($c, 0, 1));
		$c2 = ord(substr($c, 1, 1));
		return $c2 * 256 + $c1;
	}
}