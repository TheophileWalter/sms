<?php

// RSA library
require_once "lib/phpseclib/Math/BigInteger.php";
require_once "lib/phpseclib/Crypt/RSA.php";

$title = "Simple Message Signature - Generate a pair of keys";

$rsa = new Crypt_RSA();
extract($rsa->createKey(2048));
$content = file_get_contents("lib/generate.html");
$content = str_replace("{PRIVATE_KEY}", $privatekey, $content);
$content = str_replace("{PUBLIC_KEY}", $publickey, $content);

// Display the page
require_once "../lib/corpus.php";

