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
}
?>