<?php

// RSA library
require_once "lib/phpseclib/Math/BigInteger.php";
require_once "lib/phpseclib/Crypt/RSA.php";

$title = "Simple Message Signature - Check a signature";

$content = "";
if (!isset($_POST['message'])) {
	
	$content = file_get_contents("lib/check.html");
	
} else {
	
	// Isolate the message and the signature
	$lines = explode("\n", $_POST['message']);
	$message = rtrim(join("\n", array_slice($lines, 0, -3)));
	$signature = str_replace("SIGNATURE:", "", $lines[count($lines)-1]);
	
	// Generate the ckecksum of the message
	$hash = hash("sha256", $message);
	
	// Decrypt the hash with RSA public key
	$rsa = new Crypt_RSA();
	$rsa->loadKey($_POST['key']);
	$original_hash = $rsa->decrypt(base64_decode($signature));
	
	// Check for an error
	if (strpos($_POST['key'], "-----BEGIN PUBLIC KEY-----") === FALSE || strpos($_POST['key'], "-----BEGIN RSA PRIVATE KEY-----") !== FALSE) {
		
		// Print an error
		$content = file_get_contents("lib/error.html");
		$content = str_replace("{ERROR}", "Error: We are unable to check this message with this key!<br />Please check the public key.", $content);
		
	} else if (file_exists("lib/revoked/public/".hash("sha256", preg_replace( "/\r|\n/", "", join("", array_slice(explode("\n", $_POST['key']), 1, -1)))))) {
		
		// If the key has been revoked, there is an error
		$content = file_get_contents("lib/check_error.html");
		$content = str_replace("{ERROR_TYPE}", "The owner of the private key has reported that it has been compromised! This means that an unauthorized person may have signed this message.", $content);
	
	} else if ($hash != $original_hash) {
		
		// Get the error
		if (preg_match("/^[A-Fa-f0-9]{64}$/", $original_hash) !== 1) {
			$error_type = "The given public key does not correspond to the private key that sign this message.";
		} else {
			$error_type = "The message has been modified after it has been signed.";
		}
		
		// If the hash doesn't correspond, there is an error
		$content = file_get_contents("lib/check_error.html");
		$content = str_replace("{ERROR_TYPE}", "Warning: the signature does not correspond to this message!<br />".$error_type, $content);
		
	} else {
	
		// Print a success message
		$content = file_get_contents("lib/check_success.html");
		
	}
	
}

// Display the page
require_once "../lib/corpus.php";

