<?php

/* 返回用户数组，包括用户名，密码，token  */
function get_userNPT(){
	$user_obj = D("Common/Users");
	$uid=sp_get_current_admin_id();
	$admin=$user_obj->where(array("user_login"=>session('username_mcc')))->find();
	$now_token=$admin['token'];
	return array('username'=>session('username_mcc'),'password'=>session('userpwd_mcc'),'token'=>$now_token,'target'=>session('username_normal'));
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
/*发送post请求
参数：$url，请求api的url
$post_data ，请求体
*/
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
/*返回相应服务的报告，
参数：
$param : 数组类型，可以修改请求参数体中 某些Key对应的键值，可为空。
$serviceName : 服务名称(Account, Campaign , Adgroup, Keyword, Creative,NewCreative,Toolkit , DynamicCreative,DynCreativeExclusion,RealTimeData,RealTimeQueryData,RealTimePairData,ProfessionalReportId, ReportState,ReportFileUrl,BulkJob,AllChangedObjects,FileStatus,FilePath,cancelDownload,ChangedId,ChangedItemId,ChangedScale)
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
	//var_dump($post_data_temp);
/*将修改好的消息体 encode成json格式*/
	$post_data = json_encode($post_data_temp);
	//echo $url;
	//echo $post_data;
	if (S($post_data)) {
		# code...
		$output = S($post_data);
	}else{
		$output = send_post($url,$post_data);
		S($post_data,$output,604800);
	}
	

	return $output;

}
/*parameters data format:
["startDate"=>"2018-01-01","endDate"=>"2018-03-03"]
函数功能：返回账户时事报告，默认平台和设备为全部，可通过参数传入选择不同平台和设备。
时间单位可通过“unitOfTime”设置，不设置则默认返回所有情况下的数据。
*/
function getAccountReport_realtime($param = array("startDate"=>"2018-01-01","endDate"=>"2018-03-03","platform"=>0,"device"=>0)  ){
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
	
	/*时间单位设置，1,3,4,5,7，8 分别对应年、月、周、日、小时报、请求时间段*/
	$p_unitOfTime = isset($param['unitOfTime'])?$param['unitOfTime']:5;
	$p_provid = isset($param['provid'])?$param['provid']:null;
	$p_order = isset($param['order'])?$param['order']:null;
	$p_number = isset($param['number'])?$param['number']:1000;
	$p_performanceData=$p_unitOfTime == 7?array('impression','cost','cpc','click','ctr','cpm'):array('impression','cost','cpc','click','ctr','cpm','conversion','phoneConversion','bridgeConversion');
	$param1 = array('startDate' =>$param['startDate'] ,'endDate'=>$param['endDate'],"reportType"=>2,"levelOfDetails"=>2,"platform"=>$param["platform"],"device"=>$param["device"],'unitOfTime'=>$p_unitOfTime,'performanceData'=>$p_performanceData,'order'=>$p_order,'number'=>$p_number,'provid'=>$p_provid );

	$resultData =json_decode(getReport("RealTimeData",$param1));
	return json_encode($resultData);
	
}
/*获取计划实时报告，返回kpi指标数据
参数，startDate，endDate，device，platform，ids（ids可以不填，默认为全部，也可以填写计划id，数组形式，可以填写多个。）。
参照api文档的报告规则，计划报告的其他一些参数写死在函数体中
如reportType，levelOfDetail，statRange为2时表示统计全账户，则ids不填，为3时表示查询指定的ids，unitOfTime指定时间粒度。
*/
function getCampaignReport_realtime($param = array("startDate"=>"2018-01-01","endDate"=>"2018-03-03",'device'=>0,'platform'=>0  )){
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
	/*由于账户层级2，不需要ids，所以如果本方法中如果设置了statids，则可以认为是计划id*/
	$p_stateRange = isset($param['statIds'])?3:2;
	$p_statIds = isset($param['statIds'])?$param['statIds']:null;
	$p_order = isset($param['order'])?$param['order']:null;
	$p_number = isset($param['number'])?$param['number']:1000;
	$p_provid = isset($param['provid'])?$param['provid']:null;
	/*时间单位设置，1,3,4,5,7，8 分别对应年、月、周、日、小时报、请求时间段*/
	$p_unitOfTime = isset($param['unitOfTime'])?$param['unitOfTime']:5;
	$p_performanceData=$p_unitOfTime == 7?array('impression','cost','cpc','click','ctr','cpm'):array('impression','cost','cpc','click','ctr','cpm','conversion','phoneConversion','bridgeConversion');
	$param1 = array('startDate' =>$param['startDate'] ,'endDate'=>$param['endDate'],"reportType"=>10,"levelOfDetails"=>3,"platform"=>$param["platform"],"device"=>$param["device"],'statRange'=>$p_stateRange,'unitOfTime'=>$p_unitOfTime,'statIds'=>$p_statIds,'order'=>$p_order,'number'=>$p_number,'performanceData'=>$p_performanceData,'provid'=>$p_provid  );
	$resultData =json_decode(getReport("RealTimeData",$param1));
	
	return json_encode($resultData);

}
/*查询单元实时报告，返回kpi指标数据
参数，startDate，endDate，device，platform，statIds（statIids可以不填，默认为全部，也可以填写计划id或者单元id，数组形式，可以填写多个。）。
参照api文档的报告规则，计划报告的其他一些参数写死在函数体中
如reportType，levelOfDetail，statRange为2时表示统计全账户，则ids不填，为3时表示查询指定的ids，unitOfTime指定时间粒度。*/
function getAdgrouReport_realtime($param = array("startDate"=>"2018-01-01","endDate"=>"2018-03-03",'device'=>0,'platform'=>0  )){
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
	$p_statRange = isset($param['statRange'])?$param['statRange']:5;
	$p_statIds = isset($param['statIds'])?$param['statIds']:null;
	$p_order = isset($param['order'])?$param['order']:null;
	$p_number = isset($param['number'])?$param['number']:1000;
	$p_provid = isset($param['provid'])?$param['provid']:null;
	/*时间单位设置，1,3,4,5,7，8 分别对应年、月、周、日、小时报、请求时间段*/
	$p_unitOfTime = isset($param['unitOfTime'])?$param['unitOfTime']:5;
	$p_performanceData=$p_unitOfTime == 7?array('impression','cost','cpc','click','ctr','cpm'):array('impression','cost','cpc','click','ctr','cpm','conversion','phoneConversion','bridgeConversion');
	$param1 = array('startDate' =>$param['startDate'] ,'endDate'=>$param['endDate'],"reportType"=>11,"levelOfDetails"=>5,"platform"=>$param["platform"],"device"=>$param["device"],'statRange'=>$p_statRange,'unitOfTime'=>$p_unitOfTime,'statIds'=>$p_statIds,'order'=>$p_order,'number'=>$p_number,'performanceData'=>$p_performanceData ,'provid'=>$p_provid );
	$resultData =json_decode(getReport("RealTimeData",$param1));
	
	return json_encode($resultData);
}
/*查询关键词实时报告，返回kpi指标数据
参数，startDate，endDate，device，platform，statIds（statIids可以不填，默认为全部，也可以填写计划id或者单元id，数组形式，可以填写多个。）。
参照api文档的报告规则，计划报告的其他一些参数写死在函数体中
如reportType，levelOfDetail，statRange为2时表示统计全账户，则statids不填，为3时表示查询指定的计划statids，为5时表示查询指定的单元statids，unitOfTime指定时间粒度。*/
function getKeywordIdReport_realtime($param = array("startDate"=>"2018-01-01","endDate"=>"2018-03-03",'device'=>0,'platform'=>0  )){
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
	/*时间单位设置，1,3,4,5,7，8 分别对应年、月、周、日、小时报、请求时间段*/
	$p_statRange = isset($param['statRange'])?$param['statRange']:11;
	$p_performanceData=$param['unitOfTime'] == 7?array('impression','cost','cpc','click','ctr','cpm'):array('impression','cost','cpc','click','ctr','cpm','position','conversion','bridgeConversion');
	$param1 = array("reportType"=>14,"levelOfDetails"=>11,'performanceData'=>$p_performanceData,'statRange'=>$p_statRange );
	$param1 = array_merge($param,$param1);
	
	$resultData =json_decode(getReport("RealTimeData",$param1));
	
	return json_encode($resultData);
}


