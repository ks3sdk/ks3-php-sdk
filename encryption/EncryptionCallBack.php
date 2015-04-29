<?php
require_once KS3_API_PATH.DIRECTORY_SEPARATOR."encryption".DIRECTORY_SEPARATOR."EncryptionUtil.php";
//下载
class AESCBCStreamWriteCallBack{
	private $iv;
	private $cek;
	private $contentLength;
	private $buffer;//上一次调用streaming_write_callback后，未解码的数据
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
	//最后的数据大小肯定是blocksize的倍数，所以最后buffer中不会有未解密的内容。否则可以认为该文件是错误的
	public function streaming_write_callback($curl_handle,$data,$write_stream){
		$data = $this->buffer.$data;

		$length = strlen($data);
		//不能把上次的没读完的长度算在这次里
		$written_total = 0-strlen($this->buffer);
		$blocksize = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128,MCRYPT_MODE_CBC);
		if($length<$blocksize)
			$this->buffer = $data;
		else{
			for($i=0;$i < (int)($length/$blocksize);$i++){
				$dataBlock = substr($data,$i*$blocksize,$blocksize);
				$td = mcrypt_module_open(MCRYPT_RIJNDAEL_128,'',MCRYPT_MODE_CBC,'');
				mcrypt_generic_init($td,$this->cek,$this->iv);
				$decoded = mdecrypt_generic($td,$dataBlock);
				mcrypt_generic_deinit($td);
				mcrypt_module_close($td);
				$this->iv = $dataBlock;
				$this->contentLength -= $blocksize;
				//之所以确定是0，因为contentLength肯定是blocksize的倍数，最后一次肯定会是0,。否则可以认为数据时损坏的。
				$pad = 0;
				if($this->contentLength === 0){
					$pad = ord(substr($decoded,strlen($decoded)-1,1));
					if($pad>=0&&$pad<=$blocksize){
						//删除当初填充的
						$decoded = substr($decoded,0,strlen($decoded)-$pad);
					}else{
						$pad = 0;
					}
				}
				$count = fwrite($write_stream, $decoded);
				$count += $pad;
				if(!$count){
					break;
				}else{
					$written_total+=$count;
				}
			}
			if($length%$blocksize!=0){
				$this->buffer = substr($data,$length - $length%$blocksize);
			}else{
				$this->buffer = NULL;
			}
		}
		//否则curl框架会报错
		$written_total+=strlen($this->buffer);
		return $written_total;
	}
}
//上传
class AESCBCStreamReadCallBack{
	private $iv;
	private $cek;
	private $contentLength;
	private $buffer;
	private $hasread = 0;
	private $mutipartUpload =FALSE;
	private $isLastPart = FALSE;
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
	public function streaming_read_callback($curl_handle,$file_handle,$length,$read_stream,$seek_position){
		// Once we've sent as much as we're supposed to send...
		if ($this->hasread >= $this->contentLength)
		{
			// Send EOF
			return '';
		}
		// If we're at the beginning of an upload and need to seek...
		if ($this->hasread == 0 && $seek_position>0 && $seek_position !== ftell($read_stream))
		{
			if (fseek($read_stream, $seek_position) !== 0)
			{
				throw new RequestCore_Exception('The stream does not support seeking and is either not at the requested position or the position is unknown.');
			}
		}


		$blocksize = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128,MCRYPT_MODE_ECB);
		$needRead = min($this->contentLength - $this->hasread,$length);
		$read = fread($read_stream,$needRead);
		$this->hasread += strlen($read);
		$isLast = FALSE;
		if($this->hasread >= $this->contentLength){
			$isLast = TRUE;
		}
		$data = $this->buffer.$read;

		$dataLength = strlen($data);

		if(!$isLast){
			$this->buffer = substr($data,$dataLength-$dataLength%$blocksize);
			$data = substr($data, 0,$dataLength-$dataLength%$blocksize);
		}else{
			//分块上传除最后一块外肯定是blocksize大小的倍数，所以不需要填充。
			if($this->mutipartUpload){
				if($this->isLastPart){
					$this->buffer = NULL;
					$data = EncryptionUtil::PKCS5Padding($data,$blocksize);
				}else{
					//donothing
				}
			}
		}
		$td = mcrypt_module_open(MCRYPT_RIJNDAEL_128,'',MCRYPT_MODE_CBC,'');
		mcrypt_generic_init($td,$this->cek,$this->iv);
		$encrypted = mcrypt_generic($td,$data);
		mcrypt_generic_deinit($td);
		//去除自动填充的16个字节//php的当恰好为16的倍数时竟然不填充？
		//$encrypted = substr($encrypted,0,strlen($encrypted)-$blocksize);
		//取最后一个block作为下一次的iv
		$this->iv = substr($encrypted, strlen($encrypted)-$blocksize);
		return $encrypted;
	}
}
?>