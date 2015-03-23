<?php
require_once "../Ks3Client.class.php";

$client = new Ks3Client("1GL02rRYQxK8s7FQh8dV","2IDjaPOpFfkq5Zf9K4tKu8k5AKApY8S8eKV1zsRl");
testListBuckets($client);
testBucket($client);


function testListBuckets($client){
	$client->listBuckets();
}
function testBucket($client){
	$bucketName = "phpsdktestlijunwei";

    if(!$client->bucketExists(array("Bucket"=>$bucketName))){
        $client->createBucket(array("Bucket"=>$bucketName));
    }

	$client->setBucketAcl(array("Bucket"=>$bucketName,"ACL"=>"public-read"));

	//ACL
	$acl = $client->getBucketAcl(array("Bucket"=>$bucketName));

	if($acl!=="public-read"){
		throw new RuntimeException("acl expected public-read");
	}
	//Logging
	$client->setBucketLogging(array(
        "Bucket"=>$bucketName,
        "BucketLogging"=>array(
            "Enable"=>TRUE,
            "TargetBucket"=>$bucketName,
            "TargetPrefix"=>"X-KSS"
            )
        ));
    $logging = $client->getBucketLogging(array("Bucket"=>$bucketName));
    if(!$logging["Enable"]){
    	throw new RuntimeException("logging expected enabled ");
    }

    $client->setBucketLogging(array(
    "Bucket"=>$bucketName,
    "BucketLogging"=>array(
        "Enable"=>FALSE,//是否开启
        )
    ));
    $logging = $client->getBucketLogging(array("Bucket"=>$bucketName));
    if($logging["Enable"]){
    	throw new RuntimeException("logging expected disabled ");
    }

    //Location
    $location = $client->getBucketLocation(array("Bucket"=>$bucketName));
    if($location !== "HANGZHOU"){
    	throw new RuntimeException("logging location expected HANGZHOU but ".$location);
    }

    //CORS
    $client->setBucketCORS($args = array(
        "Bucket"=>$bucketName,
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
    )));

    $cors = $client->getBucketCORS(array("Bucket"=>$bucketName));

    if(count($cors)!==2){
        throw new RuntimeException("cors size expected 2 but ".count($cors));
    }

    $client->deleteBucketCORS(array("Bucket"=>$bucketName));

    $cors = $client->getBucketCORS(array("Bucket"=>$bucketName));

    if(count($cors)!==0){
        throw new RuntimeException("cors size expected 0 but ".count($cors));
    }
    //bucket exists

    if(!$client->bucketExists(array("Bucket"=>$bucketName))){
    	throw new RuntimeException("bucket exists error");
    	
    }

}
function testObject(){

}

?>