<?php
/*
��Ȩ����:֣�ݵ���Ƽ�������޹�˾;
��ϵ��ʽ:0371-69663266;
��˾��ַ:����֣�ݾ��ü��������������־�����·ͨ�Ų�ҵ԰��¥����;
��˾���:֣�ݵ���Ƽ�������޹�˾λ���й��в�����-֣��,������2007��1��,�����ڰѻ����Ƚ���Ϣ����������ͨ�ż���������ѹ�����ҵ��ʵ���ռ���������ҵ�ͻ��Ĺ�����ҵ���»�У�ȫ���ṩ��������֪ʶ��Ȩ�Ľ�����������������������������в�������ĸ�У���������������СѧУ��������ṩ�̡�Ŀǰ�����ж�Ҹ�ְ����ְ��ԺУʹ��ͨ���в��з����Ŀ���������ͷ���;

�������:����Ƽ�������������Լܹ�ƽ̨,�Լ��������֮����չ���κ��������Ʒ;
����Э��:���ֻ�У԰��ƷΪ��ҵ���,�������ΪLICENSE��ʽ;����CRMϵͳ��SunshineCRMϵͳΪGPLV3Э�����,GPLV3Э����������뵽�ٶ�����;
��������:�����ʹ�õ�ADODB��,PHPEXCEL��,SMTARY���ԭ��������,���´���������������;
*/

ini_set('display_errors', 1);
	ini_set('error_reporting', E_ALL);
	error_reporting(E_WARNING | E_ERROR);
	require_once('lib.inc.php');
	$GLOBAL_SESSION=returnsession();
validateMenuPriv("�ɹ���");
if($_GET['action']=="edit_default2")			
{
	
	$billid=$_GET['billid'];
	$url="";
	$fangshi1="DataQuery/productFrame.php?tablename=buyplanmain_detail&deelname=�ɹ�����ϸ&rowid=$billid";
	$fangshi2="buyplanmain_mingxi.php?tablename=buyplanmain_mingxi&deelname=�ɹ�����ϸ&rowid=$billid";
	$fangshi3="buyplanmain_Excel.php?tablename=buyplanmain_mingxi&deelname=�ɹ�����ϸ&rowid=$billid";

	if($url=='')
	{
		page_css();
		print "
		<table id=listtable align=center class=TableBlock width=100% border=0>
<TR><TD  class=TableHeader height=30>&nbsp;ѡ��ɹ���¼�뷽ʽ��</TD></TR>
<tr><td class=TableLine2>
		<style>
body{text-align:center;}
img{display: block;border=2}
#L{position: absolute;
left: 150;
top: 150;
width:200;
}
#M{position: absolute;
left: 450;
top: 150;
width:200;
}
#R{position: absolute;
left: 750;
top: 150;
width:200;
}
.ac{ background:#bbbbbb; border:0px solid #dddddd; }
.bc{ background:#777777; border:0px solid #999; 
  padding:1px; margin:1px; }
.cc { background:#ffffff; border:0px solid #555; 
  padding:5px; }
.dc{ background: #CCCCCC; border: 1px solid #999999;}
A IMG {
 BORDER-RIGHT: #ffffff 2px solid; BORDER-TOP: #ffffff 2px solid; BORDER-LEFT: #ffffff 2px solid; BORDER-BOTTOM: #ffffff 2px solid
}
A:hover IMG {
 BORDER-RIGHT: #FF0000 2px solid; BORDER-TOP: #FF0000 2px solid; BORDER-LEFT: #FF0000 2px solid; BORDER-BOTTOM: #FF0000 2px solid
}
</style>
<div id='L'><div><b>1.</b>��Ʒ�����Ѵ���Ҫ¼�����Ʒ����ֱ��ɨ�������ѡ�����¼��ɹ���</div><div class='ac'>
<div class='bc'><div class='cc'>
<div class='dc'><a href=$fangshi1><img src='../../Framework/images/fangshi1.jpg'></a>
</div></div></div></div></div>
<div id='M'><div><b>2.</b>��ȷ����Ʒ�����Ƿ���ڣ���Ҫͨ��ԭ������ң��������µ���Ʒ����</div><div class='ac'>
<div class='bc'><div class='cc'>
<div class='dc'><a href=$fangshi2><img src='../../Framework/images/fangshi2.jpg'></a>
</div></div></div></div></div>
<div id='R'><div><b>3.</b>ͨ��Excel����ɹ���ϸ�����Զ������µ���Ʒ����</div><div class='ac'>
<div class='bc'><div class='cc'>
<div class='dc'><a href=$fangshi3><img src='../../Framework/images/fangshi3.jpg'></a>
</div></div></div></div></div>
</td></tr></table>
";
	}
	else 
		print "<script>location='$url';</script>";
	//print "<script>location='DataQuery/productFrame.php?tablename=buyplanmain_detail&deelname=�ɹ�����ϸ&rowid=".$_GET['billid']."'</script>";

	exit;
}
if($_GET['action']=="edit_default3")			
{
print "<script>location='buyplanmain_ruku.php?rowid=".$_GET['billid']."'</script>";
exit;
}

