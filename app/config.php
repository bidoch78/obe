<?php

	session_start();

	require_once(dirname(__FILE__) . "/code/db.php");
	require_once(dirname(__FILE__) . "/code/parameters.php");

	define("_EMAILFROM_", getenv("EMAIL_FROM"));
	define("_APPLICATION_NAME_", "Basket Ecole Eure-et-Loir");
	define("_APPLICATION_URL_", getenv("APP_URL"));
	define("_SENDEMAIL_", getenv("ACTIVATE_EMAIL") == "true");

	db::instanciateDB(array("host" => getenv("DB_HOST"), "user" => getenv("DB_USER"), "password" => getenv("DB_PWD"), "database" => getenv("DB_NAME")));
	
?>