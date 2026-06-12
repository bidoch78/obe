<?php
	
	/**************************************************************************
	*
	* class : 1 fois par jours
	* Suppression des comptes qui ne sont pas valid� depuis plus d'1 mois
	***************************************************************************
	*
	* Ybi : 11/12/2018
	*/
	
	require_once(__dir__ . "/../../config.php");
	require_once(__dir__ . "/../includes/user.php");
	
	try {
		
		$userToDel = User::getUsersToDelete();
		if (count($userToDel) > 0) {
			//Generation des emails
		}
		
	}
	catch(Exception $ex) {
		
	}
	

?>