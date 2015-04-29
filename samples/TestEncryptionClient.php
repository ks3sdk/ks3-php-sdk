<?php
require_once "../Ks3EncryptionClient.class.php";
require_once "../Ks3Client.class.php";
$bucket = "test-encryption";
$keyprefix = "php/";

$filename = "D://secret.key";
$handle = fopen($filename, "r");
$contents = fread($handle, filesize ($filename));
fclose($handle);


/*$filename = "D://public.key";
$handle = fopen($filename, "r");
$pk = fread($handle, filesize ($filename));
fclose($handle);

$filename = "D://private.key";
$handle = fopen($filename, "r");
$sk = fread($handle, filesize ($filename));
fclose($handle);

$contents = array($pk,$sk);*/


$client = new Ks3EncryptionClient("lMQTr0hNlMpB0iOk/i+x","D4CsYLs75JcWEjbiI22zR3P7kJ/+5B1qdEje7A7I",$contents);


	$args = array(
		"Bucket"=>$bucket,
		"Key"=>$keyprefix."test.txt",
		"ACL"=>"public-read",
		"Content"=>"1234"
	);
	$client->putObjectByContent($args);

	$args = array(
		"Bucket"=>$bucket,
		"Key"=>$keyprefix."test.txt",
		"WriteTo"=>"D://test-down.txt"
	);
	$client->getObject($args);
