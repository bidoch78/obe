<?php
	
	require_once(dirname(__FILE__) . "/db.php");
	
	/**************************************************************************
	*
	* class : Challenge
	* gestion des challenges
	***************************************************************************
	*
	* Ybi : 14/03/2018
	*
	*/
	class Challenge
	{
		
		private $_db = null;
		
		public function __construct() {
			$this->_db = db::getConnection();
		}
		
		public static function getIdsFromArray($array) {
			
			$ids = array();
			
			foreach($array as $a) $ids[] = $a["ChallengeId"];
			
			return $ids;
			
		}
		
		public static function getCurrentChallenges($options) {
			
			$db = db::getConnection();
			
			$date = new DateTime("now");
			
			$dtStr = $date->Format("Y-m-d H:i:s");
			
			$sql = "SELECT *, DATEDIFF(ChallengeDateTo, ChallengeDateFrom) AS nbDays
						FROM challenges
						WHERE '" . $dtStr . "' >= ChallengeDateFrom AND '" . $dtStr . "' <= ChallengeDateTo
						ORDER BY nbDays DESC";
			
			$data = array();
			$result = $db->query($sql);
			while($obj = $result->fetch_assoc()) {
				$data[] = $obj;
			}
			$result->close();			
			
			return $data;
			
		}
		
	}
	
?>