/*查询创意实时报告，返回kpi指标数据
参数，startDate，endDate，device，platform，statIds（statIids可以不填，默认为全部，也可以填写计划id或者单元id，数组形式，可以填写多个。）。
参照api文档的报告规则，计划报告的其他一些参数写死在函数体中
如reportType，levelOfDetail，statRange为2时表示统计全账户，则statids不填，为3时表示查询指定的计划statids，为5时表示查询指定的单元statids，unitOfTime指定时间粒度。*/
function getCreativeIdReport_realtime($param = array("startDate"=>"2018-01-01","endDate"=>"2018-03-03",'device'=>0,'platform'=>0  )){
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
	/*时间单位设置，1,3,4,5,7，8 分别对应年、月、周、日、小时报、请求时间段*/
	$p_statRange = isset($param['statRange'])?$param['statRange']:7;
	
	$p_performanceData=$param['unitOfTime'] == 7?array('impression','cost','cpc','click','ctr','cpm'):array('impression','cost','cpc','click','ctr','cpm','position','conversion','bridgeConversion');
	$param1 = array("reportType"=>12,"levelOfDetails"=>7,'performanceData'=>$p_performanceData ,'statRange'=>$p_statRange );
	$param1 = array_merge($param,$param1);
	
	$resultData =json_decode(getReport("RealTimeData",$param1));
	
	return json_encode($resultData);
}


/*查询操作记录实时报告，返回kpi指标数据
参数，startDate，endDate，device，platform，statIds（statIids可以不填，默认为全部，也可以填写计划id或者单元id，数组形式，可以填写多个。）。
参照api文档的报告规则，计划报告的其他一些参数写死在函数体中
如reportType，levelOfDetail，statRange为2时表示统计全账户，则statids不填，为3时表示查询指定的计划statids，为5时表示查询指定的单元statids，unitOfTime指定时间粒度。*/
function getHistoryRankReport_realtime($param = array("startDate"=>"2018-01-01","endDate"=>"2018-03-03",'device'=>0,'platform'=>0  )){
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
	/*时间单位设置，1,3,4,5,7，8 分别对应年、月、周、日、小时报、请求时间段*/
	$p_unitOfTime = isset($param['unitOfTime'])?$param['unitOfTime']:5;
	$p_provid = isset($param['provid'])?$param['provid']:null;
	$p_statIds =isset($param['statIds'])?$param['statIds']:null;
	$p_statRange = isset($param['statRange'])?$param['statRange']:2;
	$p_order = isset($param['order'])?$param['order']:null;
	$p_number = isset($param['number'])?$param['number']:1000;
	$p_performanceData=array('rank1shows','rank2shows','rank3shows','rank4shows','rank1to4shows' );
	$p_device = isset($param['device'])?$param['device']:1;
	$param1 = array('startDate' =>$param['startDate'] ,'endDate'=>$param['endDate'],"reportType"=>38,"levelOfDetails"=>11,"platform"=>$param["platform"],"device"=>$p_device,'unitOfTime'=>$p_unitOfTime,'performanceData'=>$p_performanceData,'order'=>$p_order,'number'=>$p_number,'provid'=>$p_provid ,'statRange'=>$p_statRange,'statIds'=>$p_statIds);

	$resultData =json_decode(getReport("RealTimeData",$param1));
	return json_encode($resultData);
}

