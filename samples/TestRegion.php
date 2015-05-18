<?php
define("VHOST",FALSE);
require_once "../Ks3Client.class.php";
require_once "../core/Utils.class.php";

$client = new Ks3Client("2HITWMQXL2VBB3XMAEHQ","ilZQ9p/NHAK1dOYA/dTKKeIqT/t67rO6V2PrXUNr","kss.hangzhou.ksyun.com");


$bucketName = "abeijing";
$objectKey = "dir/test/中文目录/@特殊字符!.txt";

$putfile = "D://IMG.jpg";

$client->getService();

$args = array(
    	"Bucket"=>$bucketName,
    	"Key"=>$objectKey,
    	"Content"=>array(
        "content"=>$putfile,
      ),//要上传的内容
    	"ACL"=>"public-read",//可以设置访问权限,合法值,private、public-read
    );
    //$client->putObjectByFile($args);

/*$client->deleteObjects(array(
		"Bucket"=>"ksc-scm",
		"DeleteKeys"=>array("copy/test.zip","copy/123.doc")
		));*/

?>