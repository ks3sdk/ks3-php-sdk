<?php
require_once "../Ks3EncryptionClient.class.php";
require_once "../Ks3Client.class.php";
$bucket = "test-encryption";
$keyprefix = "php/";

$filename = "D://secret.key";
$handle = fopen($filename, "r");//读取二进制文件时，需要将第二个参数设置成'rb'
    
//通过filesize获得文件大小，将整个文件一下子读到一个字符串中
$contents = fread($handle, filesize ($filename));
fclose($handle);

$client = new Ks3EncryptionClient("lMQTr0hNlMpB0iOk/i+x","D4CsYLs75JcWEjbiI22zR3P7kJ/+5B1qdEje7A7I",$contents);
