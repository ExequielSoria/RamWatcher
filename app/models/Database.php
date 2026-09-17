<?php 

class Database {

	//Se conecta a la base de datos
	static public function connect(){
	
		$link = new PDO("mysql:host=".$_ENV['API_SQL_SERVER']."; dbname=".$_ENV['API_SQL_DB'], $_ENV['API_SQL_USER'], $_ENV['API_SQL_PASS']);
		$link->exec("set names utf8");
		return $link;

	}

}