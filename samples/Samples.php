<?php
require_once "../Ks3Client.class.php";

$client = new Ks3Client("2HITWMQXL2VBB3XMAEHQ","ilZQ9p/NHAK1dOYA/dTKKeIqT/t67rO6V2PrXUNr");


//_format($client->listBuckets());
//_format($client->deleteBucket(array("Bucket"=>"vcervre")));
//_format($client->createBucket(array("Bucket"=>"phptestbucket.lijunwei.001","Location"=>"BEIJING","ACL"=>"public-read")));
//_format($client->setBucketAcl(array("Bucket"=>"ksc-scm","ACL"=>"public-read-write")));
/*_format($client->setBucketCORS(array("Bucket"=>"ksc-scm",
	"CORS"=>array(
			array(
				"AllowedMethod"=>array("GET","PUT"),
				"AllowedOrigin"=>array("http://www.baidu.com"),
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
	)));*/
/*_format($client->setBucketLogging(
	array(
	"Bucket"=>"ksc-scm",
	"BucketLogging"=>array(
			"Enable"=>TRUE,
			"TargetBucket"=>"ksc-scm",
			"TargetPrefix"=>"X-KSS"
			))));*/
/*_format($client->listObjects(
	array(
		"Bucket"=>"ksc-scm",
		"Options"=>array(
			"prefix"=>"debug/",
			"delimiter"=>"/"
			)
		)
	)
);*/
/*_format($client->getBucketLocation(
	array(
		"Bucket"=>"ksc-scm"
		)
	));*/
_format($client->getBucketLogging(
	array(
		"Bucket"=>"ksc-scm"
		)
	));

function _format($response) {
	print_r($response);
}
?>