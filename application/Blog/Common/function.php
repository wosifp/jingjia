<?php

/* 返回用户数组，包括用户名，密码，token  */
function get_userNPT(){
	return array('username'=>C('username'),'password'=>C('password'),'token'=>C('token'),'target'=>C('target'));
}
function get_requestParams($ParamsName){
	
	return C($ParamsName);
}
function get_requestURL($postfix){
	return C('url').c($postfix);
}
function get_postdata($paramInfo){
	$arr_user = get_userNPT();
		/* 获取请求体参数 */
	$arr_body = get_requestParams($paramInfo);
	$arr_data = array("header" => $arr_user, "body" => $arr_body);
	$post_data = json_encode($arr_data);
	return $post_data;
}
function auto_completeParam($keyword){
	return array('info'=>$keyword.'Info','postfix'=>$keyword.'Postfix');
}
function send_post($url, $post_data) {    
      // 1. 初始化
		$ch = curl_init();
		// 2. 设置选项，包括URL
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		// 3. 执行并获取HTML文档内容
		$output = curl_exec($ch);
		if ($output === FALSE) {
			echo "CURL Error:".curl_error($ch);
		}
		/* echo $output; */
		// 4. 释放curl句柄
		curl_close($ch);  
		
        return $output;    
}
/*parameters data format:
["startDate"=>"2018-01-01","endDate"=>"2018-03-03"]
函数功能：返回账户时事报告，默认平台和设备为全部，可通过参数传入选择不同平台和设备。
时间单位可通过“unitTime”设置，不设置则默认返回所有情况下的数据。
*/
function getAccountReport_realtime($param = array("startDate"=>"2018-01-01","endDate"=>"2018-03-03","platform"=>0,"Device"=>0)  ){
	if (!strcmp("2001-09-17", $param["startDate"])) {
		# code...
		echo "开始时间不能早于2001-09-17";
		return false;
	}
	if (strcmp($param["startDate"], $param["endDate"])>0) {
		# code...
		echo "截止时间不能早于起始时间";
		return false;
	}
	$resultData = array( );
	$param1 = array('startDate' =>$param['startDate'] ,'endDate'=>$param['endDate'],"reportType"=>2,"levelOfDetails"=>2,"platform"=>$param["platform"],"Device"=>$param["Device"] );

	/*时间单位设置，1,3,4,5,7，8 分别对应年、月、周、日、小时报、请求时间段*/
	if($param["unitTime"]){
		$param1["unitTime"]=$param["unitTime"];
		$resultData =json_decode(getReport($param1,"RealTimeData"));
	}else{
		$unitTime = array("unitTime1"=>1,"unitTime3"=>3,"unitTime4"=>4,"unitTime5"=>5,"unitTime7"=>7,"unitTime8"=>8 );
		foreach ($unitTime as $key1 => $value1) {
			# code...
			$param1["unitTime"]=$value1;
			$resultData[$key1]=json_decode(getReport("RealTimeData",$param1));
		}
	}
	
	return json_encode($resultData);
	
}
/*返回相应服务的报告
参数：
$param : 数组类型，可以修改请求参数体中 某些Key对应的键值，可为空。
$serviceName : 服务名称
*/
function getReport($serviceName='Account',$param=array()){
	/*获取自动添加后缀的服务名字*/
	$param_auto=auto_completeParam($serviceName);

	/*获取post请求参数体*/
	$post_data = get_postdata($param_auto['info']);

	/*获取post请求 URL*/
	$url = get_requestURL($param_auto['postfix']);

	/*decode json格式的$post_data 数据用于修改数据的成员变量*/
	$post_data_temp = json_decode($post_data);

	/*修改成员变量*/
	if ($param) {
		# code...
		foreach ($param as $key => $value) {
		# code...
		$post_data_temp = updateItem($post_data_temp,$key,$value);
		}
	}
	
/*将修改好的消息体 encode成json格式*/
	$post_data = json_encode($post_data_temp);
	//echo $post_data;
	$output = send_post($url,$post_data);
	return $output;

}
/*函数实现查找并修改json数据 结构中与$key_param对应的成员的值，修改后的值为$value_param，源数据为$data_param
$data_param  : 要修改的json数据；
$key_param :  要查找的key值；
$value_param : $key_param对应的value，用于替换$data_param中与$key_param相同的键的值。

返回值：返回修改后的json数据
*/
function updateItem($data_param,$key_param,$value_param){
	$data_temp = $data_param;
	
	if(is_object($data_temp)){
		foreach ($data_temp as $key1 => &$value1) {
			# code...
			if ($key1 === $key_param) {
				# code...
				$value1 = $value_param;
			}else{
				$value1 = updateItem($value1,$key_param,$value_param);
			}
		}
		unset($value1);
	}
	if(is_array($data_temp)){
		foreach ($data_temp as $key2 => &$value2) {
			# code...
			if ($key2 === $key_param) {
				# code...
				$value2 = $value_param;
			}else{
				$value2 = updateItem($value2,$key_param,$value_param);

			}
		}
		unset($value2);
	}

	return $data_temp;
}


function test(){
	echo "function test successfuly";
}