<?php
class Consts {
	static $Ks3EndPoint = "kss.ksyun.com";
	static $SubResource = array("acl", "lifecycle", "location", "logging", "notification", "partNumber", "policy", "requestPayment", "torrent", "uploadId", "uploads", "versionId","versioning", "versions", "website", "delete", "thumbnail","cors","pfop","querypfop","adp","queryadp");	
	static $QueryParam = array("response-content-type","response-content-language","response-expires", "response-cache-control","response-content-disposition", "response-content-encoding", );
	static $Regions = array("BEIJING","JIYANG","HANGZHOU");
	static $Acl = array("private","public-read","public-read-write");
	static $KS3HeaderPrefix = "x-kss-";
	static $CORSElements = array("AllowedMethod","AllowedOrigin","AllowedHeader","MaxAgeSeconds","ExposeHeader");
	static $BucketLoggingElements = array("TargetBucket","TargetPrefix");
}
?>