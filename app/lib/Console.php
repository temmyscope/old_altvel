<?php
namespace app\lib;
 
use app\lib\{Migration};
use app\model\Strings;

class Console{

	public static function parse($argc, $argv){
		print("Welcome To The Larafell Framework Engineer Console:\n\n");
		if($argc > 1 && 'help' != strtolower($argv[1]) ){
			switch(strtolower($argv[1])){
				case 'app::start':
					self::configureApp($argv[2] ?? 'Larafell');
					break;
				case 'app::migrate':
					if(isset($argv[2]) && 'users' == strtolower($argv[2])){
						(new Migration)->createUsersTable();
						echo "users, user_sessions and contact_us tables have been created successfully.\n";
					}
					break;
				case 'app::api_setup':
						(new Migration)->app_setup();
						echo "API app tables have been created successfully.\n";
					break;
				case 'app::controller':
					if(isset($argv[2]) && ctype_alpha($argv[2])){
						str_ireplace('controller', '', $argv[2]);
						self::generateController($argv[2]);
					}
					break;
				case 'app::model':
					if(isset($argv[2]) && ctype_alpha($argv[2])){
						str_ireplace('model', '', $argv[2]);
						self::generateModel($argv[2]);
					}
					break;
				case 'app::api':
					if(isset($argv[2]) && ctype_alpha($argv[2])){
						str_ireplace('api', '', $argv[2]);
						self::generateApi($argv[2]);
					}
					break;
				default:
					print("Invalid Syntax. \n\n");
					break;
			}
		}else{
			self::help();
		}
		exit();
	}

	public static function generateView($name){
		$nm = ucfirst($name);
		mkdir(ROOT.DS.'app'.DS.'view'.DS.$name);
		$view = fopen(ROOT.DS."app".DS."view".DS."{$name}".DS."index.blade.php", "w+");
		$vw = "@extends('app')\n@section('title', '{$nm}')\n@section('content')\n\n\t<?php use app\lib\HTML; ?> \n\n\t<?= HTML::Card('{$nm}'); ?>\n\tThis is the {$nm} landing page\n\n@endsection";
		fwrite($view, $vw);
		fclose($view);
	}

	public static function generateModel($name){
		$nm = ucfirst($name);
		if (!file_exists(ROOT.DS."app".DS.$nm.".php")) {
			$table = strtolower($name);
			$model = fopen(ROOT.DS."app".DS."{$nm}.php", "w+");
			$vw = "<?php \nnamespace app;\n\nuse app\model\{DB, Model}; \n\nclass {$nm} extends Model{\n\n\tpublic \$id; \n\n\tpublic function __construct(\$id = null){\n\t\tstatic::\$instance = new Model();\n\t\t\$this->_db = new DB();\n\t\t\$this->_table = static::\$_tbl = '{$table}';\n\t\t\$this->_fulltextColumn = [];\n\t\t\$this->id = \$id;\n\t}\n}";
			
			fwrite($model, $vw);
			fclose($model);
			print("{$nm} Model has been generated.\n\n");
		}else{
			print("{$nm} Model already exists.\n\n");
		}
	}

	public static function generateApi($name){
		$name = strtolower($name);
		$nm = ucfirst($name);
		$cont = fopen(ROOT.DS."app".DS."api".DS."{$nm}Api.php", "w+");
		$var = "<?php \nnamespace app\api;\n\nuse app\model\{Api};\n\nclass {$nm}Api extends Api{\n\n\tpublic function __construct(\$resource, \$action){\n\t\tparent::__construct(\$resource, \$action);\n\t\t\$this->load_model('{$nm}');\n\t}\n\t}\n}";
		fwrite($cont, $var);
		fclose($cont);
		self::generateModel($name);	
		print("{$nm}Api and Model has been generated.\n\n");
		print("Add {$nm}Api to the Config Global api array to make it accessible.");
	}

