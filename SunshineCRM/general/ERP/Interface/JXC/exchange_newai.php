<?php
ini_set('display_errors', 1);
ini_set('error_reporting', E_ALL);
error_reporting(E_WARNING | E_ERROR);
require_once('lib.inc.php');
$GLOBAL_SESSION=returnsession();
$SYSTEM_PRIV_STOP = "1";
validateMenuPriv("积分兑换");

$customerid=$_GET['customerid'];
if($customerid!='' && $_GET['action']=='add_default')
{
	$ADDINIT=array("customid"=>$customerid);
}

if($_GET['action']=="add_default_data"){
	global $db;
	$sql = "select integral from customer where ROWID='".$_POST[customid]."'";
	$rs = $db->Execute($sql);
	$rs_a = $rs->GetArray();
	$integral = $rs_a[0][integral];
	if($integral<$_POST['integral']){
		print "<script language=javascript>alert('积分不够，不能兑换！');window.history.back(-1);</script>";
		exit;
	}

	$sql = "select num,price from store where prodid='".$_POST[prodid]."' and storeid='".$_POST[stockid]."'";
	$rs = $db->Execute($sql);
	$rs_a = $rs->GetArray();
	$num = $rs_a[0][num];

	$avgprice = $rs_a[0][price];
	if($num < $_POST[exchangenum]){
		print "<script language=javascript>alert('该商品库存不足！');window.history.back(-1);</script>";
		exit;
	}

	if(intval($_POST[exchangenum]) <= 0){
		print "<script language=javascript>alert('兑换数量必须大于0！');window.history.back(-1);</script>";
		exit;
	}
	if(intval($_POST[integral]) <= 0){
		print "<script language=javascript>alert('兑换积分必须大于0！');window.history.back(-1);</script>";
		exit;
	}

	// 插入数据
	$productinfo = explode('/',$_POST[prodid_ID]);
	$db->StartTrans();
	//$db->debug=1;
	$sql = "select max(rowid) as max from exchange";
	$rs = $db->Execute($sql);
	$rs_a = $rs->GetArray();
	$id = $rs_a[0]['max']+1;
	$sql = "insert into exchange(rowid,customid,prodid,integral,createtime,createman,prodname,xinghao,guige,exchangenum,stockid,beizhu) value($id,".$_POST[customid].",'".$_POST[prodid]."',".$_POST[integral].",'".date("Y-m-d H:i:s")."','".$_SESSION[LOGIN_USER_ID]."','".$productinfo[0]."','".$productinfo[1]."','".$productinfo[2]."',".intval($_POST[exchangenum]).",'".$_POST[stockid]."','".$_POST[beizhu]."')";
	$db->Execute($sql);
	
	// 更新积分
	$sql = "update customer set integral=integral-".intval($_POST[integral])." where ROWID=".$_POST[customid];
	$db->Execute($sql);

	//		// 添加费用
	//		$feiyongbillid = returnAutoIncrementUnitBillid("feiyongbillid");
	//		$sql="insert into feiyongrecord (billid,typeid,jine,accountid,chanshengdate,createman,createtime,kind) values($feiyongbillid,32,$feiyong,1,'".date("Y-m-d")."','".$_SESSION[LOGIN_USER_ID]."','".date("Y-m-d H:i:s")."',-1)";
	//		$this->db->Execute($sql);

	// 创建出库
	// 更新库存
	$sql = "update store set num=num-".intval($_POST[exchangenum])." where prodid='".$_POST[prodid]."' and storeid='".$_POST[stockid]."'";
	$db->Execute($sql);

	// 添加出库单
	$chukubillid = returnAutoIncrement("billid","stockoutmain");
	//插入新出库单
	$sql = "insert into stockoutmain (billid,zhuti,storeid,createman,createtime,dingdanbillid,state,totalnum,totalmoney,outdate,outtype,outstoreshenhe) values(".
	$chukubillid.",'积分兑换-".$chukubillid."',".$_POST[stockid].",'".$_SESSION[LOGIN_USER_ID]."','".date("Y-m-d H:i:s")."',"
	.$id.",'已出库',".intval($_POST[exchangenum]).",0,'".date("Y-m-d H:i:s")."','积分兑换出库','".$_SESSION[LOGIN_USER_ID]."')";
	$db->Execute($sql);

	//添加出库明细
	$sql = "select max(id) as max from stockoutmain_detail";
	$rs = $db->Execute($sql);
	$rs_a = $rs->GetArray();
	$insertid = $rs_a[0][max]+1;
	
	$sql = "insert into stockoutmain_detail(id,prodid,prodname,prodxinghao,prodguige,proddanwei,price,zhekou,num,beizhu,mainrowid,jine,avgprice,lirun) value($insertid,'".$_POST[prodid]."','".$productinfo[0]."','".$productinfo[1]."','".$productinfo[2]."','',0,0,".intval($_POST[exchangenum]).",'',$chukubillid,0,$avgprice".",".-$avgprice*(intval($_POST[exchangenum])).")";
	$db->Execute($sql);
	
	if ($db->HasFailedTrans()){
		print "<script language=javascript>alert('错误：".str_replace("'",  "\'", $db->ErrorMsg())."');window.history.back(-1);</script>";
		exit;
	}
	$db->CompleteTrans();

	page_css("兑换成功");
	$return=FormPageAction("action","init_default");
	print_infor("兑换成功！",'trip',"location='?$return'","?$return",1);
	exit;
}

