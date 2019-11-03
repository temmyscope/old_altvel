<?php
/*----------------------------------------------------------------------------------------------|
|										LARAFELL CONSTANTS										|
|---change the database type to your choice----change the database name to your database name---|
|--------------------change the access username to your database username-----------------------|
|--------------------make sure to use a secured password on production server-------------------|
|------------------------------Set a default site title to use----------------------------------|
|------------------------------------Set a link to favicon--------------------------------------|
|	If the console was used to configure this App, all constants here will be set and secured  	|
-----------------------------------------------------------------------------------------------*/

define('DB_TYPE', 'mysql');  
define('DB_NAME', 'larafell');
define('DB_USER', 'root');
define('DB_PASSWORD', '');
define('DB_HOST', '127.0.0.1'); //may also be filename for sqlite database

define('SITE_TITLE', 'Larafell');
define('SITE_EMAIL', 'TemmyScope@protonmail.com');
define('BRAND', 'Larafell');
define('FAVICON', '');

/*----------------------------------------------------------------------------------------------|
|									LARAFELL CONSTANTS											|
|			The remember cookie expires in 30 days by this default setting 						|
|			on prodction server, change protocol to 'https' for ssl secured site				|
-----------------------------------------------------------------------------------------------*/

define('SSL_PROTOCOL', 'http://');
define('PUBLIC_FOLDER', '/Larafell');
define('PROOT', SSL_PROTOCOL.$_SERVER['HTTP_HOST'].PUBLIC_FOLDER.'/');

/*----------------------------------------------------------------------------------------------|
|							LARAFELL SESSION AND COOKIE CONSTANTS								|
|	Please make sure to replace the following for security purposes with a 32-character string 	|
|-----------------------------------------------------------------------------------------------|
|	If the console was used to configure this App, all constants here will be set and secured  	|
|-----------------------------------------------------------------------------------------------|
|-----------------Set DEBUG value to false before deploying to production server----------------|
-----------------------------------------------------------------------------------------------*/

define('CURRENT_USER_SESSION_NAME', '72qe8BSkJ19l0u5hEv6HKYyrZVQtngDLmsjcU43NRdTFPXOWIMfAoiwaGxCzpb');
define('REMEMBER_ME_COOKIE_NAME', '8vXRwq29F1dCzUkloI6fyu7pnjmOSGWhJDxHYZ');
define('REMEMBER_ME_COOKIE_EXPIRY', 2592000);
define('REDIRECT', '2lm7tyek5Vjfi4pX8FzRhIPTdGMnUsQN0YSAxb1ELDO');
define('SALT', 'kCpJaL3E8lvc72urqZHRe51oit0NQbUT');
define('DEBUG', true);

/*----------------------------------------------------------------------------------------------
|									LARAFELL PWA CONSTANTS 										|
-----------------------------------------------------------------------------------------------*/
define('FIREBASE_SERVER_API_KEY', ''); 	#For Websites using firebase to send notification
define('ICON', ''); 					//website logo image with size of about 240 * 240

/*----------------------------------------------------------------------------------------------|
|								LARAFELL NAVIGATION BAR											|
|-----------------------------------------------------------------------------------------------|
|	this helps in setting the menu bar for guest users and loggged in users based on the array 	|
|	associative arrays can be used for menus with dropdown... 									| 
-----------------------------------------------------------------------------------------------*/
define('USER_NAVBAR', ['Home' => 'home', 'Search' => 'search', 'Logout' => 'logout']);
define('GUEST_NAVBAR', ['Login' => 'login', 'Register' => 'register', 'About' => 'about']);

/*----------------------------------------------------------------------------------------------
|								LARAFELL CONTROLLERS 											|
|				This defines all the available Controllers and their Endpoints 					|
|		The RESTRICTED LAYER is the region only available or accessible to logged in users      |
|			Loggedin user is a user whose CURRENT_USER_SESSION_NAME session is set.				|
-----------------------------------------------------------------------------------------------*/
$GLOBALS['controllers'] = [
	'AuthController' => ['login', 'register', 'forgot_password', 'activate', 'about', 'logout'],
	'ErrorsController' => ['_404', '_405', 'bad', 'denied', 'unknown'],
	RESTRICTED => [
		
		/*----------------------------------------------------------------------
		| Controllers that requires login must reside in this restricted array.
		----------------------------------------------------------------------*/
		'HomeController' => [],
		'SearchController' => []
	],
];

/*----------------------------------------------------------------------------------------------
|						LARAFELL API ENDPOINTS FOR PROCESSING API REQUESTS						|
|			All pre-defined api endpoints here should start with an uppercase letter 			|
|	The /api endpoint defines Api resources, hence, no controller can be named apiController	|
-----------------------------------------------------------------------------------------------*/
$GLOBALS['api'] = [
	/*
	|-------------------------------------------------------------------------------------------------------|
	|Register all api accessible/consumable api endpoints available in your app just like with controller 	|
	|-------------------------------------------------------------------------------------------------------|
	*/

	/*
	|-------------------------------------------------------------------------------------------------------|
	| define api version first, apis accessible under each version, and endpoints accessible under each api | 
	|-------------------------------------------------------------------------------------------------------|
	*/
];

$GLOBALS['services'] = [
	
	/*
	|-------------------------------------------------------------------------------------|
	|Register all api services yyour application makes use of in the form of: name => url |
	|-------------------------------------------------------------------------------------|
	*/
	
];

/*----------------------------------------------------------------------------------------------
|						LARAFELL FILE UPLOAD CONFIGURATIONS 									|
|			These are necessary settings for the framework's file upload system 				|
|					Values can be changed to suit development's demand 							|
-----------------------------------------------------------------------------------------------*/
define('FILE_UPLOAD_CDN', ROOT.DS.'cdn'.DS);	//the folder to upload file to
define('FILE_UPLOAD_LIMIT', 1024768); 			//default value is equivalent to 1mb

/*-----------------------------------------------------------------------------|
| This array contains the developer's set acceptable file types and mime types |
| Developer can add as many acceptable file types and mime types 			   |
------------------------------------------------------------------------------*/
define('FILE_ALLOWED_TYPES', 
	[ 
		'jpg' => 'image/jpeg', 
		'png' => 'image/png', 
		'jpeg' => 'image/jpeg'
	]
);