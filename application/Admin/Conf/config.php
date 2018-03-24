<?php 
if(file_exists('application/Blog/Conf/user.php')){
	$user = include 'application/Blog/Conf/user.php';
	
}else{
	$user = array();
}
if(file_exists('application/Blog/Conf/request_params.php')){
	$request_params = include 'application/Blog/Conf/request_params.php';
}else{
	$request_params = array();
}
$url = array('url'=>'https://api.baidu.com/json/sms/v4',);
	


return array_merge($user,$request_params,$url);