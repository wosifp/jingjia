<?php

/* 返回用户数组，包括用户名，密码，token  */
function get_userNPT(){
	return array('username'=>C('username'),'password'=>C('password'),'token'=>C('token'));
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
function DD($name='',$layer='') {
    if(empty($name)) return new Think\Model;
	echo $name.$layer.'---------','<br>';
    static $_model  =   array();
    $layer          =   $layer? : C('DEFAULT_M_LAYER');
	echo $layer.'<br>';
    if(isset($_model[$name.$layer]))
        return $_model[$name.$layer];
    $class          =   parse_res_name($name,$layer);
	/* echo $class; */
    if(class_exists($class)) {
		echo 'found class '.$class;
        $model      =   new $class(basename($name));
    }elseif(false === strpos($name,'/')){
        // 自动加载公共模块下面的模型
		echo 'not found class';
        if(!C('APP_USE_NAMESPACE')){
            import('Common/'.$layer.'/'.$class);
			echo 'C(APP_USE_NAMESPACE)';
        }else{
            $class      =   '\\Common\\'.$layer.'\\'.$name.$layer;
        }
		echo $class;
        $model      =   class_exists($class)? new $class($name) : new Think\Model($name);
    }else {
		echo 'not find';
        Think\Log::record('D方法实例化没找到模型类'.$class,Think\Log::NOTICE);
        $model      =   new Think\Model(basename($name));
    }
    $_model[$name.$layer]  =  $model;
    return $model;
}

function test(){
	echo "function test successfuly";
}