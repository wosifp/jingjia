<?php
namespace Blog \ Controller;
use Common \ Controller \ HomebaseController;
class IndexController extends HomebaseController {

	/* firstpage*/
	public function index() {
	   $target =  $_GET['target'] ? $_GET['target'] : 0;
		/* echo "this is blog index !"; */
		/* 	1、获取url
			2、获取请求数据
			3、发送请求 */
		/* Account, Campaign , Adgroup, Keyword, Creative,NewCreative,Toolkit , DynamicCreative,DynCreativeExclusion,RealTimeData,RealTimeQueryData,RealTimePairData,ProfessionalReportId, ReportState,ReportFileUrl,BulkJob,AllChangedObjects,FileStatus,FilePath,cancelDownload,ChangedId,ChangedItemId,ChangedScale*/

		echo getReport("Account");
	}



	function testajax(){
		$this->display();
	}
	function handleajax(){
		 $dd = array('s' => "ddtt", );
		$this->ajaxReturn($dd);
	}
	function jquery(){
		$this->display();
	}
	function canvas(){

		$this->display();
	}
	function test1(){
	    $date_param= array("userId=");
        $result = getReport($date_param);
        echo $result;
		$this->display();
	}
	function chart(){
		$this->display();
	}
	function chart2()
	{
		# code...
		$this->display();
	}
	function datepick(){
		$this->display();
	}
	function chinamap(){
		$this->display();
	}

}
