<?php
	ini_set('display_errors', 1);
	ini_set('error_reporting', E_ALL);
	error_reporting(E_WARNING | E_ERROR);
	require_once('lib.inc.php');
	$GLOBAL_SESSION=returnsession();
	validateMenuPriv("Ӧ�տ����");
if($_GET['action']=="mingxi")
{
	print "<script>location='v_yingshoukuanhuizong_mingxi.php?supplyid=".$_GET['rowid']."'</script>";
	exit;
}


	//���ݱ�ģ���ļ�,��ӦModelĿ¼�����v_yingshoukuanhuizong_newai.ini�ļ�
	//�������Ҫ���ƴ�ģ��,����Ҫ�޸�$parse_filename������ֵ,Ȼ���Ӧ��ModelĿ¼ ���ļ���_newai.ini�ļ�
	$filetablename		=	'v_yingshoukuanhuizong';
	$parse_filename		=	'v_yingshoukuanhuizong';
	require_once('include.inc.php');
	?>