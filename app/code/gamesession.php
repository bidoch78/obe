<?php
	
	require_once(dirname(__FILE__) . "/db.php");
	require_once(dirname(__FILE__) . "/challenge.php");
	require_once(dirname(__FILE__) . "/school.php");
	
	/**************************************************************************
	*
	* class : GameSession
	* gestion des sessions de jeu
	***************************************************************************
	*
	* Ybi : 14/03/2018
	*
	*/
	class GameSession
	{
		
		private $_db = null;
		
		public function __construct() {
			$this->_db = db::getConnection();
		}
	
		public static function getLastAccessScore($options) {
			
			if (count($options["challenges"]) == 0) return array();
			
			$db = db::getConnection();
			
			$userfilter = "";
			if (isset($options["user"])) $userfilter = "AND U.UserId = '" . $options["user"] . "'";
			
			$max = isset($options["max"]) ? $options["max"] : null;
			if (!is_numeric($max)) $max = 10;

			/* On récupère les derniers parties */
			$sql = "SELECT C.ChallengeId, MAX(UG.DateSession) AS DMax, UG.UserSchoolId FROM usergamesessions AS UG 
					LEFT JOIN userschools AS US ON (US.UserSchoolId = UG.UserSchoolId)
					LEFT JOIN users AS U ON (US.UserId = U.UserId)
					LEFT JOIN challenges AS C ON (UG.DateSession >= C.ChallengeDateFrom AND UG.DateSession <= C.ChallengeDateTo)
					WHERE U.UserExcludeStats = 0 AND C.ChallengeId IN (" . implode(",", $options["challenges"]) . ") " . $userfilter . "
					GROUP BY C.ChallengeId, UG.UserSchoolId
					ORDER BY C.ChallengeId ASC, MAX(UG.DateSession) DESC";
					
			$userschoolids = array();
			$data = array();
			$result = $db->query($sql);
			while($obj = $result->fetch_assoc()) {
				if (!isset($data[$obj["ChallengeId"]])) $data[$obj["ChallengeId"]] = array("ChallengeId" => $obj["ChallengeId"], "Schools" => array());
				$count = count($data[$obj["ChallengeId"]]["Schools"]);
				if (($count < $max) || ($max < 0)) {
					$data[$obj["ChallengeId"]]["Schools"][] = array("LastPlayDate" => $obj["DMax"], "UserSchoolId" => $obj["UserSchoolId"], "School" => null, "score" => 0, "cup" => null);
					$userschoolids[$obj["UserSchoolId"]] = $obj["UserSchoolId"];
				}
			}
			$result->close();
			
			/* récupère les infos des écoles */
			$uSchools = School::getSchoolsByIds($userschoolids, array("assoc" => true, "nodispteacher" => ($options && isset($options["nodispteacher"]) && $options["nodispteacher"] === true)));
			
			/* attache les écoles */
			foreach($data as &$challenge) {
				foreach($challenge["Schools"] as &$school) {
					$school["School"] = $uSchools[$school["UserSchoolId"]];
				}
				unset($school);
			}
			unset($challenge);
			
			/* récupère les données */
			if (count($userschoolids) > 0) {
				
				$sql = "SELECT C.ChallengeId, UG.UserSchoolId, SUM(UG.UserGameSessionNb) AS SumG FROM usergamesessions AS UG 
							LEFT JOIN userschools AS US ON (US.UserSchoolId = UG.UserSchoolId)
							LEFT JOIN users AS U ON (US.UserId = U.UserId)
							LEFT JOIN challenges AS C ON (UG.DateSession >= C.ChallengeDateFrom AND UG.DateSession <= C.ChallengeDateTo)
							WHERE U.UserExcludeStats = 0 AND C.ChallengeId IN (" . implode(",", $options["challenges"]) . ") AND UG.UserSchoolId IN (" . implode(",", $userschoolids) . ")
							GROUP BY C.ChallengeId, UG.UserSchoolId";
				
				/*$sql = "SELECT C.ChallengeId, UG.UserSchoolId, SUM(UG.NbShoots) AS SumS, SUM(UG.NbDribbles) AS SumD, SUM(UG.NbPasses) AS SumP FROM usergamesessions AS UG 
							LEFT JOIN userschools AS US ON (US.UserSchoolId = UG.UserSchoolId)
							LEFT JOIN users AS U ON (US.UserId = U.UserId)
							LEFT JOIN challenges AS C ON (UG.DateSession >= C.ChallengeDateFrom AND UG.DateSession <= C.ChallengeDateTo)
							WHERE U.UserExcludeStats = 0 AND C.ChallengeId IN (" . implode(",", $options["challenges"]) . ") AND UG.UserSchoolId IN (" . implode(",", $userschoolids) . ")
							GROUP BY C.ChallengeId, UG.UserSchoolId";*/
						
				$result = $db->query($sql);
				while($obj = $result->fetch_assoc()) {
					
					$challengeData = &$data[$obj["ChallengeId"]];
					foreach($challengeData["Schools"] as &$school) {
						if ($school["UserSchoolId"] == $obj["UserSchoolId"]) {
							$school["score"] += $obj["SumG"];
						}
					}
					unset($school, $challengeData);
					
				}
				$result->close();										

			}
			
			/* calcul coupe */
			foreach($data as &$challenge) {
				foreach($challenge["Schools"] as &$school) {
					
					if ($school["score"] > 2000) {
						$school["cup"] = "platinium";
					}
					else if ($school["score"] > 1500) {
						$school["cup"] = "or";
					}
					else if ($school["score"] > 1000) {
						$school["cup"] = "argent";
					}
					else if ($school["score"] > 500) {
						$school["cup"] = "bronze";
					}
					
				}
				unset($school);
			}
			unset($challenge);			
			
			return $data;			
			
		}
	
		public static function getUserScore($options) {
			return self::getLastAccessScore($options);
		}
		
		public static function addNewGameSession($data, $by, $options = null) {
			
			$db = db::getConnection();
			
			$sqlData = array();
			$logData = array();
			foreach($data["values"] as $type => $value) {
				if (is_numeric($value) && intval($value) > 0) {
					$sqlData[] = "('" . $data["userschoolid"] . "', '" . $data["date"] . "', '" . $type . "', '" . $value . "')";
				}
				$logData[] = $type . ":" . $value;
			}
			
			if (count($sqlData) > 0) {
				$sql = "INSERT INTO usergamesessions (UserSchoolId,DateSession,UserGameSessionType,UserGameSessionNb) VALUES " . implode(",", $sqlData);
				$db->nonQuery($sql);
			}
			
			$date = new DateTime($data["date"]);
			Log::WriteLog($data["user"], Log::GAMESESSION_NEW, $date->Format("Y-m-d H:i:s") . $data["userschoolid"] . ";" . implode(";", $logData), $by);
			
		}
		
	}
	
?>