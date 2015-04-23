<?php
interface EncryptionHandler{
	public function putObjectByContentSecurely($args=array());
	public function putObjectByFileSecurely($args=array());
	public function getObjectSecurely($args=array());
	public function initMultipartUploadSecurely($args=array());
	public function uploadPartSecurely($args=array());
	public function abortMultipartUploadSecurely($args=array());
	public function completeMultipartUploadSecurely($args=array());
}
class EncryptionEO implements EncryptionHandler{
	private $encryptionMaterials = NULL;
	private $ks3client = NULL;
	public function __construct($ks3client,$encryptionMaterials){
		$this->encryptionMaterials = $encryptionMaterials;
		$this->ks3client = $ks3client;
	}
	public function putObjectByContentSecurely($args=array()){
		return $this->ks3client->putObjectByContent($args);
	}
	public function putObjectByFileSecurely($args=array()){
		return $this->ks3client->putObjectByFile($args);
	}
	public function getObjectSecurely($args=array()){
		return $this->ks3client->getObject($args);
	}
	public function initMultipartUploadSecurely($args=array()){
		return $this->ks3client->initMultipartUpload($args);
	}
	public function uploadPartSecurely($args=array()){
		return $this->ks3client->uploadPart($args);
	}
	public function abortMultipartUploadSecurely($args=array()){
		return $this->ks3client->abortMultipartUpload($args);
	}
	public function completeMultipartUploadSecurely($args=array()){
		return $this->ks3client->completeMultipartUpload($args);
	}
}
?>