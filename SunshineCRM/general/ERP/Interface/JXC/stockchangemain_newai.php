<?php
	ini_set('display_errors', 1);
	ini_set('error_reporting', E_ALL);
	error_reporting(E_WARNING | E_ERROR);
	require_once('lib.inc.php');
	$GLOBAL_SESSION=returnsession();
	validateMenuPriv("��������");
	//���ɳ��ⵥ
	if($_GET['action']=="edit_default4")
	{
		$rowid=$_GET['billid'];
		
		//���¿��
		try {
			//��������
			$db->StartTrans();   
			$storeid = returntablefield("stockchangemain","billid",$rowid,"outstoreid");
			$sql = "select * from  stockchangemain_detail where mainrowid=".$rowid;
			$rs = $db->Execute($sql);
			$rs_detail = $rs->GetArray();
			for($i=0;$i<sizeof($rs_detail);$i++)
			{
				$ifkucun=returntablefield("product","productid",$rs_detail[$i]['prodid'],"ifkucun");
				if($ifkucun=="��")
					continue;
				$sql = "select * from store where storeid=".$storeid." and prodid='".$rs_detail[$i]['prodid']."'";
				$rs = $db->Execute($sql);
				$rs_store = $rs->GetArray();
				if(sizeof($rs_store)==0)
				{
					throw new Exception("���Ϊ��".$rs_detail[$i]['prodid']." �Ĳ�Ʒ���޿��");
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
			
			//������ⵥ״̬
			$sql = "update stockchangemain set state='3',outshenhetime='".date("Y-m-d H:i:s")."',outstoreshenhe='".$_SESSION['LOGIN_USER_ID']."' where billid=".$rowid;
			$db->Execute($sql);
					
			//�Ƿ�������ִ���
			if ($db->HasFailedTrans()) 
				print "<script language=javascript>alert('����".str_replace("'",  "\'", $db->ErrorMsg())."');window.history.back(-1);</script>";
			else 
			{ 
				page_css("������ȷ��");
				$return=FormPageAction("action","init_default");
				print_infor("������ȷ��",'trip',"location='?$return'","?$return",0);
			}
			$db->CompleteTrans();
			exit;	
		}
		catch (Exception $e) 
		{   
			print "<script language=javascript>alert('����".$e->getMessage()."');window.history.back(-1);</script>";
			exit;
		} 
	}
	//�����ȷ��
	if($_GET['action']=="edit_default3")
	{
		$rowid=$_GET['billid'];
		//���¿��
		try {
			//��������
			$db->StartTrans();   
			$storeid = returntablefield("stockchangemain","billid",$rowid,"instoreid");
			$sql = "select * from  stockchangemain_detail where mainrowid=".$rowid;
			$rs = $db->Execute($sql);
			$rs_detail = $rs->GetArray();
			for($i=0;$i<sizeof($rs_detail);$i++)
			{
				$ifkucun=returntablefield("product","productid",$rs_detail[$i]['prodid'],"ifkucun");
				if($ifkucun=="��")
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
			
			//������ⵥ״̬
			$sql = "update stockchangemain set state='4',inshenhetime='".date("Y-m-d H:i:s")."',instoreshenhe='".$_SESSION['LOGIN_USER_ID']."' where billid=".$rowid;
			$db->Execute($sql);
					
			//�Ƿ�������ִ���
			if ($db->HasFailedTrans()) 
				print "<script language=javascript>alert('����".str_replace("'",  "\'", $db->ErrorMsg())."');window.history.back(-1);</script>";
			else 
			{ 
				page_css("�����ȷ��");
				$return=FormPageAction("action","init_default");
				print_infor("�����ȷ��",'trip',"location='?$return'","?$return",0);
			}
			$db->CompleteTrans();
			exit;	
		}
		catch (Exception $e) 
		{   
			print "<script language=javascript>alert('����".$e->getMessage()."');window.history.back(-1);</script>";
			exit;
		} 
	}
	if($_GET['action']=="add_default_data" || $_GET['action']=="edit_default_data")
	{
		if($_POST['instoreid']==$_POST['outstoreid'])
		{
			print "<script language=javascript>alert('���󣺵����ֿ�͵���ֿⲻ��Ϊͬһ�ֿ�');window.history.back(-1);</script>";
    		exit;
		}
	}	
	if($_GET['action']=="edit_default2")			
	{
		$storeid=returntablefield("stockchangemain","billid",$_GET['billid'],"outstoreid");
		print "<script>location='DataQuery/productFrame.php?tablename=stockchangemain_detail&deelname=��������ϸ&rowid=".$_GET['billid']."&storeid=".$storeid."'</script>";
		exit;
	}
	//���ݱ�ģ���ļ�,��ӦModelĿ¼�����stockchangemain_newai.ini�ļ�
	//�������Ҫ���ƴ�ģ��,����Ҫ�޸�$parse_filename������ֵ,Ȼ���Ӧ��ModelĿ¼ ���ļ���_newai.ini�ļ�
	addShortCutByDate("createtime","����������ʱ��");
	$filetablename		=	'stockchangemain';
	$parse_filename		=	'stockchangemain';
	require_once('include.inc.php');
	systemhelpContent("������˵��",'100%');
?>