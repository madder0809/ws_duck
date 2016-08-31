<?php
/*
 版权归属:郑州单点科技软件有限公司;
 联系方式:0371-69663266;
 公司地址:河南郑州经济技术开发区第五大街经北三路通信产业园四楼西南;
 公司简介:郑州单点科技软件有限公司位于中国中部城市-郑州,成立于2007年1月,致力于把基于先进信息技术（包括通信技术）的最佳管理与业务实践普及到教育行业客户的管理与业务创新活动中，全面提供具有自主知识产权的教育管理软件、服务与解决方案，是中部最优秀的高校教育管理软件及中小学校管理软件提供商。目前己经有多家高职和中职类院校使用通达中部研发中心开发的软件和服务;

 软件名称:单点科技软件开发基础性架构平台,以及在其基础之上扩展的任何性软件作品;
 发行协议:数字化校园产品为商业软件,发行许可为LICENSE方式;单点CRM系统即SunshineCRM系统为GPLV3协议许可,GPLV3协议许可内容请到百度搜索;
 特殊声明:软件所使用的ADODB库,PHPEXCEL库,SMTARY库归原作者所有,余下代码沿用上述声明;
 */
ini_set('display_errors', 1);
ini_set('error_reporting', E_ALL);
error_reporting(E_WARNING | E_ERROR);
require_once('lib.inc.php');
$GLOBAL_SESSION=returnsession();

