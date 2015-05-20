<?php
require_once "../Ks3Client.class.php";
require_once "../Ks3EncryptionClient.class.php";

$client = new Ks3Client("1GL02rRYQxK8s7FQh8dV","2IDjaPOpFfkq5Zf9K4tKu8k5AKApY8S8eKV1zsRl");
testObject($client);
function testObject($client){
	$bucketName = "phpsdktestlijunwei";
	$objectKey = "dir/test/中文目录/@特殊字符!.txt";

	if(!$client->bucketExists(array("Bucket"=>$bucketName))){
        $client->createBucket(array("Bucket"=>$bucketName));
    }

    $args = array(
    	"Bucket"=>$bucketName,
    	"Key"=>$objectKey,
    	"Content"=>"1234",//要上传的内容
    	"ACL"=>"public-read",//可以设置访问权限,合法值,private、public-read
    	"ObjectMeta"=>array(//设置object的元数据,可以设置"Cache-Control","Content-Disposition","Content-Encoding","Content-Length","Content-MD5","Content-Type","Expires"。当设置了Content-Length时，请勿大于实际长度，如果小于实际长度，将只上传部分内容。
       		"Content-Type"=>"binay/ocet-stream",
       		"Content-Length"=>"1",
       		"Cache-Control"=>"no-cache",
       		"Content-Disposition"=>"attachment;",
       		"Content-Encoding"=>"gzip",
       		"Expires"=>"Mon, 23 Mar 9999 05:23:22 GMT"
        ),
    	"UserMeta"=>array(//可以设置object的用户元数据，需要以x-kss-meta-开头
        	"x-kss-meta-test"=>"test"
        )
    );

    $client->putObjectByContent($args);

    //ACL
    $acl = $client->getObjectAcl($args = array("Bucket"=>$bucketName,"Key"=>$objectKey));
    if($acl !== "public-read"){
    	throw new Exception("acl expected public-read but ".$acl);
    }
    $client->setObjectAcl($args = array("Bucket"=>$bucketName,"Key"=>$objectKey,"ACL"=>"private"));
    $acl = $client->getObjectAcl($args = array("Bucket"=>$bucketName,"Key"=>$objectKey));
    if($acl !== "private"){
    	throw new Exception("acl expected private but ".$acl);
    }
    //HEAD
    if(!$client->objectExists($args = array("Bucket"=>$bucketName,"Key"=>$objectKey))){
    	throw new Exception("object not exists!");
    	
    }
    $meta = $client->getObjectMeta($args = array("Bucket"=>$bucketName,"Key"=>$objectKey));
    $UserMeta = $meta["UserMeta"];
    assertEquals($UserMeta["x-kss-meta-test"],"test","UserMeta");
    $ObjectMeta = $meta["ObjectMeta"];
    assertEquals($ObjectMeta["Content-Type"],"binay/ocet-stream","ObjectMeta");
    assertEquals($ObjectMeta["Content-Length"],"1","ObjectMeta");
    assertEquals($ObjectMeta["Content-Disposition"],"attachment;","ObjectMeta");
    assertEquals($ObjectMeta["Content-Encoding"],"gzip","ObjectMeta");
    assertEquals($ObjectMeta["Expires"],"Mon, 23 Mar 9999 05:23:22 GMT","ObjectMeta");
    assertEquals($ObjectMeta["Cache-Control"],"no-cache","ObjectMeta");

    //DELETE 
    $client->deleteObject($args = array("Bucket"=>$bucketName,"Key"=>$objectKey));
    if($client->objectExists($args = array("Bucket"=>$bucketName,"Key"=>$objectKey))){
    	throw new Exception("object exists!");
    	
    }
}
function assertEquals($value,$expected,$info = NULL){
	if($value !== $expected){
		throw new Exception($info." expected ".$expected." but ".$value);
	}
}

?>