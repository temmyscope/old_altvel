<?php
/*	@author Elisha Temiloluwa a.k.a TemmyScope
|---------------------------------------------------------------------------|
|create access to an external autoloaded project by initializing it here  	|
|The autoloader will be loaded automatically, so don't bother.				|
|---------------------------------------------------------------------------|
*/

/*
|---------------------------------------------------------------------------|
|Laravel's Blade templating Engine for easy frontend development 			|
|---------------------------------------------------------------------------|
|
*/
use Jenssegers\Blade\Blade;
$views = ROOT.DS.'app'.DS.'view';
$cache = ROOT.DS.'app'.DS.'cache';
$blade = new Blade($views, $cache);