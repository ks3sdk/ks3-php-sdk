<?php
require_once "../Ks3Client.class.php";
require_once "../core/Utils.class.php";

$client = new Ks3Client("1GL02rRYQxK8s7FQh8dV","2IDjaPOpFfkq5Zf9K4tKu8k5AKApY8S8eKV1zsRl");
testObject($client);
function testObject($client){

	$bucketName = "phpsdktestlijunwei";
	$objectKey = "dir/test/中文目录/@特殊字符!.txt";
	$file = "D://file.rar";

	if(!$client->bucketExists(array("Bucket"=>$bucketName))){
        $client->createBucket(array("Bucket"=>$bucketName));
 	}

	$args = array(
		"Bucket"=>$bucketName,
		"Key"=>$objectKey,
		"UserMeta"=>array(
			"x-kss-meta-test"=>"test"
			),
		"ObjectMeta"=>array(
       		"Content-Type"=>"binay/ocet-stream",
       		"Cache-Control"=>"no-cache",
       		"Content-Disposition"=>"attachment;",
       		"Content-Encoding"=>"gzip",
       		"Expires"=>"Mon, 23 Mar 9999 05:23:22 GMT"
			)
		);
	$uploadid = $client->initMultipartUpload($args);
	print_r($uploadid);
	$uploadid = $uploadid["UploadId"];
	echo $uploadid."\r\n";
	//开始上传
	$partsize = 1024*1024*5;
	$total = Utils::getFileSize($file);
	$count = (int)($total/$partsize+1);
	echo $count."\r\n";
	for($i = 0;$i < $count;$i++){
		echo "upload".$i."\r\n";
		$args=array(
			"Bucket"=>$bucketName,
			"Key"=>$objectKey,
			"Options"=>array(
				"partNumber"=>$i+1,
				"uploadId"=>$uploadid
				),
			"ObjectMeta"=>array(
				"Content-Length"=>$partsize
				),
			"Content"=>array(
				"content"=>$file,
				"seek_position"=>$partsize*$i
				)
			);
		$etag = $client->uploadPart($args);
		print_r($etag);
		$etag = $etag["ETag"];
	}
	$parts = $client->listParts(array("Bucket"=>$bucketName,"Key"=>$objectKey,"Options"=>array("uploadId"=>$uploadid)));
	print_r($parts);
	$uploads = $client->listMutipartUploads(array("Bucket"=>$bucketName,"Options"=>array("max-uploads"=>1)));
	print_r($uploads);
	//结束上传
	$args=array(
		"Bucket"=>$bucketName,
		"Key"=>$objectKey,
		"Options"=>array("uploadId"=>$uploadid),
		"Parts"=>$parts["Parts"],
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
	$result = $client->completeMultipartUpload($args);
	print_r($result);
	//HEAD
    if(!$client->objectExists($args = array("Bucket"=>$bucketName,"Key"=>$objectKey))){
      throw new Exception("object not exists!");
      
    }
    $meta = $client->getObjectMeta($args = array("Bucket"=>$bucketName,"Key"=>$objectKey));
    $UserMeta = $meta["UserMeta"];
    assertEquals($UserMeta["x-kss-meta-test"],"test","UserMeta");
    $ObjectMeta = $meta["ObjectMeta"];
    $filestate = fstat(fopen($file,"r"));
    assertEquals($ObjectMeta["Content-Type"],"binay/ocet-stream","ObjectMeta");
    assertEquals($ObjectMeta["Content-Length"],$filestate["size"],"ObjectMeta");
    assertEquals($ObjectMeta["Content-Disposition"],"attachment;","ObjectMeta");
    assertEquals($ObjectMeta["Content-Encoding"],"gzip","ObjectMeta");
    assertEquals($ObjectMeta["Expires"],"Mon, 23 Mar 9999 05:23:22 GMT","ObjectMeta");
    assertEquals($ObjectMeta["Cache-Control"],"no-cache","ObjectMeta");
}
  function assertEquals($value,$expected,$info = NULL){
  if($value != $expected){
    throw new Exception($info." expected ".$expected." but ".$value);
  }
  }
?>