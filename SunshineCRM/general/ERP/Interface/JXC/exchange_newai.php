<?php
ini_set('display_errors', 1);
ini_set('error_reporting', E_ALL);
error_reporting(E_WARNING | E_ERROR);
require_once('lib.inc.php');
$GLOBAL_SESSION=returnsession();
$SYSTEM_PRIV_STOP = "1";
validateMenuPriv("���ֶһ�");

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
		print "<script language=javascript>alert('���ֲ��������ܶһ���');window.history.back(-1);</script>";
		exit;
	}

	$sql = "select num,price from store where prodid='".$_POST[prodid]."' and storeid='".$_POST[stockid]."'";
	$rs = $db->Execute($sql);
	$rs_a = $rs->GetArray();
	$num = $rs_a[0][num];

	$avgprice = $rs_a[0][price];
	if($num < $_POST[exchangenum]){
		print "<script language=javascript>alert('����Ʒ��治�㣡');window.history.back(-1);</script>";
		exit;
	}

	if(intval($_POST[exchangenum]) <= 0){
		print "<script language=javascript>alert('�һ������������0��');window.history.back(-1);</script>";
		exit;
	}
	if(intval($_POST[integral]) <= 0){
		print "<script language=javascript>alert('�һ����ֱ������0��');window.history.back(-1);</script>";
		exit;
	}

	// ��������
	$productinfo = explode('/',$_POST[prodid_ID]);
	$db->StartTrans();
	//$db->debug=1;
	$sql = "select max(rowid) as max from exchange";
	$rs = $db->Execute($sql);
	$rs_a = $rs->GetArray();
	$id = $rs_a[0]['max']+1;
	$sql = "insert into exchange(rowid,customid,prodid,integral,createtime,createman,prodname,xinghao,guige,exchangenum,stockid,beizhu) value($id,".$_POST[customid].",'".$_POST[prodid]."',".$_POST[integral].",'".date("Y-m-d H:i:s")."','".$_SESSION[LOGIN_USER_ID]."','".$productinfo[0]."','".$productinfo[1]."','".$productinfo[2]."',".intval($_POST[exchangenum]).",'".$_POST[stockid]."','".$_POST[beizhu]."')";
	$db->Execute($sql);
	
	// ���»���
	$sql = "update customer set integral=integral-".intval($_POST[integral])." where ROWID=".$_POST[customid];
	$db->Execute($sql);

	//		// ��ӷ���
	//		$feiyongbillid = returnAutoIncrementUnitBillid("feiyongbillid");
	//		$sql="insert into feiyongrecord (billid,typeid,jine,accountid,chanshengdate,createman,createtime,kind) values($feiyongbillid,32,$feiyong,1,'".date("Y-m-d")."','".$_SESSION[LOGIN_USER_ID]."','".date("Y-m-d H:i:s")."',-1)";
	//		$this->db->Execute($sql);

	// ��������
	// ���¿��
	$sql = "update store set num=num-".intval($_POST[exchangenum])." where prodid='".$_POST[prodid]."' and storeid='".$_POST[stockid]."'";
	$db->Execute($sql);

	// ��ӳ��ⵥ
	$chukubillid = returnAutoIncrement("billid","stockoutmain");
	//�����³��ⵥ
	$sql = "insert into stockoutmain (billid,zhuti,storeid,createman,createtime,dingdanbillid,state,totalnum,totalmoney,outdate,outtype,outstoreshenhe) values(".
	$chukubillid.",'���ֶһ�-".$chukubillid."',".$_POST[stockid].",'".$_SESSION[LOGIN_USER_ID]."','".date("Y-m-d H:i:s")."',"
	.$id.",'�ѳ���',".intval($_POST[exchangenum]).",0,'".date("Y-m-d H:i:s")."','���ֶһ�����','".$_SESSION[LOGIN_USER_ID]."')";
	$db->Execute($sql);

	//��ӳ�����ϸ
	$sql = "select max(id) as max from stockoutmain_detail";
	$rs = $db->Execute($sql);
	$rs_a = $rs->GetArray();
	$insertid = $rs_a[0][max]+1;
	
	$sql = "insert into stockoutmain_detail(id,prodid,prodname,prodxinghao,prodguige,proddanwei,price,zhekou,num,beizhu,mainrowid,jine,avgprice,lirun) value($insertid,'".$_POST[prodid]."','".$productinfo[0]."','".$productinfo[1]."','".$productinfo[2]."','',0,0,".intval($_POST[exchangenum]).",'',$chukubillid,0,$avgprice".",".-$avgprice*(intval($_POST[exchangenum])).")";
	$db->Execute($sql);
	
	if ($db->HasFailedTrans()){
		print "<script language=javascript>alert('����".str_replace("'",  "\'", $db->ErrorMsg())."');window.history.back(-1);</script>";
		exit;
	}
	$db->CompleteTrans();

	page_css("�һ��ɹ�");
	$return=FormPageAction("action","init_default");
	print_infor("�һ��ɹ���",'trip',"location='?$return'","?$return",1);
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
			// ɾ���һ���¼
			$sql = "DELETE a FROM exchange AS  a WHERE a.ROWID=".$exchang_row[0]['ROWID'];
			$db->Execute($sql);
			
			// ���»���
			$sql = "update customer set integral=integral+".intval($exchang_row[0][integral])." where ROWID=".$exchang_row[0][customid];
			$db->Execute($sql);

			// ���¿��
			$sql = "update store set num=num+".intval($exchang_row[0][exchangenum])." where prodid='".$exchang_row[0][prodid]."' and storeid=".$exchang_row[0][stockid]."";
			$db->Execute($sql);

			// ɾ�����ⵥ
			$sql = "select * from stockoutmain a where a.outtype='���ֶһ�����' and a.dingdanbillid=".$exchang_row[0][ROWID];
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
		print "<script language=javascript>alert('����".str_replace("'",  "\'", $db->ErrorMsg())."');window.history.back(-1);</script>";
		exit;
	}
	$db->CompleteTrans();

	page_css("ɾ���ɹ�");
	$return=FormPageAction("action","init_default");
	print_infor("ɾ���ɹ���",'trip',"location='?$return'","?$return",1);
	exit;
	
}

//���ݱ�ģ���ļ�,��ӦModelĿ¼�����exchange_newai.ini�ļ�
//�������Ҫ���ƴ�ģ��,����Ҫ�޸�$parse_filename������ֵ,Ȼ���Ӧ��ModelĿ¼ ���ļ���_newai.ini�ļ�
addShortCutByDate("createtime","�һ�ʱ��");
$SYSTEM_ADD_SQL =getCustomerRoleByCustID($SYSTEM_ADD_SQL,"customid");
$limitEditDelCust='customid';
$filetablename		=	'exchange';
$parse_filename		=	'exchange';
require_once('include.inc.php');
?>