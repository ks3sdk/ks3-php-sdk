<?php
require_once KS3_API_PATH.DIRECTORY_SEPARATOR."core".DIRECTORY_SEPARATOR."Headers.php";
require_once KS3_API_PATH.DIRECTORY_SEPARATOR."core".DIRECTORY_SEPARATOR."Utils.class.php";
require_once KS3_API_PATH.DIRECTORY_SEPARATOR."config".DIRECTORY_SEPARATOR."Consts.php";
require_once KS3_API_PATH.DIRECTORY_SEPARATOR."exceptions".DIRECTORY_SEPARATOR."Exceptions.php";

interface Signer{
	public function sign( Ks3Request $request,$args=array());
}
class DefaultContentTypeSigner implements Signer{
	public function sign(Ks3Request $request,$args=array()){
		$contentType = $request->getHeader(Headers::$ContentType);
		if(empty($contentType)){
			$request->addHeader(Headers::$ContentType,"application/xml");
		}
	}
}
class HeaderAuthSigner implements Signer{
	public function sign(Ks3Request $request,$args=array()){
		$date = gmdate('D, d M Y H:i:s \G\M\T');
		$request->addHeader(Headers::$Date, $date);

		$ak = $args["accessKey"];
		$sk = $args["secretKey"];
		$authration = "KSS ";
		$signList = array(
			$request->method,
			$request->getHeader(Headers::$ContentMd5),
			$request->getHeader(Headers::$ContentType),
			$date
			);
		$headers = AuthUtils::canonicalizedKssHeaders($request);
		$resource = AuthUtils::canonicalizedResource($request);
		if(!empty($headers)){
			array_push($signList,$headers);
		}
		array_push($signList,$resource);
		$stringToSign = join("\n",$signList);
		if(DEBUG)
			echo $stringToSign;
		$signature = base64_encode(hash_hmac('sha1', $stringToSign, $sk, true));

		$authration.=$ak.":".$signature;
		$request->addHeader(Headers::$Authorization, $authration);
	}
}
class QueryAuthSigner implements Signer{
	public function sign(Ks3Request $request,$args=array()){
		$ak = $args["accessKey"];
		$sk = $args["secretKey"];
		$authration = "KSS ";
		$stringToSign = "";

		$request->addHeader(Headers::$Authration, $authration);
	}
}
class ACLSigner implements Signer{
	public function sign(Ks3Request $request,$args=array()){
		$args = $args["args"];
		if(isset($args["ACL"])){
			$acl = $args["ACL"];
			if(!in_array($acl, Consts::$Acl)){
				throw new Ks3ClientException("unsupport acl :".$acl);
			}else{
				$request->addHeader(Headers::$Acl,$acl);
			}
		}
		if(isset($args["ACP"])){

		}
	}
}
class ContentMD5Signer implements Signer{
	public function sign(Ks3Request $request,$args=array()){
		$args = $args["args"];
		$contentmd5 = "";
		if(isset($args["ObjectMeta"][Headers::$ContentMd5])){
			$contentmd5 = $args["ObjectMeta"][Headers::$ContentMd5];
		}
		if(empty($contentmd5)){
			$body = $request->body;
			if(!empty($body)){
				$contentmd5 = Utils::hex_to_base64(md5($body));
			}
		}
		if(!empty($contentmd5))
			$request->addHeader(Headers::$ContentMd5,$contentmd5);
	}
}
class ContentLengthSigner implements Signer{
	public function sign(Ks3Request $request,$args=array()){
		$args = $args["args"];
		$contentlength = "";
		if(isset($args["ObjectMeta"][Headers::$ContentLength])){
			$contentlength = $args["ObjectMeta"][Headers::$ContentLength];
		}
		if(empty($contentlength)){
			$body = $request->body;
			if(!empty($body)){
				$contentlength = strlen($body);
			}
		}
		if(!empty($contentlength))
			$request->addHeader(Headers::$ContentLength,$contentlength);
	}
}
class AuthUtils{
	public static function canonicalizedKssHeaders(Ks3Request $request){
		$header = "";
		$headers = $request->headers;
		print_r($request);
		asort($headers,SORT_STRING);
		foreach ( $headers as $header_key => $header_value ) {
			if (substr(strtolower($header_key), 0, 6) === Consts::$KS3HeaderPrefix){
				$header .= "\n".strtolower($header_key) . ':' . $header_value ;
			}			
		}
		$header = substr($header, 1);
		return $header;
	}
	public static function canonicalizedResource(Ks3Request $request){
		$resource = "/";
		$bucket = $request->bucket;
		$key = $request->key;
		$subResource = $request->subResource;
		if(!empty($bucket)){
			$resource.=$request->bucket."/";
		}
		if(!empty($key)){
			$resource.=Utils::encodeUrl($request->key);
		}
		if(!empty($subResource)){
			$resource.="?".$request->subResource;
		}
		return $resource;
	}
}
?>