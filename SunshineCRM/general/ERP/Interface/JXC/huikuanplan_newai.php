<?php
	ini_set('display_errors', 1);
	ini_set('error_reporting', E_ALL);
	error_reporting(E_WARNING | E_ERROR);
	require_once('lib.inc.php');
	$GLOBAL_SESSION=returnsession();
	validateMenuPriv("�ؿ�ƻ�");
	
	if($_GET['action']=="edit_huikuan")		{

		print "<script>location='huikuanplan_do.php?id=".$_GET['id']."&url=".$_SERVER["PHP_SELF"]."'</script>";
		exit;
		
	}
	

	//���ݱ�ģ���ļ�,��ӦModelĿ¼�����huikuanplan_newai.ini�ļ�
	//�������Ҫ���ƴ�ģ��,����Ҫ�޸�$parse_filename������ֵ,Ȼ���Ӧ��ModelĿ¼ ���ļ���_newai.ini�ļ�
	addShortCutByDate("createtime","����ʱ��");
	$filetablename		=	'huikuanplan';
	$parse_filename		=	'huikuanplan';
	require_once('include.inc.php');
	?>