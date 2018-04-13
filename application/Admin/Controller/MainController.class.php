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

    	// 获取数据概况
        $this->assign('trade',getAccountList());

        // 获取数据概况结束
        $target = $_POST['target'] ?$_POST['target']:0;
        if($target){
            session('username_normal',$target);
        }
        
       static $p_temp = array();
        /*设置默认值*/
        $p_temp['startDate']=isset($p_temp['startDate'])?$p_temp['startDate']:date('Y-m-d',strtotime("-2 day"));
        $p_temp['endDate']=isset($p_temp['endDate'])?$p_temp['endDate']:date('Y-m-d',strtotime("-1 day"));
        $p_temp['device']=isset($p_temp['device'])?$p_temp['device']:0;
        $p_temp['unitTime']=isset($p_temp['unitTime'])?$p_temp['unitTime']:5;


        $param = array("startDate"=>$p_temp['startDate'],"endDate"=>$p_temp['endDate'],"platform"=>0,"device"=>$p_temp['device'],'unitTime'=>$p_temp['unitTime']);

        $paramr = $param;
        $paramr['device']='2';

        /* 获取关键指标数据
        **********************
        调用该函数获取请求数据，将json字符串赋值给json_string*/
        $json_string= getAccountReport_realtime($param);
        /*对变量$json_sting进行 JSON 编码 */
        //$rank_string = getHistoryRankReport_realtime($paramr);
        
        $k_temp = json_decode($json_string)->body->data;
       // $r_temp = json_decode($rank_string)->body->data;
       // var_dump($r_temp);
       // echo json_encode($k_temp);
        $keyindex_data['0']['id']=$k_temp[0]->id;
        $keyindex_data['0']['impression']=$k_temp[0]->kpis[0];
        $keyindex_data['0']['cost']=$k_temp[0]->kpis[1];
        $keyindex_data['0']['cpc']=round($k_temp[0]->kpis[2],2);
        $keyindex_data['0']['click']=$k_temp[0]->kpis[3];
        $keyindex_data['0']['ctr']=round($k_temp[0]->kpis[4],2);
        $keyindex_data['0']['cpm']=$k_temp[0]->kpis[5];
        $keyindex_data['0']['name']=$k_temp[0]->name;

        $keyindex_data['1']['id']=$k_temp[1]->id;
        $keyindex_data['1']['impression']=$k_temp[1]->kpis[0];
        $keyindex_data['1']['cost']=$k_temp[1]->kpis[1];
        $keyindex_data['1']['cpc']=round($k_temp[1]->kpis[2],2);
        $keyindex_data['1']['click']=$k_temp[1]->kpis[3];
        $keyindex_data['1']['ctr']=round($k_temp[1]->kpis[4],2);
        $keyindex_data['1']['cpm']=$k_temp[1]->kpis[5];
        $keyindex_data['1']['name']=$k_temp[1]->name;
        //var_dump($keyindex_data);
        $this->assign('keyindex_data',$keyindex_data);
       /*获取关键指标变化率mychart1 数据*/

       /*获取趋势图mychart2数据*/
       /*获取地域分布图mychart3数据*/
       /*获取分时数据mychart4 数据*/

        

    	
    	$this->display();
    }
}