<?php
	
	require_once(dirname(__FILE__) . "/db.php");
	
	/**************************************************************************
	*
	* class : School
	* gestion des écoles
	***************************************************************************
	*
	* Ybi : 28/02/2018
	*
	*/
	class School
	{
		
		private $_db = null;
		
		public function __construct() {
			$this->_db = db::getConnection();
		}
		
		public static function UserHasDefinedSchool($userId) {
			$db = db::getConnection();
			return ($db->scalarQuery("SELECT COUNT(*) FROM userschools WHERE UserId = '" . $db->escapeString($userId) . "'") > 0);
		}
	
		public static function getSchoolsByIds($ids, $options = null) {
			
			if (!is_array($ids) || count($ids) == 0) return array();
			
			$data = array();
			
			$uSchools = School::getUserSchools(array_merge(array("userschoolids" => $ids), $options));
			foreach($uSchools as $udata) {
				foreach($udata["Schools"] as $schools) {
					$data[$schools["UserSchoolId"]] = $schools;
				}
			}			
			
			if ($options && isset($options["assoc"]) && $options["assoc"] === true) {
				return $data;
			}
				
			return array_values($data);
			
		}
	
		public static function getUserSchools($options) {
			
			$db = db::getConnection();
			
			$hiddenTeacher = $options && isset($options["nodispteacher"]) && $options["nodispteacher"] === true;
	
			$sql = "SELECT *
						FROM userschools AS USC 
						LEFT JOIN userschoollevels AS USL ON (USL.UserSchoolId = USC.UserSchoolId) 
						LEFT JOIN schools as SC ON (SC.SchoolId = USC.SchoolId)
						LEFT JOIN schoolclasslevels AS SCL ON (SCL.SchoolClassLevelId = USL.SchoolClassLevelId)";
						
			$whereConds = array();
			if ($options && isset($options["userids"])) {
				$whereConds[] = "USC.UserId IN (" . implode(",", $options["userids"]) . ")";
			}
			
			if ($options && isset($options["userschoolids"])) {
				$whereConds[] = "USC.UserSchoolId IN (" . implode(",", $options["userschoolids"]) . ")";
			}

			if ($options && isset($options["activatefordate"])) {
				$dt = ($options["activatefordate"] instanceof DateTime) ? $options["activatefordate"]->format("Y-m-d H:i:s") : $dt;
				$whereConds[] = "('" . $dt . "' >= USC.DateFrom AND (USC.DateTo IS NULL OR '" . $dt . "' <= USC.DateTo))";
			}
			
			if (count($whereConds) > 0) $sql .= " WHERE " . implode(" AND ", $whereConds);
			
			$sql .= " ORDER BY USC.DateFrom DESC";
	
			$data = array();
			$result = $db->query($sql);
			while($obj = $result->fetch_assoc()) {
				
				$userId = $obj["UserId"];
				$schoolId = $obj["SchoolId"];
				
				$userData = null;
				if (isset($data[$userId])) {
					$userData = &$data[$userId];
				}
				else {
					$data[$userId] = array("UserId" => $userId, 
											"Schools" => array());
					$userData = &$data[$userId];
				}
				
				$schoolData = null;
				
				if (isset($userData["Schools"][$obj["UserSchoolId"]])) {
					$schoolData = &$userData["Schools"][$obj["UserSchoolId"]];
				}
				else {
					$userData["Schools"][$obj["UserSchoolId"]] = array("UserSchoolId" => $obj["UserSchoolId"],
															"NbStudents" => $obj["SchoolNbStudents"],
															"Teacher" => ($hiddenTeacher) ? null : $obj["SchoolTeacher"],
															"DateFrom" => $obj["DateFrom"],
															"School" => array("SchoolId" => $obj["SchoolId"], 
																				"SchoolName" => $obj["SchoolName"], 
																				"SchoolAddress" => $obj["SchoolAddress"], 
																				"SchoolZipCode" => $obj["SchoolZipCode"], 
																				"SchoolCity" => $obj["SchoolCity"], 
																				"SchoolRefNumber" => $obj["SchoolRefNumber"], 
																				"SchoolPhone" => $obj["SchoolPhone"]),
															"Levels" => array());
					$schoolData = &$userData["Schools"][$obj["UserSchoolId"]];											
				}				

				$schoolData["Levels"][] = array("SchoolClassLevelId" => $obj["SchoolClassLevelId"], 
													"SchoolClassLevelName" => $obj["SchoolClassLevelName"],
													"SchoolClassLevelDescription" => $obj["SchoolClassLevelDescription"],
													"SchoolClassLevelCycle" => $obj["SchoolClassLevelCycle"]);
				
				unset($userData);
				unset($schoolData);
				
			}
			$result->close();
			
			foreach($data as &$userData) {
				$userData["Schools"] = array_values($userData["Schools"]);
			}
			unset($userData);
			
			return array_values($data);
			
		}
		
		public static function removeUserSchool($data, $by, $options = null) {
			
			$db = db::getConnection();
			try {
					
				$db->beginTransaction();
				
				$sql = "DELETE FROM userschools WHERE UserSchoolId = '" . $data["data"]["UserSchoolId"] . "'";
				$db->nonQuery($sql);
				
				$teacher = (strlen($data["data"]["Teacher"]) > 0) ? $db->escapeString($data["data"]["Teacher"]) : "";			
				$levels = array();
				foreach($data["data"]["Levels"] as $level) $levels[] = $level["SchoolClassLevelId"];
				
				$date = new DateTime("now");
				Log::WriteLog($data["user"], Log::SCHOOL_DELUSER, $date->Format("Y-m-d H:i:s") . ";" . $data["user"] . ";" . $data["data"]["School"]["SchoolId"] . ";" . implode(".", $levels) . ";" . $teacher, $by);					
	
				$db->commitTransaction(true);
				
			}
			catch(Exception $ex) {
				$db->rollbackTransaction(true);
				throw $ex;
			}			
			
		}
		
		public static function addNewUserSchool($data, $by, $options = null) { /*$user, $school, array $schoollevel, $nbstudents, $by) */
			
			$newUSID = null;
			$db = db::getConnection();
			
			try {
				
				$db->beginTransaction();
				
				if (count($data["levels"]) == 0) throw new Exception("please define at least 1 level");
				
				$teacher = isset($data["teacher"]) ? trim($data["teacher"]) : null;
				$teacher = (strlen($teacher) > 0) ? $db->escapeString($teacher) : null;
				
				if (isset($data["userschool"]) && is_numeric($data["userschool"])) {

					$newUSID = $data["userschool"];
				
					$sql = "UPDATE userschools SET 
								SchoolId = '" . $db->escapeString($data["school"]) . "',
								SchoolNbStudents = '" . $db->escapeString($data["nbstudents"]) . "', 
								SchoolTeacher = " . ($teacher ? ("'" . $teacher . "'") : 'NULL') . "
								WHERE UserSchoolId = '" . $newUSID . "'";

					$db->nonQuery($sql);
					
					$sql = "DELETE FROM userschoollevels WHERE UserSchoolId = '" . $newUSID . "'";
					$db->nonQuery($sql);
					
					$sql = "INSERT INTO userschoollevels (UserSchoolId, SchoolClassLevelId) VALUES ";
					$tbsl = array();
					foreach($data["levels"] as $sl) {
						$tbsl[] = "('" . $newUSID . "','" . $db->escapeString($sl) . "')";
					}	
					$db->nonQuery($sql . implode(",", $tbsl));
					
					$date = new DateTime("now");
					Log::WriteLog($data["user"], Log::SCHOOL_UPDATEUSER, $date->Format("Y-m-d H:i:s") . ";" . $data["user"] . ";" . $data["school"] . ";" . implode(".", $data["levels"]) . ";" . $teacher, $by);					
				
				}
				else {
					
					$sql = "INSERT INTO userschools (UserId,DateFrom,SchoolId,SchoolNbStudents,SchoolTeacher) VALUES ('" . $data["user"] . "',now(),'" . $db->escapeString($data["school"]) . "','" . $db->escapeString($data["nbstudents"]) . "'," . ($teacher ? ("'" . $teacher . "'") : 'NULL') . ")";
					$db->nonQuery($sql);
					$newUSID = $db->getInsertId();
					
					$sql = "INSERT INTO userschoollevels (UserSchoolId, SchoolClassLevelId) VALUES ";
					$tbsl = array();
					foreach($data["levels"] as $sl) {
						$tbsl[] = "('" . $newUSID . "','" . $db->escapeString($sl) . "')";
					}	
					$db->nonQuery($sql . implode(",", $tbsl));
					
					$date = new DateTime("now");
					Log::WriteLog($data["user"], Log::SCHOOL_ADDUSER, $date->Format("Y-m-d H:i:s") . ";" . $data["user"] . ";" . $data["school"] . ";" . implode(".", $data["levels"]) . ";" . $teacher, $by);					
					
				}
				
				$db->commitTransaction(true);
				
			}
			catch(Exception $ex) {
				$db->rollbackTransaction(true);
				throw $ex;
			}
			
			return $newUSID;
			
		}
		
		public static function getSchoolLevels($options = null) {
			
			$db = db::getConnection();
			
			$data = array();
			$sql = "SELECT * FROM schoolclasslevels";
			
			$result = $db->query($sql);
			while($obj = $result->fetch_assoc()) {
				$data[] = $obj;
			}
			$result->close();			
			
			return $data;
			
		}
	
		public static function getSchools($options) {
			
			$data = array();
			$conds = array();
			$db = db::getConnection();
			
			if ($options && isset($options["filters"])) {
				
				foreach($options["filters"] as $filtername => $filtervalue) {
					if ($filtervalue <> "") {
						switch($filtername) {
							case "SchoolZipCode": $conds[] = "SchoolZipCode LIKE '%" . $db->escapeString($filtervalue) . "%'"; break;
						}
					}
				}
				
			}
			
			$sql = "SELECT * FROM schools";
			if (count($conds) > 0) $sql .= " WHERE " . implode(" OR ", $conds);

			if ($options && isset($options["order"]) && strlen($options["order"]) > 0) $sql .= " ORDER BY " . $db->escapeString($options["order"]);
			if ($options && isset($options["offset"]) && isset($options["limit"])) {
				$sql .= " LIMIT " . ($options["offset"] ? $options["offset"] : "0") . "," . $options["limit"];
			}
			
			$result = $db->query($sql);
			while($obj = $result->fetch_assoc()) {
				$data[] = $obj;
			}
			$result->close();
			
			if ($options && isset($options["crud"]) && $options["crud"] === true) {
				
				$sql = "SELECT COUNT(*) FROM schools";
				if (count($conds) > 0) $sql .= " WHERE " . implode(" OR ", $conds);				
				
				return array("records" => $data, "totalrecords" => $db->scalarQuery($sql));
				
			}
			
			return $data;
			
		}
		
	}
	
?>