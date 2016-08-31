<?php
	ini_set('display_errors', 1);
	ini_set('error_reporting', E_ALL);
	error_reporting(E_WARNING | E_ERROR);
	require_once('lib.inc.php');
	$GLOBAL_SESSION=returnsession();
	validateMenuPriv("回款计划");
	
	if($_GET['action']=="edit_huikuan")		{

		print "<script>location='huikuanplan_do.php?id=".$_GET['id']."&url=".$_SERVER["PHP_SELF"]."'</script>";
		exit;
		
	}
	

	//数据表模型文件,对应Model目录下面的huikuanplan_newai.ini文件
	//如果是需要复制此模块,则需要修改$parse_filename参数的值,然后对应到Model目录 新文件名_newai.ini文件
	addShortCutByDate("createtime","创建时间");
	$filetablename		=	'huikuanplan';
	$parse_filename		=	'huikuanplan';
	require_once('include.inc.php');
	?>