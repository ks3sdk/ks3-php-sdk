<?php
require_once "../Ks3Client.class.php";
//require_once "../Ks3EncryptionClient.class.php";
require_once "PUnit.php";
class SDKTest extends PUnit{
	protected $bucket = "php-sdk-test";
	protected $key = "test";
	protected $key_copy = "test_copy";
	protected $accesskey = "lMQTr0hNlMpB0iOk/i+x";
	protected $secrectkey = "D4CsYLs75JcWEjbiI22zR3P7kJ/+5B1qdEje7A7I";
	protected $client;
	protected $cachedir;
	public function __construct(){
		$this->client=new Ks3Client($this->accesskey,$this->secrectkey);
		$this->cachedir=KS3_API_PATH.DIRECTORY_SEPARATOR."unit".DIRECTORY_SEPARATOR."cache".DIRECTORY_SEPARATOR;
	}
	public function before(){
		if($this->client->bucketExists(array("Bucket"=>$this->bucket))){
			$keys = array();
			$objects = $this->client->listObjects(array("Bucket"=>$this->bucket));
			foreach ($objects["Contents"] as $object) {
				array_push($keys, $object["Key"]);
			}
			$this->client->deleteObjects(array("Bucket"=>$this->bucket,"DeleteKeys"=>$keys));
		}else{
			$this->client->createBucket(array("Bucket"=>$this->bucket));
		}
	}
	public function after(){}
	public function testListBuckets(){
		$buckets = $this->client->listBuckets();
		$found = FALSE;
		foreach ($buckets as $bucket) {
			if($bucket["Name"] == $this->bucket)
				$found = TRUE;
		}
		if(!$found)
			throw new Exception("list buckets expected found ".$this->bucket.",but not found");
			
	}
	public function testDeleteBucket(){
		$this->client->putObjectByContent(array("Bucket"=>$this->bucket,"Key"=>"test","Content"=>""));
		$ex = NULL;
		try{
			$this->client->deleteBucket(array("Bucket"=>$this->bucket));
		}catch(Exception $e){
			$ex = $e;
		}
		if($ex == NULL||!($ex->errorCode === "BucketNotEmpty")){
			throw new Exception("delete bucket expected BucketNotEmpty but ".$ex);
		}
	}
	public function testBucketCORS(){
		$this->client->setBucketCORS($args = array(
       		"Bucket"=>$this->bucket,
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
 	    $cors = $this->client->getBucketCORS(array("Bucket"=>$this->bucket));
 	    $this->assertEquals(count($cors),2,"bucket cors count ");
   		$this->client->deleteBucketCORS(array("Bucket"=>$this->bucket));
   		$cors = $this->client->getBucketCORS(array("Bucket"=>$this->bucket));
		$this->assertEquals(count($cors),0,"bucket cors count ");
	}
	public function testCreateBucket(){
		$ex = NULL;
		try{
			$this->client->createBucket(array("Bucket"=>$this->bucket));
		}catch(Exception $e){
			$ex = $e;
		}
		if($ex == NULL||!($ex->errorCode === "BucketAlreadyExists")){
			throw new Exception("create bucket expected BucketAlreadyExists but ".$ex);
		}
	}
	public function testACL(){
		$this->client->setBucketAcl(array("Bucket"=>$this->bucket,"ACL"=>"public-read"));
		$acl = $this->client->getBucketAcl(array("Bucket"=>$this->bucket));
		$this->assertEquals($acl,"public-read","bucket acl");
	}
	public function testBucketLogging(){
		$this->client->setBucketLogging(array(
        	"Bucket"=>$this->bucket,
        	"BucketLogging"=>array(
            	"Enable"=>TRUE,
            	"TargetBucket"=>$this->bucket,
            	"TargetPrefix"=>"X-KSS"
            )
        ));
    	$logging = $this->client->getBucketLogging(array("Bucket"=>$this->bucket));
    	$this->assertEquals($logging["Enable"],TRUE,"bucket logging enable");

    	$this->client->setBucketLogging(array(
    		"Bucket"=>$this->bucket,
    		"BucketLogging"=>array(
        		"Enable"=>FALSE,//是否开启
        	)
    	));
    	$logging = $this->client->getBucketLogging(array("Bucket"=>$this->bucket));
    	$this->assertEquals($logging["Enable"],FALSE,"bucket logging enable");
	}
	public function testBucketLocation(){
		$location = $this->client->getBucketLocation(array("Bucket"=>$this->bucket));
		$this->assertEquals($location,"HANGZHOU","bucket location ");
	}
	public function testPutObjectByContent(){
		$args = array(
        	"Bucket"=>$this->bucket,
        	"Key"=>$this->key,
        	"Content"=>"1234",//要上传的内容
        	"ACL"=>"public-read",//可以设置访问权限,合法值,private、public-read
        	"ObjectMeta"=>array(
            	"Content-Type"=>"application/xml",
            	"Content-Length"=>3
            ),
        	"UserMeta"=>array(//可以设置object的用户元数据，需要以x-kss-meta-开头
            	"x-kss-meta-test"=>"test"
            )
        );
        $this->client->putObjectByContent($args);
        $this->assertEquals($this->client->objectExists(array("Bucket"=>$this->bucket,"Key"=>$this->key)),TRUE,"object exists ");
        $meta = $this->client->getObjectMeta(array("Bucket"=>$this->bucket,"Key"=>$this->key));
        $this->assertEquals($meta["UserMeta"]["x-kss-meta-test"],"test","x-kss-meta-test");
        $this->assertEquals($meta["ObjectMeta"]["Content-Type"],"application/xml","Content-Type");
        $this->assertEquals($meta["ObjectMeta"]["Content-Length"],3,"Content-Length");
        $this->assertEquals($this->client->getObjectAcl(array("Bucket"=>$this->bucket,"Key"=>$this->key)),"public-read","object acl ");
	}
	public function testPutObjectByFile(){
		$args = array(
        	"Bucket"=>$this->bucket,
        	"Key"=>$this->key,
        	"Content"=>array(
        		"content"=>$this->cachedir."test_file"
        	),//要上传的内容
        	"ACL"=>"public-read",//可以设置访问权限,合法值,private、public-read
        	"ObjectMeta"=>array(
            	"Content-Type"=>"application/xml",
            	"Content-Length"=>100
            ),
        	"UserMeta"=>array(//可以设置object的用户元数据，需要以x-kss-meta-开头
            	"x-kss-meta-test"=>"test"
            )
        );
        $this->client->putObjectByFile($args);
        $this->assertEquals($this->client->objectExists(array("Bucket"=>$this->bucket,"Key"=>$this->key)),TRUE,"object exists ");
        $meta = $this->client->getObjectMeta(array("Bucket"=>$this->bucket,"Key"=>$this->key));
        $this->assertEquals($meta["UserMeta"]["x-kss-meta-test"],"test","x-kss-meta-test");
        $this->assertEquals($meta["ObjectMeta"]["Content-Type"],"application/xml","Content-Type");
        $this->assertEquals($meta["ObjectMeta"]["Content-Length"],100,"Content-Length");
        $this->assertEquals($this->client->getObjectAcl(array("Bucket"=>$this->bucket,"Key"=>$this->key)),"public-read","object acl ");
	}
}
$test = new SDKTest();
$test->doTest();
?>