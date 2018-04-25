<?php
/**
 * 后台查看数据API
 */
namespace Admin\Controller;

use Common\Controller\AdminbaseController;
// 指定允许其他域名访问  
header('Access-Control-Allow-Origin:*');  
// 响应类型  
header('Access-Control-Allow-Methods:*');  
// 响应头设置  
header('Access-Control-Allow-Headers:Origin, X-Requested-With, Content-Type, Accept,x-requested-with,content-type');   
class DataController extends AdminbaseController {
	
	public function _initialize() {
	    empty($_GET['upw'])?"":session("__SP_UPW__",$_GET['upw']);//设置后台登录加密码
		parent::_initialize();
		$this->initMenu();
	}
	
    /**
     * 后台框架首页
     */
   public function index(){

    }

/*数据查看-->账户查看account_check() */
    public function account_check(){
        $this->assign('trade',getAccountList());

    /*参数处理*/
    /*参数处理end*/
        static $p_temp = array();
        /*设置默认值*/

        $p_temp['startDate']=isset($p_temp['startDate'])?$p_temp['startDate']:date('Y-m-d',strtotime('-1 day'));
        $p_temp['endDate']=isset($p_temp['endDate'])?$p_temp['endDate']:date('Y-m-d');
        $p_temp['device']=isset($p_temp['device'])?$p_temp['device']:0;
        $p_temp['unitOfTime']=isset($p_temp['unitOfTime'])?$p_temp['unitOfTime']:5;

        if (IS_AJAX) {
            # code...
            var_dump($_POST);
            $statIds_temp = $_POST['statIds'];
            //var_dump($_POST['statIds']);
            foreach ($statIds_temp as $key => $value) {
                $p_temp['statIds'][] =$value['value'];
            }
            //var_dump($p_temp);
            $p_temp['unitOfTime'] = $_POST['unitOfTime']?$_POST['unitOfTime']:$p_temp['unitOfTime'];
            $p_temp['order']=$_POST['order']?$_POST['order']:$p_temp['order'];
            $datepick = explode(" ",  $_POST['datepicker']);
            $p_temp['startDate']=$datepick[0]?$datepick[0]:$p_temp['startDate'];
            $p_temp['endDate']=$datepick[2]?$datepick[2]:$p_temp['endDate'];
            $p_temp['device']=$_POST['device']?$_POST['device']:$p_temp['device'];
            
            $param_data  = dispatch_kpijob($_POST['pager_select'],$p_temp);
            $param_r=json_decode($param_data)->body->data;
            //var_dump($param_r);
            $param_return = array( );
            foreach ($param_r as $key => $value) {
                # code...
                $param_return['data']['id'][]=$value->id;
                $param_return['data']['impression'][]=$value->kpis[0];
                $param_return['data']['cost'][]=$value->kpis[1];
                $param_return['data']['cpc'][]=round($value->kpis[2]);
                $param_return['data']['click'][]=$value->kpis[3];
                $param_return['data']['ctr'][]=round($value->kpis[4]);
                $param_return['data']['cpm'][]=$value->kpis[5];
                $param_return['data']['name'][]=$value->name;
                $param_return['data']['date'][]=$value->date;
                
            }
            //var_dump(json_decode($param));
            //var_dump($param_data);
            //var_dump(gettype($param));
           $this->ajaxReturn($param_return);
        }
        
        $this->display();
    }

/*数据洞察-->关键词分析keyword_check()*/
    public function keywords_check(){      


        $p_temp = array( );
        /*获取用户列表并压到模板*/
        $this->assign('trade',getAccountList());
        /*获取要被查看的账户名字*/
        $statIds_temp = $_REQUEST["statIds"];
        S('keywords_check_pager_select',$_REQUEST['pager_select']?$_REQUEST['pager_select']:S('keywords_check_pager_select'));
        if ($_REQUEST['pager_select']=='账户') {
            $targets = array();
        foreach ($statIds_temp as $key => $value) {
            $targets[] = $value['text'];
        }
        
        $target = $targets[0] ?$targets[0]:0;
        if($target){
            session('username_normal',$target);
            }
        }else{
            foreach ($statIds_temp as $key => $value) {
                $p_temp['statIds'][] =$value['value'];
            }
        }
    /*参数处理*/

        $datepicker = explode(" ",  $_REQUEST['datepicker']);

       S('keywords_check_statIds',$p_temp['statIds']?$p_temp['statIds']:S('keywords_check_statIds'),300);
        S('keywords_check_startDate',$datepicker[0]?$datepicker[0]:S('keywords_check_startDate'),300);
        S('keywords_check_endDate',$datepicker[2]?$datepicker[2]:S('keywords_check_endDate'),300);
       
        
        S('keywords_check_device',$_REQUEST['device']?$_REQUEST['device']:S('keywords_check_device'),300);
        S('keywords_check_device',S('keywords_check_device')=='3'?0:S('keywords_check_device'));
       
        $area_str = $_REQUEST['area_value'];
        /*S('provid',$_REQUEST)*/
        S('keywords_check_area_city',$_REQUEST['area_city']?$_REQUEST['area_city']:S('keywords_check_area_city'),300);
        
        $p_temp['startDate']=S('keywords_check_startDate')?S('keywords_check_startDate'):date('Y-m-d',strtotime("-2 day"));
        $p_temp['endDate']=S('keywords_check_endDate')?S('keywords_check_endDate'):date('Y-m-d',strtotime("-1 day"));
        $p_temp['device']=S('keywords_check_device')?S('keywords_check_device'):0;
       $p_temp['statIds']=S('keywords_check_statIds')?S('keywords_check_statIds'):array();
        /*var_dump(S('keywords_check_device')) ;
        var_dump($p_temp['device']) ;*/
        $p_temp['area_city'] = S('keywords_check_area_city');
        $p_temp['provid'] = explode("-", $p_temp['area_city']);
        /*$p_temp['datepicker'] = S('keywords_check_datepicker');*/
        /*unitOfTime 次函数中暂时不用缓存*/
         $p_temp['unitOfTime']=$_REQUEST['unitOfTime']?$_REQUEST['unitOfTime']: 8;
        //var_dump($p_temp);
    /*参数处理end*/
        /*get keywords_realtimedata*/
        switch (S('keywords_check_pager_select')) {
            case '账户':
                $p_temp['statRange']=2;
                $keywords_data_json=json_decode(getKeywordIdReport_realtime($p_temp))->body->data;
                $p_temp['unitOfTime']=5;
                $keywords_data_json_byday=json_decode(getKeywordIdReport_realtime($p_temp))->body->data;
                break;
            case '计划':
                $p_temp['statRange']=3;
                $keywords_data_json=json_decode(getKeywordIdReport_realtime($p_temp))->body->data;
                $p_temp['unitOfTime']=5;
                $keywords_data_json_byday=json_decode(getKeywordIdReport_realtime($p_temp))->body->data;
                break;
            case '单元':
                $p_temp['statRange']=5;
                $keywords_data_json=json_decode(getKeywordIdReport_realtime($p_temp))->body->data;
                $p_temp['unitOfTime']=5;
                $keywords_data_json_byday=json_decode(getKeywordIdReport_realtime($p_temp))->body->data;
                break;
            case '关键词':
                $p_temp['statRange']=11;
                $keywords_data_json=json_decode(getKeywordIdReport_realtime($p_temp))->body->data;
                $p_temp['unitOfTime']=5;
                $keywords_data_json_byday=json_decode(getKeywordIdReport_realtime($p_temp))->body->data;
                break;
            default:
                break;
        }
        //var_dump($keywords_data_json);
        $keywords_ids = array( );
        foreach ($keywords_data_json as $key => $value) {
            $keywords_ids['ids'][] =$value->id;
        }
        $keywords_ids['idType']=11;
        //var_dump($keywords_ids);
        $keywords_info_data = json_decode(getReport('Keyword',$keywords_ids))->body->data;
        //var_dump($keywords_info_data);
        /*消费分析*/
        $cost_ratio = array( );
        $cost_count=0;
        $impression_count=0;
        $all_count = 0;
        foreach ($keywords_data_json as $key => $value) {
            $cost_count += $value->kpis[1]>0?1:0;
            $impression_count += $value->kpis[0]>0?1:0;
            $all_count++;
        }
        $cost_analysis['had_cost']=$cost_count;
        $cost_analysis['had_impression']=$impression_count;
        $cost_analysis['all_count']=$all_count;
        $this->assign('cost_analysis',json_encode($cost_analysis));
        /*匹配模式占比*/
        $match_ratio_data = array();
        foreach ($keywords_info_data as $key => $value) {
            foreach ($keywords_data_json as $k => $v) {
                 if ($value->keywordId==$v->id) {
                     $match_ratio_data[$value->matchType==2?$value->phraseType+10:$value->matchType]['id'][]=$v->id;
                     $match_ratio_data[$value->matchType==2?$value->phraseType+10:$value->matchType]['impression'] += (int)$v->kpis[0];
                     $match_ratio_data[$value->matchType==2?$value->phraseType+10:$value->matchType]['cost'] += (double)$v->kpis[1];
                     $match_ratio_data[$value->matchType==2?$value->phraseType+10:$value->matchType]['click'] +=(int)$v->kpis[3];
                 }
            }
        }
        //var_dump($match_ratio_data);
        $matchType_name = array(1=>'精确匹配' ,2=>'高级短语匹配',3=>'广泛匹配',11=>'同义包含',12=>'精确包含',13=>'核心包含' );
        $match_index = array('id','impression','cost','click' );
        foreach ($match_ratio_data as $key => $value) {
            foreach ($match_index as $k => $v) {
                if ($v =='id') {
                    $match_return['words_count']['name'][]=$matchType_name[$key];
                $match_return['words_count']['data'][]=json_decode(json_encode(array('name'=>$matchType_name[$key],'value'=>count($value['id']))));
                $match_return['words_count']['bar'][]=count($value['id']);
                }else{
                    $match_return[$v]['name'][]=$matchType_name[$key];
                $match_return[$v]['data'][]=json_decode(json_encode(array('name'=>$matchType_name[$key],'value'=>$value[$v])));
                $match_return['words_count']['bar'][]=$value[$v];
                }   
            }      
        }
        //var_dump($match_return);
        $this->assign('matchType_data',json_encode($match_return));
        /*详细表格数据*/
        $grid_data = array( );

        foreach ($keywords_data_json_byday as $key => $value) {
            $grid_data[$key]['日期']=$value->date;
            $grid_data[$key]['账户']=$value->name[0];
            $grid_data[$key]['计划']=$value->name[1];
            $grid_data[$key]['单元']=$value->name[2];
            $grid_data[$key]['关键词']=$value->name[3];
            $grid_data[$key]['关键词id']=$value->id;
            $grid_data[$key]['展现']=$value->kpis[0];
            $grid_data[$key]['消费']=$value->kpis[1];
            $grid_data[$key]['点击']=$value->kpis[3];
            $grid_data[$key]['cpc']=round($value->kpis[2],2);
            $grid_data[$key]['ctr']=round($value->kpis[4],2);
            $grid_data[$key]['平均排名']=round($value->kpis[6],2);
            foreach ($keywords_info_data as $k => $v) {
                if ($v->keywordId ==$value->id) {
                    $grid_data[$key]['pc质量度']=$v->pcQuality;
                    $grid_data[$key]['mobile质量度']=$v->mobileQuality;
                    $grid_data[$key]['匹配模式']=$v->matchType==2?$v->phraseType+10:$v->matchType;
                }
            }
        }
        $this->assign('grid_data',json_encode($grid_data));
        /*$keywords_data_json = json_decode(getKeywordIdReport_realtime($param))->body->data;*/
        //var_dump($keywords_data_json);
        if (IS_AJAX) {
            $ajax_data = array( );
            $ajax_data['data']['grid_data']=$grid_data;
            $ajax_data['data']['matchType_data']=$match_return;
            $ajax_data['data']['cost_analysis']=$cost_analysis;
            $this->ajaxReturn($ajax_data);
        }
        $this->display();
    }

/*数据洞察-->top排行 top_rank()*/

