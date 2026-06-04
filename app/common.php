<?php
	
	require_once(dirname(__FILE__) . "/code/user.php");
	require_once(dirname(__FILE__) . "/code/school.php");	
	
	Token::clearInvalidateTokens();	
	
	$currentPage = strtolower(basename($_SERVER["SCRIPT_FILENAME"], ".php"));
	
	$userConnected = User::getUserConnected();
	if (!$userConnected && $currentPage != "index") {
		header('Location: index.php');
	}
	
	if ($userConnected && $currentPage == "index") {
		if (!$userConnected->UserIsAdmin) {
			header('Location: overview.php');			
		}
	}
	
	if ($userConnected && !$userConnected->UserIsAdmin && !School::UserHasDefinedSchool($userConnected->UserId)) {
		if ($currentPage != "settings") {
			header('Location: settings.php');
		}
	}
	
?>