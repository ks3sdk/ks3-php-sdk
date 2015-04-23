<?php
class EncryptionUtil{
	public static function genereateOnceUsedKey($length=32){
		$randpwd = "";  
		for ($i = 0; $i < $length; $i++)  
		{  
			$randpwd .= chr(mt_rand(33, 126));  
		}  
		return $randpwd;   
	}
	public static function getEncryptionAlgm($mode){
		$result = array();
		if($mode == "EO"){
			$result["algm"] = MCRYPT_RIJNDAEL_128;
			$result["algm_mode"] = MCRYPT_MODE_CBC;
		}
		return $result;
	}
}
?>