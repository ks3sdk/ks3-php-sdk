<?php
//设置默认时区
date_default_timezone_set('Asia/Shanghai');

//检测API路径
if(!defined('KS3_API_PATH'))
define('KS3_API_PATH', dirname(__FILE__));

define("DEBUG",FALSE);

require_once KS3_API_PATH.DIRECTORY_SEPARATOR."config".DIRECTORY_SEPARATOR."Consts.php";
require_once KS3_API_PATH.DIRECTORY_SEPARATOR."core".DIRECTORY_SEPARATOR."API.php";
require_once KS3_API_PATH.DIRECTORY_SEPARATOR."core".DIRECTORY_SEPARATOR."Signers.php";
require_once KS3_API_PATH.DIRECTORY_SEPARATOR."core".DIRECTORY_SEPARATOR."Ks3Request.class.php";
require_once KS3_API_PATH.DIRECTORY_SEPARATOR."core".DIRECTORY_SEPARATOR."Handlers.php";
require_once KS3_API_PATH.DIRECTORY_SEPARATOR."core".DIRECTORY_SEPARATOR."Builders.php";
require_once KS3_API_PATH.DIRECTORY_SEPARATOR."lib".DIRECTORY_SEPARATOR."RequestCore.class.php";
require_once KS3_API_PATH.DIRECTORY_SEPARATOR."exceptions".DIRECTORY_SEPARATOR."Exceptions.php";

if(function_exists('get_loaded_extensions')){
	//检测curl扩展
	$extensions = get_loaded_extensions();
	if($extensions){
		if(!in_array('curl', $extensions)){
			throw new Ks3ClientException("系统没有安装curl扩展,请检查系统配置");
		}
	}else{
		throw new Ks3ClientException("系统没有安装任何扩展,请检查系统配置");
	}
}else{
	throw new Ks3ClientException();
}

class Ks3Client{
	private $accessKey;
	private $secretKey;
	private $endpoint;
	public function __construct($accessKey = NULL, $secretKey = NULL, $endpoint = NULL ){
		$this->accessKey = $accessKey;
		$this->secretKey = $secretKey;

		$this->endpoint = Consts::$Ks3EndPoint;
		if($endpoint)
			$this->endpoint = $endpoint;

		$this->signers = array();
	}
	public function __call($method,$args=array()){
		if(count($args) !== 0){
			if(count($args)>1||!is_array($args[0]))
				throw new Ks3ClientException("this method only needs one array argument");
			$args = $args[0];
		}
		$api = API::$API[$method];
		if(!$api){
			throw new Ks3ClientException("Not Found API");
		}
		$request = new Ks3Request();
		if($api["needBucket"]){
			if(empty($args["Bucket"])){
				throw new Ks3ClientException("this api need bucket");
			}else{
				$request->bucket = $args["Bucket"];
			}
		}
		if($api["needObject"]){
			if(empty($args["Key"])){
				throw new Ks3ClientException("this api need object key");
			}else{
				$request->key = $args["Key"];
			}
		}
		$request->method=$api["method"];
		$request->scheme="http://";
		$request->endpoint=$this->endpoint;
		//add subresource
		if(!empty($api["subResource"])){
			$request->subResource=$api["subResource"];
		}
		//add query params
		if(isset($api["queryParams"] )){
			foreach ($api["queryParams"] as $key => $value) {
				$index = explode("->",$value);
				$curIndexArg = $args;
				$add = TRUE;
				$curkey = "";
				foreach ($index as $key1 => $value1) {
					if(!isset($curIndexArg[$value1])){
						$add = FALSE;
					}else{
						$curIndexArg = $curIndexArg[$value1];
						$curkey = $value1;
					}
				}
				if(!empty($curIndexArg)&&$add){
					$request->addQueryParams($curkey,$curIndexArg);
				}
			}
		}
		if(isset($api["body"])){
			$builderName = $api["body"]["builder"];
			$builder = new $builderName();
			$request->body = $builder->build($args);
		}
		//add ext headers
		//TODO
		//sign request
		$signers = explode("->",$api["signer"]);
		foreach ($signers as $key => $value) {
			$signer = new $value();
			$signer->sign($request,array("accessKey"=>$this->accessKey,"secretKey"=>$this->secretKey,"args"=>$args));
		}

		if(DEBUG)
			print_r($request);

		if($signer instanceof HeaderAuthSigner){
			$url = $request->scheme.$request->endpoint;
			$bucket = $request->bucket;
			$key = $request->key;
			$subResource = $request->subResource;
			if(!empty($bucket)){
				$url.="/".$bucket;
			}
			if(!empty($key)){
				$url.="/".$key;
			}
			$queryString = "";
			if(!empty($subResource)){
				$queryString.="&".$subResource;
			}
			foreach ($request->queryParams as $key => $value) {
				$queryString.="&".$key."=".$value;
			}
			$queryString = substr($queryString, 1);
			if(!empty($queryString)){
				$url.="?".$queryString;
			}

			$httpRequest = new RequestCore($url);
			$httpRequest->set_method($request->method);
			foreach ($request->headers as $key => $value) {
				$httpRequest->add_header($key,$value);
			}
			$httpRequest->request_body=$request->body;
			$httpRequest->send_request();		
			$data =  new ResponseCore ( $httpRequest->get_response_header() , $httpRequest->get_response_body (), $httpRequest->get_response_code () );
			$handlers = explode("->",$api["handler"]);
			foreach ($handlers as $key => $value) {
				$handler = new $value();
				$data = $handler->handle($data);
			}
			return $data;
		}else{
			return $request->endpoint;
		}
		
	}
}

?>