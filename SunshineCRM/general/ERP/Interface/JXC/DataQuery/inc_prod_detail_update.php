<?php 
	ini_set('display_errors',1);
	ini_set('error_reporting',E_ALL);
	error_reporting(E_WARNING | E_ERROR);
	require_once("lib.inc.php");
	$GLOBAL_SESSION=returnsession();
	header('Content-Type:text/xml;charset=GB2312'); 
    $action = $_GET["action"];     //��ȡ����
    $productId = $_GET["productId"];           //��ȡ��Ʒ���
    $rowid= $_GET["rowid"];		//����ID
    $tablename=$_GET["tablename"];//����
    //ȡ�òֿ�
    $storeid="";
    $priceReadonly=0;
	$zhekouReadonly=0;
    if($tablename=="stockinmain_detail")
    	$storeid= returntablefield("stockinmain", "billid", $_GET['rowid'], "storeid");
    else if($tablename=="stockchangemain_detail")
    {
    	$storeid= returntablefield("stockchangemain", "billid", $_GET['rowid'], "outstoreid");
    	$priceReadonly=1;
    	$zhekouReadonly=1;
    }
    else if($tablename=="storecheck_detail")
    {
    	$storeid= returntablefield("storecheck", "billid", $_GET['rowid'], "storeid");
    	$priceReadonly=1;
    	$zhekouReadonly=1;
    }
     else if($tablename=="productzuzhuang_detail")
    {
    	$storeid= returntablefield("productzuzhuang", "billid", $_GET['rowid'], "outstoreid");
    	$priceReadonly=1;
    	$zhekouReadonly=1;
    }
    else if($tablename=="productzuzhuang2_detail")
    	$storeid= returntablefield("productzuzhuang", "billid", $_GET['rowid'], "instoreid");
    else if($tablename=="v_sellonedetail" || $tablename=="sellplanmain_detail")
    {
    	
    	if($tablename=="v_sellonedetail")
    		$storeid= returntablefield("sellplanmain", "billid", $_GET['rowid'], "storeid");
    }
	$id=$_GET["id"];//��ǰ��¼ID
    if ($action=="add") {
    	$im=$_GET["im"];	//���뷽ʽ 1=������2=���룬3=ѡ��
		$addnum=$_GET["addnum"]; //��������ʱ�����ӵ�����
        addProduct($productId,$im,$addnum);                           //�����²�Ʒ  
    }else if ($action=="empty") {
        clearProduct($rowid);                            //����б�
    } else if ($action=="del") {
        delProduct($id);                           //ɾ����Ʒ
    } else if ($action=="updatePrice") {
    	$price=$_GET["price"];
        updateProductPrice($id,$price);                           //���²�Ʒ�۸�
    }
    else if ($action=="updateAmount") {
    	$amount=$_GET["amount"];
        updateProductAmount($id,$amount);                           //���²�Ʒ����
    }
    else if ($action=="updateZhekou") {
    	$zhekou=$_GET["zhekou"];
        updateProductZhekou($id,$zhekou);                           //���²�Ʒ����
    }
    else if ($action=="updateMoney") {
    	$jine=$_GET["jine"];
        updateProductJine($id,$jine);                           //���²�Ʒ����
    }
    else if ($action=="updateMemo") {
    	$beizhu=$_GET["beizhu"];
        updateProductBeizhu($id,$beizhu);                           //���²�Ʒ����
    }
    else if ($action=="Save") {
        SaveAll();                           //���沢����
    }
    else if ($action=="search") {
    	$keyword=$_GET["keyword"];
        SearchProduct($keyword);                           //������Ʒ
    }
    
    function SearchProduct($keyword)
    {
    	global $db;
    	$sql = "select * from product where (productid like '%$keyword%' or productname like '%$keyword%' or oldproductid like '%$keyword%' or productcn like '%$keyword%') limit 20";
		$rs = $db->Execute($sql);
		$rs_a = $rs->GetArray();
    	if (count($rs_a) != 0) 
    	{
        	for($i=0;$i<count($rs_a);$i++)
        	{
        		$productId=$rs_a[$i]['productid'];
        		$productName=$rs_a[$i]['productname'];
        		if($rs_a[$i]['mode']!="")
					$productName=$productName."/".$rs_a[$i]['mode'];
				if($rs_a[$i]['standard']!="")
					$productName=$productName."/".$rs_a[$i]['standard'];
				/*
				$sql="select sum(num) as allnum from store where prodid='$productId'";
				$rs = $db->Execute($sql);
				$rs_b = $rs->GetArray();
				if($rs_b[0]['allnum']!=0)
					$productName.=" (".$rs_b[0]['allnum'].")";
				*/
        		print ($i+1)."��<a href=\"javascript:addProduct('$productId','add',1,1);\">$productName</a><br>";
        	}
    	}
    	else 
    		print "<font color=red>û�з��������Ĳ�Ʒ</font><a href='#'></a>";
    	exit;
    }
    function SaveAll()
    {
    	global $db;
    	global $rowid;
    	global $tablename;
		$sql="select count(*) as allcount,sum(num*price*zhekou) as allmoney from ".$tablename." where mainrowid=".$rowid;
    	$rs = $db->Execute($sql);
    	$rs_a = $rs->GetArray();
    	$allcount=$rs_a[0]['allcount'];
    	$allmoney=round($rs_a[0]['allmoney'],2);
    	if($tablename=="customerproduct_detail")
    	{
    		$sql="update customerproduct set ���=".$allmoney." where ROWID=".$rowid;
    		$db->Execute($sql);
    		$sql="update customerproduct_detail set jine=round(price*zhekou*num,2) where mainrowid=".$rowid;
    		$db->Execute($sql);
    	}
    	else if($tablename=="sellplanmain_detail")
    	{
    		
    		$sql="update sellplanmain set totalmoney=".$allmoney." where billid=".$rowid;
    		$db->Execute($sql);
    		$sql="update sellplanmain_detail set jine=round(price*zhekou*num,2) where mainrowid=".$rowid;
    		$db->Execute($sql);
    	}
    	else if($tablename=="v_sellonedetail")
    	{
    		
    		$sql="update sellplanmain set totalmoney=".$allmoney." where billid=".$rowid;
    		$db->Execute($sql);
    		$sql="update sellplanmain_detail set jine=round(price*zhekou*num,2) where mainrowid=".$rowid;
    		$db->Execute($sql);
    	}
    	else if($tablename=="buyplanmain_detail")
    	{
	    	$sql="select count(*) as allcount,sum(num*price*zhekou) as allmoney from $tablename where mainrowid=".$rowid;
	    	$rs = $db->Execute($sql);
	    	$rs_a = $rs->GetArray();
	    	$allcount=$rs_a[0]['allcount'];
	    	$allmoney=round($rs_a[0]['allmoney'],2);
	    	
	    	$totalmoney= returntablefield("buyplanmain", "billid", $_GET['rowid'], "totalmoney");
	    	if($totalmoney!=$allmoney)
	    	{
	    			print "��ϸ���ϼƱ���Ϊ��".$totalmoney;
	    			exit;
	    	}
    		
    		$state=1;
    		if($allcount>0)
    			$state=2;	
    		$sql="update buyplanmain set totalmoney=".$allmoney.",state=$state where billid='".$rowid."'";
    		$db->Execute($sql);
    		$sql="update buyplanmain_detail set jine=round(price*zhekou*num,2) where mainrowid=".$rowid;
    		$db->Execute($sql);
    	}
    	else if($tablename=="stockchangemain_detail")
    	{
    		$sql="update stockchangemain set totalmoney=".$allmoney.",state=2 where billid=".$rowid;
    		$db->Execute($sql);
    		$sql="update stockchangemain_detail set jine=round(price*zhekou*num,2) where mainrowid=".$rowid;
    		$db->Execute($sql);
    	}
    	else if($tablename=="storecheck_detail")
    	{
    		//��������
    		try {
		    	$db->StartTrans();  
		    	//$db->debug=true;
	    		$Store=new Store($db);
	    		$Store->insertStoreCheck($rowid,$allmoney);
	    		//�Ƿ�������ִ���
				if ($db->HasFailedTrans()) 
				 	print $db->ErrorMsg();
		    	$db->CompleteTrans();
    		}
    		catch (Exception $e)
			{
				print $e->getMessage();exit;
			} 
    	}
    	else if($tablename=="productzuzhuang_detail")
    	{
    		//��������
    		try {
	    	$db->StartTrans();

    		$storeid = returntablefield("productzuzhuang","billid",$rowid,"outstoreid");
    		$sql = "select * from productzuzhuang_detail where mainrowid=".$rowid;
			$rs = $db->Execute($sql);
			$rs_detail = $rs->GetArray();
			for($i=0;$i<sizeof($rs_detail);$i++)
			{
				
				$sql = "select * from store where storeid=".$storeid." and prodid='".$rs_detail[$i]['prodid']."'";
				
				$rs = $db->Execute($sql);
				$rs_store = $rs->GetArray();
				$kucun=0;
				if(sizeof($rs_store)>0)
					$kucun=$rs_store[0]['num'];
				if($kucun-$rs_detail[$i]['num']<0)
					throw new Exception("���Ϊ��".$rs_detail[$i]['prodid']." �Ĳ�Ʒ��治��");
				
				if(sizeof($rs_store)==0)
				{
						$maxid=returnAutoIncrement("id", "store");
						$sql = "insert into store (id,prodid,storeid,num,price) values($maxid,'".
		    			$rs_detail[$i]['prodid']."',".$storeid.",".$rs_detail[$i]['num'].",".$rs_detail[$i]['price'].")";
		    			$db->Execute($sql);
				}
				else 
				{
					$junjia=$rs_store[0]['price'];
					$sql = "update store set num=num-(".$rs_detail[$i]['num'].") where storeid=".$storeid." and prodid='".$rs_detail[$i]['prodid']."'";
					$db->Execute($sql);
					$sql = "update productzuzhuang_detail set price=".$junjia." where id=".$rs_detail[$i]['id'];
					$db->Execute($sql);
						
				}
				
			}
			$sql="delete from store where num=0";
			$db->Execute($sql);
    		
			$sql="update productzuzhuang set totalmoney=".$allmoney.",state=2,outshenhetime='".date("Y-m-d H:i:s")."',outstoreshenhe='".$_SESSION['LOGIN_USER_ID']."' where billid=".$rowid;
    		$db->Execute($sql);
    		$sql="update productzuzhuang_detail set jine=round(price*zhekou*num,2) where mainrowid=".$rowid;
    		$db->Execute($sql);
    		
    		//�Ƿ�������ִ���
    		
			
	    	$db->CompleteTrans();
    	}
	    catch (Exception $e)
	    {
	    	print $e->getMessage();
	    	exit;
	    }
			
    		
    	}
    	else if($tablename=="productzuzhuang2_detail")
    	{
    		$totalmoney= returntablefield("productzuzhuang", "billid", $_GET['rowid'], "totalmoney");
    		if($totalmoney!=$allmoney)
    		{
    			print "�����ϼƱ���Ϊ��".$totalmoney;
    			exit;
    		}
    		//��������
    		try {
	    	$db->StartTrans();  
    		$storeid = returntablefield("productzuzhuang","billid",$rowid,"instoreid");
    		$sql = "select * from productzuzhuang2_detail where mainrowid=".$rowid;
			$rs = $db->Execute($sql);
			$rs_detail = $rs->GetArray();
			for($i=0;$i<sizeof($rs_detail);$i++)
			{
				
				$sql = "select * from store where storeid=".$storeid." and prodid='".$rs_detail[$i]['prodid']."'";
				
				$rs = $db->Execute($sql);
				$rs_store = $rs->GetArray();
				$kucun=0;
				if(sizeof($rs_store)>0)
					$kucun=$rs_store[0]['num'];
				if($kucun+$rs_detail[$i]['num']<0)
					throw new Exception("���Ϊ��".$rs_detail[$i]['prodid']." �Ĳ�Ʒ��治��");
				
				if(sizeof($rs_store)==0)
				{
						$maxid=returnAutoIncrement("id", "store");
						$sql = "insert into store (id,prodid,storeid,num,price) values($maxid,'".
		    			$rs_detail[$i]['prodid']."',".$storeid.",".$rs_detail[$i]['num'].",".$rs_detail[$i]['price'].")";
		    			$db->Execute($sql);
				}
				else 
				{
						if($kucun+$rs_detail[$i]['num']!=0)
							$junjia=round(($rs_store[0]['price']*$kucun+$rs_detail[$i]['price']*$rs_detail[$i]['num'])/($kucun+$rs_detail[$i]['num']),2);
						else
						{
							if($rs_store[0]['price']*$kucun+$rs_detail[$i]['price']*$rs_detail[$i]['num']==0)
								$junjia=0;
							else 
								throw new Exception("���Ϊ��".$rs_detail[$i]['prodid']." �Ĳ�Ʒ�������Ϊ�㣬����Ϊ�㣬�޷������Ȩƽ����");
						}
						$sql = "update store set num=num+(".$rs_detail[$i]['num']."),price=".$junjia." where storeid=".$storeid." and prodid='".$rs_detail[$i]['prodid']."'";
						$db->Execute($sql);
				}
				
			}
			$sql="delete from store where num=0";
			$db->Execute($sql);
    		
			$sql="update productzuzhuang set state=3,inshenhetime='".date("Y-m-d H:i:s")."',instoreshenhe='".$_SESSION['LOGIN_USER_ID']."' where billid=".$rowid;
    		$db->Execute($sql);
    		$sql="update productzuzhuang2_detail set jine=round(price*zhekou*num,2) where mainrowid=".$rowid;
    		$db->Execute($sql);
    		
    		//�Ƿ�������ִ���
			
	    	$db->CompleteTrans();
    		}
    		catch (Exception $e)
	    	{
	    		print $e->getMessage();
	    		exit;
	    	}
    		
    	}
    	print "Save";
    	exit;
    } 
    function updateProductBeizhu($id,$beizhu)
    {
    	global $db;
    	global $tablename;
    	$sql="update ".$tablename." set beizhu='".$beizhu."' where id=".$id;
    	$rs = $db->Execute($sql);
    	print "updateMemo";
    	exit;
    } 
    function ifWarningPrice($price,$prodid)
    {
    	global $db;
    	global $tablename;
    	global $db;
    	$sql = "select sum(num) as allnum,sum(num*price) as allmoney from store where prodid='".$prodid."'";
        if($storeid!="")
           	$sql.=" and storeid=".$storeid;
        $rs = $db->Execute($sql);
        $rs_kucun = $rs->GetArray();
    	if($rs_kucun[0]['allnum']>0)
    	{
    		
    			return round($rs_kucun[0]['allmoney']/$rs_kucun[0]['allnum']-$price,2);
    		
    	}
    	else 
    		return 0;
    	
    }
    function updateProductJine($id,$jine)
    {
    	global $db;
    	global $tablename;
    	$sql="select * from ".$tablename." where id=".$id;
    	$rs = $db->Execute($sql);
    	$rs_a = $rs->GetArray();
    	if(count($rs_a)==0)
    	{
    		print "�˼�¼�Ѳ�����";
    		exit;
    	}
    	$prodid=$rs_a[0]['prodid'];
    	$rowid=$rs_a[0]['mainrowid'];
    	$price=$rs_a[0]['price'];
    	$num=$rs_a[0]['num'];
    	if($price*$num==0)
    	{
    		print "�۸����������Ϊ0";
    		exit;
    	}
    	$zhekou=round($jine/($price*$num),2);
    	$jine=round($price*$num*$zhekou,2);
    	$sql="update ".$tablename." set zhekou=".$zhekou." where id=".$id;
    	$rs = $db->Execute($sql);
		$sql="select sum(num) as allnum,sum(num*price*zhekou) as allmoney from ".$tablename." where mainrowid=".$rowid;
    	$rs = $db->Execute($sql);
    	$rs_a = $rs->GetArray();
    	$allnum=$rs_a[0]['allnum'];
    	$allmoney=round($rs_a[0]['allmoney'],2);
    	$warnflag=ifWarningPrice($price*$zhekou,$prodid);
    	print "updateMoney|$id|$num|$price|$zhekou|$allnum|$allmoney|$warnflag";
    	exit;
    } 
    function updateProductZhekou($id,$zhekou)
    {
    	global $db;
    	global $tablename;
    	$sql="select * from ".$tablename." where id=".$id;
    	$rs = $db->Execute($sql);
    	$rs_a = $rs->GetArray();
    	if(count($rs_a)==0)
    	{
    		print "�˼�¼�Ѳ�����";
    		exit;
    	}
    	$zhekou=$zhekou/100;
    	$prodid=$rs_a[0]['prodid'];
    	$rowid=$rs_a[0]['mainrowid'];
    	$price=$rs_a[0]['price'];
    	$num=$rs_a[0]['num'];
    	$jine=round($price*$num*$zhekou,2);
    	$sql="update ".$tablename." set zhekou=".$zhekou." where id=".$id;
    	$rs = $db->Execute($sql);
		$sql="select sum(num) as allnum,sum(num*price*zhekou) as allmoney from ".$tablename." where mainrowid=".$rowid;
    	$rs = $db->Execute($sql);
    	$rs_a = $rs->GetArray();
    	$allnum=$rs_a[0]['allnum'];
    	$allmoney=round($rs_a[0]['allmoney'],2);
    	$warnflag=ifWarningPrice($price*$zhekou,$prodid);
    	print "updateZhekou|$id|$num|$price|$zhekou|$allnum|$allmoney|$warnflag";
    	exit;
    } 
   function updateProductAmount($id,$amount)
    {
    	global $db;
    	global $tablename;
    	if($amount==0)
    	{
    		print "��������Ϊ�㣡";
    		exit;
    	}
    	if($tablename=='sellplanmain_detail' && $amount<0)
    	{
    		print "�����в�Ʒ�����������0����Ҫ�˻����ڵ������۵��в���";
    		exit;
    	}
    	$sql="select * from ".$tablename." where id=".$id;
    	$rs = $db->Execute($sql);
    	$rs_a = $rs->GetArray();
    	
    	if(count($rs_a)==0)
    	{
    		print "�˼�¼�Ѳ�����";
    		exit;
    	}
    	$rowid=$rs_a[0]['mainrowid'];
    	$price=$rs_a[0]['price'];
    	$zhekou=$rs_a[0]['zhekou'];
    	$jine=round($price*$amount*$zhekou,2);
    	$sql="update ".$tablename." set num=".$amount." where id=".$id;
    	$rs = $db->Execute($sql);
		$sql="select sum(num) as allnum,sum(num*price*zhekou) as allmoney from ".$tablename." where mainrowid=".$rowid;
    	$rs = $db->Execute($sql);
    	$rs_a = $rs->GetArray();
    	$allnum=$rs_a[0]['allnum'];
    	$allmoney=round($rs_a[0]['allmoney'],2);
    	print "updateAmount|$id|$amount|$price|$zhekou|$allnum|$allmoney|";
    	exit;
    } 
    function updateProductPrice($id,$price)
    {
    	global $db;
    	global $tablename;
    	$sql="select * from ".$tablename." where id=".$id;
    	$rs = $db->Execute($sql);
    	$rs_a = $rs->GetArray();
    	if(count($rs_a)==0)
    	{
    		print "�˼�¼�Ѳ�����";
    		exit;
    	}
    	$rowid=$rs_a[0]['mainrowid'];
    	$prodid=$rs_a[0]['prodid'];
    	$num=$rs_a[0]['num'];
    	$zhekou=$rs_a[0]['zhekou'];
    	$jine=round($price*$num*$zhekou,2);
    	$sql="update ".$tablename." set price=".$price." where id=".$id;
    	$rs = $db->Execute($sql);
		$sql="select sum(num) as allnum,sum(num*price*zhekou) as allmoney from ".$tablename." where mainrowid=".$rowid;
    	$rs = $db->Execute($sql);
    	$rs_a = $rs->GetArray();
    	
    	$allnum=$rs_a[0]['allnum'];
    	$allmoney=round($rs_a[0]['allmoney'],2);
    	$warnflag=ifWarningPrice($price*$zhekou,$prodid);
    	print "updatePrice|$id|$num|$price|$zhekou|$allnum|$allmoney|$warnflag";
    	exit;
    } 
    
     function clearProduct($rowid)
    {
    	global $db;
    	global $tablename;
    	$sql="delete from ".$tablename." where  mainrowid='$rowid'";
    	$rs = $db->Execute($sql);
    } 
    
    function addProduct($productId,$im,$addnum)
    {
    	global $db;
    	global $rowid;
    	global $tablename;
    	global $storeid;
    	$sql = "select * from product where productid='".$productId."'";
    	$rs = $db->Execute($sql);
		$rs_a = $rs->GetArray();
		if(count($rs_a)==0)
		{
			print "�����ڱ��Ϊ  $productId �Ĳ�Ʒ";
			exit;
		}
		if($rs_a[0]['user_flag']=="��")
		{
			print "��Ʒ���д˲�Ʒ�ѽ�ֹʹ��";
			exit;
		}
		$opertype=1;
    	if($addnum<0)
    		$opertype=-1;
		$pname=$rs_a[0]['productname'];
		$oldproduct=$rs_a[0]['oldproductid'];
		$guige=$rs_a[0]['standard'];
		$xinghao=$rs_a[0]['mode'];
		$danwei=$rs_a[0]['measureid'];
		$price=$rs_a[0]['sellprice'];
		$zhekou="1";
		$beizhu="";
    	//���۵���ȡ�ͻ��۸�
		if($tablename=="customerproduct_detail")
		{
			$customerid=returntablefield("customerproduct", "rowid", $rowid, "�ͻ�");
			$custState=returntablefield("customer", "rowid",$customerid,"state");
			$custprice=returntablefield("customerlever", "rowid",$custState,"relatePrice");
			if($custprice=='')
				$custprice='sellprice';
			$price=$rs_a[0][$custprice];
			if($price=='')
				$price=0;
		}
		//���۶�����ȡ�ͻ��۸�
		else if($tablename=="sellplanmain_detail" || $tablename=="v_sellonedetail")
		{
			$customerid=returntablefield("sellplanmain", "billid", $rowid, "supplyid");
			$custState=returntablefield("customer", "rowid",$customerid,"state");
			$custprice=returntablefield("customerlever", "rowid",$custState,"relatePrice");
			if($custprice=='')
				$custprice='sellprice';
			$price=$rs_a[0][$custprice];
			if($price=='')
				$price=0;
		}
		//��һ�βɹ��ļ۸�
		else if($tablename=="buyplanmain_detail")
		{
			$sql="select * from ".$tablename." where prodid='$productId' and mainrowid<>'$rowid' order by id desc";
			
    		$rs = $db->Execute($sql);
			$rs_a = $rs->GetArray();
			if(count($rs_a)>0)
			{
				$price=$rs_a[0]['price'];
				$zhekou=$rs_a[0]['zhekou'];
			}
		}
    	//���ɱ���
		else if($tablename=="stockchangemain_detail" || $tablename=="storecheck_detail" || $tablename=="productzuzhuang_detail")
		{
			$ifkucun=returntablefield("product","productid",$productId,"ifkucun");
			if($ifkucun=="��")
			{
				print "���Ϊ $productId �Ĳ�Ʒ�������棬���ܽ��п�����";
				exit;
			}
			$sql="select * from store where prodid='$productId' and storeid=".$storeid;
    		$rs = $db->Execute($sql);
			$rs_a = $rs->GetArray();
			if(count($rs_a)>0)
			{
				$price=$rs_a[0]['price'];
			}
		}
		//�Ƿ��Ѵ���
    	$sql="select * from ".$tablename." where prodid='$productId' and mainrowid='$rowid' and opertype=$opertype";
		
    	$rs = $db->Execute($sql);
		$rs_a = $rs->GetArray();
		
		if(count($rs_a)>0)
		{
			if($im==1 || $im==3)
			{
				print "���Ϊ $productId �Ĳ�Ʒ�Ѵ���";
				exit;
			}
			else//����ɨ��֧����ͬ��Ʒ�����Զ��ۼ�
			{
				$sql="update ".$tablename." set num=num+$addnum where prodid='$productId' and mainrowid=$rowid and opertype=$opertype";
				$rs = $db->Execute($sql);
			}
		}
   		else 
   		{
   			$db->StartTrans();
   			if($tablename=='v_sellonedetail')
   				$tablename1="sellplanmain_detail";
   			else
   				$tablename1=$tablename;
   			$sql="select max(id) as maxid from ".$tablename1;
   			$rs = $db->Execute($sql);
			$rs_a = $rs->GetArray();
			$maxid=$rs_a[0]['maxid']+1;
			$sql="insert into ".$tablename." (id,prodid,prodname,prodguige,prodxinghao,proddanwei,price,zhekou,num,beizhu,mainrowid,oldprodid,opertype)
			values ('$maxid','$productId','$pname','$guige','$xinghao','$danwei',$price,$zhekou,$addnum,'$beizhu',$rowid,'$oldproduct','$opertype')";
			$rs = $db->Execute($sql);
			//�Ƿ�������ִ���
			if ($db->HasFailedTrans()) 
		 		print "<script language=javascript>alert('����".str_replace("'",  "\'", $db->ErrorMsg())."');window.history.back(-1);</script>";
			$db->CompleteTrans();
   		}
    } 
   
    function delProduct($id)
    {
    	global $db;
    	global $tablename;
    	$sql="delete from ".$tablename." where id=".$id;
		$rs = $db->Execute($sql);
    }
    $imgurl=ROOT_DIR."general/ERP/Framework/images/sepan.gif";
    $imgurlgray=ROOT_DIR."general/ERP/Framework/images/sepangray.gif";

