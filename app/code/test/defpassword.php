<?php

	require_once(dirname(__FILE__) . "/../user.php");	

	$pwd = isset($_GET["p"]) ? $_GET["p"] : "";
	
	if ($pwd == "") {
		echo "no password";
		exit();
	}
	
	echo $pwd . " => " . User::encryptPassword($pwd);

?>