<?php
require_once KS3_API_PATH.DIRECTORY_SEPARATOR."exceptions".DIRECTORY_SEPARATOR."Exceptions.php";

interface Handler{
	public function handle(ResponseCore $response);
}
class ErrorResponseHandler implements Handler{
	public function handle(ResponseCore $response){
		$code = $response->status;
		print_r( $response);
		if(!$response->isOk()){
			$xml = new SimpleXMLElement($response->body);
			$exception = new Ks3ServiceException();
			$exception ->requestId = $xml->RequestId;
			$exception->errorCode = $xml->Code;
			$exception->errorMessage=$xml->Message;
			$exception->resource=$xml->Resource;
			$exception->statusCode=$code;
			throw $exception;
		}else{
			return $response;
		}
	}
}
class ListBucketsHandler implements Handler{

	public function handle(ResponseCore $response){
		$result = array();
		$xml = new SimpleXMLElement($response->body);
		foreach ($xml->Buckets->Bucket as $bucketXml) {
			$bucket = array();
			foreach ($bucketXml->children() as $key => $value) {
				$bucket[$key]=$value->__toString();
			}
			array_push($result, $bucket);
		}
		return $result;
	}
}
class ListObjectsHandler implements Handler{
	public function handle(ResponseCore $response){
		$result = array();
		$xml = new SimpleXMLElement($response->body);
		$result["Name"]=$xml->Name->__toString();
		$result["Prefix"]=$xml->Prefix->__toString();
		$result["Marker"]=$xml->Marker->__toString();
		$result["Delimiter"]=$xml->Delimiter->__toString();
		$result["MaxKeys"]=$xml->MaxKeys->__toString();
		$result["IsTruncated"]=$xml->IsTruncated->__toString();

		$contents = array();
		foreach ($xml->Contents as $contentXml) {
			$content = array();
			foreach ($contentXml->children() as $key => $value) {
				$owner = array();
				if($key === "Owner"){
					foreach ($value->children() as $ownerkey => $ownervalue) {
						$owner[$ownerkey]=$ownervalue->__toString();
					}
					$content["Owner"] = $owner;
				}else{
					$content[$key]=$value->__toString();
				}
			}
			array_push($contents, $content);
		}
		$result["Contents"] = $contents;

		$commonprefix = array();
		foreach ($xml->CommonPrefixes as $commonprefixXml) {
			foreach ($commonprefixXml->children() as $key => $value) {
				array_push($commonprefix, $value->__toString());
			}
		}
		$result["CommonPrefixes"] = $commonprefix;
		return $result;
	}
}
class GetBucketLocationHandler implements Handler{
	public function handle(ResponseCore $response){
		$xml = new SimpleXMLElement($response->body);
		$location = $xml->__toString();

		return $location;
	}
}
class GetBucketLoggingHandler implements Handler{
	public function handle(ResponseCore $response){
		$logging = array();
		$xml = new SimpleXMLElement($response->body);
		$loggingXml = $xml->LoggingEnabled;

		$logging["Enable"] = FALSE;
		foreach ($loggingXml->children() as $key => $value) {
			$logging["Enable"] = TRUE;
			$logging[$key] = $value->__toString();
		}
		return $logging;
	}
}
class BooleanHandler implements Handler{
	public function handle(ResponseCore $response){
		if(!$response->isOk()){
			return TRUE;
		}else{
			return FALSE;
		}
	}
}
?>