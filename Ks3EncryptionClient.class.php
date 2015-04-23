<?php
//检测API路径
if(!defined('KS3_API_PATH'))
define('KS3_API_PATH', dirname(__FILE__));
require_once KS3_API_PATH.DIRECTORY_SEPARATOR."Ks3Client.class.php";
require_once KS3_API_PATH.DIRECTORY_SEPARATOR."encryption".DIRECTORY_SEPARATOR."EncryptionUtil.php";
require_once KS3_API_PATH.DIRECTORY_SEPARATOR."encryption".DIRECTORY_SEPARATOR."encryptionHandlers.php";

//加密模式
if(!defined('ENCRYPTPTION_MODE'))
define("ENCRYPTPTION_MODE","EO");
//加密信息存储模式
if(!defined('ENCRYPTPTION_STORAGE_MODE'))
define("ENCRYPTPTION_STORAGE_MODE","META");


if(function_exists('get_loaded_extensions')){
	//检测mcrypt扩展
	$extensions = get_loaded_extensions();
	if($extensions){
		if(!in_array('mcrypt', $extensions)){
			throw new Ks3ClientException("please install mcrypt extension");
		}
	}else{
		throw new Ks3ClientException("please install extensions");
	}
}else{
	throw new Ks3ClientException();
} 

class Ks3EncryptionClient extends Ks3Client{

	private $encryptionHandler = NULL;
	//用户提供的主密钥,可以是对称秘钥或非对称秘钥(array)
	private $encryptionMaterials = NULL;

	public function __construct($accessKey, $secretKey,$encryptionMaterials, $endpoint = NULL ){
		parent::__construct($accessKey,$secretKey,$endpoint);
		$this->encryptionMaterials = $encryptionMaterials;
		if(ENCRYPTPTION_MODE == "EO"){
			$this->encryptionHandler = new EncryptionEO(new Ks3Client($accessKey,$secretKey,$endpoint),$encryptionMaterials);
		}else{
			throw new Ks3ClientException("unsupported encryption mode :".ENCRYPTPTION_MODE);
		}
	}

	public function putObjectByContent($args=array()){
		return $this->encryptionHandler->putObjectByContentSecurely($args);
	}
	public function putObjectByFile($args=array()){
		return $this->encryptionHandler->putObjectByFileSecurely($args);
	}
	public function getObject($args=array()){
		return $this->encryptionHandler->getObjectSecurely($args);
	}
	public function initMultipartUpload($args=array()){
		return $this->encryptionHandler->initMultipartUploadSecurely($args);
	}
	public function uploadPart($args=array()){
		return $this->encryptionHandler->uploadPartSecurely($args);
	}
	public function abortMultipartUpload($args=array()){
		return $this->encryptionHandler->abortMultipartUploadSecurely($args);
	}
	public function completeMultipartUpload($args=array()){
		return $this->encryptionHandler->completeMultipartUploadSecurely($args);
	}
}

?>