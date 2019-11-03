<?php
/*
|-------------------------------------------------------------------------------|
|LaraFell by Elisha Temiloluwa a.k.a TemmyScope ia Inspired By Phalcon & Laravel|
|-------------------------------------------------------------------------------|
*/

/*
|-------------------------------------------------------------------------------|
|							LARAFELL FRAMEWORK CONSTANTS						|
--------------------------------------------------------------------------------|
*/
define('DS', DIRECTORY_SEPARATOR);
define('ROOT', dirname(__FILE__));
define('RESTRICTED', 'Restricted');

$autoloader = dirname(__FILE__).DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'autoload.php';
if(file_exists($autoloader)){
	require($autoloader);
}
require(dirname(__FILE__).DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR.'model'.DIRECTORY_SEPARATOR.'Config.php');
require(dirname(__FILE__).DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR.'model'.DIRECTORY_SEPARATOR.'Helpers.php');
require(dirname(__FILE__).DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR.'model'.DIRECTORY_SEPARATOR.'Extern.php');

/*
|-------------------------------------------------------------------------------|
|							LARAFELL AUTOLOAD									|
|-------------------------------------------------------------------------------|
*/
spl_autoload_register(function($className){
	$classAry = explode('\\',$className);
	$class = array_pop($classAry);
	$subPath = strtolower(implode(DIRECTORY_SEPARATOR, $classAry));
	$path = dirname(__FILE__) . DIRECTORY_SEPARATOR . $subPath . DIRECTORY_SEPARATOR . $class . '.php';
	if(file_exists($path)){
	  require_once($path);
	}
});

/*----------------------------------------------------------------------------------|
|							USER SESSION STARTS										|
-----------------------------------------------------------------------------------*/
session_start();

/*----------------------------------------------------------------------------------|
|				LARAFELL ROUTER AND API CONTROLLER 						 			|
-----------------------------------------------------------------------------------*/
app\model\Router::route();