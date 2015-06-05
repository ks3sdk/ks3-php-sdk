<?php
require_once "../Ks3Client.class.php";
//require_once "../Ks3EncryptionClient.class.php";
require_once "TestUtil.php";
require_once "PUnit.php";
require_once "../lib/RequestCore.class.php";
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
	public function testObjectAcl(){
		$this->client->putObjectByContent(array("Bucket"=>$this->bucket,"Key"=>$this->key,
"Content"=>"1234"));
		$this->assertEquals($this->client->getObjectAcl(array("Bucket"=>$this->bucket,"Key"=>$this->key)),"private","object acl");
		$this->client->setObjectAcl(array("Bucket"=>$this->bucket,"Key"=>$this->key,"ACL"=>"public-read"));
		$this->assertEquals($this->client->getObjectAcl(array("Bucket"=>$this->bucket,"Key"=>$this->key)),"public-read","object acl");
	}
	public function testDeleteObject(){
		$this->client->putObjectByContent(array("Bucket"=>$this->bucket,"Key"=>$this->key,
"Content"=>"1234"));
		$this->client->deleteObject(array("Bucket"=>$this->bucket,"Key"=>$this->key));
		$this->assertEquals($this->client->objectExists(array("Bucket"=>$this->bucket,"Key"=>$this->key)),FALSE,"object exits");
	}
	public function testDeleteObjects(){
		$this->client->putObjectByContent(array("Bucket"=>$this->bucket,"Key"=>$this->key,
"Content"=>"1234"));
		$this->client->deleteObjects(array("Bucket"=>$this->bucket,"DeleteKeys"=>array($this->key)));
		$this->assertEquals($this->client->objectExists(array("Bucket"=>$this->bucket,"Key"=>$this->key)),FALSE,"object exits");		
	}
	public function testCopyObject(){
		$this->client->putObjectByContent(array("Bucket"=>$this->bucket,"Key"=>$this->key,
"Content"=>"1234"));
		$this->client->copyObject(array("Bucket"=>$this->bucket,"Key"=>$this->key_copy,"CopySource"=>array("Bucket"=>$this->bucket,"Key"=>$this->key)));
		$this->assertEquals($this->client->objectExists(array("Bucket"=>$this->bucket,"Key"=>$this->key)),TRUE,"object exits");
		$this->assertEquals($this->client->objectExists(array("Bucket"=>$this->bucket,"Key"=>$this->key_copy)),TRUE
			,"object exits");
	}
	public function testPutAndGetObject(){
		$args = array(
        	"Bucket"=>$this->bucket,
        	"Key"=>$this->key,
        	"Content"=>array(
        		"content"=>$this->cachedir."test_file"
        	),//要上传的内容
        	"ACL"=>"public-read",//可以设置访问权限,合法值,private、public-read
        	"ObjectMeta"=>array(
            	"Content-Type"=>"application/xml",
            ),
        	"UserMeta"=>array(//可以设置object的用户元数据，需要以x-kss-meta-开头
            	"x-kss-meta-test"=>"test"
            )
        );
        $this->client->putObjectByFile($args);
        $this->client->getObject(array("Bucket"=>$this->bucket,"Key"=>$this->key,"WriteTo"=>$this->cachedir."down"));
        $md5 = md5_file($this->cachedir."down");
        $md5pre = md5_file($this->cachedir."test_file");
        @unlink($this->cachedir."down");
        $this->assertEquals($md5,$md5pre,"contentmd5");
	}
	public function testPutAndGetObjectRanges(){
		$args = array(
        	"Bucket"=>$this->bucket,
        	"Key"=>$this->key,
        	"Content"=>array(
        		"content"=>$this->cachedir."test_file"
        	),//要上传的内容
        	"ACL"=>"public-read",//可以设置访问权限,合法值,private、public-read
        	"ObjectMeta"=>array(
            	"Content-Type"=>"application/xml",
            ),
        	"UserMeta"=>array(//可以设置object的用户元数据，需要以x-kss-meta-开头
            	"x-kss-meta-test"=>"test"
            )
        );
        $this->client->putObjectByFile($args);
        rangeGetAndCheckMd5($this->client,$this->bucket,$this->key,$this->cachedir."down",md5_file($this->cachedir."test_file"));
	}
	public function testInitAndAbortMultipart(){
		$initResult = $this->client->initMultipartUpload(array("Bucket"=>$this->bucket,"Key"=>$this->key));
		$uid = $initResult["UploadId"];
		$listParts = $this->client->listParts(array("Bucket"=>$this->bucket,"Key"=>$this->key,"Options"=>array("uploadId"=>$uid)));
		$this->client->abortMultipartUpload(array("Bucket"=>$this->bucket,"Key"=>$this->key,"Options"=>array("uploadId"=>$uid)));
		$ex = NULL;
		try{
			$this->client->listParts(array("Bucket"=>$this->bucket,"Key"=>$this->key,"Options"=>array("uploadId"=>$uid)));
		}catch(Exception $e){
			$ex = $e;
		}
		if($ex == NULL||!($ex->errorCode === "NoSuchUpload")){
			throw new Exception("create bucket expected NoSuchUpload but ".$ex);
		}
	}
	public function testMultipartUpload(){
		generateFile(1024*1024,$this->cachedir."multi");
		//初始化分开上传，获取uploadid
        $args = array(
            "Bucket"=>$this->bucket,
            "Key"=>$this->key,
            "ACL"=>"public-read",
            "UserMeta"=>array(
            	"x-kss-meta-test"=>"example"
            ),
        "ObjectMeta"=>array(
            "Content-Type"=>"application/xml"
            )
        );
        $uploadid = $this->client->initMultipartUpload($args);
        $uploadid = $uploadid["UploadId"];//获取到uploadid
        //开始上传
        $file = $this->cachedir."multi";//要上传的文件
        $partsize = 1024*100;
        $resource = fopen($file,"r");
        $stat = fstat($resource);
        $total = $stat["size"];//获取文件的总大小
        fclose($resource);
        $count = (int)($total/$partsize+1);//计算文件需要分几块上传
        for($i = 0;$i < $count;$i++){
            //依次上传每一块
            $args=array(
                "Bucket"=>$this->bucket,
                "Key"=>$this->key,
                "Options"=>array(
                    "partNumber"=>$i+1,
                    "uploadId"=>$uploadid
                ),
                "ObjectMeta"=>array(
                    "Content-Length"=>min($partsize,$total-$partsize*$i)//每次上传$partsize大小
                ),
                "Content"=>array(
                    "content"=>$file,
                    "seek_position"=>$partsize*$i//跳过之前已经上传的
                )
            );
            $etag = $this->client->uploadPart($args);
            $etag = $etag["ETag"];
        }
        $parts = $this->client->listParts(array("Bucket"=>$this->bucket,"Key"=>$this->key,"Options"=>array("uploadId"=>$uploadid)));
        //结束上传
        $args=array(
            "Bucket"=>$this->bucket,
            "Key"=>$this->key,
            "Options"=>array("uploadId"=>$uploadid),
            "Parts"=>$parts["Parts"]//使用之前列出的块完成分开上传
        );
        $result = $this->client->completeMultipartUpload($args);
        $this->assertEquals($this->client->getObjectAcl(array("Bucket"=>$this->bucket,"Key"=>$this->key)),"public-read","object acl");
        $meta = $this->client->getObjectMeta(array("Bucket"=>$this->bucket,"Key"=>$this->key));
        $this->assertEquals($meta["ObjectMeta"]["Content-Type"],"application/xml","Content-Type");
        $this->assertEquals($meta["ObjectMeta"]["Content-Length"],filesize($this->cachedir."multi"),"Content-Length");
        $this->assertEquals($meta["UserMeta"]["x-kss-meta-test"],"example","x-kss-meta-test");
        rangeGetAndCheckMd5($this->client,$this->bucket,$this->key,$this->cachedir."down",md5_file($this->cachedir."multi"));
        @unlink($this->cachedir."multi");
	}
	public function testListBucketsPresignedUrl(){
		$url = $this->client->generatePresignedUrl(
			array(
				"Method"=>"GET",
				"Options"=>array("Expires"=>60*10),
				"Headers"=>array("Content-Type"=>"text/plain")
				));
		$httpRequest = new RequestCore($url);
		$httpRequest->set_method("GET");
		$httpRequest->send_request();
		$body = $httpRequest->get_response_body ();	
		$this->assertEquals($httpRequest->get_response_code()." body:".$body,200,"list buckets status code");
	}
}
$test = new SDKTest();
$test->run();
?>