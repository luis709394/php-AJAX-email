<?php // You need to add server side validation and better error handling here
require_once ('PHPMailer-master/class.phpmailer.php');
include ("PHPMailer-master/class.smtp.php");
// include the class.smtp.php
include ("smtpConfig.php");
// include the file for email setting
include ("functions.php");
// include all the functions to be used

/*
 * move the uploaded file to folder upload/
 */
if (isset($_FILES)) {
	$uploaddir = 'upload/';
	//  call the uploadFile function to upload each attached file
	foreach ($_FILES as $file) {
		uploadFile($file, $uploaddir);
	}

}

/*
 * get the sender address, recever addresses, cc addresses, subject, message from the submitted form
 */
$from = test_input($_POST["from"]);
$tos = test_input($_POST["to"]);

// set $spamTo for the error of receiver addresses as 0 first, if there is error for any address,
//  $spamTo is incremented by 1
$spamTo = 0;
$addresses = explode(";", $tos);
foreach ($addresses as $key => $address) {

	$addresses[$key] = trim($address);

	if (!spamcheck($address)) {
		++$spamTo;
	}
}

// check spam for the cc field
$spamCc = 0;
if (isset($_POST["cc"])) {
	if (test_input($_POST["cc"])) {
		$ccs = explode(";", $_POST["cc"]);

		foreach ($ccs as $key => $cc) {
			$ccs[$key] = trim($cc);

			if (!spamcheck($cc)) {
				++$spamCc;
			}
		}
	}

}

// load the subject the message
$subject = test_input($_POST["subject"]);
$msg = test_input($_POST["msg"]);

// if all the email addresses are valid, send the email
if (spamcheck($from) && $spamTo == 0 && $spamCc == 0) {

	//   instantiate a PHPMailer object
	$mail = new PHPMailer(true);
	// telling the class to use SMTP
	$mail -> IsSMTP();

	try {
		// the settings of email
		$mail -> Host = $mailHostName;
		$mail -> SMTPDebug = 2;
		$mail -> SMTPAuth = $authentication;
		$mail -> SMTPSecure = $secure;
		$mail -> Port = $portNbr;
		$mail -> Username = $userName;
		$mail -> Password = $password;
		// add the address to reply to
		$mail -> AddReplyTo($from, $replyTo);	
		// add header
		$mail -> addCustomHeader($headers);

		foreach ($addresses as $key => $address) {
			// sanitize email address
            $address2=filter_var($address, FILTER_SANITIZE_EMAIL);
			// add email address
			$mail -> AddAddress($address2, 'Receiver');
		}

		$mail -> Subject = $subject;		
		// wrap message and add to the email
		$msg= wordwrap($msg,70);
		$mail -> Body = $msg;

		//   attach the uploaded files if there is any
		if (isset($_FILES)) {
			foreach ($_FILES as $file) {
				$mail -> AddAttachment('upload/' . $file["name"]);

			}
		}

		//  attach cc addresses  if necessary
		if (isset($_POST["cc"])) {
			if (test_input($_POST["cc"])) {

				foreach ($ccs as $key => $cc) {
			// sanitize email address
            $cc2=filter_var($cc, FILTER_SANITIZE_EMAIL);
			// add email address
					$mail -> AddCC($cc2);
				}
			}
		}

		// send the email
		$mail -> Send();
		// print out the message
		echo "Email Sent to the following addresses: </br>";

		//   show the destination email addresses
		foreach ($addresses as $key => $address) {
			echo $address . "</br>";
		}

		// show the cc addresses if any
		if (isset($_POST["cc"])) {
			if (test_input($_POST["cc"])) {
				echo "Cc: </br>";

				foreach ($ccs as $key => $cc) {
					echo $cc . "</br>";
				}
			}
		}

	} catch (phpmailerException $e) {
		//Pretty error messages from PHPMailer
		echo "failed in phpmailer" . $e -> errorMessage();

	} catch (Exception $e) {
		//Boring error messages from anything else!
		echo "other failure: " . $e -> getMessage();

	}
}
else {
	echo "Errors in email addresses";
}
?>

