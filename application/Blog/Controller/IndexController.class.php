<?php
namespace Blog \ Controller;
use Common \ Controller \ HomebaseController;
class IndexController extends HomebaseController
{

    /* firstpage*/
    public function index()
    {
        $target = $_GET['target'] ? $_GET['target'] : 0;
        /* echo "this is blog index !"; */
        /* 	1、获取url
            2、获取请求数据
            3、发送请求 */
        /* Account, Campaign , Adgroup, Keyword, Creative,NewCreative,Toolkit , DynamicCreative,DynCreativeExclusion,RealTimeData,RealTimeQueryData,RealTimePairData,ProfessionalReportId, ReportState,ReportFileUrl,BulkJob,AllChangedObjects,FileStatus,FilePath,cancelDownload,ChangedId,ChangedItemId,ChangedScale*/
        $param_auto = auto_completeParam('RealTimeData');
        $post_data = get_postdata($param_auto['info'], $target);
        $url = get_requestURL($param_auto['postfix']);
        $output = send_post($url, $post_data);

        echo $output;
    }


    function jquery()
    {
        $this->display();
    }

    function canvas()
    {
        $this->display();
    }

    function test()
    {
        $this->display();
    }

    function chart()
    {
        $this->display();
    }

    function test1()
    {
        $t1 = file_get_contents("public/testdata/accountReport_byday.json");
        $this->assign("test", $t1);
        $vv = json_decode($t1, true);
        $v1 = array();
        foreach ($vv as $k => $v) {
            #code...
            foreach ($v['KPIs'] as $key => $value) {
                $v1[$key][] = $value;
            }
        }
        var_dump($v1);
        $this->display();
    }

}
