<?php
	
	require_once(dirname(__FILE__) . "/db.php");
	require_once(dirname(__FILE__) . "/log.php");
	
	/**************************************************************************
	*
	* class : User
	* gestion des utilisateurs
	***************************************************************************
	*
	* Ybi : 14/12/2015
	*
	*/
	class User
	{
		
		private $_db = null;
		
		public function __construct() {
			$this->_db = db::getConnection();
		}
		
		public function getFieldsUpdated($data) {
			
			$retData = array();
			
			if (isset($data["UserEmail"]) && !strcasecmp($data["UserEmail"], $this->UserEmail)) $retData[] = "UserEmail";
			
			if (isset($data["UserFirstName"]) && strcasecmp($data["UserFirstName"], $this->UserFirstName) != 0) $retData[] = "UserFirstName";
			if (isset($data["UserLastName"]) && strcasecmp($data["UserLastName"], $this->UserLastName) != 0) $retData[] = "UserLastName";
			if (isset($data["UserPassword"]) && strcasecmp($data["UserPassword"], $this->UserPassword) != 0) $retData[] = "UserPassword";
			if (isset($data["UserActivated"]) && $data["UserActivated"] != $this->UserActivated) $retData[] = "UserActivated";
			if (isset($data["UserIsAdmin"]) && $data["UserIsAdmin"] != $this->UserIsAdmin) $retData[] = "UserIsAdmin";
			
			return $retData;
			
		}
		
		public function validateUser() {
			
			if (is_numeric($this->UserId)) {
				$sql = "UPDATE users SET UserValidateDate = now(), UserActivated = 1 WHERE UserId = " . $this->UserId . " AND UserValidateDate IS NULL";
				$this->_db->nonQuery($sql);
			}
			
		}
		
		/*********************************************
		*
		*
		**********************************************/

		public static function getUserByLogin($userEmail) {

			if (strlen($userEmail) == 0) return null;
			
			$db = db::getConnection();
			$result = $db->query("SELECT * FROM users WHERE UserEmail like '" . $db->escapeString($userEmail) . "'");
			$obj = $result->fetch_object("User");
			$result->close();
			
			return $obj;
			
		}
		
		public static function getUser($userId) {

			if (!is_numeric($userId)) return null;
			
			$db = db::getConnection();
			$result = $db->query("SELECT * FROM users WHERE UserId = " . $userId);
			$obj = $result->fetch_object("User");
			$result->close();
			
			return $obj;
			
		}
		
		public static function connect($login, $password) {
			
			$db = db::getConnection();

			$result = $db->query("SELECT * FROM users WHERE UserEmail like '" . $db->escapeString($login) . "'");
			$obj = $result->fetch_object("User");
			$result->close();
			
			$ret = array("user" => null, "error" => "NOTFOUND");
			
			if ($obj) {
				
				$ret["user"] = $obj;
				if ($obj->UserValidateDate == null) {
					$ret["error"] = "WAITVALIDATION";
				}
				elseif (!self::userIsActivated($obj)) {
					$ret["error"] = "DEACTIVATE";
				}
				elseif ($password === null || password_verify(trim($password), trim($obj->UserPassword)) === false) {
					$ret["error"] = "WRONGPASSWORD";
				}
				else {
					$ret["error"] = false;
				}
					
			}
			
			return $ret;
			
		}
		
		public static function updateUser($data, $by, $options = null) {
			
			$db = db::getConnection();
			
			if (isset($data["UserId"])) {
				
				$sql = "UPDATE users SET ";
				
				$upd = array();
				
				if (isset($data["UserEmail"])) $upd[] = " UserEmail = '" . $db->escapeString($data["UserEmail"]) . "'";
				if (isset($data["UserFirstName"])) $upd[] = " UserFirstName = '" . $db->escapeString($data["UserFirstName"]) . "'";
				if (isset($data["UserLastName"])) $upd[] = " UserLastName = '" . $db->escapeString($data["UserLastName"]) . "'";
				if (isset($data["UserActivated"])) $upd[] = " UserActivated = '" . ($data["UserActivated"] ? 0 : 1) . "'";
				if (isset($data["UserIsAdmin"])) $upd[] = " UserIsAdmin = '" . ($data["UserIsAdmin"] ? 0 : 1) . "'";
				if (isset($data["UserPassword"])) $upd[] = " UserPassword = '" . $data["UserPassword"] . "'";
				
				if (isset($data["UserValidateDate"])) {
					if ($data["UserValidateDate"] instanceof DateTime) {
						$upd[] = " UserValidateDate = '" . $data["UserValidateDate"]->format("Y-m-d H:i") . "'";
					}
					else {
						$upd[] = " UserValidateDate = " . ($data["UserValidateDate"] ? ("'" . $data["UserValidateDate"] . "'") : 'NULL');
					}
				}

				$sql .= implode(", ", $upd);
				$sql .= "WHERE UserId = '" . $db->escapeString($data["UserId"]) . "'";
				$db->nonQuery($sql);
				
				Log::WriteLog($data["UserId"], Log::USER_UPDATE,($options && isset($options["fieldsupdated"]) ? implode(",", $options["fieldsupdated"]) : null), $by);
				
			}
			else {
				
				$result = $db->query("SELECT MAX(UserId) AS MAXID FROM users");
				$obj = $result->fetch_assoc();
				$maxId = $obj["MAXID"] + 1;
				$result->close();
				
				$fields = array_merge(array("UserId", "UserCreateDate"), array_keys($data));
				$sql = "INSERT INTO users (" . implode(",", $fields) . ") VALUES ('" . $maxId . "',now(),";
				foreach($data as $key => $value) $data[$key] = "'" . $value . "'";
				$sql .= implode(",", $data) . ")";
				
				$db->nonQuery($sql);
				
				Log::WriteLog($maxId, Log::MAIL_SENT, null, $by);
				
				$data["UserId"] = $maxId;
				
			}
			
			return $data["UserId"];
			
		}
	
		public static function formatUserName($user) {
			return $user->UserFirstName . " " . $user->UserLastName;
		}
	
		public static function userIsActivated($user) {
			return ($user->UserActivated != 0);
		}

		public static function encryptPassword($pwd) {
			return password_hash(trim($pwd), PASSWORD_BCRYPT);
		}		
		
		/*** ***/
		
		private static $_user = null;
		
		public static function getUserConnected() {
			
			$userid = isset($_SESSION['obe_user']) ? $_SESSION['obe_user'] : null;
			if (!is_numeric($userid)) {
				//Check si il y a un cookie
				$cookieUser = isset($_COOKIE["obeu"]) ? $_COOKIE["obeu"] : null;
				$cookieHash = isset($_COOKIE["obeh"]) ? $_COOKIE["obeh"] : null;
				if ($cookieUser) {
					$token = new Token("REMEMBERME", $cookieUser);
					if ($token->isValidated()) {
						$cUser = self::getUser($token->getUserId());
						if ($cUser && $cUser->UserPassword == $cookieHash && self::userIsActivated($cUser)) {
							self::$_user = $cUser;
							return self::$_user;
						}
						else {
							$token->unregister();
						}
					}
				}
			}

			if (self::$_user) {
				if (self::$_user->UserId != $userid || !self::userIsActivated(self::$_user)) {
					self::$_user = null;
				}
			}
			else {
				$user = self::getUser($userid);
				if (!$user || !self::userIsActivated($user)) return null;
				self::$_user = $user;
			}
			
			return self::$_user;
			
		}
		
		public static function connectUser($user, $remimberme = false) {
			
			self::$_user = $user;
			$_SESSION['obe_user'] = $user->UserId;
			
			$tokendays = 1;
			$cookieTime = 0;
			if ($remimberme) {
				$cookieTime = time()+60*60*24*30;
				$tokendays = 30;
			}
			
			$token = new Token("REMEMBERME", Token::createNew());
			$token->register($user->UserId, Token::getExpirationDate($tokendays));
			
			setcookie("obeu", $token->getTokenId(), $cookieTime, '/');
			setcookie("obeh", $user->UserPassword, $cookieTime, '/');
			
		}
		
		public static function disconnectUser() {
			unset($_SESSION['obe_user']);
			self::$_user = null;
			setcookie("obeu", "", time() - 3600, '/');
			setcookie("obeh", "", time() - 3600, '/');			
		}
		
		public static function checkEmail($email) {
			
			if (strlen($email) > 0) {
				return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
			}

			return false;			
			
		}
		
		public static function getUsersToDelete() {
			
			$db = db::getConnection();
			
			$date = new DateTime("now");
			$date->sub(new DateInterval('P1M'));
			
			$users = array();
			$result = $db->query("SELECT * FROM users WHERE UserValidateDate IS NULL AND UserCreateDate <= '" . $date->format("Y-m-d 00:00:00") . "'");
			while($obj = $result->fetch_object("User")) {
				$users[] = $obj;
			}
			$result->close();
			
			return $users;
			
		}
		
	}

?>