    public function top_rank(){
        $p_temp = array( );
        /*获取用户列表并压到模板*/
        $this->assign('trade',getAccountList());
        /*获取要被查看的账户名字*/
        $statIds_temp = $_REQUEST["statIds"];
        S('top_rank_pager_select',$_REQUEST['pager_select']?$_REQUEST['pager_select']:S('top_rank_pager_select'));
        if ($_REQUEST['pager_select']=='账户') {
            $targets = array();
            foreach ($statIds_temp as $key => $value) {
                $targets[] = $value['text'];
            }
        
            $target = $targets[0] ?$targets[0]:0;
            if($target){
                session('username_normal',$target);
            }
        }
        S('top_rank_level_to_choose',$_REQUEST['level_to_choose']?$_REQUEST['level_to_choose']:S('top_rank_level_to_choose'),300);
        S('top_rank_indexselection',$_REQUEST['indexselection']?$_REQUEST['indexselection']:S('top_rank_indexselection'),300);
        S('top_rank_top_choice',$_REQUEST['top_choice']?$_REQUEST['top_choice']:S('top_rank_top_choice'),300);
        /*参数处理*/

        $datepicker = explode(" ",  $_REQUEST['datepicker']);

       /*S('top_rank_statIds',$p_temp['statIds']?$p_temp['statIds']:S('top_rank_statIds'),300);*/
        S('top_rank_startDate',$datepicker[0]?$datepicker[0]:S('top_rank_startDate'),300);
        S('top_rank_endDate',$datepicker[2]?$datepicker[2]:S('top_rank_endDate'),300);
       
        
        S('top_rank_device',$_REQUEST['device']?$_REQUEST['device']:S('top_rank_device'),300);
        S('top_rank_device',S('top_rank_device')=='3'?0:S('top_rank_device'));
       
        $area_str = $_REQUEST['area_value'];
        /*S('provid',$_REQUEST)*/
        S('top_rank_area_city',$_REQUEST['area_city']?$_REQUEST['area_city']:S('top_rank_area_city'),300);
        
        $p_temp['startDate']=S('top_rank_startDate')?S('top_rank_startDate'):date('Y-m-d',strtotime("-2 day"));
        $p_temp['endDate']=S('top_rank_endDate')?S('top_rank_endDate'):date('Y-m-d',strtotime("-1 day"));
        $p_temp['device']=S('top_rank_device')?S('top_rank_device'):0;
       $p_temp['statIds']=S('top_rank_statIds')?S('top_rank_statIds'):array();
        /*var_dump(S('top_rank_device')) ;
        var_dump($p_temp['device']) ;*/
        $p_temp['area_city'] = S('top_rank_area_city');
        $p_temp['provid'] = explode("-", $p_temp['area_city']);
        /*$p_temp['datepicker'] = S('top_rank_datepicker');*/
        /*unitOfTime 次函数中暂时不用缓存*/
         $p_temp['unitOfTime']=$_REQUEST['unitOfTime']?$_REQUEST['unitOfTime']: 8;
        //var_dump($p_temp);
    /*参数处理end*/
       /* echo "+++++++++";
        echo S('top_rank_level_to_choose');
        echo "+++++++++++++++";*/
        //$temp = getCreativeIdList();
        $result_json = json_decode(dispatch_kpijob(S('top_rank_level_to_choose')?S('top_rank_level_to_choose'):'计划',$p_temp))->body->data;
        //var_dump($result_json);
        //指标选择indexselection
        $level_to_choose = S('top_rank_level_to_choose')?S('top_rank_level_to_choose'):"计划";
        $indexselection = S('top_rank_indexselection') ? S('top_rank_indexselection') : "消费";
        //TOP选择level_to_choose
        $top_choice = S('top_rank_top_choice') ? S('top_rank_top_choice') : 10;
        /*grid_data*/
        $grid_data = array( );
        foreach ($result_json as $key => $value) {
            $grid_data[$key]['日期']=$value->date;
            $grid_data[$key]['账户']=$value->name[0]?$value->name[0]:'-';
            switch ($level_to_choose) {
                case '计划':
                    $grid_data[$key]['计划']=$value->name[1]?$value->name[1]:'-';
                    break;
                case '单元':
                    $grid_data[$key]['计划']=$value->name[1]?$value->name[1]:'-';
                    $grid_data[$key]['单元']=$value->name[2]?$value->name[2]:'-';
                    break;
                case '关键词':
                    $grid_data[$key]['计划']=$value->name[1]?$value->name[1]:'-';
                    $grid_data[$key]['单元']=$value->name[2]?$value->name[2]:'-';
                    $grid_data[$key]['关键词']=$value->name[3]?$value->name[3]:'-';
                    break;
                case '创意':
                    $grid_data[$key]['计划']=$value->name[1]?$value->name[1]:'-';
                    $grid_data[$key]['单元']=$value->name[2]?$value->name[2]:'-';
                    $grid_data[$key]['创意']=$value->name[3]?$value->name[3]:'-';
                    $grid_data[$key]['描述1']=$value->name[4]?$value->name[4]:'-';
                    $grid_data[$key]['url']=$value->name[6]?$value->name[6]:'-';
                    $grid_data[$key]['描述2']=$value->name[5]?$value->name[5]:'-';
                    
                    break;
                default:
                    # code...
                    break;
            }
            $grid_data[$key]['展现']=$value->kpis[0];
            $grid_data[$key]['消费']=$value->kpis[1];
            $grid_data[$key]['点击']=$value->kpis[3];
            $grid_data[$key]['cpc']=round($value->kpis[2],2);
            $grid_data[$key]['ctr']=round($value->kpis[4],2);
            $grid_data[$key]['平均排名']=round($value->kpis[6],2);
            /*foreach ($keywords_info_data as $k => $v) {
                if ($v->keywordId ==$value->id) {
                    $grid_data[$key]['pc质量度']=$v->pcQuality;
                    $grid_data[$key]['mobile质量度']=$v->mobileQuality;
                    $grid_data[$key]['匹配模式']=$v->matchType==2?$v->phraseType+10:$v->matchType;
                }
            }*/
        }
        /*grid_fields*/
        foreach ($grid_data[0] as $key => $value) {
            switch ($key) {
                case '日期':
                case '账户':
                case '关键词':
                case '创意':
                
                    $grid_fields[]=array('name'=>$key,'type'=>'text','width'=>110);
                    break;
                case '描述1':
                case '描述2':
                case 'url':
                    $grid_fields[]=array('name'=>$key,'type'=>'textarea','width'=>190);
                    break;
                default:
                     $grid_fields[]=array('name'=>$key,'type'=>'text','width'=>50);
                    break;
            }
           
        }
        //var_dump($grid_fields);
        //var_dump($grid_data);
        $grid_return = array('data' =>$grid_data ,'fields'=>$grid_fields );
        $this->assign('grid_data',json_encode($grid_return));
        /*prepareRank*/
        foreach ($grid_data as $key => $value) {
            $prepareRank['cost']['name'][]=$value[$level_to_choose];
            $prepareRank['cost']['data'][]=$value['消费'];
            $prepareRank['impression']['name'][]=$value[$level_to_choose];
            $prepareRank['impression']['data'][]=$value['展现'];
            $prepareRank['click']['name'][]=$value[$level_to_choose];
            $prepareRank['click']['data'][]=$value['点击'];
            $prepareRank['ctr']['name'][]=$value[$level_to_choose];
            $prepareRank['ctr']['data'][]=$value['ctr'];
            $prepareRank['cpc']['name'][]=$value[$level_to_choose];
            $prepareRank['cpc']['data'][]=$value['cpc'];
            
        }
        /*按指标排序*/
        $afterRank = mysort($prepareRank,true);
        //var_dump($afterRank);
        $rank_return = array();
        switch ($indexselection) {
            case '消费':
                $rank_return['name'] = array_slice($afterRank['cost']['name'],0, $top_choice);
                $rank_return['data'] = array_slice($afterRank['cost']['data'], 0,$top_choice);
                break;
            case '展现':
                $rank_return['name'] = array_slice($afterRank['impression']['name'],0, $top_choice);
                $rank_return['data'] = array_slice($afterRank['impression']['data'], 0,$top_choice);
                break;
            case '点击':
                $rank_return['name'] = array_slice($afterRank['click']['name'],0, $top_choice);
                $rank_return['data'] = array_slice($afterRank['click']['data'], 0,$top_choice);
                break;
            case '点击率':
                $rank_return['name'] = array_slice($afterRank['ctr']['name'],0, $top_choice);
                $rank_return['data'] = array_slice($afterRank['ctr']['data'], 0,$top_choice);
                break;
            case '平均价格':
                $rank_return['name'] = array_slice($afterRank['cpc']['name'],0, $top_choice);
                $rank_return['data'] = array_slice($afterRank['cpc']['data'], 0,$top_choice);
                break;
            default:
                # code...
                break;
        }
        //var_dump($rank_return);
        $this->assign('top_rank_data',json_encode($rank_return));
        if (IS_AJAX) {
            $top_rank_ajax['data']['grid_data']=$grid_return;
            $top_rank_ajax['data']['top_rank_data']=$rank_return;
            $this->ajaxReturn($top_rank_ajax);
        }
        $this->display();
    }
/*数据洞察-->增降分析changes（）*/

