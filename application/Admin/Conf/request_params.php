<?php

return array(
	/* 1、--------------------------------------------------------------------------------------- */
	'AccountPostfix'=> '/AccountService/getAccountInfo',
	'AccountInfo'=>array(
	"accountFields" => ["userId","balance","pcBalance","mobileBalance","cost","payment","budgetType","budget","regionTarget","excludeIp","openDomains","regDomain","budgetOfflineTime","weeklyBudget","userStat","isDynamicCreative","isDynamicTagSublink","isDynamicTitle","isDynamicHotRedirect","dynamicCreativeParam","userLevel"]
	),
	/* 2、--------------------------------------------------------------------------------------- */
	'CampaignPostfix'=> '/CampaignService/getCampaign',
	'CampaignInfo'=>array("campaignIds" => [],"campaignFields" => ["campaignName","budget","regionTarget","negativeWords","exactNegativeWords","schedule","budgetOfflineTime","showProb","pause","status","isDynamicCreative","campaignType","device","priceRatio"]),
	
	/*/3、--------------------------------------------------------------------------------------- */
	'AdgroupPostfix'=>'/AdgroupService/getAdgroup',
	'AdgroupInfo'=>array(
	'adgroupFields'=>['adgroupId','campaignId','adgroupName','pause','maxPrice','negativeWords','exactNegativeWords','status','accuPriceFactor','wordPriceFactor','widePriceFactor','matchPriceStatus','priceRatio','pcPriceRatio'],
	/* 查询 id 集合 :idType=5；类型为单元 id，不超过 5000 个 ;idType=3，类型为计划 id，不超过 100 个 必填，建议分批多次请求 */
	'ids' =>[67553278],
	/* 3 计划id，5单元id   必填 */
	'idType'=>3,
	),
	/*4、--------------------------------------------------------------------------------------- */
	
	'KeywordPostfix' => '/KeywordService/getWord',
	'KeywordInfo' => array(
	'wordFields' =>['keywordId','campaignId','adgroupId','keyword','price','pause','matchType','phraseType','status','wmatchprefer','pcDestinationUrl','pcQuality','pcScale','mobileDestinationUrl','mobileQuality','mobileScale','tabs','leftPriceGuide','left3PriceGuide','mPriceGuide','pcQualityFactor','mobileQualityFactor'],
	/* 查询 id 集合 idType=5；类型为单元 id，不超过 50 个 idType=11，类型为关键词id，不超过 10000 个.必填 
建议分批多次请求  */
	'ids'=>[2139138018],
	/* 必填 */
	'idType'=>5,
	 /* 是否查询关键词影子 选填，
	 默认为 0 
	0：只查询关键词本身 
	1：只查询关键词影子 
	想要获得关键词的全集，需要调用该方法两次,分别为 getTemp=0 和 getTemp=1  
说明：影子是指用户先向系统提交了关键词 A，然后又对 A 进行修改（例如修改 url），
修改后的关键词为 A’，在 A'通过审核生效之前，线上的生效关键词仍然为 A。此时
getTemp 为 0 查询到的是 A, getTemp 为 1 查询到的是 A’.  	*/
	'getTemp'=>0,
	),
	/*5、--------------------------------------------------------------------------------------- */
	
	'CreativePostfix'=>'/CreativeService/getCreative',
	'CreativeInfo'=>array(
	'creativeFields'=>['creativeId','adgroupId','title','pause','status','description1','description2','pcDestinationUrl','pcDisplayUrl','mobileDestinationUrl','mobileDisplayUrl','devicePreference','tabs'],
		/* 查询 id 集合 ;idType=5类型为单元 id，不超过 1000 个; idType=7，类型为创意 id，不超过 3000 个. 必填 
建议分批多次请求  */
	'ids'=>[2139138018],
	'idType'=>5,
		/* 是否查询创意影子 
		选填，默认为 0 
		0：只查询创意本身 
		1：只查询创意影子 
		想要获得创意的全集，需要调用该方法两次,分别为 getTemp=0 和 getTemp=1   
		说明：影子是指：用户先向系统提交了创意 A，然后又对 A 进行修改（例如修改 url），修改后的创意为 A’，在 A'通过审核生效之前，线上的生效创意仍然为 A。此时 getTemp 为 0 查询到的是 A, getTemp 为 1 查询到的是 A’.   */
	'getTemp'=>0,	
	),
	/*6、--------------------------------------------------------------------------------------- */
	'NewCreativePostfix'=>'/NewCreativeService/getSublink',
	'NewCreativeInfo'=>array(
	'ids'=>[2139138018],
/* 	5：单元 id，ids 限制 2500 个 
12：蹊径 id，ids 限制不超过 5000 个 
建议分批多次请求  */
	'idType'=>5,
	/* 必填。Device 必返，用于标识推广子
链的投放设备  */
	'sublinkFields'=>['sublinkId','adgroupId','sublinkInfos','pause','status'],
	/* 是否查询影子
必填 
0：只查询计算机推广子链接本身 
1：只查询计算机推广子链影子 
想要获得计算机推广子链的全集，需
要调用该方法两次,分别为 getTemp=0
和 getTemp=1   	*/
	'getTemp'=>0,
	/* 必填 
0：计算机 
1：移动设备  */
	'device'=>0
	
	),
	
	/*7、--------------------------------------------------------------------------------------- */
	'ToolkitPostfix'=>'/ToolkitService/getOperationRecord',
	'ToolkitInfo'=>array(
	'startDate'=>'2018-01-01',
	'endDate'=>'2018-01-30',
	'optTypes'=>[],
	'optLevel'=>3,
	'optContents'=>[]
	/* 具体参数参见api文档95页 */
	),
	
	/*8、--------------------------------------------------------------------------------------- */
	'DynamicCreativePostfix'=>'/DynamicCreativeService/getDynCreative',
	'DynamicCreativeInfo'=>array(
	'dynCreativeFields'=>['dynCreativeId','campaignId','adgroupId','bindingType','title','url','murl','status','pause'],
	'ids'=>[67553278],
	'idType'=>5
	),
	/* DynCreativeExclusionType 描述了动态创意的排除对象，该类型中的属性
取值范围在所有接口中均相同，但并不是所有属性在任何接口中均有意义。在具
体的接口描述中会有进一步描述，说明对接口有意义的属性。
	根据指定推广计划 id 获取推广计划下所有排除对象（ id 可批量）。 */
	'DynCreativeExclusionPostfix'=>'/DynamicCreativeService/getExclusionTypeByCampaignId',
	'DynCreativeExclusionInfo'=>array(
	'campaignIds'=>[67553278],
	),
	
	/*9、--------------------------------------------------------------------------------------- */
	
	/* realTimeRequestType  RealTimeRequestType 实时数据查询条件 */
	'RealTimeDataPostfix'=> '/ReportService/getRealTimeData',
	'RealTimeDataInfo'=>array(
	'realTimeRequestType'=>[
	'performanceData'=>['impression','cost','cpc','click','ctr','cpm'],
	/* 选填，默认 null ，按照时间排序：  
true ：降序  
false ：升序  
app 下载报告 / 推广电话报告，不支
持排序   */
	'order'=>null,
	'startDate'=>'2017-02-11',
	'endDate'=> '2018-01-30',
	/* 选填，默认为账户  2 ：账户粒度  3 ：计划粒度  5 ：单元粒度  7 ：创意粒度  11 ：关键词 (keywordid) 粒度  12 ：关键词 (keywordid)+ 创意粒度  6 ：关键词 (wordid) 粒度    */
	'levelOfDetails'=>2,
/* 	选填；  为 NULL 表示统计全部地域。  key:provid  ；  value: 地域代码数组  说明： app 下载报告 / 推广电话不支持 attributes  */
	//'attributes'=>null,
/* 	必填；  2 ：账户  10 ：计划  11 ：单元  14 ：关键词 (keywordid) 12 ：创意  3 ：地域  9 ：关键词 (wordid) 5 ：二级地域报告  21 ：蹊径报告  38 ：历史数据排名报告  40 ： app 下载报告  41 ：推广电话报告   */
	'reportType'=>2,
	/* 统计范围下的 id 集合。根据 StatRange 的不同类型填写不同 id  
	选填，默认 NULL ，表示统计范围为全账户  staRange 为 3 时填写计划 id; staRange 为 5 时填写单元 id; staRange 为 7 时填写创意 id; staRange 为 11 时填写关键词keywordid; staRange 为 6 时填写关键词 wordid */
	'statIds'=>[],
	/* 选填，默认值为 2 ；  2 ：账户范围  3 ：计划范围  5 ：单元范围  7 ：创意范围  11 ：关键词 (keywordid) 范围  6 ：关键词 (wordid) 范围  
注意：统计范围不能细于当前的统计粒度，例如统计粒度为计划，则统计范围不能细到单元   */
	'statRange'=>2,
	/* 选填，默认值为 5 取值范围：  5 ：分日  4 ：分周  3 ：分月  1 ：分年  7 ：分小时  8 ：请求时间段汇总 (endDate-StartDate)    */
	'unitOfTime'=>5,
	/* 返回数据条数     选填  目前实时报告中账户、计划、单元、关键词、创意报告最大支持10000 ，其他类型实时报告只支持5000 。  默认值1000 注意：超过限制或者小于等于 0 则报错  说明： app 下载报告 / 推广电话报
告、当物料量较大时，建议按计划或单元分批获取，一条对应三条返回值   */
	'number'=>100,
	/* 选填，默认值为 0 取值范围：  0 ：全部搜索推广设备  1 ：仅计算机  2 ：仅移动   */
	/* 'device'=>0, */
	/* 选填，默认为0 搜索推广：0 内部流量：3 搜索合作流量：4 以上三个值支持账户、计划、单元、关键词、创意五种基本类型报告，逻辑关系为：0=3+4   信息流推广：23 搜索信息流：14 贴吧信息流：15 全部（搜索推广+信息流推广）：99   以上四个值支持账户、计划、单元三种 基 本 类 型 报 告 ， 逻 辑 关 系 为 ：23=14+15；99=0+23  */
	'platform'=>0,
	/* 选填，默认为0 全部：0 基础样式：1 强样式：2 应用商店：3 说明： App下载报告支持样式类型：0、1、2、3 推广电话报告支持：0、1、2  */
	'styleType'=>0
	]
	),
	
	/* realTimeQueryRequestType  RealTimeQueryRequestType 搜索词报告查询条件   */
	'RealTimeQueryDataPostfix'=> '/ReportService/getRealTimeQueryData',
	'RealTimeQueryDataInfo'=>array(
	'realTimeQueryRequestType'=>[
	'performanceData'=>['click','impression'],
	'startDate'=>'2018-01-11',/*最细粒度到小时*/
	'endDate'=>'2018-01-22',
	/*   必填  （搜索词报告只能是创意粒度）  7 ：创意粒度  12 ：关键词 (keywordid)+ 创意粒度   */
	'levelOfDetails'=>12,
	/*  AttibuteType  针对特定的数据层级设置特定的统计范围   选填；  为 NULL 表示统计全部地域。  key:provid  ；  value: 地域代码数组  
	key  String 针对特定的报告类型设置特定的统计范围 目前仅对地域报告和搜索词报告使用，合法值：provid value  int[] 针对特定的报告类型设置特定的统计范围 目前仅对地域报告和搜索词报告使用，key取 provid 时，value 值为地域代码数组，为空则表示全部地域 	*/
	'attributes'=>null,
	/* 实时数据类型   必填；  6 ：搜索词   */
	'reportType'=>6,
	/* Long[]  选填，默认 NULL ，表示统计范围为全账户  staRange 为 3 时填写计划 id;  */
	'statIds'=>[],
	/* 选填，默认值为 2 ；  2 ：账户范围  3 ：计划范围  注意：统计范围不能细于当前的统计粒度，例如统计粒度为计划，则统计范围不能细到单元   */
	'statRange'=>2,
	/* 选填，默认值为 5 取值范围：  5 ：分日  8 ：请求时间段汇总 (endDate-StartDate)    */
	'unitOfTime'=>5,
	/* 选填  目前最大支持 5000 （请求超过 5000将直接返回前 5000 条）  默认值 1000  */
	'number'=>100,
	/* 选填，默认值为 0 取值范围：  0 ：全部搜索推广设备  1 ：仅计算机  2 ：仅移动   */
	'device'=>0
	]
	),
	
	/* realTimePairRequestType RealTimePairRequestType 配对报告数据查询条件   */
	'RealTimePairDataPostfix'=> '/ReportService/getRealTimePairData',
	'RealTimePairDataInfo'=>array(
	'realTimePairRequestType'=>[
	'performanceData'=>['cost','cpc','click','impression','ctr','cpm',/* 'position', */'conversion'],
	/* 选填，默认 null ，按照时间排序：  true ：降序  false ：升序   */
	/* 'order'=>false, */
	'startDate'=>'2018-01-02',
	'endDate'=>'2018-02-02',
	/* 必填  12 ：关键词 (keywordid)+ 创意粒度   */
	'levelOfDetails'=>12,
	/* AttibuteType  为 NULL 表示统计全部地域。  key:provid  ；  value: 地域代码数组  
	key  String 针对特定的报告类型设置特定的统计范围 目前仅对地域报告和搜索词报告使用，合法值：provid value  int[] 针对特定的报告类型设置特定的统计范围 目前仅对地域报告和搜索词报告使用，key取 provid 时，value 值为地域代码数组，为空则表示全部地域 	*/
	'attributes'=>null,
	/* 必填；  15 ：配对   */
	'reportType'=>15,
	/* Long[]  选填，默认 NULL ，表示统计范围为全账户  staRange 为 3 时填写计划 id; staRange 为 5 时填写单元 id; staRange 为 7 时填写创意 id; staRange 为 11 时填写关键词keywordid;  */
	'statIds'=>[],
	/* 选填，默认值为 2 ；  2 ：账户范围  3 ：计划范围  5 ：单元范围  7 ：创意范围  11 ：关键词 (keywordid) 范围  注意：统计范围不能细于当前的统计粒度，例如统计粒度为计划，则统计范围不能细到单元   */
	'statRange'=>2,
	/* 选填，默认值为 5 取值范围：  5 ：分日  4 ：分周  3 ：分月  1 ：分年  8 ：请求时间段汇总 (endDate-StartDate)    */
	'unitOfTime'=>5,
	/* 选填  目前最大支持 5000 （请求超过 5000将直接返回前 5000 条）  默认值 1000  */
	'number'=>100,
	'device'=>0
	]
	),
	
	
	/* reportRequestType  ReportRequestType  报告查询条件  返回值 reportId  string  生成的报告 ID ID 为一串 MD5 值，格式为 32 位 16 进制数。例如：8e7e3f2d84a19c5df1415957434b2bd8  */
	'ProfessionalReportIdPostfix'=> '/ReportService/getProfessionalReportId',
	'ProfessionalReportIdInfo'=>array(
	'reportRequestType'=>[
	'performanceData'=>['cost','cpc','click','impression','ctr','cpm',/* 'position', */'conversion','phoneConversion','bridgeConversion'],
	'startDate'=>'2018-01-02',
	'endDate'=>'2018-02-02',
	/* 选填；默认为 false 取值范围： true：只获取 id false：既获取 id 也获取字面  */
	'idOnly'=>false,
	/* 选填；不同的 ReportType 对应的默认值和取值范围不同，请参见规则描述 2：账户粒度 3：计划粒度 5：单元粒度 7：创意粒度 11：关键词(keywordid)粒度 12：关键词(keywordid)+创意粒度 6：关键词(wordid)粒度  */
	'levelOfDetails'=>2,
	/*AttibuteType[ ] 选填； 目前仅地域报告和搜索词报告使用了该字段，若为 NULL 表示统计全部地域。 key:provid  ； value:地域代码数组 说明： app 下载报告 / 推广电话不支持attributes  
	key  String 针对特定的报告类型设置特定的统计范围 目前仅对地域报告和搜索词报告使用，合法值：provid value  int[] 针对特定的报告类型设置特定的统计范围 目前仅对地域报告和搜索词报告使用，key取 provid 时，value 值为地域代码数组，为空则表示全部地域 */
	'attributes'=>null,
	/* 选填，默认值为 2；2：csv 格式  */
	'format'=>2,
	/* 必填； 2：账户报告 10：计划报告 11：单元报告 14：关键词报告(keywordid)报告 12：创意报告 15：配对报告 3：地域报告 6：搜索词报告 9：关键词报告(wordid) 5 ：二级地域报告  38 ：历史数据排名报告  40 ： app 下载报告  41 ：推广电话报告  */
	'reportType'=>2,
	/* Long[]  选填，默认 NULL，表示统计范围为全账户 staRange 为 3 时填写计划 id; staRange 为 5 时填写单元 id; staRange 为 7 时填写创意 id; staRange 为 11 时填写关键词 keywordid;staRange 为 6 时填写关键词 wordid  */
	'statIds'=>[],
	/* 选填，默认值为 2； 2：账户范围 3：计划范围 5：单元范围 7：创意范围 11：关键词(keywordid)范围 6：关键词(wordid)范围 注意：统计范围不能细于当前的统计粒度，例如统计粒度为计划，则统计范围不能细到单元  */
	'statRange'=>2,
	/* 选填，默认值为 5 取值范围： 5：日报 4：周报 3：月报 1：年报 7：小时报 8：请求时间段汇总(endDate-StartDate)  */
	'unitOfTime'=>5,
	'device'=>0,
	/* 选填，默认为0 搜索推广：0 内部流量：3 搜索合作流量：4 以上三个值支持账户、计划、单元、关键词、创意五种基本类型报告，逻辑关系为：0=3+4   信息流推广：23 搜索信息流：14 贴吧信息流：15 全部（搜索推广+信息流推广）：99   以上四个值支持账户、计划、单元三种基 本 类 型 报 告 ， 逻 辑 关 系 为 ：23=14+15；99=0+23  */
	'platform'=>0,
	/* 选填，默认为 0 全部： 0 基础样式： 1 强样式： 2 应用商店： 3 说明：  App 下载报告支持样式类型： 0 、 1 、 2 、3 推广电话报告支持： 0 、 1 、 2  */
	'styleType'=>0
	]
	),
	
	/* 查询报告当前的生成状态。请求中提供报告 ID ，返回中带有当前报告 ID 的处理状态。 8c36bcbbd4ff403b385b5c50cd32bd6e  */
	'ReportStatePostfix'=> '/ReportService/getReportState',
	'ReportStateInfo'=>array(
	'reportId'=>'044f66721f095e3aba76eb833dfdb148'
	),
	/* 获取报告下载地址。当报告成功生成后，使用 reportId 请求，返回相应的报告下载地址 8c36bcbbd4ff403b385b5c50cd32bd6e   */
	'ReportFileUrlPostfix'=> '/ReportService/getReportFileUrl',
	'ReportFileUrlInfo'=>array(
	'reportId'=>'044f66721f095e3aba76eb833dfdb148'
	),
	
	/*10、--------------------------------------------------------------------------------------- */
	'BulkJobPostfix'=> '/BulkJobService/getAllObjects',
	'BulkJobInfo'=>array(
	'campaignIds'=>[],
	'includeTemp'=>true,
	'format'=>1,
	'accountFields'=>['all'],
	'campaignFields'=>['all'],
	'adgroupFields'=>['all'],
	'keywordFields'=>['all','leftPriceGuide','left3PriceGuide','mPriceGuide','pcQualityFactor','mobileQualityFactor'],
	'creativeFields'=>['all'],
	'sublinkFields'=>['all'],
	'mobileSublinkFields'=>['all'],
	'phoneFields'=>['all'],
	'bridgeFields'=>['all'],
	'dynamicCreativeFields'=>['all'],
	'ecallFields'=>['all'],
	'mobileExtend'=>1
	),
	
	
	'AllChangedObjectsPostfix'=>'/BulkJobService/getAllChangedObjects',
	'AllChangedObjectsInfo'=>array(
	/* 必填；最早可以查询到上个月 1 号，例如现在是 9月 10 日，则 startTime 最早只能到 8 月 1 日，即查询 8、9 两个月  */
	'startTime'=>'2018-02-01',
	'campaignIds'=>[],
	'includeTemp'=>true,
	'format'=>1,
	'campaignFields'=>['all'],
	'adgroupFields'=>['all'],
	'keywordFields'=>['all'],
	'creativeFields'=>['all'],
	'sublinkFields'=>['all'],
	'mobileSublinkFields'=>['all'],
	'phoneFields'=>['all'],
	'bridgeFields'=>['all'],
	'dynamicCreativeFields'=>['all'],
	'ecallFields'=>['all'],
	'mobileExtend'=>1
	),
	
	'FileStatusPostfix'=>'/BulkJobService/getFileStatus',
	'FileStatusInfo'=>array(
	'fileId'=>'887c3685afacbe7818880ca782e12d70'
	),
	
	'FilePathPostfix'=>'/BulkJobService/getFilePath',
	'FilePathInfo'=>array(
	'fileId'=>'887c3685afacbe7818880ca782e12d70'
	),
	
	'cancelDownloadPostfix'=>'/BulkJobService/cancelDownload',
	'cancelDownloadInfo'=>array(
	'fileId'=>'887c3685afacbe7818880ca782e12d70'
	),
	
	
	'ChangedIdPostfix'=>'/BulkJobService/getChangedId',
	'ChangedIdInfo'=>array(
	/* 必填；不能早于 3 个月以前，例如现在是 9 月 10 日，则 startTime 最早只能到 7 月 1 日，即查询 7、8、9 三个月  */
	'startTime'=>'2018-01-01',
	'campaignLevel'=>true,
	'adgroupLevel'=>true,
	'keywordLevel'=>true,
	'creativeLevel'=>true,
	'sublinkLevel'=>true,
	'mobileSublinkLevel'=>true,
	'phoneLevel'=>true,
	'bridgeLevel'=>true,
	'dynamicCreativeLevel'=>true,
	),
	
	'ChangedItemIdPostfix'=>'/BulkJobService/getChangedItemId',
	'ChangedItemIdInfo'=>array(
	/* 必填；不能早于 3 个月以前，例如现在是 9 月 10 日，则 startTime 最早只能到 7 月 1 日，即查询 7、8、9 三个月  */
	'startTime'=>'2018-01-01',
/* 	必填字段： 1：  计划 2  ：单元 3  ：创意 4：关键词 5：动态创意 6：蹊径 7：无线蹊径 8 :  商桥 9：电话 备注： 各层级除计划类型外返回的数据限制不超过两万条。  */
	'itemType'=>1,
	/* id 不为空时必填， id 为空时无论 type 填何值，均默认为获取全账户范围下的变化 3：表示指定 id 数组为计划 id 5：表示指定 id 数组为单元 id   8 :计划+单元(仅对动态创意有效)  */
	'type'=>3,
	'ids'=>[],
	'campaignLevel'=>true,
	'adgroupLevel'=>true,
	'keywordLevel'=>true,
	'creativeLevel'=>true,
	'sublinkLevel'=>false,
	'mobileSublinkLevel'=>false,
	'phoneLevel'=>false,
	'bridgeLevel'=>false,
	'dynamicCreativeLevel'=>true,
	),
	
	
	
	/* 获取完整账户下，或者指定计划 ID 下的变化物料规模，从而帮助用户决定后续的最近更新策略。当有变化的物料规模大于一定比例时，用户不妨选择整账户下载。  
注：变化物料仅指因用户操作发生的变化，即在历史操作记录中可以查询到的操作。而对于质量度，状态这些在系统内自动发生的改变不在统计范围内。  */
	'ChangedScalePostfix'=>'/BulkJobService/getChangedScale',
	'ChangedScaleInfo'=>array(
	/* 必填；最早可以查询到上个月 1 号，例如现在是 9月 10 日，则 startTime 最早只能到 8 月 1 日，即查询 8、9 两个月  */
	'startTime'=>'2018-02-01',
	/* 选填，默认为空  为空表示获取该账户下的变化占比  */
	'campaignIds'=>[],
	/* 选填，默认值为 true； true:  统计增、删、改过的ID 数,当前 ID 总数 false：不统计增、删、改过的 ID 数，当前 ID 总数 */
	'changedCampaignScale'=>true,
	'changedAdgroupScale'=>true,
	'changedKeywordScale'=>true,
	'changedCreativeScale'=>true,
	'changedSublinkScale'=>true,
	'changedMoblieSublinkScale'=>true,
	'changedPhoneScale'=>true,
	'changedBridgeScale'=>true,
	'changedDynamicCreativeScale'=>true
	),
	
	
	
	/*11、--------------------------------------------------------------------------------------- */
	/*12、--------------------------------------------------------------------------------------- */
	/*13、--------------------------------------------------------------------------------------- */
	
	
);