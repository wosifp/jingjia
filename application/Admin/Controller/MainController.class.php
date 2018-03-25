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
        
        $t = time();
        $start = mktime(0,0,0,date("m",$t),date("d",$t),date("Y",$t)); // 当天0时0分
        $end = mktime(23,59,59,date("m",$t),date("d",$t),date("Y",$t)); // 当天23时59分
// 设备默认为0，全部
         $device = $_POST['device'] ? $_POST['device'] : 0;
        if($device){
            session("device",$device);
            $device = session("device");
        }
        
        $datepick = explode(" ",  $_POST['datepicker']);
        
        $startDate=$datepick[0];
        
        
        $endDate =$datepick[2];
        $startDate = $startDate ? $startDate : date('Y-m-d',$start);
        $endDate = $endDate ? $endDate : date('Y-m-d',$end);
        
        $param = array("startDate"=>$startDate,"endDate"=>$endDate,"platform"=>0,"Device"=>$device);

        /* 调用该函数获取请求数据，将json字符串赋值给json_string*/
        $json_string= getAccountReport_realtime($param);
        /*对变量$json_sting进行 JSON 编码 */
        $json_data = json_decode($json_string,true);
        /*lebal 数组存放的是日期数据*/
        $lebal = array();
        /*result数组存放是文档中kpis【cots,】等*/
        $result = array();
        foreach ($json_data as $key1 => $value1) {
            # code...
            $lebal[] = $value1['Date'];
            foreach ($value1['KPIs'] as $key => $value) {
                # code...
                /*以数组中的索引为key,保存对应的数值*/
                $result[$key][] = $value;
            }

        }
       // $params = array('startDate' =>'2018-02-03' ,'endDate'=>'2018-03-03' );
        //echo getAccountReport_realtime($param);
        /*assign()将源对象json_encode()的引用给目标对象''*/
        $this->assign('lebal',json_encode($lebal));
        $this->assign('result',json_encode($result));
        $this->assign('json_data',$json_string);

    	$this->assign('server_info', $info);
    	$this->display();
    }
}