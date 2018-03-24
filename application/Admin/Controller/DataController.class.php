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
     * 数据概况
     */

    public function data_check(){

        $this->display();
    }


    /**
     * 数据洞察-->关键词分析
     */

    public function keywords_check(){

        $this->display();
    }


    /**
     * 数据洞察-->TOP排行
     */

    public function top_rank(){

        $this->display();
    }


    /**
     * 数据洞察-->增降分析
     */

    public function changes(){

        $this->display();
    }

    /**
     * 数据洞察-->创意配图投放分析
     */

    public function creative(){

        $this->display();
    }


    /**
     * 数据洞察-->左上方排名分析
     */

    public function left_rank(){

        $this->display();
    }

}

