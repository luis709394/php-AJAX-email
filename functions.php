<?php


/* function to process input
 * */

function test_input($data) {
	$data = trim($data);
	$data = stripslashes($data);
	$data = htmlspecialchars($data);
	return $data;
}

/**
 * function to validate email address
 * */

function spamcheck($field) {// Sanitize e-mail address
	$field = filter_var($field, FILTER_SANITIZE_EMAIL);
	// Validate e-mail address
	if (filter_var($field, FILTER_VALIDATE_EMAIL)) {
		return TRUE;
	} else {
		return FALSE;
	}
}

/*
 * function to upload the email attachment to a location
 * @para $fileName is the name of the uploaded file
 */
function uploadFile($file, $uploaddir) {

	if ($file["error"] > 0) {
		echo "Error: " . $file["error"] . "<br>";
	} else {
		//echo "Upload: " . $file["name"] . "<br>";
		//echo "Type: " . $file["type"] . "<br>";
		//echo "Size: " . ($file["size"] / 1024) . " kB<br>";
		//echo "Stored in: " . $file["tmp_name"];
	}

	if (file_exists($uploaddir . $file["name"])) {
		echo $file["name"] . " already exists.";
	} else {
		move_uploaded_file($file["tmp_name"], $uploaddir . $file["name"]);
		//echo "Stored in: " . $uploaddir . $file["name"];
	}

}














?>