if($_GET['action']=="delete_array"){
	global $db;

	$db->StartTrans();
	//$db->debug=1;

	$delete_array = array();
	$delete_array = explode(',', trim($_GET[selectid]));
	foreach ($delete_array as $id){

		$sql = "select * from exchange a where a.ROWID=".intval($id);
		$rs = $db->Execute($sql);
		$exchang_row = $rs->GetArray();
		if(!empty($exchang_row)){
			// 删除兑换记录
			$sql = "DELETE a FROM exchange AS  a WHERE a.ROWID=".$exchang_row[0]['ROWID'];
			$db->Execute($sql);
			
			// 更新积分
			$sql = "update customer set integral=integral+".intval($exchang_row[0][integral])." where ROWID=".$exchang_row[0][customid];
			$db->Execute($sql);

			// 更新库存
			$sql = "update store set num=num+".intval($exchang_row[0][exchangenum])." where prodid='".$exchang_row[0][prodid]."' and storeid=".$exchang_row[0][stockid]."";
			$db->Execute($sql);

			// 删除出库单
			$sql = "select * from stockoutmain a where a.outtype='积分兑换出库' and a.dingdanbillid=".$exchang_row[0][ROWID];
			$rs = $db->Execute($sql);
			$stockout_row = $rs->GetArray();
			if(sizeof($stockout_row)>0)
			{
				$sql = "DELETE a FROM stockoutmain_detail a WHERE a.mainrowid=".$stockout_row[0]['billid'];
				$db->Execute($sql);
				$sql = "DELETE a FROM stockoutmain a WHERE a.billid=".$stockout_row[0]['billid'];
				$db->Execute($sql);
			}
		}
	}
	if ($db->HasFailedTrans()){
		print "<script language=javascript>alert('错误：".str_replace("'",  "\'", $db->ErrorMsg())."');window.history.back(-1);</script>";
		exit;
	}
	$db->CompleteTrans();

	page_css("删除成功");
	$return=FormPageAction("action","init_default");
	print_infor("删除成功！",'trip',"location='?$return'","?$return",1);
	exit;
	
}

//数据表模型文件,对应Model目录下面的exchange_newai.ini文件
//如果是需要复制此模块,则需要修改$parse_filename参数的值,然后对应到Model目录 新文件名_newai.ini文件
addShortCutByDate("createtime","兑换时间");
$SYSTEM_ADD_SQL =getCustomerRoleByCustID($SYSTEM_ADD_SQL,"customid");
$limitEditDelCust='customid';
$filetablename		=	'exchange';
$parse_filename		=	'exchange';
require_once('include.inc.php');
?>