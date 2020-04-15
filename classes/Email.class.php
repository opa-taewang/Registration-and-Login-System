<?php
	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\Exception;
	require ('vendor/autoload.php');

	/**
	 * 
	 */
	class Email{
 
		
		public static function send($email, $subject, $message){
			$mail = new PHPMailer;
			$mail->isSMTP();                                      // Set mailer to use SMTP
			$mail->Host = 'smtp.mailgun.org';
			//$mail->Host = 'smtp.gmail.com';
			$mail->Port = 587;																// Specify main and backup SMTP servers
			$mail->SMTPAuth = true;																// Enable SMTP authentication
			$mail->Username = 'postmaster@sandboxd3981d62c81e463f8c2fbe86c1bff485.mailgun.org';	// SMTP username
			$mail->Password = 'fb438366d02bf01ab70477e1f61b15de-c322068c-0d93618e';				// SMTP password
			$mail->SMTPSecure = 'tls';											// Enable encryption, only 'tls' is accepted
		
			//$mail->setFrom('opa.vigorous@gmail.com');
			$mail->From = 'brad@sandboxd3981d62c81e463f8c2fbe86c1bff485.mailgun.org';
			$mail->FromName = 'OPA';
			$mail->addAddress($email);                // Add a recipient
			$mail->addReplyTo('noreply@opafurniture.com');
			
			$mail->isHTML(true);
				
			$mail->WordWrap = 50;                                 // Set word wrap to 50 characters
			
			$mail->Subject = $subject;
			$mail->Body    = $message;

			try {
			  	$mail->send();
			} catch (Exception $e) {
			  	$e->getMessage();
			}
		}
	}

?>