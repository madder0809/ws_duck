<?php
	ini_set('display_errors', 1);
	ini_set('error_reporting', E_ALL);
	error_reporting(E_WARNING | E_ERROR);
	require_once('lib.inc.php');
	$GLOBAL_SESSION=returnsession();
	validateMenuPriv("Ó¦¸¶¿î»ã×Ü");
	if($_GET['action']=="" || $_GET['action']=="init_default")
	{
		
	}
		
	if($_GET['action']=="mingxi")
	{
		print "<script>location='v_supplyownmoney_mingxi.php?supplyid=".$_GET['rowid']."'</script>";
		exit;
	}	

	$filetablename		=	'v_supplyownmoney';
	$parse_filename		=	'v_supplyownmoney';
	require_once('include.inc.php');
	?>

	