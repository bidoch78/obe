<?php
	
	require_once("../../config.php");
	require_once("../includes/mailing.php");
	
	$lastEmail = isset($_POST["email"]) ? $_POST["email"] : null;
	var_dump(Mailing::eMailAddressInfo($_POST["email"]));
	
	if (isset($_POST["email"])) {
		
		$checkUser = User::getUser(1);
		$emailView = Mailing::getMustacheUserView($checkUser, array("adddefault" => true));
		$emailView["link"] = _APPLICATION_URL_;
		
		$mail = new Mailing();
								
		$email = array( 'user' => -1,
						'from' => _EMAILFROM_,
						'to' => $_POST["email"],
						'template' => "CONFIRMCREATEACCOUNT",
						'view' => $emailView );
						
		if (!$mail->AddEmail(array($email), -1, Mailing::SEND_NOW)) {
			echo "Erreur interne (impossible d'envoyer l'email).";
		}
		else {
			echo "Un nouveau lien a été envoyé à l'adresse <b>" . $_POST["email"] . "</b>";
		}
						
		// $mail = new PHPMailer(true);
		// if (!(defined("_PHPMAILER_NOISSMTP_") && _PHPMAILER_NOISSMTP_)) {
			// $mail->IsSMTP();
			// echo "IsSMTP()<br>";
		// }

		// $mail->SMTPOptions = array (
			// 'ssl' => array(
			// 'verify_peer'  => false,
			// 'verify_peer_name'  => false,
			// 'allow_self_signed' => true)
		// );
			
		// $mail->Host = "localhost";
		// $mail->SMTPAuth = false; //Local mode
		// $mail->SMTPKeepAlive = true;
		// $mail->IsHTML(true);
		// $mail->CharSet = "UTF-8";
		// $mail->Encoding = "base64";
					
		// if (defined("_PHPMAILER_DKIM_") && _PHPMAILER_DKIM_ === true) {
			// $mail->DKIM_domain = _PHPMAILER_DKIM_DOMAIN_;
			// $mail->DKIM_private = _PHPMAILER_DKIM_PRIVATE_;
			// $mail->DKIM_selector = _PHPMAILER_DKIM_SELECTOR_;
			// $mail->DKIM_pathphrase = _PHPMAILER_DKIM_PASSPHRASE_;
			// $mail->DKIM_identity = $mailFrom;
			// echo "DKIM:true<br>";
			// echo "DKIM_domain:" . _PHPMAILER_DKIM_DOMAIN_ . "<br>";
			// echo "DKIM_private:" . _PHPMAILER_DKIM_PRIVATE_ . "<br>";
			// echo "DKIM_selector:" . _PHPMAILER_DKIM_SELECTOR_ . "<br>";
			// echo "DKIM_pathphrase:" . _PHPMAILER_DKIM_PASSPHRASE_ . "<br>";
			// echo "DKIM_identity:" . $mailFrom . "<br>";
		// }
		
		// $mailFrom = null;
		// if (defined("_EMAILFROM_")) $mailFrom = _EMAILFROM_;
		// if (customHTML::getVar("global", "emailfrom")) $mailFrom = customHTML::getVar("global", "emailfrom");
					
		// if (defined("_SMTP_ADDRESS")) $mail->Host = _SMTP_ADDRESS;
		// if ($mailFrom) $mail->SetFrom($mailFrom);
		
		// echo "host:" . $mail->Host . "<br>";
		// echo "mailfrom:" . $mailFrom . "<br>";
					
		// $mail->AddAddress($_POST["email"]);
		// $mail->AltBody = "To view the message, please use an HTML compatible email viewer!";
		
		// $body = file_get_contents("content.html");
		
		// $userdata = array("userfname" => $_POST["email"]);
		// $body = MustacheMini::render($body, $userdata);
		
		// $mail->Subject = "Pharmacademy - your program (Test)";
		// $mail->MsgHTML($body);

		// try {
			// $mail->Send();
			// echo "email sent to " . $_POST["email"];
		// }
		// catch(Exception $e) {
			// echo "email error - ". $e->getMessage();
		// }
		
	}
	
?>

<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
</head>

<body class="loginBody">

    <form style="margin: 0" action="" method="post">	
        <input type="text" name="email" value="<?php echo $lastEmail; ?>"><br>
		<input type="submit" value="Submit">
    </form>

</body>
</html>