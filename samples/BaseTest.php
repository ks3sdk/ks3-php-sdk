<?php
require_once "../Ks3Client.class.php";
require_once "../Ks3EncryptionClient.class.php";
class BaseTest{
	protected $bucketName = "test-encryption";
	protected $objectKey = "dir/test/中文目录/@特殊字符!.txt";
	protected $client ;
	public function __construct(){
		$this->client = new Ks3Client("lMQTr0hNlMpB0iOk/i+x","D4CsYLs75JcWEjbiI22zR3P7kJ/+5B1qdEje7A7I");
	}
	function assertEquals($value,$expected,$info = NULL){
		if($value !== $expected){
			throw new Exception($info." expected ".$expected." but ".$value);
		}
	}
	function doTest(){
   		$r = new ReflectionClass($this);
    	foreach($r->getMethods() as $key=>$methodObj){
      	  	if($methodObj->isPrivate())
           		 $methods[$key]['type'] = 'private';
        	elseif($methodObj->isProtected())
        	     $methods[$key]['type'] = 'protected';
      		else
           		 $methods[$key]['type'] = 'public';
        	$methods[$key]['name'] = $methodObj->name;
       		$methods[$key]['class'] = $methodObj->class;
       	}
       	$error = array();
       	$success = array();
       	foreach ($methods as $method) {
       		if($method["class"] != "BaseTest"&&substr($method["name"],0,4) == "test"){
       			try{
       				if($method["type"] == "public"){
       					$this->$method["name"]();
       					array_push($success,$method["name"]);
       				}
       			}catch(Exception $e){
       				$error[$method["name"]]="".$e;
       			}
       		}
       	}
       	echo "success"."\r\n";
       	print_r($success);
       	echo "error"."\r\n";
       	print_r($error);
	}
}
?>