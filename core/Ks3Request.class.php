<?php
class Ks3Request{
	private $bucket;
	private $key;
	private $queryParams=array();
	private $headers=array();
	private $subResource;
	private $method;
	private $endpoint;
	private $scheme;
	private $body;
	private $read_stream;
	private $seek_position;
	private $write_stream;

	public function __set($property_name, $value){
		$this->$property_name=$value;
	}
	public function __get($property_name){
		if(isset($this->$property_name))
		{
			return($this->$property_name);
		}else
		{
			return(NULL);
		}
	}
	public function getHeader($key){
		if(isset($this->headers[$key])){
			return  $this->headers[$key];
		}else{
			return(NULL);
		}
	}
	public function addHeader($key,$value){
		$this->headers[$key] = $value;
	}

	public function getQueryParams($key){
		if(isset($this->queryParams[$key])){
			return  $this->queryParams[$key];
		}else{
			return(NULL);
		}
	}
	public function addQueryParams($key,$value){
		$this->queryParams[$key] = $value;
	}
	public function toUrl($endpoint){
		$url = $this->scheme.$endpoint;
		$bucket = $this->bucket;
		$key = $this->key;
		$subResource = $this->subResource;
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
		foreach ($this->queryParams as $key => $value) {
			$queryString.="&".$key."=".rawurlencode($value);
		}
		$queryString = substr($queryString, 1);
		if(!empty($queryString)){
			$url.="?".$queryString;
		}
		return $url;
	}
}
?>