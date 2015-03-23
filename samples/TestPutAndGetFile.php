<?php
require_once "../Ks3Client.class.php";

$client = new Ks3Client("1GL02rRYQxK8s7FQh8dV","2IDjaPOpFfkq5Zf9K4tKu8k5AKApY8S8eKV1zsRl");
testObject($client);
function testObject($client){
	$bucketName = "phpsdktestlijunwei";
	$objectKey = "dir/test/中文目录/@特殊字符!.txt";
  $destinationKey = "copy";

  $putfile = "D://phpput";
  $getfile = "D://phpget";

  $putfileResource = fopen($putfile,"w");
  fwrite($putfileResource, "1234567890");
  fclose($putfileResource);

	if(!$client->bucketExists(array("Bucket"=>$bucketName))){
        $client->createBucket(array("Bucket"=>$bucketName));
  }

  $args = array(
    	"Bucket"=>$bucketName,
    	"Key"=>$objectKey,
    	"Content"=>array(
        "content"=>$putfile,
        "seek_position"=>2
      ),//要上传的内容
    	"ACL"=>"public-read",//可以设置访问权限,合法值,private、public-read
    	"ObjectMeta"=>array(//设置object的元数据,可以设置"Cache-Control","Content-Disposition","Content-Encoding","Content-Length","Content-MD5","Content-Type","Expires"。当设置了Content-Length时，请勿大于实际长度，如果小于实际长度，将只上传部分内容。
       		"Content-Type"=>"binay/ocet-stream",
       		"Content-Length"=>"5",
       		"Cache-Control"=>"no-cache",
       		"Content-Disposition"=>"attachment;",
       		"Content-Encoding"=>"gzip",
       		"Expires"=>"Mon, 23 Mar 9999 05:23:22 GMT"
        ),
    	"UserMeta"=>array(//可以设置object的用户元数据，需要以x-kss-meta-开头
        	"x-kss-meta-test"=>"test"
        ),
      "Adp"=>array(
          "NotifyURL"=>"http://10.4.2.38:19090/",
          "Adps"=>array(
          array(
            "Command"=>"tag=avop&f=mp4&res=1280x720&vbr=1000k&abr=128k",
            "Key"=>"野生动物-转码.3gp"
           )
         )
      ),
      "CallBack"=>array(
          "Url"=>"http://10.4.2.38:19090/",
          "BodyMagicVariables"=>array("bucket"=>"bucket","key"=>"key"),
          "BodyVariables"=>array("name"=>"lijunwei")
       )
    );
    $client->putObjectByFile($args);
    //Copy
    $args = array(
      "Bucket"=>$bucketName,
      "Key"=>$destinationKey,
      "CopySource"=>array(
        "Bucket"=>$bucketName,
        "Key"=>$objectKey
      )
    );
    $client->copyObject($args);
    //HEAD
    if(!$client->objectExists($args = array("Bucket"=>$bucketName,"Key"=>$objectKey))){
      throw new Exception("object not exists!");
      
    }
    $meta = $client->getObjectMeta($args = array("Bucket"=>$bucketName,"Key"=>$objectKey));
    $UserMeta = $meta["UserMeta"];
    assertEquals($UserMeta["x-kss-meta-test"],"test","UserMeta");
    $ObjectMeta = $meta["ObjectMeta"];
    assertEquals($ObjectMeta["Content-Type"],"binay/ocet-stream","ObjectMeta");
    assertEquals($ObjectMeta["Content-Length"],"5","ObjectMeta");
    assertEquals($ObjectMeta["Content-Disposition"],"attachment;","ObjectMeta");
    assertEquals($ObjectMeta["Content-Encoding"],"gzip","ObjectMeta");
    assertEquals($ObjectMeta["Expires"],"Mon, 23 Mar 9999 05:23:22 GMT","ObjectMeta");
    assertEquals($ObjectMeta["Cache-Control"],"no-cache","ObjectMeta");
    //GET
    $args = array(
      "Bucket"=>$bucketName,
      "Key"=>$destinationKey,
      "Range"=>"bytes=1-2",
       "WriteTo"=>$getfile //文件保存路径,必须提供。可以是resource
    );
    $client->getObject($args);

    $content = fgets(fopen($getfile, "r"));
    assertEquals($content,"45","content");

    //GET
    $args=array(
      "Bucket"=>$bucketName,
      "Key"=>$objectKey,
      "Options"=>array(
        "Expires"=>60*60*24*10,//过期时间
        "response-content-type"=>"application/xml"//覆盖返回的http header,支持的值"response-expires","response-content-encoding","response-content-disposition","response-content-language","response-content-type","response-cache-control"
        )
    );
    echo $client->generatePresignedUrl($args);
    //DELETE
    $args = array(
      "Bucket"=>$bucketName,
      "DeleteKeys"=>array($objectKey,$destinationKey)
    );
    $client->deleteObjects($args);
    if($client->objectExists($args = array("Bucket"=>$bucketName,"Key"=>$objectKey))){
      throw new Exception("object exists!");  
    }
    if($client->objectExists($args = array("Bucket"=>$bucketName,"Key"=>$destinationKey))){
      throw new Exception("object exists!");  
    }
  }
  function assertEquals($value,$expected,$info = NULL){
  if($value !== $expected){
    throw new Exception($info." expected ".$expected." but ".$value);
  }
}
?>