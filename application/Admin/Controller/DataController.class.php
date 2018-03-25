<?php
/**
 * 后台查看数据API
 */
namespace Admin\Controller;

use Common\Controller\AdminbaseController;

class DataController extends AdminbaseController {
	
	public function _initialize() {
	    empty($_GET['upw'])?"":session("__SP_UPW__",$_GET['upw']);//设置后台登录加密码
		parent::_initialize();
		$this->initMenu();
	}
	
    /**
     * 后台框架首页
     */
//    public function index() {
//        $this->load_menu_lang();
//
//        $this->assign("menus", D("Common/Menu")->menu_json());
//       	$this->display();
//    }
//
//    private function load_menu_lang(){
//        $default_lang=C('DEFAULT_LANG');
//
//        $langSet=C('ADMIN_LANG_SWITCH_ON',null,false)?LANG_SET:$default_lang;
//
//	    $apps=sp_scan_dir(SPAPP."*",GLOB_ONLYDIR);
//	    $error_menus=array();
//	    foreach ($apps as $app){
//	        if(is_dir(SPAPP.$app)){
//	            if($default_lang!=$langSet){
//	                $admin_menu_lang_file=SPAPP.$app."/Lang/".$langSet."/admin_menu.php";
//	            }else{
//	                $admin_menu_lang_file=SITE_PATH."data/lang/$app/Lang/".$langSet."/admin_menu.php";
//	                if(!file_exists_case($admin_menu_lang_file)){
//	                    $admin_menu_lang_file=SPAPP.$app."/Lang/".$langSet."/admin_menu.php";
//	                }
//	            }
//
//	            if(is_file($admin_menu_lang_file)){
//	                $lang=include $admin_menu_lang_file;
//	                L($lang);
//	            }
//	        }
//	    }
//    }



    /**
     * 数据概况--暂时不用
     */

    public function data_summarize(){
        $user_id = session(ADMIN_ID);
        if($user_id ==1 ){ // 说明是mcc账户
            // 查出该mcc账户下的所有子账户数据
            $data = M("users")->where('id != 1')->select();
            $result=array();
            foreach ($data as $key => $value) {
                $result[$value['trade']][$value['companyname']][]=$value;
            }
//            print_r($result);die;
           $this->assign('trade',$result);
        }else{
            // 子账户
            $result = M("users")->where(array('id'=>session(ADMIN_ID)))->select();
            $this->assign('user',$result);
        }
        $this->display();
    }



    /**
     * 数据查看
     */

    public function data_check(){
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

        //分时段 默认为5,分日
        $unitOfTime = $_POST['unit_data'] ? $_POST['unit_data'] : 5;
        if($unitOfTime){
            session("unit_data",$unitOfTime);
            $unitOfTime=session("unit_data");
        }
        //echo $unitOfTime;
        $param = array("startDate"=>$startDate,"endDate"=>$endDate,"platform"=>0,"Device"=>$device);


        $this->display();
    }


    /**
     * 数据洞察-->关键词分析
     */

    public function keywords_check(){
        $this->assign('trade',getAccountList());
        $this->display();
    }


    /**
     * 数据洞察-->TOP排行
     */

    public function top_rank(){
        $this->assign('trade',getAccountList());
        $this->display();
    }


    /**
     * 数据洞察-->增降分析
     */

     function changes(){
        $this->assign('trade',getAccountList());
        $this->display();
    }

    /**
     * 数据洞察-->创意配图投放分析
     */

    public function creative(){
         $this->assign('trade',getAccountList());
        $this->display();
    }


    /**
     * 数据洞察-->左上方排名分析
     */

    public function left_rank(){
         $this->assign('trade',getAccountList());
        $this->display();
    }

}