function getRegionReport_realtime($param = array("startDate"=>"2018-01-01","endDate"=>"2018-03-03",'device'=>0,'platform'=>0  )){
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
	/*时间单位设置，1,3,4,5，8 分别对应年、月、周、日、请求时间段*/
	$p_unitOfTime = isset($param['unitOfTime'])?$param['unitOfTime']:5;
	$p_provid = isset($param['provid'])?$param['provid']:null;
	$p_statRange = isset($param['statRange'])?$param['statRange']:2;
	$p_levelOfDetails = isset($param['levelOfDetails'])?$param['levelOfDetails']:2;
	$p_order = isset($param['order'])?$param['order']:true;
	$p_number = isset($param['number'])?$param['number']:1000;
	$p_performanceData=array('impression','cost','cpc','click','ctr','cpm','position','conversion' );
	$p_device = isset($param['device'])?$param['device']:0;
	$p_reportType = isset($param['reportType'])?$param['reportType']:3;
	$param1 = array('startDate' =>$param['startDate'] ,'endDate'=>$param['endDate'],"reportType"=>$p_reportType,"levelOfDetails"=>$p_levelOfDetails,"platform"=>$param["platform"],"device"=>$p_device,'unitOfTime'=>$p_unitOfTime,'performanceData'=>$p_performanceData,'order'=>$p_order,'number'=>$p_number,'provid'=>$p_provid );

	$resultData =json_decode(getReport("RealTimeData",$param1));
	return json_encode($resultData);
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
function getAccountList(){
	$p_id = session('p_id');
        
    if($p_id ==1  ){ // 说明是mcc账户
            // 查出该mcc账户下的所有子账户数据
        $data = M("users")->where('p_id = '.session('ADMIN_ID'))->select();
        $result=array();
        foreach ($data as $key => $value) {
            $result[$value['trade']][$value['companyname']][]=$value;
        }
       
    }else{
        // 子账户
        $data = M("users")->where(array('id'=>session(ADMIN_ID)))->select();
        $result=array();

        foreach ($data as $key => $value) {
            $result[""][$value['companyname']][]=$value;
        }
    }
    
    return $result;
}
/*获取计划的名字*/
function getCampaignIdList(){
	$temp = json_decode(getReport('Campaign'))->body->data ;
	if (empty($temp)) {
		# code...
		$params = array('mobileExtend' => 1 );
		//var_dump($temp);
		$temp = json_decode(getReport('Campaign',$params))->body->data;
	}
	$result = array( );
    foreach ($temp as $key => $value) {
        $result[session('username_normal')][]=$value;
    }

	//var_dump($result);
	return $result;
}
function getAdgroupIdList(){
	$temp = json_decode(getReport('Campaign'))->body->data ;
	
	$result = array( );
	$ids = array( );
	foreach ($temp as $key => $value) {

		$ids[]=$value->campaignId;
	}
	$idtype = 3;
	$params = array('ids' => $ids,'idType'=>$idtype );

	$adgroupdata = json_decode(getReport('Adgroup',$params))->body->data;

	//var_dump($adgroupdata);
	foreach ($adgroupdata as $key1 => $value1) {
		# code...
		$tt = $temp;
		foreach ($tt as $key2 => $value2) {
			if ($value2->campaignId == $value1->campaignId) {
				$result[session('username_normal')][$value2->campaignName][]=$value1;
			}
			
		}
		
	}
	
	//var_dump($result);
	return $result;
}
/**/
function getKeyWordIDList($param = array()){
	if (empty($param)) {
		# code...
		$all_temp = getAdgroupIdList();
		$result = array();
		$ids = array( );
		$idtype = 5;
		foreach ($all_temp as $key1 => $value1) {
			# code...
			foreach ($value1 as $key2 => $value2) {
				# code...
				foreach ($value2 as $key3 => $value3) {
					# code...
					$ids[] = $value3->adgroupId;
				}
			}
		}
		$param1 = array('ids' => $ids,'idType'=>$idtype,'getTemp'=>0 );
		$keywordData = json_decode(getReport('Keyword',$param1))->body->data;
		$param2 = array('ids' => $ids,'idType'=>$idtype,'getTemp'=>1);
		$keywordShadowData=json_decode(getReport('Keyword',$param2))->body->data;
		foreach ($keywordShadowData as $k => $v) {
			# code...
			$keywordData[] = $v;
		}
		foreach ($keywordData as $key => $value) {
			# code...
			$d1 = $all_temp;
			foreach ($d1 as $key1 => $value1) {
				# code...
				$d2 = $value1;
				foreach ($d2 as $key2 => $value2) {
					# code...
					$d3 =$value2;
					foreach ($d3 as $key3 => $value3) {
						# code...
						if ($value->adgroupId ==$value3->adgroupId) {
						# code...
						$result[$key1][$key2][$value3->adgroupName][]=$value;
						}
					}	
				}
			}
		}

		return $result;
	}else{

	}
}
function getCreativeIdList($param = array()){
	if (empty($param)) {
		# code...
		$all_temp = getAdgroupIdList();
		$result = array();
		$ids = array( );
		$idtype = 5;
		foreach ($all_temp as $key1 => $value1) {
			# code...
			foreach ($value1 as $key2 => $value2) {
				# code...
				foreach ($value2 as $key3 => $value3) {
					# code...
					$ids[] = $value3->adgroupId;
				}
			}
		}
		$param1 = array('ids' => $ids,'idType'=>$idtype,'getTemp'=>0 );
		$keywordData = json_decode(getReport('Creative',$param1))->body->data;
		$param2 = array('ids' => $ids,'idType'=>$idtype,'getTemp'=>1);
		$keywordShadowData=json_decode(getReport('Creative',$param2))->body->data;
		foreach ($keywordShadowData as $k => $v) {
			# code...
			$keywordData[] = $v;
		}
		//var_dump($keywordData);
		foreach ($keywordData as $key => $value) {
			# code...
			$d1 = $all_temp;
			foreach ($d1 as $key1 => $value1) {
				# code...
				$d2 = $value1;
				foreach ($d2 as $key2 => $value2) {
					# code...
					$d3 =$value2;
					foreach ($d3 as $key3 => $value3) {
						# code...
						if ($value->adgroupId ==$value3->adgroupId) {
						# code...
						$result[$key1][$key2][$value3->adgroupName][]=$value;
						}
					}	
				}
			}
		}

		return $result;
	}else{

	}
}
function dispatch_kpijob($param_string="计划",$params = array()){
	$result = array();
	//var_dump($params);
	/*echo "dispatch_myjob";
	echo $param_string;*/
	switch ($param_string) {
		case '账户':
			# code...
		return getAccountReport_realtime($params);
			break;
		case '计划':
			# code...
		return getCampaignReport_realtime($params);
			break;
		case '单元':
		return getAdgrouReport_realtime($params);
			break;
		case '关键词':
			# code...
		return getKeywordIdReport_realtime($params);
			break;
		case '创意':
			# code...
		return getCreativeIdReport_realtime($params);
			break;
		default:
			# code...
		return false;
			break;
	}

}
function dispatch_myjob($param_string="计划"){
	$result = array();
	//var_dump($param_string);
	/*echo "dispatch_myjob";
	echo $param_string;*/
	switch ($param_string) {
		case '计划':
			# code...
		return getCampaignIdList();
			break;
		case '单元':
		return getAdgroupIdList();
			break;
		case '关键词':
			# code...
		return getKeyWordIDList();
			break;
		case '创意':
			# code...
		return getCreativeIdList();
			break;
        case '账户':
            # code...
        return getAccountList();
            break;
		default:
			# code...
		return false;
			break;
	}
}
 function mysort($array, $desc = false){
     	//var_dump($array);
       foreach ($array as $k => &$v) {
         if ($desc) {
         	for ($i= count($v['data']); $i >0 ; $i--) { 
         		for ($j=0; $j < $i-1 ; $j++) { 
         			if ($v['data'][$j] < $v['data'][$j+1] ) {
         				$temp = $v['data'][$j];
         				$v['data'][$j]=$v['data'][$j+1];
         				$v['data'][$j+1]=$temp;
         				$temp1 = $v['name'][$j];
         				$v['name'][$j]=$v['name'][$j+1];
         				$v['name'][$j+1]=$temp1;
         			}else{	
         				continue;
         			}	
         		}
         	}
         }else{
         	for ($i= count($v['data']); $i >0 ; $i--) { 
         		for ($j=0; $j < $i-1 ; $j++) { 
         			if ($v['data'][$j] > $v['data'][$j+1] ) {
         				$temp = $v['data'][$j];
         				$v['data'][$j]=$v['data'][$j+1];
         				$v['data'][$j+1]=$temp;
         				$temp1 = $v['name'][$j];
         				$v['name'][$j]=$v['name'][$j+1];
         				$v['name'][$j+1]=$temp1;
         			}else{	
         				continue;
         			}	
         		}
         	}
         }
       }
       unset($v);
       //var_dump($array);
       return $array;


    }
/*在数组中查找key键，有则返回true，没有则返回false*/
function deep_in_array($needle,$arr)
{

	foreach ($arr as $k => $v) {
		if ($v == $needle) {
			return true;
		}
		if (is_array($v) || is_object($v)) {
			if (deep_in_array($needle,$v)) {
				return true;
			}else{
				continue;
			}
		}
	}
	return false;
}

function account_function_byyear($category='账户',$params=array()){
	$result_json = json_decode(dispatch_kpijob($category,$params))->body->data;

	return $result_json;
}

function account_function_bymonth($category='账户',$params=array()){
	$result_json = json_decode(dispatch_kpijob($category,$params))->body->data;
	return $result_json;
}

function account_function_byweek($category='账户',$params=array()){
	
	$result_json = json_decode(dispatch_kpijob($category,$params))->body->data;

	$k_avgrank_trend = json_decode(getKeywordIdReport_realtime($params))->body->data;
	if (!$result_json) {
         $trend_data['date'][] =0;
           $trend_data['impression'][] =0;
           $trend_data['cost'][] =0;
           $trend_data['cpc'][] =0;
           $trend_data['click'][] =0;
           $trend_data['ctr'][] =0;
           $trend_data['cpm'][] =0;
           $trend_data['conversion'][] =0;
           $trend_data['phoneConversion'][] =0;
           $trend_data['bridgeConversion'][] =0;
           $trend_data['name']=0;
           $trend_data['position'][] = 0;
                    
       }
       foreach ($result_json as $key => $value) {
           $trend_data['date'][] =$value->date;
           $trend_data['impression'][] =$value->kpis[0];
           $trend_data['cost'][] =$value->kpis[1];
           $trend_data['cpc'][] =round($value->kpis[2],2);
           $trend_data['click'][] =$value->kpis[3];
           $trend_data['ctr'][] =round($value->kpis[4],2);
           $trend_data['cpm'][] =round($value->kpis[5],2);
           $trend_data['conversion'][] =$value->kpis[6];
           $trend_data['phoneConversion'][] =$value->kpis[7];
           $trend_data['bridgeConversion'][] =$value->kpis[8];
           $trend_data['name']=$value->name;
           $n_count =0;
           $sum_temp=0;
           foreach ($k_avgrank_trend as $k1 => $v1) {
              if (strcmp($v1->date, $value->date)) {
                  $sum_temp += $v1->kpis[6];
                  if ($v1->kpis[6] > 0) {
                    $n_count++;
                  }
              }
            }  
            $trend_data['position'][] = round($sum_temp/$n_count,2);     
       }
	return $trend_data;
}

function account_function_byday($category='账户',$params=array()){

	$result_json = json_decode(dispatch_kpijob($category,$params))->body->data;

	$k_avgrank_trend = json_decode(getKeywordIdReport_realtime($params))->body->data;
	if ($category =="账户") {
       foreach ($result_json as $key => $value) {
           $trend_data['date'][] =$value->date;
           $trend_data['impression'][] =$value->kpis[0];
           $trend_data['cost'][] =$value->kpis[1];
           $trend_data['cpc'][] =round($value->kpis[2],2);
           $trend_data['click'][] =$value->kpis[3];
           $trend_data['ctr'][] =round($value->kpis[4],2);
           $trend_data['cpm'][] =round($value->kpis[5],2);
           $trend_data['conversion'][] =$value->kpis[6];
           $trend_data['phoneConversion'][] =$value->kpis[7];
           $trend_data['bridgeConversion'][] =$value->kpis[8];
           $trend_data['name']=$value->name;
           $n_count =0;
           $sum_temp=0;
           foreach ($k_avgrank_trend as $k1 => $v1) {
              if (strcmp($v1->date, $value->date)) {
                  $sum_temp += $v1->kpis[6];
                  if ($v1->kpis[6] > 0) {
                    $n_count++;
                  }
              }
            }  
            $trend_data['position'][] = round($sum_temp/$n_count,2);     
       }
	}else{
		foreach ($result_json as $key => $value) {
			$result_temp[$value->date]['date'] = $value->date;
			$result_temp[$value->date]['impression'] += $value->kpis[0];
			$result_temp[$value->date]['cost'] += $value->kpis[1] ;
			$result_temp[$value->date]['click'] += $value->kpis[3];
			$result_temp[$value->date]['name'] = $value->name;
		}
		foreach ($result_temp as $key => $value) {
			$result_temp[$key]['cpc'] = round($value['cost']/$value['click'],2);
			$result_temp[$key]['ctr'] =round($value['cost']/$value['impression'],2);
		}
		/*var_dump($result_temp);*/
		foreach ($result_temp as $key => $value) {
			$trend_data['date'][] =$value['date'];
            $trend_data['impression'][] =$value['impression'];
            $trend_data['cost'][] =$value['cost'];
            $trend_data['cpc'][] =$value['cpc'];
            $trend_data['click'][] =$value['click'];
            $trend_data['ctr'][] =$value['ctr'];
            $trend_data['name']=$value['name'];
            $n_count =0;
            $sum_temp=0;
           foreach ($k_avgrank_trend as $k1 => $v1) {
              if (strcmp($v1->date, $value->date)) {
                  $sum_temp += $v1->kpis[6];
                  if ($v1->kpis[6] > 0) {
                    $n_count++;
                  }
              }
            }  
            $trend_data['position'][] = round($sum_temp/$n_count,2);
		}
	}
	
	if (!$result_json) {
         $trend_data['date'][] =0;
           $trend_data['impression'][] =0;
           $trend_data['cost'][] =0;
           $trend_data['cpc'][] =0;
           $trend_data['click'][] =0;
           $trend_data['ctr'][] =0;
           $trend_data['cpm'][] =0;
           $trend_data['conversion'][] =0;
           $trend_data['phoneConversion'][] =0;
           $trend_data['bridgeConversion'][] =0;
           $trend_data['name']=0;
           $trend_data['position'][] = 0;
                    
       }
       /*var_dump($trend_data);*/
       
	return $trend_data;
}

function account_function_byhour($category='账户',$params=array()){
	$fenshi_t = json_decode(dispatch_kpijob($category,$params))->body->data;
	$fenshi_temp['date'] = array('00','01','02','03','04','05','06','07','08','09','10','11','12','13','14','15','16','17','18','19','20','21','22','23');
       $fenshi_temp['name']=$fenshi_t[0]->name;
        foreach ($fenshi_temp['date'] as $key => $value) {
            $fenshi_temp['impression'][(int)$value] = isset($fenshi_temp['impression'][$value])?$fenshi_temp['impression'][$value]:0;
            $fenshi_temp['cost'][(int)$value] = isset($fenshi_temp['cost'][$value])?$fenshi_temp['cost'][$value]:0;
            $fenshi_temp['click'][(int)$value] = isset($fenshi_temp['click'][$value])?$fenshi_temp['click'][$value]:0;
       }

       foreach ($fenshi_t as $key => $value) {
            $n = explode(' ', $value->date);
            $nn = explode(':',$n[1]);
           $fenshi_temp['impression'][(int)$nn[0]] += $value->kpis[0];
           $fenshi_temp['cost'][(int)$nn[0]] += $value->kpis[1];
           $fenshi_temp['click'][(int)$nn[0]] += $value->kpis[3];
       }
       foreach ($fenshi_temp['date'] as $key => $value) {
           $fenshi_temp['cpc'][(int)$value] = $fenshi_temp['click'][(int)$value]==0?0:round($fenshi_temp['cost'][(int)$value]/$fenshi_temp['click'][(int)$value],2);
           $fenshi_temp['ctr'][(int)$value] = $fenshi_temp['impression'][(int)$value]==0?0:round($fenshi_temp['click'][(int)$value]/$fenshi_temp['impression'][(int)$value],2);
       }
	return $fenshi_temp;
}

function account_function_byDrate($category='账户',$params=array()){
	$param_temp = $params;
	$param_temp['unitOfTime']=8;

	$param_temp['device'] =1;
	$pc_json = account_function_byday($category,$param_temp);
	$param_temp['device'] =2;
	$mobile_json = account_function_byday($category,$param_temp);
	$result_json['pc'] = $pc_json;
	$result_json['mobile'] = $mobile_json;
	return $result_json;
}

function account_function_byregion($category='账户',$params=array()){
	/*获取地域(二级地域)分布图mychart3数据*/
       $param_region=$params;
       $param_region['unitOfTime']=8;
       $param_region2 =$param_region;
       $param_region2['reportType']=5;

       $region_json = json_decode(getRegionReport_realtime($param_region))->body->data ;
       $region_json2 = json_decode(getRegionReport_realtime($param_region2))->body->data ;
       //var_dump($region_json2);
       foreach ($region_json as $key => $value) {
           $region_grid['impression']['name'][]=$value->name[3];
           $region_grid['cost']['name'][]=$value->name[3];
           $region_grid['cpc']['name'][]=$value->name[3];
           $region_grid['click']['name'][]=$value->name[3];
           $region_grid['ctr']['name'][]=$value->name[3];
           $region_grid['cpm']['name'][]=$value->name[3];
           $region_grid['position']['name'][]=$value->name[3];

           $region_grid['impression']['data'][]=$value->kpis[0];
           $region_grid['cost']['data'][]=$value->kpis[1];
           $region_grid['cpc']['data'][]=round($value->kpis[2],2);
           $region_grid['click']['data'][]=$value->kpis[3];
           $region_grid['ctr']['data'][]=round($value->kpis[4],2);
           $region_grid['cpm']['data'][]=round($value->kpis[5],2);
           $region_grid['position']['data'][]=$value->kpis[6];
       }
       $region_grid = mysort($region_grid,true);
       foreach ($region_json as $key => $value) {
           $region_map['impression'][] =json_decode(json_encode(array('name' =>$value->name[3] ,'value'=>round($value->kpis[0]),'id'=>$value->id )))  ;
           $region_map['cost'][] =json_decode(json_encode(array('name' =>$value->name[3] ,'value'=>$value->kpis[1],'id'=>$value->id )))  ;
           $region_map['cpc'][] =json_decode(json_encode(array('name' =>$value->name[3] ,'value'=>round($value->kpis[2],2),'id'=>$value->id )))  ;
           $region_map['click'][] =json_decode(json_encode(array('name' =>$value->name[3] ,'value'=>$value->kpis[3],'id'=>$value->id )))  ;
           $region_map['ctr'][] =json_decode(json_encode(array('name' =>$value->name[3] ,'value'=>round($value->kpis[4],2),'id'=>$value->id )))  ;
           $region_map['cpm'][] =json_decode(json_encode(array('name' =>$value->name[3] ,'value'=>round($value->kpis[5],2),'id'=>$value->id )))  ;
           $region_map['position'][] =json_decode(json_encode(array('name' =>$value->name[3] ,'value'=>$value->kpis[6],'id'=>$value->id )))  ;
       }
       $region_data = array('region_map' =>$region_map ,'region_grid'=>$region_grid );
       
    /*region 2二级地域*/

       foreach ($region_json2 as $key => $value) {
           $region_grid2['impression']['name'][]=$value->name[3];
           $region_grid2['cost']['name'][]=$value->name[3];
           $region_grid2['cpc']['name'][]=$value->name[3];
           $region_grid2['click']['name'][]=$value->name[3];
           $region_grid2['ctr']['name'][]=$value->name[3];
           $region_grid2['cpm']['name'][]=$value->name[3];
           $region_grid2['position']['name'][]=$value->name[3];

           $region_grid2['impression']['data'][]=$value->kpis[0];
           $region_grid2['cost']['data'][]=$value->kpis[1];
           $region_grid2['cpc']['data'][]=round($value->kpis[2],2);
           $region_grid2['click']['data'][]=$value->kpis[3];
           $region_grid2['ctr']['data'][]=round($value->kpis[4],2);
           $region_grid2['cpm']['data'][]=round($value->kpis[5],2);
           $region_grid2['position']['data'][]=$value->kpis[6];
       }
       $region_grid2 = mysort($region_grid2,true);
       foreach ($region_json2 as $key => $value) {
           $region_map2['impression'][] =json_decode(json_encode(array('name' =>$value->name[3] ,'value'=>round($value->kpis[0]),'id'=>$value->id )))  ;
           $region_map2['cost'][] =json_decode(json_encode(array('name' =>$value->name[3] ,'value'=>$value->kpis[1],'id'=>$value->id )))  ;
           $region_map2['cpc'][] =json_decode(json_encode(array('name' =>$value->name[3] ,'value'=>round($value->kpis[2],2),'id'=>$value->id )))  ;
           $region_map2['click'][] =json_decode(json_encode(array('name' =>$value->name[3] ,'value'=>$value->kpis[3],'id'=>$value->id )))  ;
           $region_map2['ctr'][] =json_decode(json_encode(array('name' =>$value->name[3] ,'value'=>round($value->kpis[4],2),'id'=>$value->id )))  ;
           $region_map2['cpm'][] =json_decode(json_encode(array('name' =>$value->name[3] ,'value'=>round($value->kpis[5],2),'id'=>$value->id )))  ;
           $region_map2['position'][] =json_decode(json_encode(array('name' =>$value->name[3] ,'value'=>$value->kpis[6],'id'=>$value->id )))  ;
       }
       $region_data2 = array('region_map2' =>$region_map2 ,'region_grid2'=>$region_grid2 );
       $result_json = array('region_data' =>$region_data ,'region_data2'=>$region_data2 );
	return $result_json;
}

function account_function_bytire($category='账户',$params=array()){
	//var_dump($params);
	$result_json = json_decode(dispatch_kpijob($category,$params))->body->data;

	$k_avgrank_trend = json_decode(getKeywordIdReport_realtime())->body->data;
	switch ($category) {
		case '账户':
		case '计划':
		case '单元':
			foreach ($result_json as $key => $value) {
				$trend_data[$key]['date'] =$value->date;
				$trend_data[$key]['impression'] =$value->kpis[0];
				$trend_data[$key]['cost'] =$value->kpis[1];
				$trend_data[$key]['cpc'] =round($value->kpis[2],2);
				$trend_data[$key]['click'] =$value->kpis[3];
				$trend_data[$key]['ctr'] =round($value->kpis[4],2);
				$trend_data[$key]['cpm'] =round($value->kpis[5],2);
				$trend_data[$key]['conversion'] =$value->kpis[6];
				$trend_data[$key]['phoneConversion'] =$value->kpis[7];
				$trend_data[$key]['bridgeConversion'] =$value->kpis[8];
				if($category=='账户'){
					$trend_data[$key]['name']=$value->name[0];
				}elseif($category=='计划'){
					$trend_data[$key]['name']=$value->name[1];
				}else {
					$trend_data[$key]['name']=$value->name[2];
				}
				
				$n_count =0;
				$sum_temp=0;
				foreach ($k_avgrank_trend as $k1 => $v1) {
			   		if (strcmp($v1->date, $value->date)) {
				   	$sum_temp += $v1->kpis[6];
				   		if ($v1->kpis[6] > 0) {
					 	$n_count++;
				   		}
			   		}
			 	}  
			 	$trend_data[$key]['position'] = round($sum_temp/$n_count,2);     
			}
			break;
		case '关键词':
		foreach ($result_json as $key => $value) {
			$trend_data[$key]['date'] =$value->date;
			$trend_data[$key]['impression'] =$value->kpis[0];
			$trend_data[$key]['cost'] =$value->kpis[1];
			$trend_data[$key]['cpc'] =round($value->kpis[2],2);
			$trend_data[$key]['click'] =$value->kpis[3];
			$trend_data[$key]['ctr'] =round($value->kpis[4],2);
			$trend_data[$key]['cpm'] =round($value->kpis[5],2);
			$trend_data[$key]['position'] =$value->kpis[6];
			$trend_data[$key]['conversion'] =$value->kpis[7];
			$trend_data[$key]['bridgeConversion'] =$value->kpis[8];
			$trend_data[$key]['name']=$value->name[3];    
		}
			break;
		case '创意':
		foreach ($result_json as $key => $value) {
			$trend_data[$key]['date'] =$value->date;
			$trend_data[$key]['impression'] =$value->kpis[0];
			$trend_data[$key]['cost'] =$value->kpis[1];
			$trend_data[$key]['cpc'] =round($value->kpis[2],2);
			$trend_data[$key]['click'] =$value->kpis[3];
			$trend_data[$key]['ctr'] =round($value->kpis[4],2);
			$trend_data[$key]['cpm'] =round($value->kpis[5],2);
			$trend_data[$key]['position'] =$value->kpis[6];
			$trend_data[$key]['conversion'] =$value->kpis[7];
			$trend_data[$key]['bridgeConversion'] =$value->kpis[8];
			$trend_data[$key]['name']=$value->name[3];    
		}
			break;
		default:
			# code...
			break;
	}
	return $trend_data;
}
function account_function_bydata($category='账户',$params=array()){
	$result_json = json_decode(dispatch_kpijob($category,$params))->body->data;
	foreach ($result_json as $key => $value) {
		switch ($category) {
			case '账户':
				$pie_data['name'][]=$value->name[0];
				$pie_data['impression']['data'][]=array('name'=>$value->name[0],'value'=>$value->kpis[0]);
				$pie_data['cost']['data'][]=array('name'=>$value->name[0],'value'=>$value->kpis[1]);
				$pie_data['click']['data'][]=array('name'=>$value->name[0],'value'=>$value->kpis[3]);
				break;
			case '计划':
				$pie_data['name'][]=$value->name[1];
				$pie_data['impression']['data'][]=array('name'=>$value->name[1],'value'=>$value->kpis[0]);
				$pie_data['cost']['data'][]=array('name'=>$value->name[1],'value'=>$value->kpis[1]);
				$pie_data['click']['data'][]=array('name'=>$value->name[1],'value'=>$value->kpis[3]);
				break;
			case '单元':
				$pie_data['name'][]=$value->name[2];
				$pie_data['impression']['data'][]=array('name'=>$value->name[2],'value'=>$value->kpis[0]);
				$pie_data['cost']['data'][]=array('name'=>$value->name[2],'value'=>$value->kpis[1]);
				$pie_data['click']['data'][]=array('name'=>$value->name[2],'value'=>$value->kpis[3]);
				break;
			case '关键词':
				$pie_data['name'][]=$value->name[3];
				$pie_data['impression']['data'][]=array('name'=>$value->name[3],'value'=>$value->kpis[0]);
				$pie_data['cost']['data'][]=array('name'=>$value->name[3],'value'=>$value->kpis[1]);
				$pie_data['click']['data'][]=array('name'=>$value->name[3],'value'=>$value->kpis[3]);
				break;
			case '创意':
				$pie_data['name'][]=$value->name[3];
				$pie_data['impression']['data'][]=array('name'=>$value->name[3],'value'=>$value->kpis[0]);
				$pie_data['cost']['data'][]=array('name'=>$value->name[3],'value'=>$value->kpis[1]);
				$pie_data['click']['data'][]=array('name'=>$value->name[3],'value'=>$value->kpis[3]);
				break;
			default:
				# code...
				break;
		}
	}
	return $pie_data;
}
function account_function_bytire_compare($category='账户',$params=array()){
	$result_json = json_decode(dispatch_kpijob($category,$params))->body->data;
	foreach ($result_json as $key => $value) {
		switch ($category) {
			case '账户':
				
			case '计划':
				
			case '单元':
				if ($category=='账户') {
					$tire_cmp_data['name'][]=$value->name[0];
				}elseif ($category=='计划') {
					$tire_cmp_data['name'][]=$value->name[1];
				}else{
					$tire_cmp_data['name'][]=$value->name[2];
				}
				$tire_cmp_data['impression'][]=$value->kpis[0];
				$tire_cmp_data['cost'][]=$value->kpis[1];
				$tire_cmp_data['cpc'][]=$value->kpis[2];
				$tire_cmp_data['click'][]=$value->kpis[3];
				$tire_cmp_data['ctr'][]=$value->kpis[4];
				
				break;
			case '关键词':
			case '创意':
				$tire_cmp_data['name'][]=$value->name[3];
				$tire_cmp_data['impression'][]=$value->kpis[0];
				$tire_cmp_data['cost'][]=$value->kpis[1];
				$tire_cmp_data['cpc'][]=$value->kpis[2];
				$tire_cmp_data['click'][]=$value->kpis[3];
				$tire_cmp_data['ctr'][]=$value->kpis[4];
				$tire_cmp_data['position'][]=$value->kpis[6];
				
				break;
			
			default:
				# code...
				break;
		}
	}
	return $tire_cmp_data;
}
function leftrank_function_byday($params=array()){
	$result_json = json_decode(getHistoryRankReport_realtime($params))->body->data;
	foreach ($result_json as $key => $value) {
		$trend_data[$value->date]['first'] +=$value->kpis[0];
		$trend_data[$value->date]['second'] +=$value->kpis[1];
		$trend_data[$value->date]['third'] +=$value->kpis[2];
		$trend_data[$value->date]['forth'] +=$value->kpis[3];
	}
	foreach ($trend_data as $key => $value) {
		$return_data['date'][]=$key;
		$return_data['data']['first'][]=array('name'=>'排名第一展现','value'=>$value['first']);
		$return_data['data']['second'][]=array('name'=>'排名第二展现','value'=>$value['second']);
		$return_data['data']['third'][]=array('name'=>'排名第三展现','value'=>$value['third']);
		$return_data['data']['forth'][]=array('name'=>'排名第四展现','value'=>$value['forth']);
			
	}
	/*grid_data*/
	foreach ($result_json as $key => $value) {
		$grid_data[$key]['日期']=$value->date;
		$grid_data[$key]['账户']=$value->name[0];
		$grid_data[$key]['计划']=$value->name[1];
		$grid_data[$key]['单元']=$value->name[2];
		$grid_data[$key]['关键词']=$value->name[3];
		$grid_data[$key]['第一展现排名']=$value->kpis[0];
		$grid_data[$key]['第二展现排名']=$value->kpis[1];
		$grid_data[$key]['第三展现排名']=$value->kpis[2];
		$grid_data[$key]['第四展现排名']=$value->kpis[3];
	}
	/*grid_fields*/
        foreach ($grid_data[0] as $key => $value) {
            switch ($key) {
                case '日期':
                case '账户':
                case '关键词':
                case '创意':
                
                    $grid_fields[]=array('name'=>$key,'type'=>'text','width'=>110);
                    break;
                case '描述1':
                case '描述2':
                case 'url':
                    $grid_fields[]=array('name'=>$key,'type'=>'textarea','width'=>190);
                    break;
                default:
                     $grid_fields[]=array('name'=>$key,'type'=>'text','width'=>70);
                    break;
            }
           
        }
	$return_data['grid_data']=$grid_data;
	$return_data['grid_fields']=$grid_fields;
	return $return_data;
}
function leftrank_function_byhour($params){
	$result_json = json_decode(getHistoryRankReport_realtime($params))->body->data;

	foreach ($result_json as $key => $value) {
		$d = explode(' ', $value->date);
		$trend_data[(int)$d[1]]['first'] +=$value->kpis[0];
		$trend_data[(int)$d[1]]['second'] +=$value->kpis[1];
		$trend_data[(int)$d[1]]['third'] +=$value->kpis[2];
		$trend_data[(int)$d[1]]['forth'] +=$value->kpis[3];
	}
	$hours = array(0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23 );
	foreach ($hours as $key => $value) {
		$hour_data[$key]['first']=$trend_data[$key]['first']?$trend_data[$key]['first']:0;
		$hour_data[$key]['second']=$trend_data[$key]['first']?$trend_data[$key]['second']:0;
		$hour_data[$key]['third']=$trend_data[$key]['first']?$trend_data[$key]['third']:0;
		$hour_data[$key]['forth']=$trend_data[$key]['first']?$trend_data[$key]['forth']:0;
	}
	foreach ($hour_data as $key => $value) {
		$return_data['date'][]=$key;
		$return_data['data']['first'][]=array('name'=>'排名第一展现','value'=>$value['first']);
		$return_data['data']['second'][]=array('name'=>'排名第二展现','value'=>$value['second']);
		$return_data['data']['third'][]=array('name'=>'排名第三展现','value'=>$value['third']);
		$return_data['data']['forth'][]=array('name'=>'排名第四展现','value'=>$value['forth']);
			
	}
	/*grid_data*/
	foreach ($result_json as $key => $value) {
		$grid_data[$key]['日期']=$value->date;
		$grid_data[$key]['账户']=$value->name[0];
		$grid_data[$key]['计划']=$value->name[1];
		$grid_data[$key]['单元']=$value->name[2];
		$grid_data[$key]['关键词']=$value->name[3];
		$grid_data[$key]['第一展现排名']=$value->kpis[0];
		$grid_data[$key]['第二展现排名']=$value->kpis[1];
		$grid_data[$key]['第三展现排名']=$value->kpis[2];
		$grid_data[$key]['第四展现排名']=$value->kpis[3];
	}
	/*grid_fields*/
        foreach ($grid_data[0] as $key => $value) {
            switch ($key) {
                case '日期':
                case '账户':
                case '关键词':
                case '创意':
                
                    $grid_fields[]=array('name'=>$key,'type'=>'text','width'=>110);
                    break;
                case '描述1':
                case '描述2':
                case 'url':
                    $grid_fields[]=array('name'=>$key,'type'=>'textarea','width'=>190);
                    break;
                default:
                     $grid_fields[]=array('name'=>$key,'type'=>'text','width'=>70);
                    break;
            }
           
        }
	$return_data['grid_data']=$grid_data;
	$return_data['grid_fields']=$grid_fields;
	return $return_data;
}
function test(){
	echo "function test successfuly";
}