    public function changes(){
        $p_temp = array( );
        /*获取用户列表并压到模板*/
        $this->assign('trade',getAccountList());
        /*获取要被查看的账户名字*/
        $statIds_temp = $_REQUEST["statIds"];
        S('changes_pager_select',$_REQUEST['pager_select']?$_REQUEST['pager_select']:S('changes_pager_select'));
        if ($_REQUEST['pager_select']=='账户') {
            $targets = array();
            foreach ($statIds_temp as $key => $value) {
                $targets[] = $value['text'];
            }
        
            $target = $targets[0] ?$targets[0]:0;
            if($target){
                session('username_normal',$target);
            }
        }
        S('changes_tire',$_REQUEST['tire']?$_REQUEST['tire']:S('changes_tire'),300);
        S('changes_filterparams',$_REQUEST['filterparams']?$_REQUEST['filterparams']:S('changes_filterparams'),300);
        S('changes_produce_flag',$_REQUEST['produce_flag']?$_REQUEST['produce_flag']:S('changes_produce_flag'),300);
        /*参数处理*/

        $datepicker = explode(" ",  $_REQUEST['datepicker']);

       /*S('changes_statIds',$p_temp['statIds']?$p_temp['statIds']:S('changes_statIds'),300);*/
        S('changes_startDate',$datepicker[0]?$datepicker[0]:S('changes_startDate'),300);
        S('changes_endDate',$datepicker[2]?$datepicker[2]:S('changes_endDate'),300);
       
        
        S('changes_device',$_REQUEST['device']?$_REQUEST['device']:S('changes_device'),300);
        S('changes_device',S('changes_device')=='3'?0:S('changes_device'));
       
        $area_str = $_REQUEST['area_value'];
        /*S('provid',$_REQUEST)*/
        S('changes_area_city',$_REQUEST['area_city']?$_REQUEST['area_city']:S('changes_area_city'),300);
        
        $p_temp['startDate']=S('changes_startDate')?S('changes_startDate'):date('Y-m-d',strtotime("-2 day"));
        $p_temp['endDate']=S('changes_endDate')?S('changes_endDate'):date('Y-m-d',strtotime("-1 day"));
        $p_temp['device']=S('changes_device')?S('changes_device'):0;
       $p_temp['statIds']=S('changes_statIds')?S('changes_statIds'):array();
        /*var_dump(S('changes_device')) ;
        var_dump($p_temp['device']) ;*/
        $p_temp['area_city'] = S('changes_area_city');
        $p_temp['provid'] = explode("-", $p_temp['area_city']);
        /*$p_temp['datepicker'] = S('changes_datepicker');*/
        /*unitOfTime 次函数中暂时不用缓存*/
         $p_temp['unitOfTime']=$_REQUEST['unitOfTime']?$_REQUEST['unitOfTime']: 8;
        //var_dump($p_temp);
         /*上期时间段起止时间*/
        $param0 = $p_temp;
        $n_t = strtotime($p_temp['endDate'])-strtotime($p_temp['startDate']);
        
        $param0['startDate']=date('Y-m-d',strtotime($p_temp['startDate'])-$n_t-86400);
        $param0['endDate'] = date('Y-m-d',strtotime($p_temp['startDate'])-86400);
    /*参数处理end*/
    //层级选择变量 
        $tire = S('changes_tire')?S('changes_tire'):"计划";
        /*本期数据*/
         $result_json = json_decode(dispatch_kpijob($tire,$p_temp))->body->data;

         /*上期数据*/
         $result_json_0 = json_decode(dispatch_kpijob($tire,$param0))->body->data;
        
        /*过滤条件变量filterparams*/
        $filterparams = S('changes_filterparams') ? S('changes_filterparams') : "消费额";
        //TOP选择数量
        switch ($tire) {
            case '计划':
                $remain_number =10;       
                break;
            case '单元':
                $remain_number =50;       
                break;
            case '关键词':
                $remain_number =100;       
                break;
            
            default:
                $remain_number=10;
                break;
        }
        /*grid_data*/
        $grid_data = array( );
        foreach ($result_json as $key => $value) {
            $grid_data[$key]['日期']=$value->date;
            $grid_data[$key]['id']=$value->id;
            $grid_data[$key]['账户']=$value->name[0]?$value->name[0]:'-';
            switch ($tire) {
                case '计划':
                    $grid_data[$key]['计划']=$value->name[1]?$value->name[1]:'-';
                    break;
                case '单元':
                    $grid_data[$key]['计划']=$value->name[1]?$value->name[1]:'-';
                    $grid_data[$key]['单元']=$value->name[2]?$value->name[2]:'-';
                    break;
                case '关键词':
                    $grid_data[$key]['计划']=$value->name[1]?$value->name[1]:'-';
                    $grid_data[$key]['单元']=$value->name[2]?$value->name[2]:'-';
                    $grid_data[$key]['关键词']=$value->name[3]?$value->name[3]:'-';
                    break;
                case '创意':
                    $grid_data[$key]['计划']=$value->name[1]?$value->name[1]:'-';
                    $grid_data[$key]['单元']=$value->name[2]?$value->name[2]:'-';
                    $grid_data[$key]['创意']=$value->name[3]?$value->name[3]:'-';
                    $grid_data[$key]['描述1']=$value->name[4]?$value->name[4]:'-';
                    $grid_data[$key]['url']=$value->name[6]?$value->name[6]:'-';
                    $grid_data[$key]['描述2']=$value->name[5]?$value->name[5]:'-';
                    
                    break;
                default:
                    # code...
                    break;
            }
            $grid_data[$key]['展现']=$value->kpis[0];
            $grid_data[$key]['消费']=$value->kpis[1];
            $grid_data[$key]['点击']=$value->kpis[3];
            $grid_data[$key]['cpc']=round($value->kpis[2],2);
            $grid_data[$key]['ctr']=round($value->kpis[4],2);
            $grid_data[$key]['平均排名']=round($value->kpis[6],2);
        }
        //var_dump($grid_data);
        /*grid_data0*/
        $grid_data0 = array( );
        foreach ($result_json_0 as $key => $value) {
            $grid_data0[$key]['日期']=$value->date;
            $grid_data0[$key]['id']=$value->id;
            $grid_data0[$key]['账户']=$value->name[0]?$value->name[0]:'-';
            switch ($tire) {
                case '计划':
                    $grid_data0[$key]['计划']=$value->name[1]?$value->name[1]:'-';
                    break;
                case '单元':
                    $grid_data0[$key]['计划']=$value->name[1]?$value->name[1]:'-';
                    $grid_data0[$key]['单元']=$value->name[2]?$value->name[2]:'-';
                    break;
                case '关键词':
                    $grid_data0[$key]['计划']=$value->name[1]?$value->name[1]:'-';
                    $grid_data0[$key]['单元']=$value->name[2]?$value->name[2]:'-';
                    $grid_data0[$key]['关键词']=$value->name[3]?$value->name[3]:'-';
                    break;
                case '创意':
                    $grid_data0[$key]['计划']=$value->name[1]?$value->name[1]:'-';
                    $grid_data0[$key]['单元']=$value->name[2]?$value->name[2]:'-';
                    $grid_data0[$key]['创意']=$value->name[3]?$value->name[3]:'-';
                    $grid_data0[$key]['描述1']=$value->name[4]?$value->name[4]:'-';
                    $grid_data0[$key]['url']=$value->name[6]?$value->name[6]:'-';
                    $grid_data0[$key]['描述2']=$value->name[5]?$value->name[5]:'-';
                    
                    break;
                default:
                    # code...
                    break;
            }
            $grid_data0[$key]['展现']=$value->kpis[0];
            $grid_data0[$key]['消费']=$value->kpis[1];
            $grid_data0[$key]['点击']=$value->kpis[3];
            $grid_data0[$key]['cpc']=round($value->kpis[2],2);
            $grid_data0[$key]['ctr']=round($value->kpis[4],2);
            $grid_data0[$key]['平均排名']=round($value->kpis[6],2);
        }
        //var_dump($grid_data0);
        $grid_compare = array();
        foreach ($grid_data as $key => $value) {
            if (deep_in_array($value['id'], $grid_data0)) {
                foreach ($grid_data0 as $k => $v) {
                    if ($value['id']==$v['id']) {
                    foreach ($value as $kv => $vv) {
                        switch ($kv) {
                            case '展现':
                                $grid_compare[$key]['本期展现']=$vv;
                                $grid_compare[$key]['上期展现']=$v[$kv];
                                $grid_compare[$key]['展现变化量']=$vv-$v[$kv];
                                $grid_compare[$key]['展现变化率']=$vv==0?'-':round(($vv-$v[$kv])/$vv,4);
                                break;
                            case '消费':
                                $grid_compare[$key]['本期消费']=$vv;
                                $grid_compare[$key]['上期消费']=$v[$kv];
                                $grid_compare[$key]['消费变化量']=$vv-$v[$kv];
                                $grid_compare[$key]['消费变化率']=$vv==0?'-':round(($vv-$v[$kv])/$vv,4);
                                break;
                            case '点击':
                                $grid_compare[$key]['本期点击']=$vv;
                                $grid_compare[$key]['上期点击']=$v[$kv];
                                $grid_compare[$key]['点击变化量']=$vv-$v[$kv];
                                $grid_compare[$key]['点击变化率']=$vv==0?'-':round(($vv-$v[$kv])/$vv,4);
                                break;
                            case 'cpc':
                                $grid_compare[$key]['本期cpc']=$vv;
                                $grid_compare[$key]['上期cpc']=$v[$kv];
                                $grid_compare[$key]['cpc变化量']=round($vv-$v[$kv],4);
                                $grid_compare[$key]['cpc变化率']=$vv==0?'-':round(($vv-$v[$kv])/$vv,4);
                                break;
                            case 'ctr':
                                $grid_compare[$key]['本期ctr']=$vv;
                                $grid_compare[$key]['上期ctr']=$v[$kv];
                                $grid_compare[$key]['ctr变化量']=round($vv-$v[$kv],4);
                                $grid_compare[$key]['ctr变化率']=$vv==0?'-':round(($vv-$v[$kv])/$vv,4);
                                break;
                            case '平均排名':
                                $grid_compare[$key]['本期平均排名']=$vv;
                                $grid_compare[$key]['上期平均排名']=$v[$kv];
                                $grid_compare[$key]['平均排名变化量']=$vv-$v[$kv];
                                $grid_compare[$key]['平均排名变化率']=$vv==0?'-':round(($vv-$v[$kv])/$vv,4);
                                break;
                            
                            default:
                                $grid_compare[$key][$kv]=$vv;
                                break;
                        }
                    }
                    }
                }
            }else{
                foreach ($value as $kv => $vv) {
                        switch ($kv) {
                            case '展现':
                                $grid_compare[$key]['本期展现']=$vv;
                                $grid_compare[$key]['上期展现']=0;
                                $grid_compare[$key]['展现变化量']=$vv;
                                $grid_compare[$key]['展现变化率']=$vv==0?'-':1;
                                break;
                            case '消费':
                                $grid_compare[$key]['本期消费']=$vv;
                                $grid_compare[$key]['上期消费']=0;
                                $grid_compare[$key]['消费变化量']=$vv;
                                $grid_compare[$key]['消费变化率']=$vv==0?'-':1;
                                break;
                            case '点击':
                                $grid_compare[$key]['本期点击']=$vv;
                                $grid_compare[$key]['上期点击']=0;
                                $grid_compare[$key]['点击变化量']=$vv;
                                $grid_compare[$key]['点击变化率']=$vv==0?'-':1;
                                break;
                            case 'cpc':
                                $grid_compare[$key]['本期cpc']=$vv;
                                $grid_compare[$key]['上期cpc']=0;
                                $grid_compare[$key]['cpc变化量']=round($vv,4);
                                $grid_compare[$key]['cpc变化率']=$vv==0?'-':1;
                                break;
                            case 'ctr':
                                $grid_compare[$key]['本期ctr']=$vv;
                                $grid_compare[$key]['上期ctr']=0;
                                $grid_compare[$key]['ctr变化量']=$vv;
                                $grid_compare[$key]['ctr变化率']=$vv==0?'-':1;
                                break;
                            case '平均排名':
                                $grid_compare[$key]['本期平均排名']=$vv;
                                $grid_compare[$key]['上期平均排名']=0;
                                $grid_compare[$key]['平均排名变化量']=$vv;
                                $grid_compare[$key]['平均排名变化率']=$vv==0?'-':1;
                                break;
                            
                            default:
                                $grid_compare[$key][$kv]=$vv;
                                break;
                        }
                    }
            }    
        }
        $grid_compare_lenth = count($grid_compare);
        foreach ($grid_data0 as $key => $value) {
            /*var_dump(deep_in_array(64724742032,$grid_data));*/
            if (!deep_in_array($value['id'],$grid_data)) {
                foreach ($value as $kv => $vv) {
                        switch ($kv) {
                            case '展现':
                                $grid_compare[$grid_compare_lenth]['本期展现']=0;
                                $grid_compare[$grid_compare_lenth]['上期展现']=$vv;
                                $grid_compare[$grid_compare_lenth]['展现变化量']=0-$vv;
                                $grid_compare[$grid_compare_lenth]['展现变化率']='-';
                                break;
                            case '消费':
                                $grid_compare[$grid_compare_lenth]['本期消费']=0;
                                $grid_compare[$grid_compare_lenth]['上期消费']=$vv;
                                $grid_compare[$grid_compare_lenth]['消费变化量']=0-$vv;
                                $grid_compare[$grid_compare_lenth]['消费变化率']='-';
                                break;
                            case '点击':
                                $grid_compare[$grid_compare_lenth]['本期点击']=0;
                                $grid_compare[$grid_compare_lenth]['上期点击']=$vv;
                                $grid_compare[$grid_compare_lenth]['点击变化量']=0-$vv;
                                $grid_compare[$grid_compare_lenth]['点击变化率']='-';
                                break;
                            case 'cpc':
                                $grid_compare[$grid_compare_lenth]['本期cpc']=0;
                                $grid_compare[$grid_compare_lenth]['上期cpc']=$vv;
                                $grid_compare[$grid_compare_lenth]['cpc变化量']=round(0-$vv,4);
                                $grid_compare[$grid_compare_lenth]['cpc变化率']='-';
                                break;
                            case 'ctr':
                                $grid_compare[$grid_compare_lenth]['本期ctr']=0;
                                $grid_compare[$grid_compare_lenth]['上期ctr']=$vv;
                                $grid_compare[$grid_compare_lenth]['ctr变化量']=round(0-$vv,4);
                                $grid_compare[$grid_compare_lenth]['ctr变化率']='-';
                                break;
                            case '平均排名':
                                $grid_compare[$grid_compare_lenth]['本期平均排名']=0;
                                $grid_compare[$grid_compare_lenth]['上期平均排名']=$vv;
                                $grid_compare[$grid_compare_lenth]['平均排名变化量']=0-$vv;
                                $grid_compare[$grid_compare_lenth]['平均排名变化率']='-';
                                break;
                            
                            default:
                                $grid_compare[$grid_compare_lenth][$kv]=$vv;
                                break;
                        }
                    }
                $grid_compare_lenth++;
            }
        }
        //var_dump($grid_compare);
        /*grid_fields*/
        foreach ($grid_compare[0] as $key => $value) {
            switch ($key) {
                case '日期':
                case '账户':
                case '关键词':
                case '创意':
                
                    $grid_fields[]=array('name'=>$key,'type'=>'text','width'=>110);
                    break;
                case 'id':
                    $grid_fields[]=array('name'=>$key,'type'=>'text','width'=>110,'visible'=>false);
                    break;
                case '描述1':
                case '描述2':
                case 'url':
                    $grid_fields[]=array('name'=>$key,'type'=>'textarea','width'=>190);
                    break;
                default:
                     $grid_fields[]=array('name'=>$key,'type'=>'text','width'=>80);
                    break;
            }
           
        }
        //var_dump($grid_fields);
        //var_dump($grid_data);
        $grid_return = array('data' =>$grid_compare ,'fields'=>$grid_fields );
        $this->assign('grid_data',json_encode($grid_return));
        /*prepareRank*/
        foreach ($grid_compare as $key => $value) {
            $prepareRank['cost']['name'][]=$value[$tire];
            $prepareRank['cost']['data'][]=$value['消费变化量'];
            $prepareRank['impression']['name'][]=$value[$tire];
            $prepareRank['impression']['data'][]=$value['展现变化量'];
            $prepareRank['click']['name'][]=$value[$tire];
            $prepareRank['click']['data'][]=$value['点击变化量'];
            $prepareRank['ctr']['name'][]=$value[$tire];
            $prepareRank['ctr']['data'][]=$value['ctr变化量'];
            $prepareRank['cpc']['name'][]=$value[$tire];
            $prepareRank['cpc']['data'][]=$value['cpc变化量'];
            $prepareRank['平均排名']['name'][]=$value[$tire];
            $prepareRank['平均排名']['data'][]=$value['平均排名变化量'];   
        }
         foreach ($grid_compare as $key => $value) {
            $prepareRank_change_rate['cost']['name'][]=$value[$tire];
            $prepareRank_change_rate['cost']['data'][]=$value['消费变化率'];
            $prepareRank_change_rate['impression']['name'][]=$value[$tire];
            $prepareRank_change_rate['impression']['data'][]=$value['展现变化率'];
            $prepareRank_change_rate['click']['name'][]=$value[$tire];
            $prepareRank_change_rate['click']['data'][]=$value['点击变化率'];
            $prepareRank_change_rate['ctr']['name'][]=$value[$tire];
            $prepareRank_change_rate['ctr']['data'][]=$value['ctr变化率'];
            $prepareRank_change_rate['cpc']['name'][]=$value[$tire];
            $prepareRank_change_rate['cpc']['data'][]=$value['cpc变化率'];
            $prepareRank_change_rate['平均排名']['name'][]=$value[$tire];
            $prepareRank_change_rate['平均排名']['data'][]=$value['平均排名变化率'];   
        }
        /*按指标排序*/
        $produce_flag = S('changes_produce_flag') ? S('changes_produce_flag') : "增长";
        if ($produce_flag=="增长") {
            $afterRank = mysort($prepareRank,true);
            $afterRank_byrate = mysort($prepareRank_change_rate,true);
        }else{
            $afterRank = mysort($prepareRank,false);
            $afterRank_byrate = mysort($prepareRank_change_rate,false);
        }
        
        //var_dump($afterRank_byrate);
        $rank_return = array();
        switch ($tire) {
            case '计划':
                $top_choice = 10;
                break;
            case '单元':
                $top_choice = 50;
                break;
            case '关键词':
                $top_choice = 100;
                break;
            
            default:
                $top_choice = 10;
                break;
        }
        switch ($filterparams) {
            case '消费额':
                $rank_return['name'] = array_slice($afterRank['cost']['name'],0, $top_choice);
                $rank_return['data'] = array_slice($afterRank['cost']['data'], 0,$top_choice);
                break;
            case '消费变化率':
                $rank_return['name'] = array_slice($afterRank_byrate['cost']['name'],0, $top_choice);
                $rank_return['data'] = array_slice($afterRank_byrate['cost']['data'], 0,$top_choice);
                break;
            case '展现额':
                $rank_return['name'] = array_slice($afterRank['impression']['name'],0, $top_choice);
                $rank_return['data'] = array_slice($afterRank['impression']['data'], 0,$top_choice);
                break;
            case '展现变化率':
                $rank_return['name'] = array_slice($afterRank_byrate['impression']['name'],0, $top_choice);
                $rank_return['data'] = array_slice($afterRank_byrate['impression']['data'], 0,$top_choice);
                break;
            case '点击额':
                $rank_return['name'] = array_slice($afterRank['click']['name'],0, $top_choice);
                $rank_return['data'] = array_slice($afterRank['click']['data'], 0,$top_choice);
                
                break;
            case '点击变化率':
                $rank_return['name'] = array_slice($afterRank_byrate['click']['name'],0, $top_choice);
                $rank_return['data'] = array_slice($afterRank_byrate['click']['data'], 0,$top_choice);
                break;
            case '点击率':
                $rank_return['name'] = array_slice($afterRank['ctr']['name'],0, $top_choice);
                $rank_return['data'] = array_slice($afterRank['ctr']['data'], 0,$top_choice);
                break;
            case '点击率变化率':
                $rank_return['name'] = array_slice($afterRank_byrate['ctr']['name'],0, $top_choice);
                $rank_return['data'] = array_slice($afterRank_byrate['ctr']['data'], 0,$top_choice);
                
                break;
            case '平均价格':
                $rank_return['name'] = array_slice($afterRank['cpc']['name'],0, $top_choice);
                $rank_return['data'] = array_slice($afterRank['cpc']['data'], 0,$top_choice);
                
                break;
            case '平均价格变化率':
                $rank_return['name'] = array_slice($afterRank_byrate['cpc']['name'],0, $top_choice);
                $rank_return['data'] = array_slice($afterRank_byrate['cpc']['data'], 0,$top_choice);
                break;
            default:
                # code...
                break;
        }
        //var_dump($rank_return);
        $this->assign('top_rank_data',json_encode($rank_return));
        if (IS_AJAX) {
            $changes_data['data']['grid_data']=$grid_return;
            $changes_data['data']['top_rank_data']=$rank_return;
            $this->ajaxReturn($changes_data);
        }
        $this->display();
    }
/*数据洞察-->创意配图投放分析*/

