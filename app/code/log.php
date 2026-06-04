<?php
	
	require_once(dirname(__FILE__) . "/db.php");
	
	/**************************************************************************
	*
	* class : Log
	* gestion des logs
	***************************************************************************
	*
	* Ybi : 22/02/2018
	*
	*/
	class Log
	{
		
		const USER_CREATION = "USERCREATION";
		const USER_VALIDATION = "USERVALIDATION";
		const USER_UPDATE = "USERUPDATE";
		const USER_CONNECT = "USERCONNECT";
		const USER_DISCONNECT = "USERDISCONNET";
		const USER_FORGOTPASSWORD = "USERFORGOTPASSWORD";
		
		const MAIL_CREATION = "MAILCREATE";
		const MAIL_TEMPLATENOTFOUND = "MAILTEMPLATENOTFOUND";
		const MAIL_SENT = "MAILSENT";
		
		const SCHOOL_ADDUSER = "USERADDSCHOOL";
		const SCHOOL_DELUSER = "USERDELSCHOOL";
		const SCHOOL_UPDATEUSER = "USERUPDATESCHOOL";
		const GAMESESSION_NEW = "GAMESESSIONADD";
		
		private $_db = null;
		
		public function __construct() {
			$this->_db = db::getConnection();
		}
		
		public static function WriteLog($userId, $logInfo, $detail, $logCreateByUserId, $error = null) {
			
			$db = db::getConnection();
			
			$strError = ($error) ? ("'" . $error . "'") : 'NULL';
			
			$sql = "INSERT INTO logs (logDate, logUserId, logInfo, logDetail, logCreateByUserId, logError) VALUES ";
			$sql .= "(now(),'" . $db->escapeString($userId) . "','" . $db->escapeString($logInfo) . "'," . ($detail ? ("'" . $detail . "'") : "NULL") . ",'" . ($detail ? $db->escapeString($logCreateByUserId) : '-1') . "'," . $strError . ")";
				
			$db->nonQuery($sql);
				
		}
	
	}
	
?>