<?php
require_once "../Ks3Client.class.php";
require_once "../core/Utils.class.php";

$client = new Ks3Client("2HITWMQXL2VBB3XMAEHQ","ilZQ9p/NHAK1dOYA/dTKKeIqT/t67rO6V2PrXUNr");
//print_r(listBuckets($client));
//print_r(deleteBucket($client));
//print_r(deleteBucketCORS($client));
//print_r(listObjects($client));
//print_r(getBucketAcl($client));
//print_r(getBucketCORS($client));
//print_r(getBucketLocation($client));
//print_r(getBucketLogging($client));
//print_r(bucketExists($client));
//print_r(createBucket($client));
//print_r(setBucketAcl($client));
//print_r(setBucketCORS($client));
//print_r(setBucketLogging($client));
//print_r(deleteObject($client));
//print_r(deleteObjects($client));
//print_r(getObject($client));
print_r(getObjectAcl($client));
//print_r(objectExists($client));
//print_r(getObjectMeta($client));
//print_r(setObjectAcl($client));
//print_r(copyObject($client));
//print_r(putObjectByFile($client));
//print_r(multipartUpload($client));
//print_r(abortMultipartUpload($client));
//print_r(generatePresignedUrl($client));
//print_r(putObjectWithAdpAndCallBack($client));
//print_r(multipartUploadWithAdpAndCallBack($client));
//print_r(putAdp($client));
//print_r(postObject($client));
function listBuckets($client){
	return $client->listBuckets();
}
function deleteBucket($client){
	return $client->deleteBucket(array("Bucket"=>"ksc-scm"));
}
function deleteBucketCORS($client){
	return $client->deleteBucketCORS(array("Bucket"=>"ksc-scm"));
}
function listObjects($client){
	$args = array(
		"Bucket"=>"lijunwei.test",
		"Options"=>array(
			//"prefix"=>"dir/",
			"max-keys"=>4,
			//"marker"=>"123.pdf",
			"delimiter"=>"/"
			)
		);
	return $client->listObjects($args);
}
function getBucketAcl($client){
	return $client->getBucketAcl(array("Bucket"=>"aaphp"));
}
function getBucketCORS($client){
	return $client->getBucketCORS(array("Bucket"=>"ksc-scm"));
}
function getBucketLocation($client){
	return $client->getBucketLocation(array("Bucket"=>"ksc-scm"));
}
function getBucketLogging($client){
	return $client->getBucketLogging(array("Bucket"=>"ksc-scm"));
}
function bucketExists($client){
	$args = array("Bucket"=>"ksc-scm");
	return $client->bucketExists($args);
}
function createBucket($client){
	$args = array(
		"Bucket"=>"ksc-scm",
		"ACL"=>"private"
		);
	return $client->createBucket($args);
}
function setBucketAcl($client){
	$args = array(
		"Bucket"=>"ksc-scm",
		"ACL"=>"private"
		);
	return $client->setBucketAcl($args);
}
function setBucketCORS($client){
	$args = array(
		"Bucket"=>"ksc-scm",
		"CORS"=>array(
			array(
				"AllowedMethod"=>array("GET","PUT"),
				"AllowedOrigin"=>array("http://www.kingsoft.com"),
				"AllowedHeader"=>array("*"),
				"ExposeHeader"=>array("*"),
				"MaxAgeSeconds"=>10
			),
			array(
				"AllowedMethod"=>array("GET","PUT"),
				"AllowedOrigin"=>array("*"),
				"AllowedHeader"=>array("*"),
				"ExposeHeader"=>array("*"),
				"MaxAgeSeconds"=>10
			)
		)
	);
	return $client->setBucketCORS($args);
}
function setBucketLogging($client){
	$args = array(
		"Bucket"=>"ksc-scm",
		"BucketLogging"=>array(
			"Enable"=>TRUE,
			"TargetBucket"=>"ksc-scm",
			"TargetPrefix"=>"X-KSS"
			)
		);
	return $client->setBucketLogging($args);
}
function deleteObject($client){
	$args = array(
		"Bucket"=>"ksc-scm",
		"Key"=>"123.pdf"
		);
	return $client->deleteObject($args);
}
function deleteObjects($client){
	$args = array(
		"Bucket"=>"ksc-scm",
		"DeleteKeys"=>array("copy/test.zip","copy/123.doc")
		);
	return $client->deleteObjects($args);
}
function getObject($client){
	$args = array(
		"Bucket"=>"aaphp",
		"Key"=>"multi.exe",
		"Range"=>array(
			"start"=>NULL,
			"end"=>4,
			),
		"WriteTo"=>"D://test.zip"
		);
	return $client->getObject($args);
}
function getObjectAcl($client){
	$args = array(
		"Bucket"=>"aaphp",
		"Key"=>"鲁谷 Tunnelblick VPN 配置.tblk.zip"
		);
	return $client->getObjectAcl($args);
}
function objectExists($client){
	$args = array(
		"Bucket"=>"ksc-scm",
		"Key"=>"123.pdf"
		);
	return $client->objectExists($args);
}
function getObjectMeta($client){
	$args = array(
		"Bucket"=>"aaphp",
		"Key"=>"test.zip"
		);
	return $client->getObjectMeta($args);
}
function setObjectAcl($client){
	$args = array(
		"Bucket"=>"aaphp",
		"Key"=>"test.zip",
		"ACL"=>"private"
		);
	return $client->setObjectAcl($args);
}
function copyObject($client){
	$args = array(
		"Bucket"=>"aaphp",
		"Key"=>"copy/test.zip",
		"CopySource"=>array(
			"Bucket"=>"aaphp",
			"Key"=>"test.zip"
			)
		);
	return $client->copyObject($args);
}
function putObjectByFile($client){
	$file = "D://phpput";
	if(Utils::chk_chinese($file)){
		$file = iconv('utf-8','gbk',$file);
	}
	$content = $file;
	$args = array(
		"Bucket"=>"aaphp",
		"Key"=>"stream_upload1.txt",
		"ACL"=>"public-read",
		"ObjectMeta"=>array(
			"Content-Type"=>"image/jpg",//âÃâââ¬Â¼â¬ÃºâÃâââ¬Â¼âÃ»âÃâââ¬Â¼â¬Ã³ÎÃªÃâÃ§â¬Â¼âÂ¿âÃâââ¬Â¼â¬Ã³âÃâââ¬Â¼âÃ±âÃâââ¬Â¼â¬ÃºâÃâââ¬Â¼âÃ¦âÃâââ¬Â¼â¬Ã³âÃâââ¬Â¼âââÃâââ¬Â¼â¬Ã³ÎÃªÃâÃ§â¬Â¼ÎÃÃ¡0-10âÃâââ¬Â¼â¬ÃºâÃâââ¬Â¼âÃ»âÃâââ¬Â¼â¬Ã³ÎÃªÃâÃ§â¬Â¼ÎÃ«Ã¡ÎÃªÃâÂ½â¬Â¼âÃ§ÎÃªÃâÃ§â¬Â¼âÃ¤ÎÃªÃâÃ§â¬Â¼âÂ«âÃâââ¬Â¼â¬ÃºâÃâââ¬Â¼âÃ¡âÃâââ¬Â¼ÎÃÃ³ÎÃªÃâÃ§â¬Â¼ÎÃÃ¡ÎÃªÃâÂ½â¬Â¼âÃ§ÎÃªÃâÃ§â¬Â¼âÃ¤ÎÃªÃâÃ§â¬Â¼ââ¢,
			),
		"Content"=>array(
			"content"=>$file,
			"seek_position"=>0
			),
	);
	return $client->putObjectByFile($args);
}
function multipartUpload($client){
	$args = array(
		"Bucket"=>"aaphp",
		"Key"=>"multi.zip",
		"UserMeta"=>array(
			"x-kss-meta-test"=>"example"
			),
		"ObjectMeta"=>array(
			"Content-Type"=>"text/plain"
			)
		);
	$uploadid = $client->initMultipartUpload($args);
	print_r($uploadid);
	$uploadid = $uploadid["UploadId"];
	echo $uploadid."\r\n";
	//âÃâââ¬Â¼â¬ÃºâÃâââ¬Â¼âÃ»âÃâââ¬Â¼â¬Ã³âÃâââ¬Â¼ââÎÃªÃâÂ½â¬Â¼âÃ§ÎÃªÃâÃ§â¬Â¼âÃ§âÃâââ¬Â¼âÃâÃâââ¬Â¼â¬ÃºâÃâââ¬Â¼âÃ»âÃâââ¬Â¼â¬Ã³âÃâââ¬Â¼âÃ­ÎÃªÃâÂ½â¬Â¼âÃ§ÎÃªÃâÃ§â¬Â¼âÃ¤âÃâââ¬Â¼ââ¤âÃâââ¬Â¼â¬ÃºâÃâââ¬Â¼âÃ¦âÃâââ¬Â¼â¬Ã³âÃâââ¬Â¼âââÃâââ¬Â¼ÎÃÃ³ÎÃªÃâÃ§â¬Â¼ÎÃÃ¡âÃâââ¬Â¼â¬ÃºâÃâââ¬Â¼âÃ¦âÃâââ¬Â¼â¬Ã³âÃâââ¬Â¼âââÃâââ¬Â¼â¬Ã³ÎÃªÃâÃ§â¬Â¼ÎÃÃ¡

	$file = "D://âÃâââ¬Â¼â¬ÃºâÃâââ¬Â¼âÂ£ÎÃªÃâÂ½â¬Â¼âÃ§ÎÃªÃâÃ§â¬Â¼âÃ¤ÎÃªÃâÃ§â¬Â¼âÂ¼âÃâââ¬Â¼â¬Ã³âÃâââ¬Â¼âÂ¬âÃâââ¬Â¼â¬ÃºâÃâââ¬Â¼âÃ»âÃâââ¬Â¼â¬Ã³âÃâââ¬Â¼ââ¡âÃâââ¬Â¼â¬Ã³âÃâââ¬Â¼ââ¢âÃâââ¬Â¼â¬ÃºâÃâââ¬Â¼âÂ£ÎÃªÃâÂ½â¬Â¼âÃ§ÎÃªÃâÃ§â¬Â¼âÃ¤ÎÃªÃâÃ§â¬Â¼âÂ¼ÎÃªÃâÂ½â¬Â¼âÃ§ÎÃªÃâÃ§â¬Â¼âÃ¤âÃâââ¬Â¼âÃ âÃâââ¬Â¼â¬ÃºâÃâââ¬Â¼âÃ¦âÃâââ¬Â¼â¬Ã³âÃâââ¬Â¼ââ¡âÃâââ¬Â¼â¬Ã³âÃâââ¬Â¼âââÃâââ¬Â¼â¬ÃºâÃâââ¬Â¼âÃ»âÃâââ¬Â¼â¬Ã³âÃâââ¬Â¼âÃ¦âÃâââ¬Â¼â¬Ã³âÃâââ¬Â¼ââ¤.rar";
	if(Utils::chk_chinese($file)){
		$file = iconv('utf-8','gbk',$file);
	}
	$total = Utils::getFileSize($file);
	$partsize = 1024*1024*5;
	$count = (int)($total/$partsize+1);
	echo $count."\r\n";
	for($i = 0;$i < $count;$i++){
		echo "upload".$i."\r\n";
		$args=array(
			"Bucket"=>"aaphp",
			"Key"=>"multi.zip",
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
	$parts = $client->listParts(array("Bucket"=>"aaphp","Key"=>"multi.zip","Options"=>array("uploadId"=>$uploadid)));
	print_r($parts);
	//âÃâââ¬Â¼â¬ÃºâÃâââ¬Â¼âÃ­âÃâââ¬Â¼â¬Ã³âÃâââ¬Â¼ââ¡ÎÃªÃâÂ½â¬Â¼âÃ§ÎÃªÃâÃ§â¬Â¼âÃ¤ÎÃªÃâÃ§â¬Â¼âââÃâââ¬Â¼â¬ÃºâÃâââ¬Â¼âÂ£âÃâââ¬Â¼â¬Ã³ÎÃªÃâÃ§â¬Â¼ââ£âÃâââ¬Â¼ÎÃÃ³âÃâââ¬Â¼âââÃâââ¬Â¼â¬ÃºâÃâââ¬Â¼âÃ¦âÃâââ¬Â¼â¬Ã³âÃâââ¬Â¼âââÃâââ¬Â¼ÎÃÃ³ÎÃªÃâÃ§â¬Â¼ÎÃÃ¡âÃâââ¬Â¼â¬ÃºâÃâââ¬Â¼âÃ¦âÃâââ¬Â¼â¬Ã³âÃâââ¬Â¼âââÃâââ¬Â¼â¬Ã³ÎÃªÃâÃ§â¬Â¼ÎÃÃ¡
	$args=array(
		"Bucket"=>"aaphp",
		"Key"=>"multi.zip",
		"Options"=>array("uploadId"=>$uploadid),
		"Parts"=>$parts["Parts"]
		);
	$result = $client->completeMultipartUpload($args);
	print_r($result);
}
function abortMultipartUpload($client){
	$args=array(
		"Bucket"=>"aaphp",
		"Key"=>"multi.zip",
		"Options"=>array("uploadId"=>"1234")
		);
	return $client->abortMultipartUpload($args);
}
function generatePresignedUrl($client){
	$args=array(
		"Bucket"=>"aaphp",
		"Key"=>"multi.zip",
		"Options"=>array(
			"Expires"=>60*60*24*10,
			"response-content-type"=>"application/xml"
			)
		);
	return $client->generatePresignedUrl($args);
}
function putObjectWithAdpAndCallBack($client){
	$content = "D://âÃâââ¬Â¼â¬ÃºâÃâââ¬Â¼âÃ³ÎÃªÃâÂ½â¬Â¼âÃ§ÎÃªÃâÃ§â¬Â¼âÃ¤âÃâââ¬Â¼âÃ âÃâââ¬Â¼ÎÃÃ³âÃâââ¬Â¼ââ£âÃâââ¬Â¼â¬ÃºâÃâââ¬Â¼âÃ­ÎÃªÃâÂ½â¬Â¼âÃ§ÎÃªÃâÃ§â¬Â¼âÃ¤ÎÃªÃâÃ§â¬Â¼ââ£âÃâââ¬Â¼ÎÃÃ³âÃâââ¬Â¼âââÃâââ¬Â¼â¬ÃºâÃâââ¬Â¼âÃ»âÃâââ¬Â¼ÎÃÃ³ÎÃªÃâÃ§â¬Â¼ÎÃÃ¡âÃâââ¬Â¼â¬Ã³âÃâââ¬Â¼âÃ¡âÃâââ¬Â¼â¬ÃºâÃâââ¬Â¼âÃ­ÎÃªÃâÂ½â¬Â¼âÃ§ÎÃªÃâÃ§â¬Â¼âÃ¤âÃâââ¬Â¼âÂ¬âÃâââ¬Â¼â¬Ã³âÃâââ¬Â¼âÃ³.3gp";
	$args = array(
		"Bucket"=>"aaphp",
		"Key"=>"âÃâââ¬Â¼â¬ÃºâÃâââ¬Â¼âÃ³ÎÃªÃâÂ½â¬Â¼âÃ§ÎÃªÃâÃ§â¬Â¼âÃ¤âÃâââ¬Â¼âÃ âÃâââ¬Â¼ÎÃÃ³âÃâââ¬Â¼ââ£âÃâââ¬Â¼â¬ÃºâÃâââ¬Â¼âÃ­ÎÃªÃâÂ½â¬Â¼âÃ§ÎÃªÃâÃ§â¬Â¼âÃ¤ÎÃªÃâÃ§â¬Â¼ââ£âÃâââ¬Â¼ÎÃÃ³âÃâââ¬Â¼âââÃâââ¬Â¼â¬ÃºâÃâââ¬Â¼âÃ»âÃâââ¬Â¼ÎÃÃ³ÎÃªÃâÃ§â¬Â¼ÎÃÃ¡âÃâââ¬Â¼â¬Ã³âÃâââ¬Â¼âÃ¡âÃâââ¬Â¼â¬ÃºâÃâââ¬Â¼âÃ­ÎÃªÃâÂ½â¬Â¼âÃ§ÎÃªÃâÃ§â¬Â¼âÃ¤âÃâââ¬Â¼âÂ¬âÃâââ¬Â¼â¬Ã³âÃâââ¬Â¼âÃ³.3gp",
		"ACL"=>"public-read",
		"Content"=>array(
			"content"=>$content
			),
		"Adp"=>array(
			"NotifyURL"=>"http://10.4.2.38:19090/",
			"Adps"=>array(
				array(
					"Command"=>"tag=avop&f=mp4&res=1280x720&vbr=1000k&abr=128k",
					"Key"=>"âÃâââ¬Â¼â¬ÃºâÃâââ¬Â¼âÃ³ÎÃªÃâÂ½â¬Â¼âÃ§ÎÃªÃâÃ§â¬Â¼âÃ¤âÃâââ¬Â¼âÃ âÃâââ¬Â¼ÎÃÃ³âÃâââ¬Â¼ââ£âÃâââ¬Â¼â¬ÃºâÃâââ¬Â¼âÃ­ÎÃªÃâÂ½â¬Â¼âÃ§ÎÃªÃâÃ§â¬Â¼âÃ¤ÎÃªÃâÃ§â¬Â¼ââ£âÃâââ¬Â¼ÎÃÃ³âÃâââ¬Â¼âââÃâââ¬Â¼â¬ÃºâÃâââ¬Â¼âÃ»âÃâââ¬Â¼ÎÃÃ³ÎÃªÃâÃ§â¬Â¼ÎÃÃ¡âÃâââ¬Â¼â¬Ã³âÃâââ¬Â¼âÃ¡âÃâââ¬Â¼â¬ÃºâÃâââ¬Â¼âÃ­ÎÃªÃâÂ½â¬Â¼âÃ§ÎÃªÃâÃ§â¬Â¼âÃ¤âÃâââ¬Â¼âÂ¬âÃâââ¬Â¼â¬Ã³âÃâââ¬Â¼âÃ³-âÃâââ¬Â¼â¬ÃºâÃâââ¬Â¼âÃ¡âÃâââ¬Â¼â¬Ã³âÃâââ¬Â¼ââ£âÃâââ¬Â¼â¬Ã³âÃâââ¬Â¼âÃâÃâââ¬Â¼â¬ÃºâÃâââ¬Â¼âÃ­âÃâââ¬Â¼â¬Ã³ÎÃªÃâÃ§â¬Â¼ÎÃÃ¡âÃâââ¬Â¼â¬Ã³ÎÃªÃâÃ§â¬Â¼âÃ .3gp"
				)
			)
		),
		"CallBack"=>array(
			"Url"=>"http://10.4.2.38:19090/",
			"BodyMagicVariables"=>array("bucket"=>"bucket","key"=>"key"),
			"BodyVariables"=>array("name"=>"lijunwei")
		)
	);
	return $client->putObjectByFile($args);
}
function  multipartUploadWithAdpAndCallBack($client){
	$args = array(
		"Bucket"=>"aaphp",
		"Key"=>"multi.zip",
		"UserMeta"=>array(
			"x-kss-meta-test"=>"example"
			),
		"ObjectMeta"=>array(
			"Content-Type"=>"text/plain"
			)
		);
	$uploadid = $client->initMultipartUpload($args);
	print_r($uploadid);
	$uploadid = $uploadid["UploadId"];
	echo $uploadid."\r\n";
	//âÃâââ¬Â¼â¬ÃºâÃâââ¬Â¼âÃ»âÃâââ¬Â¼â¬Ã³âÃâââ¬Â¼ââÎÃªÃâÂ½â¬Â¼âÃ§ÎÃªÃâÃ§â¬Â¼âÃ§âÃâââ¬Â¼âÃâÃâââ¬Â¼â¬ÃºâÃâââ¬Â¼âÃ»âÃâââ¬Â¼â¬Ã³âÃâââ¬Â¼âÃ­ÎÃªÃâÂ½â¬Â¼âÃ§ÎÃªÃâÃ§â¬Â¼âÃ¤âÃâââ¬Â¼ââ¤âÃâââ¬Â¼â¬ÃºâÃâââ¬Â¼âÃ¦âÃâââ¬Â¼â¬Ã³âÃâââ¬Â¼âââÃâââ¬Â¼ÎÃÃ³ÎÃªÃâÃ§â¬Â¼ÎÃÃ¡âÃâââ¬Â¼â¬ÃºâÃâââ¬Â¼âÃ¦âÃâââ¬Â¼â¬Ã³âÃâââ¬Â¼âââÃâââ¬Â¼â¬Ã³ÎÃªÃâÃ§â¬Â¼ÎÃÃ¡

	$file = "D://âÃâââ¬Â¼â¬ÃºâÃâââ¬Â¼âÃ³ÎÃªÃâÂ½â¬Â¼âÃ§ÎÃªÃâÃ§â¬Â¼âÃ¤âÃâââ¬Â¼âÃ âÃâââ¬Â¼ÎÃÃ³âÃâââ¬Â¼ââ£âÃâââ¬Â¼â¬ÃºâÃâââ¬Â¼âÃ­ÎÃªÃâÂ½â¬Â¼âÃ§ÎÃªÃâÃ§â¬Â¼âÃ¤ÎÃªÃâÃ§â¬Â¼ââ£âÃâââ¬Â¼ÎÃÃ³âÃâââ¬Â¼âââÃâââ¬Â¼â¬ÃºâÃâââ¬Â¼âÃ»âÃâââ¬Â¼ÎÃÃ³ÎÃªÃâÃ§â¬Â¼ÎÃÃ¡âÃâââ¬Â¼â¬Ã³âÃâââ¬Â¼âÃ¡âÃâââ¬Â¼â¬ÃºâÃâââ¬Â¼âÃ­ÎÃªÃâÂ½â¬Â¼âÃ§ÎÃªÃâÃ§â¬Â¼âÃ¤âÃâââ¬Â¼âÂ¬âÃâââ¬Â¼â¬Ã³âÃâââ¬Â¼âÃ³.3gp";
	if(Utils::chk_chinese($file)){
		$file = iconv('utf-8','gbk',$file);
	}
	$partsize = 1024*1024*5;
	$total = Utils::getFileSize($file);
	$count = (int)($total/$partsize+1);
	echo $count."\r\n";
	for($i = 0;$i < $count;$i++){
		echo "upload".$i."\r\n";
		$args=array(
			"Bucket"=>"aaphp",
			"Key"=>"multi.zip",
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
	$parts = $client->listParts(array("Bucket"=>"aaphp","Key"=>"multi.zip","Options"=>array("uploadId"=>$uploadid)));
	print_r($parts);
	//âÃâââ¬Â¼â¬ÃºâÃâââ¬Â¼âÃ­âÃâââ¬Â¼â¬Ã³âÃâââ¬Â¼ââ¡ÎÃªÃâÂ½â¬Â¼âÃ§ÎÃªÃâÃ§â¬Â¼âÃ¤ÎÃªÃâÃ§â¬Â¼âââÃâââ¬Â¼â¬ÃºâÃâââ¬Â¼âÂ£âÃâââ¬Â¼â¬Ã³ÎÃªÃâÃ§â¬Â¼ââ£âÃâââ¬Â¼ÎÃÃ³âÃâââ¬Â¼âââÃâââ¬Â¼â¬ÃºâÃâââ¬Â¼âÃ¦âÃâââ¬Â¼â¬Ã³âÃâââ¬Â¼âââÃâââ¬Â¼ÎÃÃ³ÎÃªÃâÃ§â¬Â¼ÎÃÃ¡âÃâââ¬Â¼â¬ÃºâÃâââ¬Â¼âÃ¦âÃâââ¬Â¼â¬Ã³âÃâââ¬Â¼âââÃâââ¬Â¼â¬Ã³ÎÃªÃâÃ§â¬Â¼ÎÃÃ¡
	$args=array(
		"Bucket"=>"aaphp",
		"Key"=>"multi.zip",
		"Options"=>array("uploadId"=>$uploadid),
		"Parts"=>$parts["Parts"],
		"Adp"=>array(
			"NotifyURL"=>"http://10.4.2.38:19090/",
			"Adps"=>array(
				array(
					"Command"=>"tag=avop&f=mp4&res=1280x720&vbr=1000k&abr=128k",
					"Key"=>"âÃâââ¬Â¼â¬ÃºâÃâââ¬Â¼âÃ³ÎÃªÃâÂ½â¬Â¼âÃ§ÎÃªÃâÃ§â¬Â¼âÃ¤âÃâââ¬Â¼âÃ âÃâââ¬Â¼ÎÃÃ³âÃâââ¬Â¼ââ£âÃâââ¬Â¼â¬ÃºâÃâââ¬Â¼âÃ­ÎÃªÃâÂ½â¬Â¼âÃ§ÎÃªÃâÃ§â¬Â¼âÃ¤ÎÃªÃâÃ§â¬Â¼ââ£âÃâââ¬Â¼ÎÃÃ³âÃâââ¬Â¼âââÃâââ¬Â¼â¬ÃºâÃâââ¬Â¼âÃ»âÃâââ¬Â¼ÎÃÃ³ÎÃªÃâÃ§â¬Â¼ÎÃÃ¡âÃâââ¬Â¼â¬Ã³âÃâââ¬Â¼âÃ¡âÃâââ¬Â¼â¬ÃºâÃâââ¬Â¼âÃ­ÎÃªÃâÂ½â¬Â¼âÃ§ÎÃªÃâÃ§â¬Â¼âÃ¤âÃâââ¬Â¼âÂ¬âÃâââ¬Â¼â¬Ã³âÃâââ¬Â¼âÃ³-âÃâââ¬Â¼â¬ÃºâÃâââ¬Â¼âÃ¡âÃâââ¬Â¼â¬Ã³âÃâââ¬Â¼ââ£âÃâââ¬Â¼â¬Ã³âÃâââ¬Â¼âÃâÃâââ¬Â¼â¬ÃºâÃâââ¬Â¼âÃ­âÃâââ¬Â¼â¬Ã³ÎÃªÃâÃ§â¬Â¼ÎÃÃ¡âÃâââ¬Â¼â¬Ã³ÎÃªÃâÃ§â¬Â¼âÃ .3gp"
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
	$taskid = $result["TaskID"];
	$task = $client->getAdp(array("TaskID"=>$taskid));
	print_r($task);
}
function putAdp($client){
	$args=array(
		"Bucket"=>"aaphp",
		"Key"=>"multi.zip",
		"Adp"=>array(
			"NotifyURL"=>"http://10.4.2.38:19090/",
			"Adps"=>array(
				array(
					"Command"=>"tag=avop&f=mp4&res=1280x720&vbr=1000k&abr=128k",
					"Key"=>"âÃâââ¬Â¼â¬ÃºâÃâââ¬Â¼âÃ³ÎÃªÃâÂ½â¬Â¼âÃ§ÎÃªÃâÃ§â¬Â¼âÃ¤âÃâââ¬Â¼âÃ âÃâââ¬Â¼ÎÃÃ³âÃâââ¬Â¼ââ£âÃâââ¬Â¼â¬ÃºâÃâââ¬Â¼âÃ­ÎÃªÃâÂ½â¬Â¼âÃ§ÎÃªÃâÃ§â¬Â¼âÃ¤ÎÃªÃâÃ§â¬Â¼ââ£âÃâââ¬Â¼ÎÃÃ³âÃâââ¬Â¼âââÃâââ¬Â¼â¬ÃºâÃâââ¬Â¼âÃ»âÃâââ¬Â¼ÎÃÃ³ÎÃªÃâÃ§â¬Â¼ÎÃÃ¡âÃâââ¬Â¼â¬Ã³âÃâââ¬Â¼âÃ¡âÃâââ¬Â¼â¬ÃºâÃâââ¬Â¼âÃ­ÎÃªÃâÂ½â¬Â¼âÃ§ÎÃªÃâÃ§â¬Â¼âÃ¤âÃâââ¬Â¼âÂ¬âÃâââ¬Â¼â¬Ã³âÃâââ¬Â¼âÃ³-âÃâââ¬Â¼â¬ÃºâÃâââ¬Â¼âÃ¡âÃâââ¬Â¼â¬Ã³âÃâââ¬Â¼ââ£âÃâââ¬Â¼â¬Ã³âÃâââ¬Â¼âÃâÃâââ¬Â¼â¬ÃºâÃâââ¬Â¼âÃ­âÃâââ¬Â¼â¬Ã³ÎÃªÃâÃ§â¬Â¼ÎÃÃ¡âÃâââ¬Â¼â¬Ã³ÎÃªÃâÃ§â¬Â¼âÃ .3gp"
				)
			)
		)
	);
	$result = $client->putAdp($args);
	print_r($result);
	$taskid = $result["TaskID"];
	$task = $client->getAdp(array("TaskID"=>$taskid));
	print_r($task);
}
function postObject($client){

	$postData = array(
		"key"=>"~!@\\#$\%^&*()_+-=qw",
		"acl"=>"public-read"
		);
	$unKnowData=array("122","334");

	print_r($client->postObject("ksc-scm",$postData,$unKnowData));
}
?>