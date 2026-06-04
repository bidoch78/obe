<?php
	
	require_once(dirname(__FILE__) . "/token.php");
	
	/**************************************************************************
	*
	* class : db
	* gestion des tokens
	***************************************************************************
	*
	* Ybi : 10/12/2015
	*
	*/
	class db
	{
		
		private $_parameters = null;
		private $_mysqli = null;
		
		private $_lastAutoCommit = null;
		private $_transactionInProgress = false;
		
		public function __construct($parameters) {
			mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
			$this->_parameters = $parameters;
		}
		
		public function connect() {
			
			if ($this->_mysqli) return;
			$this->_mysqli = new mysqli($this->_parameters["host"], $this->_parameters["user"], $this->_parameters["password"], $this->_parameters["database"]);
			if ($this->_mysqli->connect_error) throw new Exception("database connection error (" . $this->_mysqli->connect_errno . "): " . $this->_mysqli->connect_error);
			$this->_mysqli->query("SET NAMES UTF8");
			$this->_transactionInProgress = false;
				
		}
		
		public function reconnect() {
			
			$this->_mysqli = new mysqli($this->_parameters["host"], $this->_parameters["user"], $this->_parameters["password"], $this->_parameters["database"]);
			if ($this->_mysqli->connect_error) throw new Exception("database connection error (" . $this->_mysqli->connect_errno . "): " . $this->_mysqli->connect_error);
			$this->_mysqli->query("SET NAMES UTF8");
			$this->_transactionInProgress = false;
			
		}		
		
		public function getDB() {
			$this->connect();
			return $this->_mysqli;
		}
		
		public function getInsertId() {
			$this->connect();
			return mysqli_insert_id($this->_mysqli);
		}
	
		public function query($qry, $resultmode = MYSQLI_STORE_RESULT) {
			$this->connect();
			return $this->_mysqli->query($qry, $resultmode);
		}

		public function nonQuery($qry) {
			$this->connect();
			return $this->_mysqli->query($qry);
		}
		
		public function scalarQuery($qry) {
			$result = $this->query($qry);
			$row = $result->fetch_row();
			$value = ($row) ? $row[0] : null;
			$result->close();
			return $value;
		}
		
		public function escapeString($str, $options = null) {
			$this->connect();
			if ($str) {
				$applyhtml = true;
				if ($options && isset($options["htmlentities"]) && $options["htmlentities"] === false) $applyhtml = false;
				if ($applyhtml) {
					$str = preg_replace("/<script\b[^>]*>([\s\S]*?)<\/script>/m", "", $str);
					$str = htmlentities($str);
				}
			}
			return $this->_mysqli->escape_string($str);
		}

		public function beginTransaction() {
			if ($this->_transactionInProgress) return;			
			$this->connect();
			if (!$this->_mysqli) throw new Exception("mysqli object not instanciate");
			$this->_lastAutoCommit = $this->scalarQuery("select @@autocommit") == "1";
			$this->_mysqli->autocommit(false);
			$this->_transactionInProgress = true;
		}
		
		public function commitTransaction($stop = false) {
			if (!$this->_transactionInProgress) return;
			if (!$this->_mysqli) throw new Exception("mysqli object not instanciate");
			$this->_mysqli->commit();
			if ($stop) $this->stopTransaction();
		}
		
		public function rollbackTransaction($stop = false) {
			if (!$this->_transactionInProgress) return;
			if (!$this->_mysqli) throw new Exception("mysqli object not instanciate");
			$this->_mysqli->rollback();
			if ($stop) $this->stopTransaction();
		}
		
		public function stopTransaction() {
			if (!$this->_transactionInProgress) return;
			if (!$this->_mysqli) throw new Exception("mysqli object not instanciate");
			$this->_mysqli->autocommit($this->_lastAutoCommit);
			$this->_transactionInProgress = false;
		}		
				
		/********
		*
		********/
	
		private static $_singleton = null;
		
		public static function instanciateDB($parameters) {
			self::$_singleton = new self($parameters);
		}
		
		public static function getConnection() {
			return self::$_singleton;
		}
	
	}

?>