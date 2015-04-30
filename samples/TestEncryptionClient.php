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


/*	$args = array(
		"Bucket"=>$bucket,
		"Key"=>$keyprefix."test.txt",
		"ACL"=>"public-read",
		"Content"=>array("content"=>"D://IMG.jpg")
	);
	$client->putObjectByFile($args);*/
$filelist = array();

for($begin = 0;$begin <1501712;){
	$index = rand(5000,100000);
	$range = array("start"=>$begin,"end"=>$begin+$index);
	$destFile = "D://testdown/IMG-down.jpg".$begin."-".($begin+$index);
	array_push($filelist,$destFile);
	$begin += ($index+1);
	$args = array(
		"Bucket"=>$bucket,
		"Key"=>$keyprefix."test.txt",
		"Range"=>$range,
		"WriteTo"=>$destFile
	);
	$client->getObject($args);
}

foreach ($filelist as $key => $value) {
	$handle = fopen($value,"r");
	$content = fread($handle,filesize($value));
	fclose($handle);

	file_put_contents("D://testdown/IMG.jpg",$content,FILE_APPEND);
	@unlink($value);
}
echo base64_encode(md5_file("D://IMG.jpg"))."\n";
echo base64_encode(md5_file("D://testdown/IMG.jpg"));
	
