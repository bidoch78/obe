<?php

	session_start();

	require_once(dirname(__FILE__) . "/code/db.php");
	require_once(dirname(__FILE__) . "/code/parameters.php");

	define("_EMAILFROM_", "contact@obe28.com");
	define("_APPLICATION_NAME_", "Basket Ecole Eure-et-Loir");
	define("_APPLICATION_URL_", "https://www.obe28.com");
	
	define("_SENDEMAIL_", true);
	
	//db::instanciateDB(array("host" => "obecompfqj28.mysql.db", "user" => "obecompfqj28", "password" => "Zm8Evfy8bdyPNFds", "database" => "obecompfqj28"));
	db::instanciateDB(array("host" => "localhost", "user" => "actuser", "password" => "rt75lolo", "database" => "obe"));
	
?>