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
		$param_auto=auto_completeParam('RealTimeData');
		$post_data = get_postdata($param_auto['info'],$target);
		$url = get_requestURL($param_auto['postfix']);
		$output = send_post($url,$post_data);
		
		echo $output;
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

}
