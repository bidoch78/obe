<?php
	
	require_once(dirname(__FILE__) . "/db.php");
	require_once(dirname(__FILE__) . "/log.php");	
	require_once(dirname(__FILE__) . "/user.php");
	require_once(dirname(__FILE__) . "/mustache-mini.php");
	require_once(dirname(__FILE__) . "/PHPMailer/src/PHPMailer.php");
	require_once(dirname(__FILE__) . "/PHPMailer/src/Exception.php");
	require_once(dirname(__FILE__) . "/PHPMailer/src/SMTP.php");
	
	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\Exception;
	use PHPMailer\PHPMailer\SMTP;
	
	/**************************************************************************
	*
	* class : Mailing
	* gestion des emails
	***************************************************************************
	*
	* Ybi : 11/12/2018
	*
	*/
	class Mailing
	{
		
		const SEND_NOW = true;
		
		private $_db = null;
		
		public function __construct() {
			$this->_db = db::getConnection();
		}
		
		public function getTemplate($name) {
			
			$result = $this->_db->query("SELECT * FROM emailstemplate WHERE EmailTemplateKey like '" . $this->_db->escapeString($name) . "'");
			
			$obj = $result->fetch_assoc();
			$result->close();
			
			return $obj;
			
		}
		
		public function AddEmail($emails, $by, $sendnow = false) {
			
			$idEmailsCreated = array();
			$templateLoaded = array();
			
			foreach($emails as $email) {
				
				if (!isset($templateLoaded[$email["template"]])) {
					$tpl = $this->getTemplate($email["template"]);
					if ($tpl) $templateLoaded[$email["template"]] = $tpl;
				}
				
				if (!isset($templateLoaded[$email["template"]])) {
					Log::WriteLog($email["user"], Log::MAIL_TEMPLATENOTFOUND, $email["template"], $by);
					return false;
				}
				else {
					
					$tpl = $templateLoaded[$email["template"]];
					$subject = MustacheMini::render($tpl["EmailTemplateSubject"], $email["view"]);
					$body = MustacheMini::render($tpl["EmailTemplateBody"], $email["view"]);
					
					$sql = "INSERT INTO emails (UserId, EmailFrom, EmailTo, EmailCc, EmailSubject, EmailBody, EmailSubmitDate, EmailSubmitBy) VALUES (";
					
					$sql .= "'" . $this->_db->escapeString($email["user"]) . "',";
					$sql .= "'" . $email["from"] . "',";
					$sql .= "'" . $email["to"] . "',";
					if (isset($email["cc"]) && strlen($email["cc"]) > 0) { $sql .= "'" . $email["cc"] . "',"; } else { $sql .= "NULL,"; }
					$sql .= "'" . $this->_db->escapeString($subject) . "',";
					$sql .= "'" . $this->_db->escapeString($body, array("htmlentities" => false)) . "',";
					$sql .= "now(),";
					$sql .= "'" . $by . "')";
					
					$this->_db->nonQuery($sql);
					
					$id = $this->_db->getInsertId();
					$idEmailsCreated[] = $id;
					
					Log::WriteLog($email["user"], Log::MAIL_CREATION, $email["template"] . ":" . $id, $by);
				
				}
				
			}
			
			if ($sendnow && count($idEmailsCreated) > 0) {
				return $this->sendEmail($by, array("sendlimit" => count($idEmailsCreated), "emailids" => $idEmailsCreated));
			}
			
			return true;
			
		}
		
		public function sendEmail($by, $options) {
			
			$return = true;
			
			$sendLimit = (!$options || !isset($options["sendlimit"])) ? 50 : $options["sendlimit"];
			
			$ids = array();
			
			if ($options && isset($options["emailids"])) $ids = $options["emailids"];
			
			if (_SENDEMAIL_) {
				
				$PHPmail = new PHPMailer(true);

				$PHPmail->SMTPOptions = array (
					'ssl' => array(
					'verify_peer'  => false,
					'verify_peer_name'  => false,
					'allow_self_signed' => true)
				);
					
				if (_EMAILSMTP_) $PHPmail->IsSMTP();

				$PHPmail->Host = _EMAILHOST_;
				$PHPmail->SMTPAuth = false; //Local mode
				$PHPmail->SMTPKeepAlive = true;
				$PHPmail->IsHTML(true);
				$PHPmail->CharSet = "UTF-8";
				$PHPmail->Encoding = "base64";
				$PHPmail->Port = _EMAILPORT_;
				
			}
			
			$sqlEmails = "SELECT * FROM emails WHERE EmailId IN (" . implode(",", $ids) . ")";

			$result = $this->_db->query($sqlEmails);
			while($obj = $result->fetch_assoc()) {
				
				$emailSent = false;
				
				if (_SENDEMAIL_) {

					$addrInfoFrom = self::eMailAddressInfo($obj["EmailFrom"]);
					$addrInfoTo = self::eMailAddressInfo($obj["EmailTo"]);
					$addrInfoCc = self::eMailAddressInfo($obj["EmailCc"]);
					
					try {
					
						if ($addrInfoFrom["ok"] && $addrInfoTo["ok"]) {
						
							$PHPmail->clearAllRecipients();
							$PHPmail->clearAttachments();
							$PHPmail->clearCustomHeaders();
							
							$PHPmail->SetFrom($addrInfoFrom["email"], $addrInfoFrom["name"]);
							$PHPmail->AddAddress($addrInfoTo["email"], $addrInfoTo["name"]);
							//at the moment only 1 cc
							if ($addrInfoCc["ok"]) $PHPmail->AddCC($addrInfoCc["email"], $addrInfoCc["name"]);
							
							$PHPmail->Subject = $obj["EmailSubject"];
							$PHPmail->MsgHTML($obj["EmailBody"]);
							
							$PHPmail->Send();
							$emailSent = true;
							
						}
						else {
							
							$strError = array();
							if (!$addrInfoFrom["ok"]) $strError[] = "email `" . $obj["EmailFrom"] . "` incorrect";
							if (!$addrInfoTo["ok"]) $strError[] = "email `" . $obj["EmailTo"] . "` incorrect";
							Log::WriteLog($obj["UserId"], Log::MAIL_SENT, $obj["EmailId"], $by, implode(" - ", $strError));
							
						}
					
					}
					catch(Exception $ex) {
						Log::WriteLog($obj["UserId"], Log::MAIL_SENT, $obj["EmailId"], $by, $ex->getMessage());
					}
					
				}
				else {
					$emailSent = true;
				}
				
				$this->_db->nonQuery("UPDATE emails SET EmailSendDate = now() WHERE EmailId = '" . $this->_db->escapeString($obj["EmailId"]) . "'");
				
				if ($emailSent) Log::WriteLog($obj["UserId"], Log::MAIL_SENT, $obj["EmailId"], $by);
				
			}
			$result->close();
			
			return $return;
			
		}
		
		public static function getMustacheUserView(User $user, $options = null) {
			
			$prefix = "user";
			if ($options && isset($options["prefix"])) $prefix = $options["prefix"];
			
			$data = array();
			
			$data[$prefix . "id"] = $user->UserId;
			$data[$prefix . "lname"] = $user->UserLastName;
			$data[$prefix . "fname"] = $user->UserFirstName;
			$data[$prefix . "login"] = $user->UserEmail;
			$data[$prefix . "email"] = $user->UserEmail;
			
			if ($user->UserId > 0) {
				$data[$prefix . "unsubscribe"] = "/?unsubscribe=" . $user->UserId . "." . md5($user->UserLastName . "_" . $user->UserFirstName . "_" . $user->UserEmail . "_" . $user->UserPassword);
				$data[$prefix . "unsubscribe"] = _APPLICATION_URL_ . $data[$prefix . "unsubscribe"];
			}			
			
			if ($options && isset($options["adddefault"]) && $options["adddefault"] === true) {
				$defValues = self::getMustacheDefaultView($options);
				$data = array_merge($data, $defValues);
			}
			
			return $data;
			
		}
		
		public static function getMustacheDefaultView($options = null) {
			
			$data = array();
			$data["url"] = _APPLICATION_URL_;
			$data["appname"] = _APPLICATION_NAME_;
			$data["emailcontact"] = _EMAILFROM_;
			
			return $data;
			
		}
		
		public static function eMailAddressInfo($email) {
			
			$data = array("email" => '', "name" => '', "ok" => false);			
			
			if (!$email) return $data;
			
			$dpos = strpos($email, "<");
			$epos = strpos($email, ">");
			
			if ($dpos === false || $epos === false || ($dpos > $epos)) {
				$data["email"] = $email;
			}
			else {
				$data["email"] = trim(substr($email, $dpos+1, $epos - $dpos - 1));
				$data["name"] = substr($email, 0, $dpos) . substr($email, $dpos);
			}
			
			$data["ok"] = filter_var($data["email"], FILTER_VALIDATE_EMAIL) !== false;
			
			return $data;
			
		}
		
	
	}
	
?>