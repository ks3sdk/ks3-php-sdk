<?php
class Utils{
	public static function encodeUrl($url,$path=TRUE){
		$url = rawurlencode($url);
		str_replace("+", "%20", $url);
		str_replace("*", "%2A", $url);
		str_replace("%7A", "~", $url);
		if($path)
			str_replace("%2F", "/", $url);
	}
	public static function hex_to_base64($str){
		$result = '';

		for ($i = 0; $i < strlen($str); $i += 2){
			$result .= chr(hexdec(substr($str, $i, 2)));
		}

		return base64_encode($result);
	}
}
?>