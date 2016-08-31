<?php
	ini_set('display_errors', 1);
	ini_set('error_reporting', E_ALL);
	error_reporting(E_WARNING | E_ERROR);
	require_once('lib.inc.php');
	$GLOBAL_SESSION=returnsession();
	validateMenuPriv("库存调拨单");
	//生成出库单
	if($_GET['action']=="edit_default4")
	{
		$rowid=$_GET['billid'];
		
		//更新库存
		try {
			//开启事务
			$db->StartTrans();   
			$storeid = returntablefield("stockchangemain","billid",$rowid,"outstoreid");
			$sql = "select * from  stockchangemain_detail where mainrowid=".$rowid;
			$rs = $db->Execute($sql);
			$rs_detail = $rs->GetArray();
			for($i=0;$i<sizeof($rs_detail);$i++)
			{
				$ifkucun=returntablefield("product","productid",$rs_detail[$i]['prodid'],"ifkucun");
				if($ifkucun=="否")
					continue;
				$sql = "select * from store where storeid=".$storeid." and prodid='".$rs_detail[$i]['prodid']."'";
				$rs = $db->Execute($sql);
				$rs_store = $rs->GetArray();
				if(sizeof($rs_store)==0)
				{
					throw new Exception("编号为：".$rs_detail[$i]['prodid']." 的产品已无库存");
				}
				else 
				{
					$junjia=$rs_store[0]['price'];
					$sql = "update store set num=num-(".$rs_detail[$i]['num'].") where storeid=".$storeid." and prodid='".$rs_detail[$i]['prodid']."'";
					$db->Execute($sql);
					$sql = "update stockchangemain_detail set price=".$junjia." where id=".$rs_detail[$i]['id'];
					$db->Execute($sql);
				}
				//print $sql;exit;
				
			}
			$sql="delete from store where num=0";
			$db->Execute($sql);
			
			//更新入库单状态
			$sql = "update stockchangemain set state='3',outshenhetime='".date("Y-m-d H:i:s")."',outstoreshenhe='".$_SESSION['LOGIN_USER_ID']."' where billid=".$rowid;
			$db->Execute($sql);
					
			//是否事务出现错误
			if ($db->HasFailedTrans()) 
				print "<script language=javascript>alert('错误：".str_replace("'",  "\'", $db->ErrorMsg())."');window.history.back(-1);</script>";
			else 
			{ 
				page_css("出库已确认");
				$return=FormPageAction("action","init_default");
				print_infor("出库已确认",'trip',"location='?$return'","?$return",0);
			}
			$db->CompleteTrans();
			exit;	
		}
		catch (Exception $e) 
		{   
			print "<script language=javascript>alert('错误：".$e->getMessage()."');window.history.back(-1);</script>";
			exit;
		} 
	}
	//入库库管确认
	if($_GET['action']=="edit_default3")
	{
		$rowid=$_GET['billid'];
		//更新库存
		try {
			//开启事务
			$db->StartTrans();   
			$storeid = returntablefield("stockchangemain","billid",$rowid,"instoreid");
			$sql = "select * from  stockchangemain_detail where mainrowid=".$rowid;
			$rs = $db->Execute($sql);
			$rs_detail = $rs->GetArray();
			for($i=0;$i<sizeof($rs_detail);$i++)
			{
				$ifkucun=returntablefield("product","productid",$rs_detail[$i]['prodid'],"ifkucun");
				if($ifkucun=="否")
					continue;
				$sql = "select * from store where storeid=".$storeid." and prodid='".$rs_detail[$i]['prodid']."'";
				
				$rs = $db->Execute($sql);
				$rs_store = $rs->GetArray();
				if(sizeof($rs_store)==0)
				{
					$maxid=returnAutoIncrement("id", "store");
					$sql = "insert into store (id,prodid,storeid,num,price) values($maxid,'".
					$rs_detail[$i]['prodid']."',".$storeid.",".$rs_detail[$i]['num'].",".$rs_detail[$i]['price'].")";
					$db->Execute($sql);
				}
				else 
				{
					$junjia=round(($rs_store[0]['price']*$rs_store[0]['num']+$rs_detail[$i]['price']*$rs_detail[$i]['num'])/($rs_store[0]['num']+$rs_detail[$i]['num']),2);
					$sql = "update store set num=num+(".$rs_detail[$i]['num']."),price=".$junjia." where storeid=".$storeid." and prodid='".$rs_detail[$i]['prodid']."'";
					$db->Execute($sql);
				}
				//print $sql;exit;
				
			}
			$sql="delete from store where num=0";
			$db->Execute($sql);
			
			//更新入库单状态
			$sql = "update stockchangemain set state='4',inshenhetime='".date("Y-m-d H:i:s")."',instoreshenhe='".$_SESSION['LOGIN_USER_ID']."' where billid=".$rowid;
			$db->Execute($sql);
					
			//是否事务出现错误
			if ($db->HasFailedTrans()) 
				print "<script language=javascript>alert('错误：".str_replace("'",  "\'", $db->ErrorMsg())."');window.history.back(-1);</script>";
			else 
			{ 
				page_css("入库已确认");
				$return=FormPageAction("action","init_default");
				print_infor("入库已确认",'trip',"location='?$return'","?$return",0);
			}
			$db->CompleteTrans();
			exit;	
		}
		catch (Exception $e) 
		{   
			print "<script language=javascript>alert('错误：".$e->getMessage()."');window.history.back(-1);</script>";
			exit;
		} 
	}
	if($_GET['action']=="add_default_data" || $_GET['action']=="edit_default_data")
	{
		if($_POST['instoreid']==$_POST['outstoreid'])
		{
			print "<script language=javascript>alert('错误：调出仓库和调入仓库不能为同一仓库');window.history.back(-1);</script>";
    		exit;
		}
	}	
	if($_GET['action']=="edit_default2")			
	{
		$storeid=returntablefield("stockchangemain","billid",$_GET['billid'],"outstoreid");
		print "<script>location='DataQuery/productFrame.php?tablename=stockchangemain_detail&deelname=调拨单明细&rowid=".$_GET['billid']."&storeid=".$storeid."'</script>";
		exit;
	}
	//数据表模型文件,对应Model目录下面的stockchangemain_newai.ini文件
	//如果是需要复制此模块,则需要修改$parse_filename参数的值,然后对应到Model目录 新文件名_newai.ini文件
	addShortCutByDate("createtime","调拨单创建时间");
	$filetablename		=	'stockchangemain';
	$parse_filename		=	'stockchangemain';
	require_once('include.inc.php');
	systemhelpContent("库存调拨说明",'100%');
?>