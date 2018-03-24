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
        $p_id = session('p_id');
        
        if($p_id ==1  ){ // 说明是mcc账户
            // 查出该mcc账户下的所有子账户数据
            $data = M("users")->where('p_id = '.session('ADMIN_ID'))->select();
            $result=array();
            foreach ($data as $key => $value) {
                $result[$value['trade']][$value['companyname']][]=$value;
            }
            $this->assign('trade',$result);
        }else{
            // 子账户
            $result = M("users")->where(array('id'=>session(ADMIN_ID)))->select();
            $this->assign('user',$result);
        }
        // 获取数据概况结束
        $filename  = "./public/testdata/accountReport_byday.json";
        
        $json_string = file_get_contents($filename);
        $json_data = json_decode($json_string,true);
        $lebal = array();
        $result = array();
        foreach ($json_data as $key1 => $value1) {
            # code...
            $lebal[] = $value1['Date'];
            foreach ($value1['KPIs'] as $key => $value) {
                # code...
                $result[$key][] = $value;
            }

        }
        $params = array('startDate' =>'2018-02-03' ,'endDate'=>'2018-03-03' );
        echo getAccountReport_realtime($params);
        $this->assign('lebal',json_encode($lebal));
        $this->assign('result',json_encode($result));
        $this->assign('json_data',$json_string);

    	$this->assign('server_info', $info);
    	$this->display();
    }
}