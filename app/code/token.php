<?php
	
	require_once(dirname(__FILE__) . "/user.php");	

	/**************************************************************************
	*
	* class : Token
	* gestion des tokens
	***************************************************************************
	*
	* Ybi : 11/04/2014
	*
	*/
	class Token {
		
		private $_token = null;
		private $_db = null;
		private $_context = null;
		private $_userId = null;
		private $_tokenData = null;
		
		public function __construct($context, $token = null, $delete = true) {
			
			if ($context != "" && !is_string($context)) throw new Exception("Context is not defined");
		
			$this->_context = $context;
			$this->_token = $token;
			$this->_db = db::getConnection();
			if ($delete) self::clearInvalidateTokens();
		}
		
		public function getTokenId() { return $this->_token; }
		
		public function isValidated() {
		
			$sql = 'SELECT Expiration FROM tokens WHERE Context = "' . $this->_context . '" AND Token = "' . $this->_db->escapeString($this->_token) . '"';
		
			$dt = $this->_db->query($sql);
			$data = $dt->fetch_assoc();
			$dt->close();
			
			if ($data) {
				
				$date = new DateTime($data["Expiration"]);
				$now = new DateTime("now");

				return ($date > $now);
				
			}
			
			return false;
			
		}
		
		public function getUserId() {
			
			if (is_numeric($this->_userId)) return $this->_userId;
			
			if ($this->_token == "") return null;
		
			$sql = 'SELECT UserId FROM tokens WHERE Token = "' . $this->_db->escapeString($this->_token) . '"';
			$dt = $this->_db->query($sql);
			$data = $dt->fetch_assoc();
			$this->_userId = $data["UserId"];
			$dt->close();
			
			return $this->_userId;
			
		}
	
		// public function getData() {
			
			// if ($this->_tokenData !== null) return $this->_tokenData;
			
			// if ($this->_token == "") return null;
			
			// $sql = 'SELECT TokenData FROM tokens WHERE Token = "' . $this->_token . '"';
			// $this->_tokenData = $this->_prov->ExecuteScalar($sql);			
			
			// return $this->_tokenData;
			
		// }
		
		public function unregister() {
			
			$this->_userId = null;
			
			if ($this->_token == "") return;
			
			$sql = 'DELETE FROM tokens WHERE Token = "' . $this->_db->escapeString($this->_token) . '"';
			$this->_db->nonQuery($sql);
			
		}
		
		// public function registerOnArray($userid, $date_expiration = null, $token_data = null) {
			
			// if ($this->_token == "") return;
			
			// if ($date_expiration === null) {
				// $dt = new DateTime("now");
				// $dt->modify("+1day");
			// }
			// else {
				// $dt = $date_expiration;
			// }
			
			// $tokenValue = $token_data === null ? 'NULL': ("'" . $this->_prov->StringSQL($token_data) . "'");		
				
			// $data = array();
			// $data["ctxt"] = $this->_context;
			// $data["t"] = $this->_token;
			// $data["uId"] = $userid;
			// $data["dExp"] = $dt->format("Y-m-d H:i:s");
			// $data["tVal"] = $tokenValue;
			
			// return $data;
			
		// }
		
		public function register($userid, $date_expiration = null, $token_data = null) {
			
			if ($this->_token == "") return;
			
			$this->_userId = $userid;
			
			if ($date_expiration === null) {
				$dt = new DateTime("now");
				$dt->modify("+1day");
			}
			else {
				$dt = $date_expiration;
			}
					
			$tokenValue = $token_data === null ? 'NULL': ("'" . $this->_db->escapeString($token_data) . "'");		
					
			$sql = 'INSERT INTO tokens (Context, Token, UserId, Expiration, TokenData) VALUES ("' . $this->_context . '", "' . $this->_token . '", ' . $userid . ', "' . $dt->format("Y-m-d H:i:s") . '", ' . $tokenValue . ')';

			$this->_db->nonQuery($sql);
			
		}
		
		// public static function removeTokenOfUser($context, $user, $prov = null) {
			
			// $prov = ($prov == null) ? ProviderPoolingManager::DefaultProvider() : $prov;
			// if (strlen($context) > 0 && strlen($user) > 0) {
				// $sql = 'DELETE FROM tokens WHERE UserId = "' . $user . '" AND Context = "' . $context . '"';
				// $qry = $prov->ExecuteNonQuery($sql, false);						
			// }
			
		// }
	
		public static function createNew() {
			return hash('sha256', uniqid() . rand());
		}
		
		public static function getExpirationDate($nbdays = 1) {
			$dt = new DateTime("now");
			$dt->modify("+" . $nbdays . "day");
			return $dt;
		}
		
		// public static function registerTokens(array $tokens, $prov = null) {
			
			// if (count($tokens) > 0) {
				
				// $sql = 'INSERT INTO tokens (Context, Token, UserId, Expiration, TokenData) VALUES ';
				// $max = count($tokens);
				// for($i = 0; $i < $max; $i++) {
					// $sql .= '("' . $tokens[$i]["ctxt"] . '", "' . $tokens[$i]["t"] . '", ' . $tokens[$i]["uId"] . ', "' . $tokens[$i]["dExp"] . '", ' . $tokens[$i]["tVal"] . ')';
					// if ($i+1 < $max) $sql .= ",";
				// }
				
				// $prov = ($prov == null) ? ProviderPoolingManager::DefaultProvider() : $prov;
				// $prov->ExecuteNonQuery($sql, false);
				
			// }
			
			
		// }	

		public static function clearInvalidateTokens() {
			
			$db = db::getConnection();
			
			$dt = new DateTime("now");
			$sql = 'DELETE FROM tokens WHERE Expiration < "' . $dt->format("Y-m-d H:i:s") . '"';
			$db->nonQuery($sql);
		
		}
		
	}

?>