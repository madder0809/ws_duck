<?php
	ini_set('display_errors', 1);
	ini_set('error_reporting', E_ALL);
	error_reporting(E_WARNING | E_ERROR);
	require_once('lib.inc.php');
	$GLOBAL_SESSION=returnsession();
	validateMenuPriv("应收款汇总");
if($_GET['action']=="mingxi")
{
	print "<script>location='v_yingshoukuanhuizong_mingxi.php?supplyid=".$_GET['rowid']."'</script>";
	exit;
}


	//数据表模型文件,对应Model目录下面的v_yingshoukuanhuizong_newai.ini文件
	//如果是需要复制此模块,则需要修改$parse_filename参数的值,然后对应到Model目录 新文件名_newai.ini文件
	$filetablename		=	'v_yingshoukuanhuizong';
	$parse_filename		=	'v_yingshoukuanhuizong';
	require_once('include.inc.php');
	?>