if($_GET['action']=="saveruku")			
{
	$rowid=$_GET['rowid'];
	$totalnum=$_POST['allamount1'];
	$totalmoney=$_POST['allmoney1'];
	$storeid=$_POST['stock'];
	try 
	{
		
		$Store=new Store($db);
	    //��������
	    $db->StartTrans();  
	    //$db->debug=1;
	    $Store->insertCaiGouRuKu($rowid,$totalnum,$totalmoney,$storeid);
		
	    page_css("������ⵥ");
	    $db->CompleteTrans();
		//�Ƿ�������ִ���
		if ($db->HasFailedTrans()) 
			throw new Exception($db->ErrorMsg());
		else 
		{ 
			
			$return=FormPageAction("action","init_default");
			print_infor("��ⵥ�����ɣ��ȴ����ȷ��",'trip',"location='?$return'","?$return",0);
			
		}

	}
	catch (Exception $e)
	{
		print "<script language=javascript>alert('����".$e->getMessage()."');window.history.back(-1);</script>";
	}   
	exit;	
	
}
//��ֹ�ɹ���
if($_GET['action']=="terminate")			
{
	page_css("");
	$rowid=$_GET['billid'];
	$confirm=$_GET['confirm'];
		
	if($confirm=='')
	{
		$buybillinfo=returntablefield("buyplanmain","billid",$rowid,"paymoney,rukumoney");
		$paymoney=$buybillinfo['paymoney'];
		$rukumoney=$buybillinfo['rukumoney'];
		$tip='';
		if($paymoney>$rukumoney)
			$tip="�Ѹ��������������ึ�Ľ���ת��Ԥ�����˻�";
		
		print "<script language=javascript>if(confirm('�Ƿ�ȷ����ֹ�ɹ�����\\r\\n".$tip."')){location='?billid=$rowid&action=terminate&rowid=$rowid&confirm=1'}else{window.history.back(-1)}</script>";
		exit;
	}
	try 
	{
		
		$Store=new Store($db);
	    //��������
	    $db->StartTrans();  
	    //$db->debug=1;
	    $Store->terminateCaiGou($rowid);
		
	    
	    $db->CompleteTrans();
		//�Ƿ�������ִ���
		if ($db->HasFailedTrans()) 
			throw new Exception($db->ErrorMsg());
		else 
		{ 
			$_GET['confirm']='';
			$return=FormPageAction("action","init_default");
			print_infor("�ɹ�������ֹ",'trip',"location='?$return'","?$return",0);
			
		}

	}
	catch (Exception $e)
	{
		print "<script language=javascript>alert('����".$e->getMessage()."');window.history.back(-1);</script>";
	}   
	exit;	
	
}
if($_GET['action']=="add_default")		
{
	$CUR_HOUR = date('Y-m-d',strtotime("+3 days"));
	$ADDINIT=array("daohuodate"=>$CUR_HOUR);
	
}
if($_GET['action']=="add_default_data")		
{
	$supplyid=$_POST['supplyid'];
	$linkman=$_POST['linkman'];
	$caigoudate=$_POST['caigoudate'];
	$daohuodate=$_POST['daohuodate'];
	$createman=$_SESSION['LOGIN_USER_ID'];
	$createtime=date('Y-m-d H:i:m');
	$totalmoney=floatval($_POST['totalmoney']);
	$oddment=floatval($_POST['oddment']);
	$realmoney=floatval($_POST['paymoney']);
	$accountid=$_POST['accountid'];
	
	try 
	{
		if($totalmoney>0 && $totalmoney<($oddment+$realmoney))
			throw new Exception("ȥ����Ѹ����ܴ����ܽ�� $totalmoney");
		if($totalmoney<0 && $totalmoney>($oddment+$realmoney))
			throw new Exception("ȥ����Ѹ�����С���ܽ�� $totalmoney");
	    //��������
	    $db->StartTrans();  
	    $billid = returnAutoIncrement("billid","buyplanmain");
	    //�����¼�¼
		$sql="insert into buyplanmain (billid,zhuti,supplyid,linkman,caigoudate,daohuodate,createman,createtime,totalmoney,
		oddment,paymoney,accountid,guanliandingdan,guanliankehu,beizhu,user_flag) values($billid,'".$_POST['zhuti']."','$supplyid',
		'$linkman','$caigoudate','$daohuodate','$createman','$createtime',$totalmoney,0,0,$accountid,
		'".$_POST['guanliandingdan']."','".$_POST['guanliankehu']."','".$_POST['beizhu']."',1)";
		$db->Execute($sql);
	    $CaiWu=new CaiWu($db);
	    if($realmoney!=0)
	    	$CaiWu->insertFukuanReocord($supplyid,$billid,$realmoney,$accountid,$createman,'����֧��',$oddment,'','','');
	   
		//�Ƿ�������ִ���
		page_css();
		$db->CompleteTrans();
		if ($db->HasFailedTrans()) 
			throw new Exception($db->ErrorMsg());
		else 
		{ 
			
			$return=FormPageAction("action","init_default");
			print_infor("�ɹ��������ɣ���¼����ϸ",'trip',"location='?$return'","?$return",0);
			
		}

	}
	catch (Exception $e)
	{
		print "<script language=javascript>alert('����".$e->getMessage()."');window.history.back(-1);</script>";
	}  
	exit;	 
}
//�����ɹ�����
if($_GET['action']=="delete_array")			
{
	$selectid=$_GET['selectid'];
	$selectid=explode(",", $selectid);
	try 
	{
		//��������
		$CaiWu=new CaiWu($db);
	    $db->StartTrans();  
		for($i=0;$i<sizeof($selectid);$i++)
		{
			if($selectid[$i]!="")
			{
					
				$billid=$selectid[$i];
				$sql="update buyplanmain set user_flag=-1 where billid=$billid and user_flag>-1";
				$rs=$db->Execute($sql);
				if ($rs === false)
					throw new Exception("�����ڴ˼�¼");	
				$CaiWu->deleteFukuanReocordByBillid($billid);
				$CaiWu->deleteshoupiaoByBillid($billid);
			}

		}
		$db->CompleteTrans();
		page_css("");
		//�Ƿ�������ִ���
		if ($db->HasFailedTrans()) 
			throw new Exception($db->ErrorMsg());
		else 
		{ 
			$return=FormPageAction("action","init_default");
			print_infor("�ɹ����ѳ���",'trip',"location='?$return'","?$return",0);
		}
	}
	catch(Exception $e)
	{
		print "<script language=javascript>alert('����".$e->getMessage()."');window.history.back(-1);</script>";
	}
	exit;
}
addShortCutByDate("caigoudate","�ɹ�����");
$filetablename = "buyplanmain";
require_once( "include.inc.php" );
systemhelpcontent( "�ɹ���������", "100%" );

?>
