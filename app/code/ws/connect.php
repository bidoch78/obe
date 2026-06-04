<?php

	require_once(dirname(__FILE__) . "/../../config.php");
	require_once(dirname(__FILE__) . "/../user.php");
	require_once(dirname(__FILE__) . "/../mailing.php");
	
	$return = array("err" => 0, "data" => null);
	
	try {
			
		Token::clearInvalidateTokens();	
			
		switch($_REQUEST["action"]) {
			
			case "forgotpassword":
			
				$email = isset($_POST["l"]) ? $_POST["l"] : "";
			
				if (!User::checkEmail($email)) {
					$return["err"] = -101101;
					$return["errMessage"] = "Veuillez renseigner une adresse email valide.";	
				}
				else {
					
					$checkUser = User::getUserByLogin($email);
					if ($checkUser) {
						
						$token = new Token("FORGOTPASSWORD", Token::createNew());
						$token->register($checkUser->UserId, Token::getExpirationDate(7));
						
						$emailView = Mailing::getMustacheUserView($checkUser, array("adddefault" => true));
						$emailView["link"] = _APPLICATION_URL_ . "/?fpassword=" . $token->getTokenId();
						
						$mail = new Mailing();
						
						$email = array( 'user' => $checkUser->UserId,
										'from' => _EMAILFROM_,
										'to' => $checkUser->UserEmail,
										'template' => "FORGOTPASSWORD",
										'view' => $emailView );
						
						if (!$mail->AddEmail(array($email), $checkUser->UserId, Mailing::SEND_NOW)) {
							unset($return["message"]);
							$return["err"] = -101102;
							$return["errMessage"] = "Erreur interne (impossible d'envoyer l'email).";	
						}
						else {
							$return["message"] = "Un nouveau lien a été envoyé à l'adresse <b>" . $checkUser->UserEmail . "</b><br>";
						}								
						
					}
					else {
						$return["err"] = -101105;
						$return["errMessage"] = "Désolé aucun compte ne correspond à cet email.";
					}
					
				}
			
				break;
			
			case "login":
				
				if ($_POST["src"] == "signin") {
				
					if (isset($_POST["l"]) && isset($_POST["p"]) ) {
						
						$retUserCon = User::connect($_POST["l"], $_POST["p"]);
						switch($retUserCon["error"]) {
							case "WRONGPASSWORD":
							case "NOTFOUND":
								$return["err"] = -100102;
								$return["errMessage"] = "Veuillez vérifier votre email et votre mot de passe.";									
								break;
							case "WAITVALIDATION":
								$return["err"] = -100103;
								$return["errMessage"] = "Votre compte est en cours de validation";
								break;
							case "DEACTIVATE":
								$return["err"] = -100104;
								$return["errMessage"] = "Votre compte est désactivé. Veuillez contacter un administrateur.";
								break;
							default: 
								if ($retUserCon["user"]) {
									User::connectUser($retUserCon["user"], isset($_POST["r"]) ? ($_POST["r"] == "1") : false);
									Log::WriteLog($retUserCon["user"]->UserId, Log::USER_CONNECT, null, $retUserCon["user"]->UserId);
									if ($retUserCon["user"]->UserIsAdmin) {
										$return["data"] = array("redirect" => "settings.php");
									}
									else {
										$return["data"] = array("redirect" => "overview.php");
									}									
								}
								else {
									$return["err"] = -100100;
									$return["errMessage"] = "Veuillez vérifier votre email et votre mot de passe.";										
								}
						}
						
					}
					else {
						$return["err"] = -100101;
						$return["errMessage"] = "Veuillez vérifier votre email et votre mot de passe.";
					}
				
				}
				
				if ($_POST["src"] == "vsubscribe") {
					
					$email = isset($_POST["l"]) ? $_POST["l"] : "";
					$tokenid = isset($_POST["t"]) ? $_POST["t"] : "";
					$continue = true;
					
					if (!User::checkEmail($email)) {
						$return["err"] = -100301;
						$return["errMessage"] = "Veuillez renseigner une adresse email valide.";	
					}
					else {
						
						$checkUser = User::getUserByLogin($email);
						if ($checkUser) {
							
							$token = new Token("SUBSCRIBE", $tokenid);
							if ($token->isValidated()) {
								
								if ($token->getUserId() == $checkUser->UserId) {
									
									//On laisse le lien se supprimer tout seul
									Log::WriteLog($checkUser->UserId, Log::USER_VALIDATION, null, $checkUser->UserId);
									$checkUser->validateUser();
									
									$return["message"] = 'Votre compte est maintenant activé.<br><div class="link_connectme">Cliquez ici pour vous connecter.<div>';
									
								}
								else {
									$return["err"] = -100304;
									$return["errMessage"] = 'Désolé ce lien ne correspond pas à votre compte.<br><div class="link_generatenewlink">Cliquez ici pour en envoyer un nouveau lien.<div>';									
								}
								
							}
							else {
								$return["err"] = -100303;
								$return["errMessage"] = 'Désolé le lien a expiré.<br><div class="link_generatenewlink">Cliquez ici pour en envoyer un nouveau lien.<div>';
							}
							
						}
						else {
							$return["err"] = -100302;
							$return["errMessage"] = "Désolé aucun compte ne correspond à cet email.";
						}
						
					}
					
				}
				
				if ($_POST["src"] == "snewlink") {
					
					$email = isset($_POST["l"]) ? $_POST["l"] : "";
					$checkUser = User::getUserByLogin($email);
					if ($checkUser) {
						
						$token = new Token("SUBSCRIBE", Token::createNew());
						$token->register($checkUser->UserId, Token::getExpirationDate(7));
						
						$emailView = Mailing::getMustacheUserView($checkUser, array("adddefault" => true));
						$emailView["link"] = _APPLICATION_URL_ . "/?vsubscribe=" . $token->getTokenId();
						
						$mail = new Mailing();
						
						$email = array( 'user' => $checkUser->UserId,
										'from' => _EMAILFROM_,
										'to' => $checkUser->UserEmail,
										'template' => "CONFIRMCREATEACCOUNT",
										'view' => $emailView );
						
						if (!$mail->AddEmail(array($email), $checkUser->UserId, Mailing::SEND_NOW)) {
							unset($return["message"]);
							$return["err"] = -100602;
							$return["errMessage"] = "Erreur interne (impossible d'envoyer l'email).";	
						}
						else {
							$return["message"] = "Un nouveau lien a été envoyé à l'adresse <b>" . $checkUser->UserEmail . "</b><br>";
						}		
						
					}
					else {
						$return["err"] = -100601;
						$return["errMessage"] = "Désolé votre compte est introuvable.";
					}
					
				}
				
				if ($_POST["src"] == "subscribe") {
					
					$firstname = isset($_POST["fn"]) ? $_POST["fn"] : "";
					$lastname = isset($_POST["ln"]) ? $_POST["ln"] : "";
					$email = isset($_POST["l"]) ? $_POST["l"] : "";
					$password =  isset($_POST["p"]) ? $_POST["p"] : "";
					
					if ($firstname && $lastname && $email && $password) {
						
						if (!User::checkEmail($email)) {
							$return["err"] = -100201;
							$return["errMessage"] = "Veuillez renseigner une adresse email valide.";													
						}
						else {
							
							$continue = true;
							
							$checkUser = User::getUserByLogin($email);
							if ($checkUser) {
								//On check si toujours en phase de validation
								if ($checkUser->UserValidateDate) {
									$return["err"] = -100202;
									$return["errMessage"] = "Cette adresse email est déjà utilisée.";	
									$continue = false;									
								}
							}
						
							if ($continue && strlen($password) < 6) {
								$return["err"] = -100205;
								$return["errMessage"] = "Votre mot de passe doit comporter au moins 6 caractères.";									
								$continue = false;
							}
						
							if ($continue) {
								
								$return["message"] = "Pour valider votre compte, veuillez cliquer sur le lien envoyé à l'adresse <b>" . $email . "</b><br>";
								
								$updateOptions = null;
								$userData = array('UserEmail' => $email, 'UserPassword' => User::encryptPassword($password), 'UserFirstName' => $firstname, 'UserLastName' => $lastname);
								if ($checkUser) {
									$userData["UserId"] = $checkUser->UserId;
									$updateOptions = array("fieldsupdated" => $checkUser->getFieldsUpdated($userData));
								}
									
								$userId = User::updateUser($userData, -1, $updateOptions);
								$newUser = User::getUser($userId);
								
								$token = new Token("SUBSCRIBE", Token::createNew());
								$token->register($userId, Token::getExpirationDate(7));
								
								$emailView = Mailing::getMustacheUserView($newUser, array("adddefault" => true));
								$emailView["link"] = _APPLICATION_URL_ . "/?vsubscribe=" . $token->getTokenId();
								
								$mail = new Mailing();
								
								$email = array( 'user' => $newUser->UserId,
												'from' => _EMAILFROM_,
												'to' => $newUser->UserEmail,
												'template' => "CONFIRMCREATEACCOUNT",
												'view' => $emailView );
								
								if (!$mail->AddEmail(array($email), $newUser->UserId, Mailing::SEND_NOW)) {
									unset($return["message"]);
									$return["err"] = -100204;
									$return["errMessage"] = "Erreur interne (impossible d'envoyer l'email).";	
								}
																	
							}
						
						}
						
					}
					else {
						$return["err"] = -100200;
						$return["errMessage"] = "Veuillez renseigner tous les champs.";						
					}
					
				}
				
				if ($_POST["src"] == "changepassword") {
					
					$email = isset($_POST["l"]) ? $_POST["l"] : "";
					$password1 =  isset($_POST["p1"]) ? $_POST["p1"] : "";
					$password2 =  isset($_POST["p2"]) ? $_POST["p2"] : "";
					$tokenid = isset($_POST["t"]) ? $_POST["t"] : "";
					
					if ($email && $password1 && $password2) {
						
						$continue = true;
						
						if ($continue && $password1 != $password2) {
							$return["err"] = -100401;
							$return["errMessage"] = "Assuez-vous que les mots de passe (mot de passe & confirmation) soient identiques.";
							$continue = false;
						}
						
						if ($continue && strlen($password1) < 6) {
							$return["err"] = -100402;
							$return["errMessage"] = "Votre mot de passe doit comporter au moins 6 caractères.";									
							$continue = false;
						}
						
						$checkUser = User::getUserByLogin($email);
						if ($continue && !$checkUser) {
							$return["err"] = -100403;
							$return["errMessage"] = 'Impossible de trouver votre compte. Email introuvable.';
							$continue = false;
						}
						
						$token = new Token("FORGOTPASSWORD", $tokenid);
						if ($continue && !$token->isValidated()) {
							$return["err"] = -100404;
							$return["errMessage"] = 'Désolé le lien a expiré.';
							$continue = false;
						}
						
						if ($continue && $token->getUserId() != $checkUser->UserId) {
							$return["err"] = -100405;
							$return["errMessage"] = 'Désolé ce lien ne correspond pas à votre compte.';		
							$continue = false;
						}
						
						if ($continue) {
							
							//Update password
							$userData = array('UserId' => $checkUser->UserId,'UserPassword' => User::encryptPassword($password1));
							$updateOptions = array("fieldsupdated" => $checkUser->getFieldsUpdated($userData));
							User::updateUser($userData, -1, $updateOptions);
														
							//Envoie un email
							$emailView = Mailing::getMustacheUserView($checkUser, array("adddefault" => true));
							
							$tokenForgotPassword = new Token("FORGOTPASSWORD", Token::createNew());
							$tokenForgotPassword->register($checkUser->UserId, Token::getExpirationDate(7));
						
							$emailView = Mailing::getMustacheUserView($checkUser, array("adddefault" => true));
							$emailView["link"] = _APPLICATION_URL_ . "/?fpassword=" . $tokenForgotPassword->getTokenId();
						
							$mail = new Mailing();
							
							$email = array( 'user' => $checkUser->UserId,
											'from' => _EMAILFROM_,
											'to' => $checkUser->UserEmail,
											'template' => "NEWPASSWORD",
											'view' => $emailView );
							
							$mail->AddEmail(array($email), $checkUser->UserId, Mailing::SEND_NOW);
							
							$token->unregister();
							
							//Connexion de l'utilisateur
							User::connectUser($checkUser);
							Log::WriteLog($checkUser->UserId, Log::USER_CONNECT, null, $checkUser->UserId);
							
							$return["message"] = 'Votre mot passe vient d\'être modifié.<br><div class="link_reloadpage">Cliquez ici pour connecter.<div>';
							
						}
						
					}
					else {
						$return["err"] = -100300;
						$return["errMessage"] = "Veuillez renseigner tous les champs.";	
					}
					
				}
				
				break;
			
			case "disconnect":
				
				$cUser = User::getUserConnected();
				if ($cUser) Log::WriteLog($cUser->UserId, Log::USER_DISCONNECT, null, $cUser->UserId);
				User::disconnectUser();
				break;
			
			default:
				throw new Exception("unknown action");
			
		}
		
	}
	catch(Exception $ex) {
		
		$return["err"] = -100200;
		$return["errMessage"] = $ex->getMessage();
		
	}
	
	echo json_encode($return);
	 
?>