if($_GET['action']=="add_default")
{
	$ADDINIT=array("fahuostate"=>-1,"kaipiaostate"=>-1);
	
}
if($_GET['action']=="add_default_data")
{
	$_POST['billtype']=3;
	$billid = returnAutoIncrement("billid","sellplanmain");
	$_POST['zhuti']='店面销售单-'.$billid;
	
}
if($_GET['action']=="add_default_data" || $_GET['action']=="edit_default_data")
{
	if($_POST['fahuostate']=='')
		$_POST['fahuostate']='-1';
	if($_POST['kaipiaostate']=='')
		$_POST['kaipiaostate']='-1';
}
if($_GET['action']=="edit_detail")
{
	print "<script>location='DataQuery/productFrame.php?tablename=v_sellonedetail&deelname=店面销售单明细&rowid=".$_GET['billid']."'</script>";
	exit;
}
if($_GET['action']=="edit_finish")
{
	$sql="select sum(price*zhekou*num) as jine from sellplanmain_detail where mainrowid=".$_GET['billid'];
	$rs=$db->Execute($sql);
	$rs_a=$rs->GetArray();
	$allmoney=round($rs_a[0]['jine'],2);
	$totalmoney=returntablefield("sellplanmain", "billid", $_GET['billid'], "totalmoney");
	if($allmoney!=$totalmoney)
	{
		print "<script language=javascript>alert('错误：单据金额与明细合计不一致，请重新保存明细');window.history.back(-1);</script>";
		exit;
	}
	print "<script>location='sellonemain_zhixing.php?billid=".$_GET['billid']."'</script>";
	exit;
}
if($_GET['action']=="finish")
{
	$billid=$_GET['billid'];
	$billinfo=returntablefield("sellplanmain", "billid", $billid, "user_flag,zhuti,fahuostate,kaipiaostate,storeid,supplyid,linkman,address,mobile,fapiaoneirong,fapiaotype,fapiaono");
	$user_flag=$billinfo['user_flag'];
	$zhuti=$billinfo['zhuti'];
	$storeid=$billinfo['storeid'];
	$customerid=$billinfo['supplyid'];
	$shouhuoren=returntablefield("linkman","rowid",$billinfo['linkman'],"linkmanname");;
	$address=$billinfo['address'];
	$mobile=$billinfo['mobile'];
	$fapiaoneirong=$billinfo['fapiaoneirong'];
	$fapiaotype=$billinfo['fapiaotype'];
	$fapiaono=$billinfo['fapiaono'];

	try {

		if($user_flag>0)
			throw  new Exception("此单已执行过，不能重复执行");

		//开启事务
		global $db;
		//$db->debug=1;
		$db->StartTrans();

		$CaiWu =new CaiWu($db);
		$Store =new Store($db);

		//出库
		$chukubillid=$Store->insertSellOneChuKu($billid,$zhuti,$storeid);

		//付款
		$accountid=$_POST['accountid'];
		$oddment=floatval($_POST['quling']);
		$shoukuan=floatval($_POST['shoukuan']);
		$opertype='';
		if($_POST['ifpay']==1)
		{
			//付全款
			$opertype='货款收取';
		}
		else
		{
			//付押金
			$opertype='收押金';
		}

		//插入新回款记录
		if($shoukuan!=0 || $oddment!=0)
		{
			$CaiWu->insertShoukuanReocord($customerid,$billid,$shoukuan,$accountid,$_SESSION['LOGIN_USER_ID'],$opertype,$oddment);
		}

		//发货
		if($billinfo['fahuostate']==0 && $chukubillid>0)
		{
			$Store->insertFaHuo($chukubillid,$customerid,$billid,$shouhuoren,$mobile,$address);
		}
		//开票
		if($billinfo['kaipiaostate']==0 && $shoukuan+$oddment!=0)
		{
			$CaiWu->insertKaiPiao($customerid,$billid,$fapiaoneirong,$fapiaotype,$fapiaono,$shoukuan+$oddment,$_SESSION['LOGIN_USER_ID']);
		}

		$db->CompleteTrans();
		page_css("店面销售单");
		//是否事务出现错误
		if ($db->HasFailedTrans())
			throw  new Exception($db->ErrorMsg());
		else
		{
			
			$return=FormPageAction("action","init_default");
			print_infor("店面销售单执行完成",'trip',"location='?$return'","?$return",0);

		}		
	}
	catch (Exception $e)
	{
		print "<script language=javascript>alert('错误：".$e->getMessage()."');window.history.back(-1);</script>";
	}
	exit;
}
//撤销店面销售单
if($_GET['action']=="delete_array")			
{
	$selectid=$_GET['selectid'];
	$selectid=explode(",", $selectid);
	try 
	{
		//开启事务
		$CaiWu=new CaiWu($db);
	    $db->StartTrans();  
		for($i=0;$i<sizeof($selectid);$i++)
		{
			if($selectid[$i]!="")
			{
					
				$billid=$selectid[$i];
				$sql="update sellplanmain set user_flag=-1 where billid=$billid and user_flag>-1";
				$rs=$db->Execute($sql);
				if ($rs === false)
					throw new Exception("不存在此记录");	

			}

		}
		$db->CompleteTrans();
		//是否事务出现错误
		page_css("");
		if ($db->HasFailedTrans()) 
			throw new Exception($db->ErrorMsg());
		else 
		{ 
			$return=FormPageAction("action","init_default");
			print_infor("店面销售单已撤销",'trip',"location='?$return'","?$return",0);
		}
    	
	}
	catch(Exception $e)
	{
		print "<script language=javascript>alert('错误：".$e->getMessage()."');window.history.back(-1);</script>";
	}
	exit;	
}
if($_GET['action']=="printXiaoPiao")
{
	//去除打印设置参数
	@$ini_file = @parse_ini_file( "../Framework/sellone_print_config.ini",true);
	$sell_order_field_config = $ini_file['sell_order_print_field'];
	$sell_order_detail_field_config = $ini_file['sell_order_detail_print_field'];
	$fujia = $ini_file['fujia']['con'];
	$fujia = str_replace("^^","<br>",$fujia);
	asort($sell_order_field_config);
	asort($sell_order_detail_field_config);
	@$ini_file = @parse_ini_file( "../Framework/global_config.ini",true);
	$print_paper_width = $ini_file['paper_size']['width'];
	$print_paper_height = $ini_file['paper_size']['height'];
	
	$page_foot_fields=array();//页脚显示字段
	$page_head_fields=array();//页头显示字段
	foreach ($sell_order_field_config as $key=>$val)
	{
		if($val<0)
			array_push($page_foot_fields, $key);
		else 
			array_push($page_head_fields, $key);
	}
	$page_foot_fields=array_reverse($page_foot_fields);
	$mainfieldsarray=array_merge($page_head_fields,$page_foot_fields);
	$mainfields=implode(",", $mainfieldsarray);
	$page_foot_fields=array_flip($page_foot_fields);
	$page_head_fields=array_flip($page_head_fields);
	

	// 获取销售单打印字段中文名
	$sql = "select fieldname,chinese from systemlang where tablename='v_sellone'";
	$rs=$db->Execute($sql);
	$rs_a = $rs->GetArray();
	foreach ($rs_a as $row){
		if(isset($page_foot_fields[$row['fieldname']])){
			$page_foot_fields[$row['fieldname']]=array();
			$page_foot_fields[$row['fieldname']]['name'] = $row['chinese'];
			
		}
		if(isset($page_head_fields[$row['fieldname']])){
			$page_head_fields[$row['fieldname']]=array();
			$page_head_fields[$row['fieldname']]['name'] = $row['chinese'];
			
		}
	}
	//取得值
	$sql = "select $mainfields from v_sellone where billid='".$_GET['billid']."'";
	$rs=$db->Execute($sql);
	$rs_a = $rs->GetArray();
	
	foreach ($page_foot_fields as $key=>$val){
		if(isset($rs_a[0][$key])){
			$page_foot_fields[$key]['value']=$rs_a[0][$key];
		}
	}
	foreach ($page_head_fields as $key=>$val){
		if(isset($rs_a[0][$key])){
			$page_head_fields[$key]['value']=$rs_a[0][$key];
		}
	}
	//取得过滤器
	$sellone_columns=returntablecolumn('v_sellone');
	@$sellplanmain_ini_file = @parse_ini_file('../JXC/Model/sellone_newai.ini',true);
	$showlistfieldlist = explode(',', $sellplanmain_ini_file['view_default']['showlistfieldlist']);
	$showlistfieldfilter = explode(',', $sellplanmain_ini_file['view_default']['showlistfieldfilter']);
	foreach ($showlistfieldlist as $key=>$val){
		if(isset($sellone_columns[$val]) && isset($page_head_fields[$sellone_columns[$val]])){
			$page_head_fields[$sellone_columns[$val]]['filter'] =  $showlistfieldfilter[$key];
		}
		if(isset($sellone_columns[$val]) && isset($page_foot_fields[$sellone_columns[$val]])){
			$page_foot_fields[$sellone_columns[$val]]['filter'] =  $showlistfieldfilter[$key];
		}
	}
	//过滤后的值
	foreach ($page_foot_fields as $key=>$val){
		$filterArray=explode(":",$val['filter']);
		if($filterArray[0]=="tablefilter" || $filterArray[0]=="tablefiltercolor")
		{
			$relationTable_columns=returntablecolumn($filterArray[1]);
			$page_foot_fields[$key]['value']=returntablefield($filterArray[1], $relationTable_columns[$filterArray[2]], $page_foot_fields[$key]['value'], $relationTable_columns[$filterArray[3]]);
		}
	}
	foreach ($page_head_fields as $key=>$val){
	$filterArray=explode(":",$val['filter']);
		if($filterArray[0]=="tablefilter" || $filterArray[0]=="tablefiltercolor")
		{
			$relationTable_columns=returntablecolumn($filterArray[1]);
			$page_head_fields[$key]['value']=returntablefield($filterArray[1], $relationTable_columns[$filterArray[2]], $page_head_fields[$key]['value'], $relationTable_columns[$filterArray[3]]);
		}
	}

	//输出表头
	echo "<div id='head'><table border=0 style='width:".($print_paper_width-9)."mm;margin:0;padding:0;font-size:12px;'>
	<tr><td colspan=2 align=center><H2>".$page_head_fields['billid']['value']."</H2></td></tr>";
	foreach ($page_head_fields as $key=>$val)
	{
		if($key=="billid") continue;
		echo "<tr><td nowrap valign=top width=20%>".$val['name'].":</td><td valign=top>".$val['value']."</td></tr>";

	}
	echo "</table></div>";
	
	//单据明细表头
	$page_main_fields=array();
	foreach ($sell_order_detail_field_config as $key=>$val)
	{
		array_push($page_main_fields, $key);
	}
	
	//单据明细数据
	$sql = "SELECT * FROM sellplanmain_detail a  WHERE a.mainrowid=".$_GET['billid'];
	$rs=$db->Execute($sql);
	$detail = $rs->GetArray();
	
	$sell_data=array();//销售
	$back_data=array();//退货
	$gift_data=array();//赠品
	foreach ($detail as $key=>$val)
	{
		if($val['jine']==0)
			array_push($gift_data, $val);
		else 
		{
			if($val['num']<0)
				array_push($back_data, $val);
			else 
				array_push($sell_data, $val);
		}
	}
	// 获取销售单明细打印字段中文名
	
	$page_main_fields=array_flip($page_main_fields);
	
	$sql = "select fieldname,chinese from systemlang where tablename='sellplanmain_detail'";
	$rs=$db->Execute($sql);
	$rs_a = $rs->GetArray();
	foreach ($rs_a as $row){
		if(isset($page_main_fields[$row['fieldname']])){
			$page_main_fields[$row['fieldname']] = $row['chinese'];
		}
	}
	
	$cols=sizeof($page_main_fields);
	//输出内容
	echo "<div id='maindata'><table border=0 style='width:".($print_paper_width-9)."mm;margin:0;padding:0;font-size:12px;'>
	<tr><td colspan=$cols align=center><hr color='#000000' width=100%></td></tr><tr>";
	foreach ($page_main_fields as $key=>$val)
	{
		echo "<td><b>".$val."</b></td>";

	}
	echo "</tr>";
	$sell_num=0;
	$sell_jine=0;
	foreach ($sell_data as $key=>$val)
	{
		echo "<tr>";
		foreach ($page_main_fields as $itemkey=>$itemval)
		{
			echo "<td>".$val[$itemkey]."</td>";
		}
		echo "</tr>";
		$sell_num=$sell_num+$val['num'];
		$sell_jine=$sell_jine+$val['jine'];
	}
	echo "<tr><td colspan=$cols><b>小计</b>：数量：$sell_num 金额：<b>".number_format($sell_jine,2)."</b><br>&nbsp;</td></tr>";
	if(sizeof($back_data)>0)
	{
		echo "<tr><td colspan=$cols><b>退货：</b></td></tr>";
		$sell_num=0;
		$sell_jine=0;
		foreach ($back_data as $key=>$val)
		{
			echo "<tr>";
			foreach ($page_main_fields as $itemkey=>$itemval)
			{
				echo "<td>".$val[$itemkey]."</td>";
			}
			echo "</tr>";
			$sell_num=$sell_num+$val['num'];
			$sell_jine=$sell_jine+$val['jine'];
		}
		echo "</tr>";
		echo "<tr><td colspan=$cols><b>小计</b>：数量：$sell_num 金额：<b>".number_format($sell_jine,2)."</b><br>&nbsp;</td></tr>";
	}
	if(sizeof($gift_data)>0)
	{
		echo "<tr><td colspan=$cols><b>赠品：</b></td></tr>";
		$sell_num=0;
		
		foreach ($gift_data as $key=>$val)
		{
			echo "<tr>";
			foreach ($page_main_fields as $itemkey=>$itemval)
			{
				echo "<td>".$val[$itemkey]."</td>";
			}
			echo "</tr>";
			$sell_num=$sell_num+$val['num'];
			
		}
		echo "</tr>";
		echo "<tr><td colspan=$cols><b>小计</b>：数量：$sell_num <br></td></tr>";
	}
	echo "</table></div>";
	//输出表尾
	echo "<div id='foot'><table border=0 style='width:".($print_paper_width-9)."mm;margin:0;padding:0;font-size:12px;'>
	<tr><td colspan=$cols align=center><hr color='#000000' width=100%></td></tr><tr>";
	foreach ($page_foot_fields as $key=>$val)
	{
		echo "<tr><td nowrap valign=top width=20%>".$val['name'].":</td><td><b>".$val['value']."</b></td></tr>";

	}
	echo "<tr><td colspan=$cols>$fujia</td></tr>";
	echo "</table></div>";
	
	?>
	<script language="javascript" src="../LODOP60/LodopFuncs.js"></script>
<object id="LODOP" classid="clsid:2105C259-1E0C-4534-8141-A753534CB4CA" width=0 height=0> 
		<embed id="LODOP_EM" type="application/x-print-lodop" width=0 height=0></embed> 
	</object>
<script type="text/javascript">
var LODOP; //声明为全局变量 
	LODOP=getLodop(document.getElementById('LODOP'),document.getElementById('LODOP_EM'));  
	//这里3表示纵向打印且纸高“按内容的高度”；纸宽80mm；45表示页底空白4.5mm
	
	LODOP.PRINT_INIT("<?php echo $page_head_fields['billid']['value']?>");
	LODOP.SET_PRINT_PAGESIZE(3,<?php echo $print_paper_width*10?>,75,"");
	LODOP.ADD_PRINT_HTM('0%','0%','100%','100%',"<body leftmargin=0>"+document.documentElement.innerHTML+"</body>");
	LODOP.SET_PRINT_STYLEA(0,"Horient",3);
	LODOP.SET_SHOW_MODE("HIDE_PAPER_BOARD",1);
	LODOP.SET_PREVIEW_WINDOW(1,1,1,800,600,"<?php echo $page_head_fields['billid']['value']?>.开始打印");//打印前弹出选择打印机的对话框	
	LODOP.SET_PRINT_MODE("AUTO_CLOSE_PREWINDOW",1);//打印后自动关闭预览窗口
	LODOP.PREVIEW();
</script>
<?php exit;
}
$realtablename="sellplanmain";
addShortCutByDate("createtime","制单时间");
$SYSTEM_ADD_SQL =getCustomerRoleByCustID($SYSTEM_ADD_SQL,"supplyid");
$limitEditDelCust='supplyid';
$filetablename = "v_sellone";
$parse_filename	="sellone";
require_once( "include.inc.php" );
systemhelpcontent( "店面销售单", "100%" );
print "<iframe name='hideframe' width=0 height=0 border=0 src=''/>";
?>