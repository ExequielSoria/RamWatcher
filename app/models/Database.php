<?php 

class Connection{

	function connect() {
	
	  $dsn = "mysql:host=".$_ENV["MYSQL_HOST"]."; dbname=".$_ENV["DATABASE_NAME"];
	  $user = $_ENV["MYSQL_USER"];
	  $pass = $_ENV['MYSQL_PASSWORD'];
	  
	  $link = new PDO($dsn, $user, $pass);
	  $link->exec("SET NAMES utf8");
	  return $link;
	  
	}

}
