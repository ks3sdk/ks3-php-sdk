<?php
require_once KS3_API_PATH.DIRECTORY_SEPARATOR."exceptions".DIRECTORY_SEPARATOR."Exceptions.php";
class Logger{
	function info($msg){
		$this->log("INFO",$msg);
	}
	function error($msg){
		$this->log("ERROR",$msg);
	}
	function warn($msg){
		$this->log("WARN",$msg);
	}
	function debug($msg){
		$this->log("DEBUG",$msg);
	}
	private function log($level,$msg){
		$date = gmdate('D, d M Y H:i:s \G\M\T');
		$log = $date." ".$level."\r\n".$msg."\r\n";
		if(defined('LOG_PATH') ){
			$log_path = LOG_PATH;
			if(empty($log_path)){
				$log_path = KS3_API_PATH.DIRECTORY_SEPARATOR.'log'.DIRECTORY_SEPARATOR;
			}elseif(!file_exists($log_path)){
				throw new Ks3ClientException("log dir not exists");
			}
		}else{
			$log_path = KS3_API_PATH.DIRECTORY_SEPARATOR.'log'.DIRECTORY_SEPARATOR;
		}
		
		//检测日志目录是否存在
		if(!file_exists($log_path)){
			echo $log_path;
			throw new Ks3ClientException("log dir not exists");
		}

		$log_name = $log_path.'ks3_php_sdk_'.date('Y-m-d').'.log';

		if(DISPLAY_LOG){
			echo $log;
		}
		
		if(LOG){
			if(!error_log($log,3,$log_name)){
				throw new Ks3ClientException("write to log file error");
			}
		}
	}
}
?>