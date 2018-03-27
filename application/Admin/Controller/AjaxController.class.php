<?php
namespace Admin \ Controller;
use Common \ Controller \ HomebaseController;
class AjaxController extends HomebaseController {

	/* firstpage*/
	public function index() {
	}
	function handleajax(){
		 $dd = array('s' => "ddtt", );
		$this->ajaxReturn($dd);
	}
	public function handle_data_check(){
        $param_data  = dispatch_myjob($_POST['pager_select']);
        //var_dump($param_data);

       $result = array('data' =>$param_data);
        $this->ajaxReturn($result);
}

}
