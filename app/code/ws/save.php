<?php

	require_once(dirname(__FILE__) . "/../../config.php");
	require_once(dirname(__FILE__) . "/../user.php");
	require_once(dirname(__FILE__) . "/../school.php");
	require_once(dirname(__FILE__) . "/../gamesession.php");
	
	$return = array("err" => 0, "data" => null);
	
	try {
			
		switch($_REQUEST["fct"]) {
			
			case "addschool":
				
				//add
				$newSchoolId = School::addNewUserSchool($_POST, $_POST["user"]);
				$return["data"] = School::getUserSchools(array("userschoolids" => array($newSchoolId)));
				
				break;
			
			case "delschool":
				
				School::removeUserSchool($_POST, $_POST["user"]);
				$return["data"] = array("UserSchoolIdDeleted" => $_POST["data"]["UserSchoolId"]);
				
				break;
			
			case "addgamesession":
			
				GameSession::addNewGameSession($_POST, $_POST["user"]);
			
				break;
			
			default:
				throw new Exception("unknown `" . $_REQUEST["fct"] . "` function");
			
		}
		
	}
	catch(Exception $ex) {
		
		$return["err"] = -105100;
		$return["errMessage"] = $ex->getMessage();
		
	}
	
	echo json_encode($return);
	 
?>