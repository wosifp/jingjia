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
        
       static $p_temp = array();
        /*设置默认值*/
        $datepick = explode(" ",  $_REQUEST['datepicker']);
        $p_temp['startDate']=$datepick[0]?$datepick[0]:$p_temp['startDate'];
        $p_temp['endDate']=$datepick[2]?$datepick[2]:$p_temp['endDate'];
        $p_temp['startDate']=$p_temp['startDate']?$p_temp['startDate']:date('Y-m-d',strtotime("-2 day"));
        $p_temp['endDate']=$p_temp['endDate']?$p_temp['endDate']:date('Y-m-d',strtotime("-1 day"));
        $p_temp['device']=$_REQUEST['device']?$_REQUEST['device']:0;
        $p_temp['unitOfTime']=isset($p_temp['unitOfTime'])?$p_temp['unitOfTime']:5;

       
        $param = array("startDate"=>$p_temp['startDate'],"endDate"=>$p_temp['endDate'],"platform"=>0,"device"=>$p_temp['device'],'unitOfTime'=>$p_temp['unitOfTime']);
        

        /*$paramr = $param;
        $paramr['device']='2';
*/      
        /*86400*/
        $param0 = $param;
        $n_t = strtotime($param['endDate'])-strtotime($param['startDate']);
        
        $param0['startDate']=date('Y-m-d',strtotime($param['startDate'])-$n_t-86400);
        $param0['endDate'] = date('Y-m-d',strtotime($param['startDate'])-86400);
        
        
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
            $key_data['0']['impression'] +=$value->kpis[0];
            $key_data['0']['cost'] +=$value->kpis[1];
            $key_data['0']['click'] +=$value->kpis[3];
        }
        $key_data['0']['cpc'] =$key_data['0']['click']==0?0: round($key_data['0']['cost']/$key_data['0']['click'],2);
        $key_data['0']['ctr'] = $key_data['0']['click']==0?0:round($key_data['0']['impression']/$key_data['0']['click'],2);

        foreach ($k_temp as $key => $value) {
            $key_data['1']['impression'] +=$value->kpis[0];
            $key_data['1']['cost'] +=$value->kpis[1];
            $key_data['1']['click'] +=$value->kpis[3];
        }
        $key_data['1']['cpc'] =$key_data['1']['click']==0?0: round($key_data['1']['cost']/$key_data['1']['click'],2);
        $key_data['1']['ctr'] = $key_data['1']['click']==0?0:round($key_data['1']['impression']/$key_data['1']['click'],2);
       // echo json_encode($k_temp);
        
        //var_dump($keyindex_data);
        $this->assign('keyindex_data',$key_data);
       /*获取关键指标变化率mychart1 数据*/
       
       /*获取趋势图mychart2数据*/
       foreach ($k_temp as $key => $value) {
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
       }
       //var_dump($trend_data);
       $this->assign("trend_data",json_encode($trend_data));
       /*获取地域分布图mychart3数据*/
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

           $fenshi_temp['kpis']['impression'][(int)explode(':',explode(' ', $value->date)[1])[0]] += $value->kpis[0];
           $fenshi_temp['kpis']['cost'][(int)explode(':',explode(' ', $value->date)[1])[0]] += $value->kpis[1];
           $fenshi_temp['kpis']['click'][(int)explode(':',explode(' ', $value->date)[1])[0]] += $value->kpis[3];
       }
       foreach ($fenshi_temp['date'] as $key => $value) {
           $fenshi_temp['kpis']['cpc'][(int)$value] = $fenshi_temp['kpis']['click'][(int)$value]==0?0:round($fenshi_temp['kpis']['cost'][(int)$value]/$fenshi_temp['kpis']['click'][(int)$value],2);
           $fenshi_temp['kpis']['ctr'][(int)$value] = $fenshi_temp['kpis']['impression'][(int)$value]==0?0:round($fenshi_temp['kpis']['click'][(int)$value]/$fenshi_temp['kpis']['impression'][(int)$value],2);
       }
       $this->assign("fenshi_data",json_encode($fenshi_temp));
       //var_dump($fenshi_temp);



        

    	
    	$this->display();
    }
}