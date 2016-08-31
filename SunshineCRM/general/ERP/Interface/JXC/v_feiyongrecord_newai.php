<?php
	ini_set('display_errors', 1);
	ini_set('error_reporting', E_ALL);
	error_reporting(E_WARNING | E_ERROR);
	require_once('lib.inc.php');
	$GLOBAL_SESSION=returnsession();
	validateMenuPriv("费用支出单");
	if($_GET['action']=="add_default_data")		{
		
		global $db;
		//开启事务
	    $db->StartTrans(); 

	    $CaiWu=new CaiWu($db);
	    $CaiWu->insertFeiYongAccount($_POST['typeid'],$_POST['jine'],$_POST['accountid'],$_SESSION['LOGIN_USER_ID'],$_POST['kind'],$_POST['chanshengdate'],$_POST['beizhu']);
	    
	    //是否事务出现错误
		if ($db->HasFailedTrans()) 
		 	print "<script language=javascript>alert('错误：".str_replace("'",  "\'", $db->ErrorMsg())."');window.history.back(-1);</script>";
		else 
		{ 
			page_css("费用支出单");
			$return=FormPageAction("action","init_default");
			print_infor("费用支出单增加成功",'trip',"location='?$return'","?$return",0);
			
		}
    	$db->CompleteTrans();
		exit;	
		
	}
	if($_GET['action']=="delete_array")			
	{
		$selectid=$_GET['selectid'];
		$selectid=explode(",", $selectid);
		//开启事务
	    $db->StartTrans(); 
		for($i=0;$i<sizeof($selectid);$i++)
		{
			if($selectid[$i]!="")
			{
				global $db;
				$feiyonginfo=returntablefield("feiyongrecord","billid",$selectid[$i],"accountid,jine");
				$accountid=$feiyonginfo['accountid'];
				$jine=$feiyonginfo['jine'];
			    $sql="delete from feiyongrecord where billid=".$selectid[$i];
			    $db->Execute($sql);
			    $oldjine=returntablefield("bank","rowid",$accountid,"jine");
			    $sql="update bank set jine=jine+(".$jine.") where rowid=".$accountid;
			    $db->Execute($sql);
			    $sql="insert accessbank (bankid,oldjine,jine,opertype,guanlianbillid,createman,createtime) values (".$accountid.",$oldjine,".$jine.
			    ",'费用支出',$selectid[$i],'".$_SESSION['LOGIN_USER_ID']."','".date("Y-m-d H:i:s")."')";
			    $db->Execute($sql);
	    
			}
		}
		//是否事务出现错误
		if ($db->HasFailedTrans()) 
		 	print "<script language=javascript>alert('错误：".str_replace("'",  "\'", $db->ErrorMsg())."');window.history.back(-1);</script>";
		else 
		{ 
			page_css("费用支出单");
			$return=FormPageAction("action","init_default");
			print_infor("费用支出单删除成功",'trip',"location='?$return'","?$return",0);
			
		}
    	$db->CompleteTrans();
		exit;
	}

	//数据表模型文件,对应Model目录下面的v_feiyongrecord_newai.ini文件
	//如果是需要复制此模块,则需要修改$parse_filename参数的值,然后对应到Model目录 新文件名_newai.ini文件
	addShortCutByDate("createtime","创建时间");
	$filetablename		=	'v_feiyongrecord';
	$parse_filename		=	'v_feiyongrecord';
	require_once('include.inc.php');
	?>