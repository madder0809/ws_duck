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

require_once( "lib.inc.php" );
$sessionkey = returnsesskey( );
$GLOBAL_SESSION = returnsession( );
$SUNSHINE_USER_NAME = $_SESSION['SUNSHINE_USER_NAME'];
validateMenuPriv("����̵㵥");
//ֻ�д˲ֿ����Ա���ܴ����̵㵥
if($_GET['action']=="add_default_data")			
{
	$storeid=$_POST['storeid'];
	$userid = returntablefield( "stock", "rowid", $storeid, "user_id" );
	$useridArray=explode(",", $userid);
	if(!in_array($_SESSION['LOGIN_USER_ID'], $useridArray))
	{
		print "<script language=javascript>alert('����ֻ�д˲ֿ�Ŀ����Ȩ�޴����̵㵥');window.history.back(-1);</script>";
    	exit;
	}
	$_POST['billid']=returnAutoIncrementUnitBillid("storecheckbillid");
}
if($_GET['action']=="edit_default2")			
{
print "<script>location='DataQuery/productFrame.php?tablename=storecheck_detail&deelname=����̵����&rowid=".$_GET['billid']."'</script>";
exit;
}
//�����̵㵥
if($_GET['action']=="delete_array")			
{
	$selectid=$_GET['selectid'];
	$selectid=explode(",", $selectid);
	try 
	{
		//��������
		$Store=new Store($db);
	    $db->StartTrans();  
		for($i=0;$i<sizeof($selectid);$i++)
		{
			if($selectid[$i]!="")
				$Store->deleteStoreCheck($selectid[$i]);
		}
		//�Ƿ�������ִ���
		if ($db->HasFailedTrans()) 
		 	print "<script language=javascript>alert('����".str_replace("'",  "\'", $db->ErrorMsg())."');window.history.back(-1);</script>";
		else 
		{ 
			page_css("�̵㵥ɾ��");
			$return=FormPageAction("action","init_default");
			print_infor("�̵㵥�ѳɹ�ɾ��",'trip',"location='?$return'","?$return",0);
		}
    	$db->CompleteTrans();
		exit;	
	}
	catch(Exception $e)
	{
		print "<script language=javascript>alert('����".$e->getMessage()."');window.history.back(-1);</script>";
    	exit;
	}
	
}
addShortCutByDate("createtime","����ʱ��");
$SYSTEM_ADD_SQL=getKucunByUserid($SYSTEM_ADD_SQL,$_SESSION['LOGIN_USER_ID']);
$filetablename = "storecheck";
require_once( "include.inc.php" );
systemhelpContent("����̵㵥˵��",'100%');
?>
