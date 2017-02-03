<?php

// RSA library
require_once "lib/phpseclib/Math/BigInteger.php";
require_once "lib/phpseclib/Crypt/RSA.php";

$title = "Simple Message Signature - Revoke a key";

$content = "";
if (!isset($_POST['private_key'])) {
	
	$content = file_get_contents("lib/revoke.html");
	
} else {
	
	// Check for an error
	if (strpos($_POST['public_key'], "-----BEGIN PUBLIC KEY-----") === FALSE || strpos($_POST['private_key'], "-----BEGIN RSA PRIVATE KEY-----") === FALSE) {
		
		// Print an error
		$content = file_get_contents("lib/error.html");
		$content = str_replace("{ERROR}", "Error: We are unable revoke these keys!<br />Please check your private and public keys.", $content);
		
	} else {
		
		// Check if the two key form a pair
		$base_pair_test = "THIS IS A TEST STRING!";
		$rsa = new Crypt_RSA();
		$rsa->loadKey($_POST['private_key']);
		$pair_test = $rsa->encrypt($base_pair_test);
		$rsa->loadKey($_POST['public_key']);
		$pair_test = $rsa->decrypt($pair_test);
		
		if ($base_pair_test != $pair_test) {
		
			// Print an error
			$content = file_get_contents("lib/error.html");
			$content = str_replace("{ERROR}", "Error: These keys does not form a correct pair!<br />Please check your private and public keys.", $content);
			
		} else {
			
			if(isset($_POST['g-recaptcha-response']))
				$captcha = $_POST['g-recaptcha-response'];

			if(!$captcha) {
		
				// Print an error
				$content = file_get_contents("lib/error.html");
				$content = str_replace("{ERROR}", "Error: Please fill the anti-robot system!", $content);
				
			} else {
	
				$response = json_decode(file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=6LcSlx4TAAAAAM1CZ3eHeIh2cfEiSoTIhozx2KH_&response=".$captcha."&remoteip=".$_SERVER['REMOTE_ADDR']), true);
				
				if($response['success'] == false) {
		
					// Print an error
					$content = file_get_contents("lib/error.html");
					$content = str_replace("{ERROR}", "Error: You filled the anti-robot system incorrectly!", $content);
					
				} else {
	
					// Generate the ckecksum of the keys
					$hash_private = hash("sha256", preg_replace( "/\r|\n/", "", join("", array_slice(explode("\n", $_POST['private_key']), 1, -1))));
					$hash_public = hash("sha256", preg_replace( "/\r|\n/", "", join("", array_slice(explode("\n", $_POST['public_key']), 1, -1))));
					
					// Mark as revoked
					file_put_contents("lib/revoked/public/".$hash_public, "");
					file_put_contents("lib/revoked/private/".$hash_private, "");
			
					// Print the success message
					$content = file_get_contents("lib/revoke_success.html");
					
				}
				
			}
		
		}
		
	}
	
}

// Display the page
require_once "../lib/corpus.php";