    public function creative(){
         $this->assign('trade',getAccountList());
        /*参数处理*/
        
        /*参数处理end*/
        $this->display();
    }

/*数据洞察-->左上方排名分析*/

    public function left_rank(){
         $this->assign('trade',getAccountList());
        /*参数处理*/
        
        /*参数处理end*/
        $this->display();
    }

/*历史操作 operation_record()*/
    public function operation_record(){
        /*$result = getReport("Toolkit");
        echo $result;*/ 
        /*静态数组p——temp，保存前台已经点击过的选择按钮值*/
        static $p_temp = array();
        /*设置默认值*/
        $p_temp['startDate']=isset($p_temp['startDate'])?$p_temp['startDate']:date('Y-m-d',strtotime('-1 day'));
        $p_temp['endDate']=isset($p_temp['endDate'])?$p_temp['endDate']:date('Y-m-d');
        $p_temp['device']=isset($p_temp['device'])?$p_temp['device']:0;

        if (IS_AJAX) {
            # code...
            /*如果有需要处理的前端传来的数据，先处理数据，比如范围选择，地域代码数据，*/
             /*更新$p_temp数组*/
            $p_unitOfTime = $_POST['unitOfTime']?$_POST['unitOfTime']:$p_temp;
            $p_temp['order']=$_POST['order']?$_POST['order']:$p_temp['order'];
            $datepick = explode(" ",  $_POST['datepicker']);
            $p_temp['startDate']=$datepick[0]?$datepick[0]:$p_temp['startDate'];
            $p_temp['endDate']=$datepick[2]?$datepick[2]:$p_temp['endDate'];
            $p_temp['device']=$_POST['device']?$_POST['device']:$p_temp['device'];
            
            $param_data  = dispatch_kpijob($_POST['pager_select'],$p_temp);
            
            //var_dump(json_decode($param));

            
           $this->ajaxReturn($param_data);
        }
        
         $this->display();
    }
/*排名优化 optimize（）*/
    public function optimize(){
        /*参数处理*/
        
        /*参数处理end*/
        $this->display();
    }




// 0415 数据查看-测试
    public function chakan(){
        // 设备
        $device = $_REQUEST['device'] ? $_REQUEST['device'] : 0;
        // 日期
        if($_REQUEST['riqi']){
            $riqi = explode(" ",  $_REQUEST['riqi']);
            $startDate=$riqi[0];
            $endDate =$riqi[2];
        }else{
            $startDate=date('Y-m-d',strtotime('-1 day'));
            $endDate =date('Y-m-d');
        }
        // 地域
        if($_REQUEST['attributes']){
            $attributes = explode("-",  $_REQUEST['attributes']);
        }else{
            $attributes = null;
        }
        // 查看类型：默认为5    1分年；3分月；4分周；5分日；7分时；8请求时间段汇总(endDate- StartDate)
        $unitOfTime = $_REQUEST['unitOfTime'] ? $_REQUEST['unitOfTime'] : 5;

        $p_temp = array();
        if (IS_AJAX) {
            # code...
            $statIds_temp = $_REQUEST['statIds'];
            //var_dump($_POST['statIds']);
            foreach ($statIds_temp as $key => $value) {
                $p_temp['statIds'][] =$value['value'];
            }
            $p_temp['order']=$_POST['order']?$_POST['order']:$p_temp['order'];
            $p_temp['startDate']=$startDate;
            $p_temp['endDate']=$endDate;
            $p_temp['device']=$device;

            $param_data  = dispatch_kpijob($_POST['pager_select'],$p_temp);
            $param_r=json_decode($param_data)->body->data;
            $param_return = array( );
            foreach ($param_r as $key => $value) {
                # code...
                $param_return['data']['id'][]=$value->id;
                $param_return['data']['impression'][]=$value->kpis[0];
                $param_return['data']['cost'][]=$value->kpis[1];
                $param_return['data']['cpc'][]=round($value->kpis[2]);
                $param_return['data']['click'][]=$value->kpis[3];
                $param_return['data']['ctr'][]=round($value->kpis[4]);
                $param_return['data']['cpm'][]=$value->kpis[5];
                $param_return['data']['name'][]=$value->name;
                $param_return['data']['date'][]=$value->date;
            }
            $this->ajaxReturn($param_return);
        }

        $this->display();
    }
/*** 数据查看 data_check()*/

