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
	
		validateMenuPriv("�ͻ���ϵ��");
$customerid=$_GET['customerid'];
if($customerid!='' && $_GET['action']=='add_default')
{
	$ADDINIT=array("customerid"=>$customerid);
}
if($_GET['action']=='add_default_data' || $_GET['action']=='edit_default_data')
{
	
	$_POST['linkmanpy'] = ����תƴ������ĸ($_POST['linkmanname']);

}
if($_GET['action']=='operation_sendsms')
{
	validateMenuPriv("�ֻ�����");
	$selectid=$_GET['selectid'];
	print "<script>location='sms_sendlist_newai.php?action=add_default&sendlist=".$selectid."&fromsrc=customer';</script>";
	exit;
	
}
if($_GET['action']=='operation_sendemail')
{
	validateMenuPriv("�����ʼ�");
	$selectid=$_GET['selectid'];
	print "<script>location='../CRM/email_newai.php?action=add_default&sendlist=".$selectid."&fromsrc=customer';</script>";
	exit;
	
}
if($_GET['action']=='operation_printKuaiDi')
{
	
	$selectid=$_GET['selectid'];
	print "<script>
	location.href='../CRM/printKuaiDi.php?CustOrSupply=customer&linkmanlist=$selectid',null,'height=600,width=855,status=1,toolbar=no,menubar=no,location=no,scrollbars=yes,resizable=yes';
	</script>";
	exit;
	
}
$SYSTEM_ADD_SQL =getCustomerRoleByCustID($SYSTEM_ADD_SQL,"customerid");

$limitEditDelCust='customerid';
$filetablename = "linkman";
require_once( "include.inc.php" );
?>
