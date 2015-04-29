<?php
require_once "../Ks3Client.class.php";

$client = new Ks3Client("CO1iLH8FsG9yMfZvdEse","Dt8g8zzm1BFeRq74D+A2FARRs40n/ADy3ij8O8zv");


$args=array(
		"Bucket"=>"videobucket",
		"Key"=>"一分钟教你预防老年痴呆.mov",
		"Adp"=>array(
			"NotifyURL"=>"http://10.4.2.38:19090/",
			"Adps"=>array(
				array(
					"Command"=>"tag=avop&f=mp4",
					"Bucket"=>"videobucket",//需要拥有对该bucket写的权限.不提供的话将为数据所在的bucket
					"Key"=>"一分钟教你预防老年痴呆.mp4",//可以不提供，不提供的话将会是随机值。
				)
				//......可以有多条命令
			)
		)
	);

$client->putAdp($args);

?>