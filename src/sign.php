<?php

// RSA library
require_once "lib/phpseclib/Math/BigInteger.php";
require_once "lib/phpseclib/Crypt/RSA.php";

$title = "Simple Message Signature - Sign a message";

$content = "";
if (!isset($_POST['message'])) {
	
	$content = file_get_contents("lib/sign.html");
	
} else {
	
	// Generate the ckecksum of the message
	$hash = hash("sha256", $_POST['message']);
	
	// Crypt the hash with RSA private key
	$rsa = new Crypt_RSA();
	$rsa->loadKey($_POST['key']);
	$signature = $rsa->encrypt($hash);
	
	// Check for an error
	if ($signature == "" || strpos($_POST['key'], "-----BEGIN PUBLIC KEY-----") !== FALSE || strpos($_POST['key'], "-----BEGIN RSA PRIVATE KEY-----") === FALSE) {
		
		// Print an error
		$content = file_get_contents("lib/error.html");
		$content = str_replace("{ERROR}", "Error: We are unable to sign your message with this key!<br />Please check your private key.", $content);
		
	} else if (file_exists("lib/revoked/private/".hash("sha256", preg_replace( "/\r|\n/", "", join("", array_slice(explode("\n", $_POST['key']), 1, -1)))))) {
		
		// If the key has been revoked
		$content = file_get_contents("lib/error.html");
		$content = str_replace("{ERROR}", "Error: That key has been revoked!<br />Please use another private key.", $content);
		
	} else {
	
		// Print the signature
		$content = file_get_contents("lib/signed.html");
		$content = str_replace("{MESSAGE}", htmlspecialchars($_POST['message'])."\n\nThis message has been signed with Simple Message Signature, check that signature at https://walter.tw/sms/check\nSIGNATURE:".base64_encode($signature), $content);
		
	}
	
}

// Display the page
require_once "../lib/corpus.php";

