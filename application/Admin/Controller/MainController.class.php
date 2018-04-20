<?php
namespace Admin\Controller;

use Common\Controller\AdminbaseController;

class MainController extends AdminbaseController {
	
    public function index(){
    	
    	$mysql= M()->query("select VERSION() as version");
    	$mysql=$mysql[0]['version'];
    	$mysql=empty($mysql)?L('UNKNOWN'):$mysql;
    	
    	//server infomaions
    	$info = array(
    			L('OPERATING_SYSTEM') => PHP_OS,
    			L('OPERATING_ENVIRONMENT') => $_SERVER["SERVER_SOFTWARE"],
    	        L('PHP_VERSION') => PHP_VERSION,
    			L('PHP_RUN_MODE') => php_sapi_name(),
				L('PHP_VERSION') => phpversion(),
    			L('MYSQL_VERSION') =>$mysql,
    			L('PROGRAM_VERSION') => THINKCMF_VERSION . "&nbsp;&nbsp;&nbsp; [<a href='http://www.thinkcmf.com' target='_blank'>ThinkCMF</a>]",
    			L('UPLOAD_MAX_FILESIZE') => ini_get('upload_max_filesize'),
    			L('MAX_EXECUTION_TIME') => ini_get('max_execution_time') . "s",
    			L('DISK_FREE_SPACE') => round((@disk_free_space(".") / (1024 * 1024)), 2) . 'M',
    	);

    	// 获取数据概况账户列表
        $this->assign('trade',getAccountList());

        // 获取数据概况账户列表结束
        $target = $_POST['target'] ?$_POST['target']:0;
        if($target){
            session('username_normal',$target);
        }
        
       /*static $p_temp = array();*/
        /*设置默认值*/
      
        $datepicker = explode(" ",  $_REQUEST['datepicker']);
        S('firtp_datepicker',$_REQUEST['datepicker']?$_REQUEST['datepicker']:S('firtp_datepicker'),300);
        
        S('firtp_startDate',$datepicker[0]?$datepicker[0]:S('firtp_startDate'),300);
        S('firtp_endDate',$datepicker[2]?$datepicker[2]:S('firtp_endDate'),300);
        S('firtp_device',$_REQUEST['device']?$_REQUEST['device']==3?0:$_REQUEST['firtp_device']:S(device),300);
        $area_str = $_REQUEST['area_value'];
        /*S('provid',$_REQUEST)*/
        S('firtp_area_city',$_REQUEST['area_city']?$_REQUEST['area_city']:S('firtp_area_city'),300);
        $p_temp = array( );
        $p_temp['startDate']=S('firtp_startDate')?S('firtp_startDate'):date('Y-m-d',strtotime("-2 day"));
        $p_temp['endDate']=S('firtp_endDate')?S('firtp_endDate'):date('Y-m-d',strtotime("-1 day"));
        $p_temp['device']=S('firtp_device')?S('firtp_device'):0;
        $p_temp['unitOfTime']=8;
        
        $p_temp['area_city'] = S('firtp_area_city');
        $p_temp['provid'] = explode("-", $p_temp['area_city']);
        $p_temp['datepicker'] = S('firtp_datepicker');
        
       /* echo $p_temp['startDate'];
       echo "<br>";
       echo $p_temp['endDate'];
       echo "<br>";
       echo $p_temp['device'];
       echo "<br>";
       echo $p_temp['area_city'];
       echo "<br>";*/
        //var_dump(S('startDate')[0]);
       /* $this->assign("area_city",$p_temp['area_city']);
        $this->assign("device_selected",$p_temp['device']);
        $this->assign("datepicked",$p_temp['datepicker']);*/
        /*echo $p_temp['area_city'];echo "<br>";
        echo $p_temp['device'];echo "<br>";
        echo $p_temp['datepicker'];echo "<br>";*/
        $param = array("startDate"=>$p_temp['startDate'],"endDate"=>$p_temp['endDate'],"platform"=>0,"device"=>$p_temp['device'],'unitOfTime'=>$p_temp['unitOfTime'],'provid'=>$p_temp['provid']);
        

        /*$paramr = $param;
        $paramr['device']='2';
            */      
        /*86400*/
        $param0 = $param;
        $n_t = strtotime($param['endDate'])-strtotime($param['startDate']);
        
        $param0['startDate']=date('Y-m-d',strtotime($param['startDate'])-$n_t-86400);
        $param0['endDate'] = date('Y-m-d',strtotime($param['startDate'])-86400);
        /*获取平均排名数据 时间段*/
        $rank_range_json = json_decode(getKeywordIdReport_realtime($param))->body->data;

        $rank_range_json0=json_decode(getKeywordIdReport_realtime($param0))->body->data;

        //var_dump($rank_range_json);
        $rank_length = 0;
        $rank_temp = 0;
        foreach ($rank_range_json as $key => $value) {
            if ((int)$value->kpis[6]==0) {
                continue;
            }else{
                $rank_temp += $value->kpis[6];
                $rank_length++;
            }
            
        }
        $avgrank1= round($rank_temp / $rank_length ,2);
        $rank_length = 0;
        $rank_temp = 0;
        foreach ($rank_range_json0 as $key => $value) {
            if ((int)$value->kpis[6]==0) {
                continue;
            }else{
                $rank_temp += $value->kpis[6];
                $rank_length++;
            }
            
        }
        $avgrank0 = round($rank_temp / $rank_length ,2);
        
        /* 获取关键指标数据
        **********************
        调用该函数获取请求数据，将json字符串赋值给json_string*/
        $json_string= getAccountReport_realtime($param);
        /*对变量$json_sting进行 JSON 编码 */
        //$rank_string = getHistoryRankReport_realtime($paramr);
        $json_string_0 = getAccountReport_realtime($param0);
        $k_temp = json_decode($json_string)->body->data;
       // $r_temp = json_decode($rank_string)->body->data;
        //var_dump($k_temp);
        $k_temp_0 =json_decode($json_string_0)->body->data;
        foreach ($k_temp_0 as $key => $value) {
            $key_data['0']['impression'] =$value->kpis[0];
            $key_data['0']['cost'] =$value->kpis[1];
            $key_data['0']['cpc'] =round($value->kpis[2],2);
            $key_data['0']['click'] =$value->kpis[3];
            $key_data['0']['ctr'] = round($value->kpis[4],2);
        }
        foreach ($k_temp as $key => $value) {
            $key_data['1']['impression'] =$value->kpis[0];
            $key_data['1']['cost'] =$value->kpis[1];
            $key_data['1']['cpc'] =round($value->kpis[2],2);
            $key_data['1']['click'] =$value->kpis[3];
            $key_data['1']['ctr'] = round($value->kpis[4],2);
        }
       // echo json_encode($k_temp);
        $key_data['0']['position'] = $avgrank0;
        $key_data['1']['position'] = $avgrank1;
        //var_dump($keyindex_data);
        $this->assign('keyindex_data',$key_data);
        $this->assign('keyindex_data_rate',json_encode($key_data));
       /*获取关键指标变化率mychart1 数据*/
       
       /*获取趋势图mychart2数据*/
       $params_trend = $param;
       $params_trend['unitOfTime']=5;
       $k_avgrank_trend = json_decode(getKeywordIdReport_realtime($params_trend))->body->data;
       //var_dump($k_avgrank_trend);
       $k_trend = json_decode(getAccountReport_realtime($params_trend))->body->data;
       foreach ($k_trend as $key => $value) {
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
           foreach ($k_avgrank_trend as $k1 => $v1) {
                    if (strcmp($v1->date, $value->date)) {
                        $trend_data['position'][] = round($v1->kpis[6]);
                    }
                }     
       }
       //var_dump($trend_data);
       $this->assign("trend_data",json_encode($trend_data));
       /*获取地域(二级地域)分布图mychart3数据*/
       $param_region=$param;
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
       $this->assign("region_data",json_encode($region_data));
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
       $this->assign("region_data2",json_encode($region_data2));


       //var_dump($region_data);
       /*获取分时数据mychart4 数据*/
       $param_fenshi = $param;
       $param_fenshi['unitOfTime']=7;

       $fenshi_string = getAccountReport_realtime($param_fenshi);
       $fenshi_t = json_decode($fenshi_string)->body->data;
       //var_dump($fenshi_t);
      
       $fenshi_temp['date'] = array('00','01','02','03','04','05','06','07','08','09','10','11','12','13','14','15','16','17','18','19','20','21','22','23');
       $fenshi_temp['name']=$fenshi_t[0]->name;
        foreach ($fenshi_temp['date'] as $key => $value) {
            $fenshi_temp['kpis']['impression'][(int)$value] = isset($fenshi_temp['kpis']['impression'][$value])?$fenshi_temp['kpis']['impression'][$value]:0;
            $fenshi_temp['kpis']['cost'][(int)$value] = isset($fenshi_temp['kpis']['cost'][$value])?$fenshi_temp['kpis']['cost'][$value]:0;
            $fenshi_temp['kpis']['click'][(int)$value] = isset($fenshi_temp['kpis']['click'][$value])?$fenshi_temp['kpis']['click'][$value]:0;
       }

       foreach ($fenshi_t as $key => $value) {
            $n = explode(' ', $value->date);
            $nn = explode(':',$n[1]);
           $fenshi_temp['kpis']['impression'][(int)$nn[0]] += $value->kpis[0];
           $fenshi_temp['kpis']['cost'][(int)$nn[0]] += $value->kpis[1];
           $fenshi_temp['kpis']['click'][(int)$nn[0]] += $value->kpis[3];
       }
       foreach ($fenshi_temp['date'] as $key => $value) {
           $fenshi_temp['kpis']['cpc'][(int)$value] = $fenshi_temp['kpis']['click'][(int)$value]==0?0:round($fenshi_temp['kpis']['cost'][(int)$value]/$fenshi_temp['kpis']['click'][(int)$value],2);
           $fenshi_temp['kpis']['ctr'][(int)$value] = $fenshi_temp['kpis']['impression'][(int)$value]==0?0:round($fenshi_temp['kpis']['click'][(int)$value]/$fenshi_temp['kpis']['impression'][(int)$value],2);
       }
       $this->assign("fenshi_data",json_encode($fenshi_temp));
       //var_dump($fenshi_temp);
       


        if (IS_AJAX) {
            /*$this->success('123','',true);*/
           /* $this->ajaxReturn(true);*/
           $ajax_result = array( );
           $ajax_result['data']['key_data']=$key_data;
           $ajax_result['data']['trend_data']=$trend_data;
           $ajax_result['data']['region_data']=$region_data;
           $ajax_result['data']['region_data2']=$region_data2;
           $ajax_result['data']['fenshi_data']=$fenshi_temp;
           $this->ajaxReturn($ajax_result);
        }
        //var_dump($fenshi_temp);
    	
    	$this->display();
    }
}