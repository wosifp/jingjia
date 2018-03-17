<?php
namespace Blog \ Controller;
use Common \ Controller \ HomebaseController;

class IndexController extends HomebaseController {

	/* firstpage*/
	public function index() {

		/* echo "this is blog index !"; */
		/* 	1、获取url
			2、获取请求数据
			3、发送请求 */
		/* Account, Campaign , Adgroup, Keyword, Creative,NewCreative,Toolkit , DynamicCreative,DynCreativeExclusion,RealTimeData,RealTimeQueryData,RealTimePairData,ProfessionalReportId, ReportState,ReportFileUrl,BulkJob,AllChangedObjects,FileStatus,FilePath,cancelDownload,ChangedId,ChangedItemId,ChangedScale*/
		$param_auto=auto_completeParam('Campaign');
		$post_data = get_postdata($param_auto['info']);
		$url = get_requestURL($param_auto['postfix']);
		/* echo '{"url":"';
		echo $url;
		echo '","post_data":';
		echo $post_data;
		echo ',"return":'; */
		$output = send_post($url,$post_data);
		
		echo $output;
		/* echo '}'; */
		/* $this->display(); */
		/* return $output; */
	}
	function jquery(){
		$this->display();
	}
	function canvas(){
		$this->display();
	}
	function test(){
		$this->display();
	}
	function chart(){
		$this->display();
	}
}
