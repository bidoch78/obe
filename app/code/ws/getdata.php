<?php

	require_once(dirname(__FILE__) . "/../../config.php");
	require_once(dirname(__FILE__) . "/../user.php");
	require_once(dirname(__FILE__) . "/../school.php");
	require_once(dirname(__FILE__) . "/../gamesession.php");
	
	$return = array("err" => 0, "records" => array(), "totalrecords" => 0);

	$limit = !isset($_POST["yt_limit"]) ? 20 : $_POST["yt_limit"];
	$offset = !isset($_POST["yt_offset"]) ? 0 : $_POST["yt_offset"];
	$order = !isset($_POST["yt_order"]) ? '' : $_POST["yt_order"];
	
	try {
			
		switch($_REQUEST["get"]) {
			
			case "schools":
			
				$filtercp = (isset($_POST["filtercp"]) && strlen($_POST["filtercp"]) > 0) ? $_POST["filtercp"] : null;
				
				$options = array('crud' => true, 'filters' => array('SchoolZipCode' => $filtercp), 'order' => $order, 'limit' => $limit, 'offset' => $offset);
				
				$dt = School::getSchools($options);
				
				$return["records"] = $dt["records"];
				$return["totalrecords"] = $dt["totalrecords"];
				
				break;
			
			case "lastgames":
				
				$return["records"] = GameSession::getLastAccessScore(array("challenges" => array($_POST["challenge"]), "nodispteacher" => true));
				$return["totalrecords"] = count($return["records"]);
				
				break;
			
			case "userschools":
				
				$return["records"] = School::getUserSchools(array("userids" => array($_POST["user"])));
				$return["totalrecords"] = count($return["records"]);
				
				break;
			
			default:
				throw new Exception("unknown `get` action");
			
		}
		
	}
	catch(Exception $ex) {
		
		$return["err"] = -101100;
		$return["errMessage"] = $ex->getMessage();
		
	}
	
	echo json_encode($return);
	 
?>