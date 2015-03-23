<?php
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
		echo $date." ".$level."\r\n".$msg;
	}
}
?>