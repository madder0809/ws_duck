<?php
	ini_set('display_errors', 1);
	ini_set('error_reporting', E_ALL);
	error_reporting(E_WARNING | E_ERROR);
	require_once('lib.inc.php');
	$GLOBAL_SESSION=returnsession();
validateMenuPriv("��Ʒά��");

	function getIds($parentid)
	{
		global $db;
		global $ids;
		$sql = "select rowid from producttype where parentid='".$parentid."'";
		$rs = $db->Execute($sql);
		$rs_a = $rs->GetArray();
	
		if(sizeof($rs_a)==0)
			return;
		else 
		{
			for($i=0;$i<sizeof($rs_a);$i++)	
			{
				$ids=$ids.",".$rs_a[$i]['rowid'];
				getIds($rs_a[$i]['rowid']);
			}
		}
	}

	if($_GET['action']=="edit_default_data"||$_GET['action']=="add_default_data")		{
		if($_GET['productid']!=$_POST['productid'])
		{
			$productid = returntablefield("product","productid",$_POST['productid'],"productid");
			if($productid!='')
			{
				print "<script language='javascript'>alert('��Ʒ��� $productid �Ѵ���');window.history.back(-1);</script>";
				exit;
			}
			$mainrowid=returntablefield("buyplanmain_detail","prodid",$_GET['productid'],"mainrowid");
			if($mainrowid!="")
			{
				print "<script language='javascript'>alert('��Ʒ��� ".$_GET['productid']." �Ѵ����ڲɹ��� $mainrowid �У������޸ı��');window.history.back(-1);</script>";
				exit;
			}
		}
		if($_POST['oldproductid']!='' && $_POST['supplyid']!='')
		{
			$sql="select productid from product where oldproductid='".$_POST['oldproductid']."' and supplyid=".$_POST['supplyid']." and productid<>'".$_GET['productid']."'";
			$rs=$db->Execute($sql);
			$rs_a=$rs->GetArray();
			if(sizeof($rs_a)>0)
			{
				print "<script language='javascript'>alert('�˳����Ѵ���ԭ����Ϊ ".$_POST['oldproductid']." �Ĳ�Ʒ');window.history.back(-1);</script>";
				exit;
			}
		}
		$_POST['productcn'] = ����תƴ������ĸ($_POST['productname']);
	}
	else if($_GET['action']=="" && ($_GET['producttype']!=""))
	{
		$ids=$_GET['producttype'];
		getIds($_GET['producttype']);
		$SYSTEM_ADD_SQL = "and producttype in (".$ids.")";
		$_GET['producttype']="";
	}
	//�ж��Ƿ���ʹ��
if($_GET['action']=="delete_array")			
{
	$selectid=$_GET['selectid'];
	$selectid=explode(",", $selectid);
	for($i=0;$i<sizeof($selectid);$i++)
	{
		if($selectid[$i]!="")
		{
			
			$mainrowid=returntablefield("stockinmain_detail","prodid",$selectid[$i],"mainrowid");
			
			if($mainrowid!="")
			{
				print "<script language='javascript'>alert('��Ʒ��� $selectid[$i] �Ѵ�������ⵥ $mainrowid �У�����ɾ����ⵥ');window.history.back(-1);</script>";
				exit;
			}
		}
	}
}
if($_GET['action']=="getbarcode")			
{
	$maxid=1;
	$sql="select max(substr(productid,9,4)) as maxid from product where left(productid,8)='".$_GET['barcode']."'";
	$rs=$db->Execute($sql);
	$rs_a=$rs->GetArray();
	if(!empty($rs_a[0][maxid]))
		$maxid=$rs_a[0][maxid]+1;
	print $maxid;
	exit;
}
if($_GET['action']=="add_default" && $_GET['supplyid']!='')
{
	$sql="select * from product where supplyid='".$_GET['supplyid']."' order by productid desc limit 0,1";
	$rs=$db->Execute($sql);
	$rs_a=$rs->GetArray();
	if(sizeof($rs_a)==1)
	{
		$ADDINIT=array("measureid"=>$rs_a[0]['measureid'],"producttype"=>$rs_a[0]['producttype'],"storemin"=>$rs_a[0]['storemin'],"storemax"=>$rs_a[0]['storemax'],"ifkucun"=>$rs_a[0]['ifkucun']);

	}
}
$filetablename = "product";
require_once( "include.inc.php" );
?>
