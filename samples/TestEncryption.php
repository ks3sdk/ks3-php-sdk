<?php
require_once "BaseTest.php";

class TestEncryption extends BaseTest{
	private $sseckey;
	public function __construct(){
		parent::__construct();
		$filename = "secret.key";
		$handle = fopen($filename, "r");
		$sseckey = fread($handle, filesize ($filename));
		fclose($handle);
		$this->sseckey = $sseckey;
	}
	public function testPutObjectSSEAndGetHeadObject(){
		$args = array(
       		"Bucket"=>$this->bucketName,
        	"Key"=>$this->objectKey,
        	"Content"=>"12345",//要上传的内容
       		 "ACL"=>"public-read",//可以设置访问权限,合法值,private、public-read
       		 "ObjectMeta"=>array(//设置object的元数据,可以设置"Cache-Control","Content-Disposition","Content-Encoding","Content-Length","Content-MD5","Content-Type","Expires"。当设置了Content-Length时，请勿大于实际长度，如果小于实际长度，将只上传部分内容。
            	"Content-Type"=>"binay/ocet-stream"
            ),
        	"UserMeta"=>array(//可以设置object的用户元数据，需要以x-kss-meta-开头
          	  "x-kss-meta-test"=>"test"
            ),
      	  	"SSE"=>array(
        		"Algm"=>"AES256"//暂时支持AES256
   			 )
        );
		$result = $this->client->putObjectByContent($args);
		$this->assertEquals($result["SSEAlgm"],"AES256");

		$args = array(
       		"Bucket"=>$this->bucketName,
        	"Key"=>$this->objectKey
        	);
		$result = $this->client->getObjectMeta($args);
		$this->assertEquals($result["ObjectMeta"]["SSEAlgm"],"AES256");

		 $args = array(
        	"Bucket"=>$this->bucketName,
        	"Key"=>$this->objectKey,
       		"WriteTo"=>"D://testdown.txt" //文件保存路径,必须提供。可以是resource
        );
		 $this->client->getObject($args);
		 $this->assertEquals("12345",file_get_contents("D://testdown.txt"));
	}
	public function testPutObjectSSECAndGetHeadObject(){
		$args = array(
       		"Bucket"=>$this->bucketName,
        	"Key"=>$this->objectKey,
        	"Content"=>"12345",//要上传的内容
       		 "ACL"=>"public-read",//可以设置访问权限,合法值,private、public-read
       		 "ObjectMeta"=>array(//设置object的元数据,可以设置"Cache-Control","Content-Disposition","Content-Encoding","Content-Length","Content-MD5","Content-Type","Expires"。当设置了Content-Length时，请勿大于实际长度，如果小于实际长度，将只上传部分内容。
            	"Content-Type"=>"binay/ocet-stream"
            ),
        	"UserMeta"=>array(//可以设置object的用户元数据，需要以x-kss-meta-开头
          	  "x-kss-meta-test"=>"test"
            ),
      	  	"SSEC"=>array(
        		"Key"=>$this->sseckey
   			 )
        );
		$result = $this->client->putObjectByContent($args);
		$this->assertEquals($result["SSECAlgm"],"AES256");
		$this->assertEquals($result["SSECKeyMD5"],Utils::hex_to_base64(md5($this->sseckey)));

		$args = array(
       		"Bucket"=>$this->bucketName,
        	"Key"=>$this->objectKey,
        	"SSEC"=>array(
        		"Key"=>$this->sseckey
   			 )
        	);
		$result = $this->client->getObjectMeta($args);
		$this->assertEquals($result["ObjectMeta"]["SSECAlgm"],"AES256");
		$this->assertEquals($result["ObjectMeta"]["SSECKeyMD5"],Utils::hex_to_base64(md5($this->sseckey)));

		$args = array(
        	"Bucket"=>$this->bucketName,
        	"Key"=>$this->objectKey,
       		"WriteTo"=>"D://testdown.txt", //文件保存路径,必须提供。可以是resource
       		"SSEC"=>array(
        		"Key"=>$this->sseckey
   			 )
        );
		$this->client->getObject($args);
		$this->assertEquals("12345",file_get_contents("D://testdown.txt"));
	}
	public function testMultipartUploadSSE(){
		$file = "D://phpput";
		$args = array(
       		"Bucket"=>$this->bucketName,
        	"Key"=>$this->objectKey,
        	"SSE"=>array(
        		"Algm"=>"AES256"
        		)
		);
		$uploadid = $this->client->initMultipartUpload($args);

		$this->assertEquals($uploadid["SSEAlgm"],"AES256");

		$uploadid = $uploadid["UploadId"];
		//开始上传
		$args=array(
			"Bucket"=>$this->bucketName,
        	"Key"=>$this->objectKey,
			"Options"=>array(
				"partNumber"=>1,
				"uploadId"=>$uploadid
				),
			"Content"=>array(
				"content"=>$file
				)
			);
		$etag = $this->client->uploadPart($args);

		$this->assertEquals($etag["SSEAlgm"],"AES256");
		$etag = $etag["ETag"];

		$parts = $this->client->listParts(array("Bucket"=>$this->bucketName,"Key"=>$this->objectKey,"Options"=>array("uploadId"=>$uploadid)));
		//结束上传
		$args=array(
			"Bucket"=>$this->bucketName,
        	"Key"=>$this->objectKey,
			"Options"=>array("uploadId"=>$uploadid),
			"Parts"=>$parts["Parts"],
		);
		$result = $this->client->completeMultipartUpload($args);
		$this->assertEquals($result["SSEAlgm"],"AES256");
	}
	public function testMultipartUploadSSEC(){
		$file = "D://phpput";
		$args = array(
       		"Bucket"=>$this->bucketName,
        	"Key"=>$this->objectKey,
        	"SSEC"=>array(
        		"Key"=>$this->sseckey
        		)
		);
		$uploadid = $this->client->initMultipartUpload($args);

		$this->assertEquals($uploadid["SSECAlgm"],"AES256");
		$this->assertEquals($uploadid["SSECKeyMD5"],Utils::hex_to_base64(md5($this->sseckey)));

		$uploadid = $uploadid["UploadId"];
		//开始上传
		$args=array(
			"Bucket"=>$this->bucketName,
        	"Key"=>$this->objectKey,
			"Options"=>array(
				"partNumber"=>1,
				"uploadId"=>$uploadid
				),
			"Content"=>array(
				"content"=>$file
				),
			"SSEC"=>array(
        		"Key"=>$this->sseckey
        		)
			);
		$etag = $this->client->uploadPart($args);

		$this->assertEquals($etag["SSECAlgm"],"AES256");
		$this->assertEquals($etag["SSECKeyMD5"],Utils::hex_to_base64(md5($this->sseckey)));

		$etag = $etag["ETag"];

		$parts = $this->client->listParts(array("Bucket"=>$this->bucketName,"Key"=>$this->objectKey,"Options"=>array("uploadId"=>$uploadid)));
		//结束上传
		$args=array(
			"Bucket"=>$this->bucketName,
        	"Key"=>$this->objectKey,
			"Options"=>array("uploadId"=>$uploadid),
			"Parts"=>$parts["Parts"],
		);
		$result = $this->client->completeMultipartUpload($args);
		$this->assertEquals($result["SSECAlgm"],"AES256");
		$this->assertEquals($result["SSECKeyMD5"],Utils::hex_to_base64(md5($this->sseckey)));
	}
	public function testCopyObject(){
		try{
			$this->client->deleteObject(array("Bucket"=>$this->bucketName,"Key"=>"copy".$this->objectKey));
		}catch(Exception $e){}
		$args = array(
       		"Bucket"=>$this->bucketName,
        	"Key"=>$this->objectKey,
        	"Content"=>"12345",//要上传的内容
       		 "ACL"=>"public-read",//可以设置访问权限,合法值,private、public-read
       		 "ObjectMeta"=>array(//设置object的元数据,可以设置"Cache-Control","Content-Disposition","Content-Encoding","Content-Length","Content-MD5","Content-Type","Expires"。当设置了Content-Length时，请勿大于实际长度，如果小于实际长度，将只上传部分内容。
            	"Content-Type"=>"binay/ocet-stream"
            ),
        	"UserMeta"=>array(//可以设置object的用户元数据，需要以x-kss-meta-开头
          	  "x-kss-meta-test"=>"test"
            ),
      	  	"SSEC"=>array(
        		"Key"=>$this->sseckey
   			 )
        );
		$result = $this->client->putObjectByContent($args);

		$args = array(
			"Bucket"=>$this->bucketName,
        	"Key"=>"copy".$this->objectKey,
        	"CopySource"=>array(
        		"Bucket"=>$this->bucketName,
        		"Key"=>$this->objectKey
        		),
        	 "SSECSource"=>array(
        	 	"Key"=>$this->sseckey
        	 	),
        	 "SSEC"=>array(
        	 	"Key"=>$this->sseckey
        	 	)
			);
		$result = $this->client->copyObject($args);
	}
}
$test = new TestEncryption();
$test->doTest();

?>