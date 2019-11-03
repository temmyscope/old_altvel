<?php
namespace app\lib;
use app\model\DB;

class Migration extends DB{
	public function createTable($name, $attr){
		$sql="CREATE TABLE IF NOT EXISTS {$name}({$attr}) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
		if($this->pdo->exec($sql)){
			echo "Table {$name} has been created";
		}
	}

	public function app_setup(){
		$this->createTable("apps", 
			"id int NOT NULL AUTO_INCREMENT,
			app_name varchar(225) NOT NULL,
			app_email varchar(225) NOT NULL,
			app_key varchar(225) NOT NULL,
			created_at timestamp NULL DEFAULT NULL,
			updated_at timestamp NULL DEFAULT NULL,
			verified enum('true', 'false') DEFAULT 'false',
			deleted enum('true', 'false') DEFAULT 'false',
			PRIMARY KEY (id)");
	}

	public function users(){
		/*----------------------------------------------------------------------------------------------|
		|	This function creates users, user_sessions and contact_us tables  							|
		-----------------------------------------------------------------------------------------------*/
		$this->createTable("users", 
			"id int NOT NULL AUTO_INCREMENT,
			name varchar(225) COLLATE utf8mb4_unicode_ci NOT NULL,
			email varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
			password varchar(225) COLLATE utf8mb4_unicode_ci NOT NULL,
			backup_pass varchar(225) COLLATE utf8mb4_unicode_ci NOT NULL,
			created_at timestamp NULL DEFAULT NULL,
			updated_at timestamp NULL DEFAULT NULL,
			activation varchar(225) COLLATE utf8mb4_unicode_ci NOT NULL,
			verified enum('true', 'false') DEFAULT 'false',
			deleted enum('true', 'false') DEFAULT 'false',
			PRIMARY KEY (id),
			UNIQUE KEY (email)");
		$this->createTable("user_sessions", 
			"id int NOT NULL AUTO_INCREMENT,
			user_id int(11) DEFAULT NULL,
			session varchar(225) COLLATE utf8mb4_unicode_ci NOT NULL,
			user_agent varchar(225) COLLATE utf8mb4_unicode_ci NOT NULL,
			push_token varchar(225) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
			PRIMARY KEY (id)");
		$this->createTable("contact_us", 
			"id int NOT NULL AUTO_INCREMENT,
			email varchar(225) COLLATE utf8mb4_unicode_ci NOT NULL,
			feedback varchar(2000) COLLATE utf8mb4_unicode_ci NOT NULL,
			created_at timestamp NULL DEFAULT NULL,
			deleted enum('true', 'false') DEFAULT 'false',			
			PRIMARY KEY (id)");
	}
}