	public static function generateController($name){
		$name = strtolower($name);
		$nm = ucfirst($name);
		$cont = fopen(ROOT.DS."app".DS."controller".DS."{$nm}Controller.php", "w+");
		$var = "<?php \nnamespace app\controller;\n\nuse app\model\{Controller, Strings};\n\nclass {$nm}Controller extends Controller{\n\n\tpublic function __construct(\$controller, \$action){\n\t\tparent::__construct(\$controller, \$action);\n\t\t\$this->load_model('{$nm}');\n\t}\n\n\tpublic function IndexEndPoint(){ \n\t\tview('{$name}.index'); \n\t}\n}";
		fwrite($cont, $var);
		fclose($cont);
		self::generateModel($name);
		self::generateView($name);
		print("{$nm}Controller, Model and corresponding view has been generated.\n\n");
		print("Add {$nm}Controller to the Config Structure Array & User_Navbar to make it accessible from browser.");
	}

	public static function help(){
		print("To generate secured keys for your Larafell app, use:\n\t");
		print("\"php Engineer App::start\" \n\n");
		echo "To generate the initial framework users table: \n\t \"php Engineer App::Migrate Users\" \n\n";
		echo "To generate a controller: \n\t \"php Engineer App::Controller {{ controller_name }}\" \n\n";
		echo "To generate a model: \n\t \"php Engineer App::Model {{ model_name }}\" \n\n";
		echo "To generate an Api controller: \n\t \"php Engineer App::Api {{ name }}\" ";
	}

	public static function configureApp($name){
		$config = ROOT.DS.'app'.DS.'model'.DS.'Config.php';
		self::configureTITLE($config, $name);
		self::configureBRAND($config, $name);
		self::configureFOLDER($config, $name);

		self::configureREDIRECT($config);
		self::configureCOOKIE($config);
		self::configureSESSION($config);
		self::configureSALT($config);

		print("Environment Security Configurations have been successfully set up.\n");
		print("Please rename your root folder (i.e. your current folder) to {$name}. \n");
		print("You may still have to manually setup your DB configurations for a production\nserver in the app/model/Config.php file of your application.\n");
		exit();
	}

	public static function configureTITLE($file, $title){
		file_put_contents($file, implode('', array_map(function($data) use ($title){
			return (strstr($data, "define('SITE_TITLE'")) ? "define('SITE_TITLE', '{$title}');\n" : $data;
		}, file($file))));
	}

	public static function configureBRAND($file, $brand){
		file_put_contents($file, implode('', array_map(function($data) use ($brand){
			return (strstr($data, "define('BRAND'")) ? "define('BRAND', '{$brand}');\n" : $data;
		}, file($file))));
	}

	public static function configureFOLDER($file, $brand){
		file_put_contents($file, implode('', array_map(function($data) use ($brand){
			return (strstr($data, "define('PUBLIC_FOLDER'")) ? "define('PUBLIC_FOLDER', '/{$brand}');\n" : $data;
		}, file($file))));
	}

	public static function configureREDIRECT($file){ 
		file_put_contents($file, implode('', array_map(function($data){
			$const = Strings::Random();
			return (strstr($data, "define('REDIRECT'")) ? "define('REDIRECT', '{$const}');\n" : $data;
		}, file($file))));
	}

	public static function configureCOOKIE($file){
		file_put_contents($file, implode('', array_map(function($data){
			$const = Strings::Random();
			return (strstr($data, "define('REMEMBER_ME_COOKIE_NAME'")) ? "define('REMEMBER_ME_COOKIE_NAME', '{$const}');\n" : $data;
		}, file($file))));
	}

	public static function configureSESSION($file){
		file_put_contents($file, implode('', array_map(function($data){
			$const = Strings::Random();
			return (strstr($data, "define('CURRENT_USER_SESSION_NAME'")) ? "define('CURRENT_USER_SESSION_NAME', '{$const}');\n" : $data;
		}, file($file))));
	}

	public static function configureSALT($file){
		file_put_contents($file, implode('', array_map(function($data){
			$const = Strings::Rand();
			return (strstr($data, "define('SALT'")) ? "define('SALT', '{$const}');\n" : $data;
		}, file($file))));
	}
}