   public  function data_check(){
            
        
        $target = $_POST['target'] ?$_POST['target']:0;
        if($target){
            session('username_normal',$target);
        }
        /*参数处理*/
        
        /*参数处理end*/
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
        $param = array("startDate"=>$startDate,"endDate"=>$endDate,"platform"=>0,"device"=>$device);


        $this->display();
    }
/*trade_check()*/
    public function trade_check(){
        $this->assign('trade',getAccountList());
        $this->display();
    }
//        static $p_temp = array();
//        /*设置默认值*/
//        $p_temp['startDate']=isset($p_temp['startDate'])?$p_temp['startDate']:date('Y-m-d',strtotime('-1 day'));
//        $p_temp['endDate']=isset($p_temp['endDate'])?$p_temp['endDate']:date('Y-m-d');
//        $p_temp['device']=isset($p_temp['device'])?$p_temp['device']:0;
//        $p_temp['unitOfTime']=isset($p_temp['unitOfTime'])?$p_temp['unitOfTime']:5;
//        if (IS_AJAX) {
//            # code...
//            $statIds_temp = $_POST['statIds'];
//            //var_dump($_POST['statIds']);
//            foreach ($statIds_temp as $key => $value) {
//                $p_temp['statIds'][] =$value['value'];
//            }
//            //var_dump($p_temp);
//            $p_temp['unitOfTime'] = $_POST['unitOfTime']?$_POST['unitOfTime']:$p_temp['unitOfTime'];
//            $p_temp['order']=$_POST['order']?$_POST['order']:$p_temp['order'];
//            $datepick = explode(" ",  $_POST['datepicker']);
//            $p_temp['startDate']=$datepick[0]?$datepick[0]:$p_temp['startDate'];
//            $p_temp['endDate']=$datepick[2]?$datepick[2]:$p_temp['endDate'];
//            $p_temp['device']=$_POST['device']?$_POST['device']:$p_temp['device'];
//
//            $param_data  = dispatch_kpijob($_POST['pager_select'],$p_temp);
//            $param_r=json_decode($param_data)->body->data;
//
//            $param_return = array( );
//            foreach ($param_r as $key => $value) {
//                # code...
//                $param_return['data']['id'][]=$value->id;
//                $param_return['data']['impression'][]=$value->kpis[0];
//                $param_return['data']['cost'][]=$value->kpis[1];
//                $param_return['data']['cpc'][]=round($value->kpis[2]);
//                $param_return['data']['click'][]=$value->kpis[3];
//                $param_return['data']['ctr'][]=round($value->kpis[4]);
//                $param_return['data']['cpm'][]=$value->kpis[5];
//                $param_return['data']['name'][]=$value->name;
//                $param_return['data']['date'][]=$value->date;
//
//            }
//            //var_dump(json_decode($param));
//            //var_dump($param_data);
//            //var_dump(gettype($param));
//            $this->ajaxReturn($param_return);
//        }
//
//        $this->display();
    

}