?>
<form name="form2">
<table align=center class=TableBlock width=100% border=0 id="table1">
<tr >
	<td align=center class=TableHeader>��Ʒ���</td>
    <td align=center class=TableHeader>��Ʒ����</td>
    <td align=center class=TableHeader>���</td>
    <td align=center class=TableHeader>�ͺ�</td>
    <td align=center class=TableHeader>��λ</td>
    <td align=center class=TableHeader>�۸�</td>
    <td align=center class=TableHeader>�ۿ�</td>
    <td align=center class=TableHeader>����</td>
    <td align=center class=TableHeader>��ǰ���</td>
    <td align=center class=TableHeader>���</td>
    <td align=center class=TableHeader>��ע</td>
    <td align=center class=TableHeader>ɾ��</td>
</tr>

<?php 
	$sql = "select * from ".$tablename." where mainrowid=".$rowid;
	$rs = $db->Execute($sql);
	$rs_a = $rs->GetArray();
    if (count($rs_a) != 0) 
    {
    	
    	$allnum=0;
    	$allmoney=0;
    	$class="";
        for($i=0;$i<count($rs_a);$i++)
        {
        	$allnum=$allnum+$rs_a[$i]['num'];
        	$allmoney=$allmoney+round($rs_a[$i]['num']*$rs_a[$i]['price']*$rs_a[$i]['zhekou'],2);
        	if($i%2==1)
        		$class="TableLine1";
        	else
        		$class="TableLine2";
        	$jine=round($rs_a[$i]['price']*$rs_a[$i]['zhekou']*$rs_a[$i]['num'],2);
        	
?>
            <tr class=<?php echo $class?>>
            	<td align=center><?php echo $rs_a[$i]['prodid']?></td>
                <td align=center><?php echo $rs_a[$i]['prodname']?></td>
                <td align="center"><?php echo $rs_a[$i]['prodguige']?></td>
                <td align="center"><?php echo $rs_a[$i]['prodxinghao']?></td>
                <td align="center"><?php echo $rs_a[$i]['proddanwei']?></td>
             
                <td align="center"><input  
                <?php if($tablename=="buyplanmain_detail") 
                     	echo "title='Ĭ���ϴμ۸�'";
                     if($priceReadonly) 
                     	echo "readonly class=SmallStatic";else echo "class=SmallInput"?> style="width:60px" id="price_<?php echo $rs_a[$i]['id']?>" onkeydown="focusNext(event)" onKeyPress="return inputFloat(event)" value="<?php echo $rs_a[$i]['price']?>" onchange="updatePrice(<?php echo $rs_a[$i]['id']?>,this.value)"></td>
                <td align="center"><input <?php if($zhekouReadonly) echo "readonly class=SmallStatic";else echo "class=SmallInput"?> class="SmallInput" style="width:60px" id="zhekou_<?php echo $rs_a[$i]['id']?>" onkeydown="focusNext(event)" onKeyPress="return inputFloat(event)" value="<?php echo $rs_a[$i]['zhekou']*100?>" onchange="updateZhekou(<?php echo $rs_a[$i]['id']?>,this.value)">%</td>
           	    <td align="center">
				<?php 
           	    $colorset='';
           	    $readonly="class='SmallInput'";
           	    if($tablename=="sellplanmain_detail" || $tablename=="v_sellonedetail")
    			{
	           	    $hascolor=returntablefield("product","productid", $rs_a[$i]['prodid'], "hascolor");
	                if($hascolor=='��')
	                {
	                	$sql="select sum(num) as allnum from sellplanmain_detail_color where id=".$rs_a[$i]['id'];
	                	$rs=$db->Execute($sql);
						$rs_c = $rs->GetArray();
						$readonly="class='SmallStatic' readonly";
						
						if($rs_c[0]['allnum']==$rs_a[$i]['num']-$rs_a[$i]['recnum'])
							$colorset= "<a href='javascript:PopColorInput(".$rs_a[$i]['id'].",\"sellplanmain_detail_color\");' title='������ɫ����'><img id='img_".$rs_a[$i]['id']."' src=$imgurl></a>";
						else
	                		$colorset= "<a href='javascript:PopColorInput(".$rs_a[$i]['id'].",\"sellplanmain_detail_color\");' title='��δ������ɫ����'><img id='img_".$rs_a[$i]['id']."' src=$imgurlgray></a>";
	                }
    			}
                ?>
                <input <?php echo $readonly?> style="width:60px" id="num_<?php echo $rs_a[$i]['id']?>" onkeydown="focusNext(event)" onKeyPress="return <?php if($_SESSION['numzero']==0)print "inputInteger(event)";else print "inputFloat(event)";?>" value="<?php echo $rs_a[$i]['num']?>" onchange="updateAmount(<?php echo $rs_a[$i]['id']?>,this.value)"><?php echo $colorset?></td>
                <?php 
          
                $sql = "select sum(num) as allnum,sum(num*price) as allmoney from store where prodid='".$rs_a[$i]['prodid']."'";
                if($storeid!="")
                	$sql.=" and storeid=".$storeid;
                $rs = $db->Execute($sql);
                $rs_kucun = $rs->GetArray();
                $color="green";	
                if($rs_a[$i]['num']>$rs_kucun[0]['allnum'])
                	$color="red";
                $warning="";
                if($rs_kucun[0]['allnum']!=0)
                {
	                $chae=round($rs_kucun[0]['allmoney']/$rs_kucun[0]['allnum']-$rs_a[$i]['price']*$rs_a[$i]['zhekou'],2);
	                if($tablename=="sellplanmain_detail" || $tablename=="v_sellonedetail")
	                {
	                	
	                	if($rs_kucun[0]['allnum']>0 && $chae>0)
	                		$warning="<img src='../../../Framework/images/warning.gif' title='�ۺ�۱ȳɱ��۵�$chaeԪ'>";
	                }
	                if($tablename=="buyplanmain_detail")
	                {
	                	if($rs_kucun[0]['allnum']>0 && $chae<0)
	                		$warning="<img src='../../../Framework/images/warning.gif' title='�ۺ�۱ȳɱ��۸�".-$chae."Ԫ'>";
	                }
                }
                ?>
                <td align="center"><font color=<?php echo $color?>><?php echo $rs_kucun[0]['allnum']?></font></td>
                <td align="center"><span id="warning_<?php echo $rs_a[$i]['id']?>"><?php echo $warning?></span><input <?php echo $disable ?> class="SmallInput" style="width:60px" id="jine_<?php echo $rs_a[$i]['id']?>" onkeydown="focusNext(event)" onKeyPress="return inputFloat(event)" value="<?php echo $jine?>" onchange="updateMoney(<?php echo $rs_a[$i]['id']?>,this.value)"> Ԫ</td>
                <td align="center"><input class="SmallInput" size=12 id="beizhu_<?php echo $rs_a[$i]['id']?>" value="<?php echo $rs_a[$i]['beizhu']?>" onchange="updateMemo(<?php echo $rs_a[$i]['id']?>,this.value)"></td>
                <td align="center"><input type="button"  onclick="delProduct('<?php echo $rs_a[$i]['id']?>')" value="ɾ��"></td>
            </tr>
            <?php 
        }
        ?>
        <tr class=TableHeader >
             <td align=center>�ܼ�</td>
             <td></td><td></td><td></td><td></td><td></td><td></td>
             <td align="center"><div id="allamount"><?php echo $allnum?></div></td><td></td>
             <td align="center"><div id="allmoney"><?php echo $allmoney?> Ԫ</div></td>
             <td></td><td></td>
        <?php
    } else {
        ?>
        <tr>
            <td colspan="11" style="height:50px" align="center">����û��ѡ���κβ�Ʒ</td>
        </tr>
        <?php
    }
?>

</table>
</form>
