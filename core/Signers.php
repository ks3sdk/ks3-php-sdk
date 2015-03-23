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
class StreamContentTypeSigner implements Signer{
	public function sign(Ks3Request $request,$args=array()){
		$contentType = $request->getHeader(Headers::$ContentType);
		if(empty($contentType)){
			$request->addHeader(Headers::$ContentType,"application/ocet-stream");
		}
	}
}
class SuffixContentTypeSigner implements Signer{
	public function sign(Ks3Request $request,$args=array()){
		$key = $request->key;
		$objArr = explode('/', $key);
		$basename = array_pop($objArr);
		$extension = explode ( '.', $basename );
		$extension = array_pop ( $extension );
		$content_type = Utils::get_mimetype(strtolower($extension));
		$request->addHeader(Headers::$ContentType,$content_type);
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
		$signature = base64_encode(hash_hmac('sha1', $stringToSign, $sk, true));

		$authration.=$ak.":".$signature;
		$request->addHeader(Headers::$Authorization, $authration);
	}
}
class QueryAuthSigner implements Signer{
	public function sign(Ks3Request $request,$args=array()){
		$ak = $args["accessKey"];
		$sk = $args["secretKey"];
		$expires = $args["args"]["Options"]["Expires"];
		$expiresSencond = time()+$expires;

		$resource = AuthUtils::canonicalizedResource($request);

		$signList = array(
			"GET",
			"",
			"",
			$expiresSencond,
			$resource
			);

		$stringToSign = join("\n",$signList);
		$signature = base64_encode(hash_hmac('sha1', $stringToSign, $sk, true));
		$request->addQueryParams("KSSAccessKeyId",$ak);
		$request->addQueryParams("Signature",$signature);
		$request->addQueryParams("Expires",$expiresSencond);
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
class ObjectMetaSigner implements Signer{
	public function sign(Ks3Request $request,$args=array()){
		$args = $args["args"];
		if(isset($args["ObjectMeta"])){
			$ObjectMeta = $args["ObjectMeta"];
			if(is_array($ObjectMeta)){
				foreach ($ObjectMeta as $key => $value) {
					if(in_array($key,Consts::$ObjectMeta)){
						$request->addHeader($key,$value);
					}
				}
			}
		}
	}
}
class MultipartObjectMetaSigner implements Signer{
	public function sign(Ks3Request $request,$args=array()){
		$args = $args["args"];
		if(isset($args["ObjectMeta"])){
			$ObjectMeta = $args["ObjectMeta"];
			if(is_array($ObjectMeta)){
				foreach ($ObjectMeta as $key => $value) {
					if(in_array($key,Consts::$MultipartObjectMeta)){
						$request->addHeader($key,$value);
					}
				}
			}
		}
	}
}
class UserMetaSigner implements Signer{
	public function sign(Ks3Request $request,$args=array()){
		$args = $args["args"];
		if(isset($args["UserMeta"])){
			$UserMeta = $args["UserMeta"];
			if(is_array($UserMeta)){
				foreach ($UserMeta as $key => $value) {
					if (substr(strtolower($key), 0, 10) === Consts::$UserMetaPrefix){
						$request->addHeader($key,$value);
					}
				}
			}
		}
	}
}
class CopySourceSigner implements Signer{
	public function sign(Ks3Request $request,$args=array()){
		$args = $args["args"];
		if(isset($args["CopySource"])){
			$CopySource = $args["CopySource"];
			if(is_array($CopySource)){
				if(!isset($CopySource["Bucket"]))
					throw new Ks3ClientException("you should provide copy source bucket");
				if(!isset($CopySource["Key"]))
					throw new Ks3ClientException("you should provide copy source key");
				$bucket = $CopySource["Bucket"];
				$key = Utils::encodeUrl($CopySource["Key"]);
				$request->addHeader(Headers::$CopySource,"/".$bucket."/".$key);
			}
		}
	}
}
class StreamUploadSigner implements Signer{
	public function sign(Ks3Request $request,$args=array()){
		$args = $args["args"];
		if(isset($args["Content"])&&is_array($args["Content"])&&isset($args["Content"]["content"])){
			$content = $args["Content"]["content"];
			$seek_position = 0;
			$isFile = FALSE;
			if (!is_resource($content)){
				$isFile = TRUE;
				if(Utils::chk_chinese($content)){
					$content = iconv('utf-8','gbk',$content);
				}
				if(!file_exists($content))
					throw new Ks3ClientException("the specified file does not exist ");
				$content = fopen($content,"r");
			}
			//优先取用户设置seek_position，没有的话取ftell
			if(isset($args["Content"]["seek_position"])&&isset($args["Content"]["seek_position"])>0){
				$seek_position = $args["Content"]["seek_position"];
			}else if(!$isFile){
				$seek_position = ftell($content);
			}
			$request->seek_position = $seek_position;

			$stats = fstat($content);
			$resourceLength = 0;
			if ($stats && $stats["size"] >= 0){
				$length = $stats["size"];
				$resourceLength = $length;
				$position = $seek_position;
				if ($position !== false && $position >= 0)
				{
					$length -= $position;
				}
			}
			$lengthInMeta = $request->getHeader(Headers::$ContentLength);
			if(!empty($lengthInMeta)&&$lengthInMeta>0){
				$length = $lengthInMeta;
			}
			//防止设置的length大于resource的length
			if($length + $seek_position > $resourceLength){
				$length = $resourceLength - $seek_position;
			}
			$request->read_stream = $content;
			$request->addHeader(Headers::$ContentLength,$length);
			$request->seek_position = $seek_position;
		}
	}
}
class RangeSigner{
	public function sign(Ks3Request $request,$args=array()){
		$args = $args["args"];
		if(isset($args["Range"])){
			$Range = $args["Range"];
			if(is_array($Range)){
				$start = $Range["start"];
				$end = $Range["end"];
				$range = "bytes=".$start."-".$end;
				$request->addHeader(Headers::$Range,$range);
			}else
				$request->addHeader(Headers::$Range,$Range);
		}
	}
}
class GetObjectSigner{
	public function sign(Ks3Request $request,$args=array()){
		$args = $args["args"];
		if(isset($args["WriteTo"])){
			$WriteTo = $args["WriteTo"];
			if(is_resource($WriteTo)){
				$request->write_stream = $WriteTo;
			}else{
				if(Utils::chk_chinese($WriteTo)){
					$WriteTo = iconv('utf-8','gbk',$WriteTo);
				}
				$request->write_stream = fopen($WriteTo,"w");
			}
		}else{
			throw new Ks3ClientException("argument WriteTo is needed");
		}
	}
}
class AdpSigner{
	public function sign(Ks3Request $request,$args=array()){
		$args = $args["args"];
		if(isset($args["Adp"])){
			$Adp = $args["Adp"];
			if(is_resource($WriteTo)){
				$request->write_stream = $WriteTo;
			}else{
				$request->write_stream = fopen($WriteTo,"w");
			}
		}
	}
}
class CallBackSigner{

}
class AuthUtils{
	public static function canonicalizedKssHeaders(Ks3Request $request){
		$header = "";
		$headers = $request->headers;
		ksort($headers,SORT_STRING);
		foreach ( $headers as $header_key => $header_value ) {
			if (substr(strtolower($header_key), 0, 6) === Consts::$KS3HeaderPrefix){
				$header .= "\n".$header_key . ':' . $header_value ;
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

		$encodeParams = "";
		$querys = $request->queryParams;
		if(!empty($subResource)){
			$querys[$subResource] = NULL;
		}
		ksort($querys,SORT_STRING);
		foreach ($querys as $key => $value) {
			if(in_array($key,Consts::$SubResource)||in_array($key,Consts::$QueryParam)){
				if(empty($value)){
					$encodeParams.="&".$key;
				}else{
					$encodeParams.="&".$key."=".$value;
				}
			}
		}
		$encodeParams = substr($encodeParams,1);

		$resource = str_replace("//","/%2F", $resource);

		if(!empty($encodeParams)){
			$resource.="?".$encodeParams;
		}
		return $resource;
	}
}
?>