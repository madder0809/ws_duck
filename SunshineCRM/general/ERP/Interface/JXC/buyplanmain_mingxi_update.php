<?php 
	ini_set('display_errors',1);
	ini_set('error_reporting',E_ALL);
	error_reporting(E_WARNING | E_ERROR);
	require_once("lib.inc.php");
	$GLOBAL_SESSION=returnsession();
	header('Content-Type:text/xml;charset=GB2312'); 
    $action = $_GET["action"];     //获取操作
    $productId = $_GET["productId"];           //获取产品编号
    $rowid= $_GET["rowid"];//主表ID
    $tablename=$_GET["tablename"];//表名
   
    $disable="";
    
	$id=$_GET["id"];//当前记录ID
    if ($action=="add") {
        addProduct($_GET['oldproductid'],$_GET['supplyid']);                           //增加新产品
    } else if ($action=="empty") {
        clearProduct($rowid);                            //清空列表
    } else if ($action=="del") {
        delProduct($id);                           //删除产品
    } else if ($action=="updatePrice") {
    	$price=$_GET["price"];
        updateProductPrice($id,$price);                           //更新产品价格
    }
    else if ($action=="updateAmount") {
    	$amount=$_GET["amount"];
        updateProductAmount($id,$amount);                           //更新产品数量
    }
    else if ($action=="updateZhekou") {
    	$zhekou=$_GET["zhekou"];
        updateProductZhekou($id,$zhekou);                           //更新产品数量
    }
    else if ($action=="updateMoney") {
    	$jine=$_GET["jine"];
        updateProductJine($id,$jine);                           //更新产品数量
    }
    else if ($action=="updateMemo") {
    	$beizhu=$_GET["beizhu"];
        updateProductBeizhu($id,$beizhu);                           //更新产品数量
    }
    else if ($action=="autodingjia") {
    	$gongshi=$_GET["gongshi"];
        autoUpdateProductSellPrice($gongshi);                           //更新产品数量
    }
    else if ($action=="Save") {
        SaveAll();                           //保存并返回
    }
    else if ($action=="SaveExcel") {
        SaveExcelAll();                           //保存并返回
    }
    else if ($action=="updateSellPrice") {
    	$price=$_GET["price"];
        updateProductSellPrice($id,$price);                           //更新产品价格
    }
    else if ($action=="autocreateproduct") {
        autoCreateProduct();                           //更新产品价格
    }
    else if ($action=="colorinput") {
        colorinput();                           //颜色分配
    }
    function colorinput()
    {
    	global $db;
    	global $rowid;
    	$colorarray=$_GET['colorarray'];
    	$tablename=$_GET['tablename'];
    	$colorarray=explode(",", $colorarray);
    	$sql="delete from ".$tablename."_color where id=$rowid";
		$db->Execute($sql);
    	$sql="select * from productcolor";
		$rs = $db->Execute($sql);
		$rs_a = $rs->GetArray(); 
		for($i=0;$i<sizeof($rs_a);$i++)
		{
			if(intval($colorarray[$i])==0)
				continue;
			$sql="insert into ".$tablename."_color values($rowid,".$rs_a[$i]['id'].",".$colorarray[$i].")";
			$db->Execute($sql);
		}
		$sql="SELECT COUNT(*) as allnum FROM information_schema.TABLES WHERE TABLE_NAME='$tablename'";
		$rs = $db->Execute($sql);
		if($rs->fields['allnum']==1)
		{
			$sql="delete from ".$tablename."_color where id not in (select id from $tablename)";
			$db->Execute($sql);
		}
		print "ok";
		exit;
    }
    function SaveExcelAll()
    {
    	global $db;
    	global $rowid;
    	global $tablename;
    	
		$sql="select count(*) as allcount,sum(num*round(price,2)*zhekou) as allmoney from $tablename where mainrowid=".$rowid;
    	$rs = $db->Execute($sql);
    	$rs_a = $rs->GetArray();
    	$allcount=$rs_a[0]['allcount'];
    	$allmoney=round($rs_a[0]['allmoney'],2);
    	
    	$totalmoney= returntablefield("buyplanmain", "billid", $_GET['rowid'], "totalmoney");
    	if(floatval($totalmoney)!=floatval($allmoney))
    	{
    			print "明细金额合计必须为：".floatval($totalmoney).",当前为：$allmoney";
    			exit;
    	}
    	
    	$supplyid= returntablefield("buyplanmain", "billid", $rowid, "supplyid");
    	
    	$sql="select * from $tablename where mainrowid=".$rowid." and oldproductid not in (select oldproductid from product where supplyid=$supplyid)";
    	$rs = $db->Execute($sql);
    	$rs_a = $rs->GetArray();
    	if(sizeof($rs_a)>0)
    	{
    		print "原厂码为：".$rs_a[0]['oldproductid']."的产品还未生成产品编号";
    		exit;
    	}
    	
    	try 
		{
			$db->StartTrans();  
			$sql="delete from buyplanmain_detail where mainrowid=".$rowid;
	    	$db->Execute($sql);
	    	$sql = "select a.*,b.productid,b.sellprice as psellprice,b.productname,b.measureid from $tablename a left join product b on a.oldproductid=b.oldproductid and b.supplyid=$supplyid where mainrowid=".$rowid;
			$rs = $db->Execute($sql);
			$rs_a = $rs->GetArray();
			for($i=0;$i<count($rs_a);$i++)
		    {
		    	$id=returntablefield("buyplanmain_detail", "mainrowid",$rowid,"id","prodid",$rs_a[$i]['productid']);
		    	if($id!='')
		    		$sql="update buyplanmain_detail set num=num+(".$rs_a[$i]['num'].") where id=$id";
		    	else 
		    	{
		    		$id=returnAutoIncrement("id", "buyplanmain_detail");
		    		$sql="insert into buyplanmain_detail(id,oldprodid,prodid,proddanwei,prodname,mainrowid,zhekou,num,price) 
		    		values($id,'".$rs_a[$i]['oldproductid']."','".$rs_a[$i]['productid']."','".$rs_a[$i]['measureid']."','".$rs_a[$i]['productname']."',$rowid,1,".$rs_a[$i]['num'].",".$rs_a[$i]['price'].")";
		    	}
		    	$db->Execute($sql);
		    		
		    }
		    $sql="update buyplanmain_detail set jine=round(num*price*zhekou,2) where mainrowid=".$rowid;
	    	$db->Execute($sql);
	    	$sql="delete from $tablename where mainrowid=".$rowid;
	    	$db->Execute($sql);
	    	$state=1;
	    	if($allcount>0)
	    		$state=2;	
	    	$sql="update buyplanmain set state=$state where billid='".$rowid."'";
	    	$db->Execute($sql);
	    	$db->CompleteTrans();
		}
    	catch (Exception $e) 
		{   
			print $e->getMessage();
			exit;
		} 
    	print "Save";
    	exit;
    } 
    function autoCreateProduct()
	{
		global $rowid;
		global $tablename;
		global $db;
		try 
		{
			$db->StartTrans();  
			$supplyid= returntablefield("buyplanmain", "billid", $rowid, "supplyid");
			$sql = "select a.*,b.productid,b.sellprice as psellprice from $tablename a left join product b on a.oldproductid=b.oldproductid and b.supplyid=$supplyid where b.productid is null and mainrowid=".$rowid;
			$rs = $db->Execute($sql);
			$rs_a = $rs->GetArray();
			$prodArray=array();
			for($i=0;$i<count($rs_a);$i++)
	        {
	        	if($rs_a[$i]['prodname']=='')
	        		throw new Exception("商品".$rs_a[$i]['oldproductid']."的名称不能为空");
	        	if(floatval($rs_a[$i]['sellprice'])==0)
	        		throw new Exception("商品".$rs_a[$i]['oldproductid']."的零售价不能为0");
	        		
	        	if($prodArray[$rs_a[$i]['oldproductid']]=='')//生成13位Ean条码格式
	        	{
	        		
	        		if($rs_a[$i]['prodid']=='')
	        		{
		        		$barcode="2";
		        		$str_supplyid=$supplyid;
		        		
		        		while(strlen($str_supplyid)<4)
		        			$str_supplyid="0".$str_supplyid;
		        		
		        		if($rs_a[$i]['prodtype']=='')
		        			throw new Exception("商品".$rs_a[$i]['oldproductid']."的类型不能为空");
		        		$str_prodtype=$rs_a[$i]['prodtype'];
		        		while(strlen($str_prodtype)<3)
		        			$str_prodtype="0".$str_prodtype;
		        		$barcode.=$str_supplyid.$str_prodtype;
		        		
		        		$maxid=1;
						$sql="select max(substr(productid,9,4)) as maxid from product where left(productid,8)='".$barcode."'";
						$rs=$db->Execute($sql);
						$rs_b=$rs->GetArray();
						if(!empty($rs_b[0][maxid]))
							$maxid=$rs_b[0][maxid]+1;
						while(strlen($maxid)<4)
		        			$maxid="0".$maxid;
		        		$barcode.=$maxid;
		        		$jishu=0;
						$oushu=0;
						for($j=0;$j<6;$j++)
						{
							$jishu=$jishu+intval($barcode{$j*2});
							$oushu=$oushu+intval($barcode{$j*2+1});
						}
						$jiaoyan=10-(($jishu+$oushu*3)%10);
						if($jiaoyan==10)
							$jiaoyan=0;
						$barcode.=$jiaoyan;
						$rs_a[$i]['prodid']=$barcode;	
	        		}
					$prodArray[$rs_a[$i]['oldproductid']]=$rs_a[$i]['prodid'];
	        	}
	        	else 
	        		$rs_a[$i]['prodid']=$prodArray[$rs_a[$i]['oldproductid']];
	        	if($rs_a[$i]['productid']=='')//创建新产品
	        	{
	        		$productid=returntablefield("product", "productid", $rs_a[$i]['prodid'], "productid");
	        		if($productid=='')
	        		{
		        		if($rs_a[$i]['danwei']=='')
		        			$rs_a[$i]['danwei']='个';
		        		$productcn=汉字转拼音首字母($rs_a[$i]['prodname']);
		        		$sql="insert into product (productid,productname,measureid,producttype,sellprice,productcn,oldproductid,ifkucun,supplyid) 
		        		values('".$rs_a[$i]['prodid']."','".$rs_a[$i]['prodname']."','".$rs_a[$i]['danwei']."',".$rs_a[$i]['prodtype'].",".$rs_a[$i]['sellprice']
		        		.",'$productcn','".$rs_a[$i]['oldproductid']."','是',$supplyid)";
		        		$db->Execute($sql);
	        		}
	        	}
	        	
	        	
	        }
	        $db->CompleteTrans();
		}
	    catch (Exception $e) 
		{   
			print $e->getMessage();
			exit;
		} 
		
	}
	function autoUpdateProductSellPrice($gongshi)
	{
		global $rowid;
		global $tablename;
		global $db;
		$sql="update unit set dingjiagongshi='".$gongshi."' where id=".$_SESSION['deptid'];
		$db->Execute($sql);
		$supplyid= returntablefield("buyplanmain", "billid", $rowid, "supplyid");
		$sql = "select a.*,b.productid,b.sellprice as psellprice from $tablename a left join product b on a.oldproductid=b.oldproductid and b.supplyid=$supplyid where b.productid is null and mainrowid=".$rowid;
		$rs = $db->Execute($sql);
		$rs_a = $rs->GetArray();
		$prodArray=array();
		for($i=0;$i<count($rs_a);$i++)
        {
        	if($prodArray[$rs_a[$i]['oldproductid']]!='' && floatval($prodArray[$rs_a[$i]['oldproductid']])!=floatval($rs_a[$i]['price']))
        	{
        		print "商品".$rs_a[$i]['oldproductid']." 重复，并且采购单价不一样";
        		exit;
        	}
        	$prodArray[$rs_a[$i]['oldproductid']]=$rs_a[$i]['price'];
        	$replace=str_replace("a1",$rs_a[$i]['price'], $gongshi);
        	$replace=str_replace("Math.","", $replace);
        	$sellprice="";
        	eval("\$sellprice=".$replace.";");
        	$sql="update $tablename set sellprice=".$sellprice." where id=".$rs_a[$i]['id'];
			$db->Execute($sql);
        }
	}
    function SearchProduct($keyword)
    {
    	global $db;
    	$sql = "select * from product where (productname like '%$keyword%' or mode like '%$keyword%' or standard like '%$keyword%' or productcn like '%$keyword%') limit 100";
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
        		print ($i+1)."、<a href=\"javascript:addProduct('$productId','add',1,1);\">$productName</a><br>";
        	}
    	}
    	else 
    		print "<font color=red>没有符合条件的产品</font><a href='#'></a>";
    	exit;
    }
    function SaveAll()
    {
    	global $db;
    	global $rowid;
    	global $tablename;
		$sql="select count(*) as allcount,sum(num*price*zhekou) as allmoney from $tablename where mainrowid=".$rowid;
    	$rs = $db->Execute($sql);
    	$rs_a = $rs->GetArray();
    	$allcount=$rs_a[0]['allcount'];
    	$allmoney=round($rs_a[0]['allmoney'],2);
    	
    	$totalmoney= returntablefield("buyplanmain", "billid", $_GET['rowid'], "totalmoney");
    	if($totalmoney!=$allmoney)
    	{
    			print "明细金额合计必须为：".$totalmoney;
    			exit;
    	}
    	$sql="update $tablename set jine=round(price*zhekou*num,2) where mainrowid=".$rowid;
    	$db->Execute($sql);
    	$state=1;
    	if($allcount>0)
    		$state=2;	
    	$sql="update buyplanmain set state=$state where billid='".$rowid."'";
    	$db->Execute($sql);
    	
    	
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

        $rs = $db->Execute($sql);
        $rs_kucun = $rs->GetArray();
    	if($rs_kucun[0]['allnum']>0 && $price>$rs_kucun[0]['allmoney']/$rs_kucun[0]['allnum'])
    		return 1;
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
    		print "此记录已不存在";
    		exit;
    	}
    	$prodid=$rs_a[0]['prodid'];
    	$rowid=$rs_a[0]['mainrowid'];
    	$price=$rs_a[0]['price'];
    	$num=$rs_a[0]['num'];
    	if($price*$num==0)
    	{
    		print "价格和数量不能为0";
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
    		print "此记录已不存在";
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
    		print "数量不能为零！";
    		exit;
    	}
    	$sql="select * from ".$tablename." where id=".$id;
    	$rs = $db->Execute($sql);
    	$rs_a = $rs->GetArray();
    	
    	if(count($rs_a)==0)
    	{
    		print "此记录已不存在";
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
    		print "此记录已不存在";
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
    function updateProductSellPrice($id,$price)
    {
    	global $db;
    	global $tablename;
    	$sql="select * from ".$tablename." where id=".$id;
    	$rs = $db->Execute($sql);
    	$rs_a = $rs->GetArray();
    	if(count($rs_a)==0)
    	{
    		print "此记录已不存在";
    		exit;
    	}
    	
    	$sql="update ".$tablename." set sellprice=".$price." where id=".$id;
    	$rs = $db->Execute($sql);
		
    	print "updateSellPrice";
    	exit;
    } 
     function clearProduct($rowid)
    {
    	global $db;
    	global $tablename;
    	$sql="delete from ".$tablename." where  mainrowid='$rowid'";
    	$rs = $db->Execute($sql);
    } 
    function getlastprice($productid)
    {
    	global $db;
    	global $tablename;
    	$sql="select * from $tablename where prodid='$productid' and mainrowid<>'$rowid' order by id desc";
			
    	$rs = $db->Execute($sql);
		$rs_a = $rs->GetArray();
		if(count($rs_a)>0)
		{
			$price=$rs_a[0]['price'];
			$zhekou=$rs_a[0]['zhekou'];
		}
		return $price*$zhekou;
    }
    function addProduct($oldproductid,$supplyid)
    {
    	global $db;
    	global $rowid;
    	global $tablename;
    	global $storeid;
    	
    	try 
    	{
    		//是否已存在
	    	$sql="select * from $tablename where oldprodid='$oldproductid' and mainrowid='$rowid'";
	    	$rs = $db->Execute($sql);
			$rs_a = $rs->GetArray();
			
			if(count($rs_a)>0)
				throw new Exception("采购单中原厂码为 $oldproductid 的产品已存在");
			
	    	$sql = "select * from product where oldproductid='$oldproductid' and supplyid=$supplyid";
	    	$rs = $db->Execute($sql);
			$rs_a = $rs->GetArray();
			if(count($rs_a)!=0)
			{
				$productid=$rs_a[0]['productid'];
				$prodguige=$rs_a[0]['standard'];
				$prodxinghao=$rs_a[0]['mode'];
				$proddanwei=$rs_a[0]['measureid'];
				$id=returnAutoIncrement("id", $tablename);
				$lastprice=getlastprice($productid);
				$prodname=$rs_a[0]['productname'];
				$sql="insert into $tablename(id,oldprodid,prodid,prodguige,prodxinghao,proddanwei,lastprice,prodname,mainrowid,zhekou) values($id,'$oldproductid','$productid','$prodguige','$prodxinghao','$proddanwei',$lastprice,'$prodname',$rowid,1)";
				$db->Execute($sql);
				
			}
			else 
			{
				print "add|$oldproductid|$supplyid";
				exit;
			}

   		}
   		catch(Exception $e)
   		{
   			print $e->getMessage();
    		exit;
   		}
    } 
    function delProduct($id)
    {
    	global $db;
    	global $tablename;
    	$sql="delete from ".$tablename." where id=".$id;
		$rs = $db->Execute($sql);
    }
    if($tablename=='buyplanmain_detail')
    {
?>
<form name="form2">
<table align=center class=TableBlock width=100% border=0 id="table1">
<tr >
	<td align=center class=TableHeader>原厂码</td>
	<td align=center class=TableHeader>产品编号</td>
    <td align=center class=TableHeader>产品名称</td>
    <td align=center class=TableHeader>单位</td>
    <td align=center class=TableHeader>上次价格</td>
    <td align=center class=TableHeader>价格</td>
    <td align=center class=TableHeader>折扣</td>
    <td align=center class=TableHeader>数量</td>
    <td align=center class=TableHeader>当前库存</td>
    <td align=center class=TableHeader>金额</td>
    <td align=center class=TableHeader>备注</td>
    <td align=center class=TableHeader>删除</td>
</tr>

<?php 
	$sql = "select * from $tablename where mainrowid=".$rowid;
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
            	<td align="center"><?php echo $rs_a[$i]['oldprodid']?></td>
            	<td align="center"><?php echo $rs_a[$i]['prodid']?></td>
              <td align="center"><?php echo $rs_a[$i]['prodname']?></td>
              <td align="center"><?php echo $rs_a[$i]['proddanwei']?></td>
              <td align="center"><?php echo $rs_a[$i]['lastprice']?></td>
                <td align="center"><input class="SmallInput" size=8 id="price_<?php echo $rs_a[$i]['id']?>" onkeydown="focusNext(event)" onKeyPress="return inputFloat(event)" value="<?php echo $rs_a[$i]['price']?>" onchange="updatePrice(<?php echo $rs_a[$i]['id']?>,this.value)"></td>
                <td align="center"><input class="SmallInput" size=8 id="zhekou_<?php echo $rs_a[$i]['id']?>" onkeydown="focusNext(event)" onKeyPress="return inputFloat(event)" value="<?php echo $rs_a[$i]['zhekou']*100?>" onchange="updateZhekou(<?php echo $rs_a[$i]['id']?>,this.value)">%</td>
           
                <td align="center"><input class="SmallInput" size=8 id="num_<?php echo $rs_a[$i]['id']?>" onkeydown="focusNext(event)" onKeyPress="return <?php if($_SESSION['numzero']==0)print "inputInteger(event)";else print "inputFloat(event)";?>" value="<?php echo $rs_a[$i]['num']?>" onchange="updateAmount(<?php echo $rs_a[$i]['id']?>,this.value)"></td>
                <?php 
                $sql = "select sum(num) as allnum,sum(num*price) as allmoney from store where prodid='".$rs_a[$i]['prodid']."'";
                $rs = $db->Execute($sql);
                $rs_kucun = $rs->GetArray();
                $warning="";
                if($rs_kucun[0]['allnum']>0 && $rs_a[$i]['price']*$rs_a[$i]['zhekou']>$rs_kucun[0]['allmoney']/$rs_kucun[0]['allnum'])
                	$warning="<img src='../../Framework/images/warning.gif' title='折后价高于成本价'>";
                ?>
                <td align="center"><?php echo $rs_kucun[0]['allnum']?></td>
                <td align="center"><span id="warning_<?php echo $rs_a[$i]['id']?>"><?php echo $warning?></span><input <?php echo $disable ?> class="SmallInput" size=8 id="jine_<?php echo $rs_a[$i]['id']?>" onkeydown="focusNext(event)" onKeyPress="return inputFloat(event)" value="<?php echo $jine?>" onchange="updateMoney(<?php echo $rs_a[$i]['id']?>,this.value)"> 元</td>
                <td align="center"><input class="SmallInput" size=12 id="beizhu_<?php echo $rs_a[$i]['id']?>" value="<?php echo $rs_a[$i]['beizhu']?>" onchange="updateMemo(<?php echo $rs_a[$i]['id']?>,this.value)"></td>
                <td align="center"><input type="button"  onclick="delProduct('<?php echo $rs_a[$i]['id']?>')" value="删除"></td>
            </tr>
            <?php 
        }
        ?>
        <tr class=TableHeader >
             <td align=center>总计</td>
             <td></td><td></td><td></td><td></td><td></td><td></td>
             <td align="center"><div id="allamount"><?php echo $allnum?></div></td><td></td>
             <td align="center"><div id="allmoney"><?php echo $allmoney?> 元</div></td>
             <td></td><td></td>
        <?php
    } else {
        ?>
        <tr>
            <td colspan="12" style="height:50px" align="center">您还没有选择任何产品</td>
        </tr>
        <?php
    }
?>

</table>
</form>
<?php 
}
else if($tablename=='buyplanmain_mingxi')
{
?>
<form name="form2">
<table align=center class=TableBlock width=100% border=0 id="table1">
<tr >
	<td align=center class=TableHeader>类别</td>
	<td align=center class=TableHeader>原厂码</td>
	<td align=center class=TableHeader>产品编号</td>
    <td align=center class=TableHeader>产品名称</td>
    <td align=center class=TableHeader>单位</td>
    <td align=center class=TableHeader>采购单价</td>
    <td align=center class=TableHeader>数量</td>
    <td align=center class=TableHeader>金额</td>
    <td align=center class=TableHeader>备注</td>
    <td align=center class=TableHeader>零售价</td>
</tr>

<?php 
	$supplyid= returntablefield("buyplanmain", "billid", $rowid, "supplyid");
	$sql = "select a.*,b.productid,b.sellprice as psellprice,c.name as typename from $tablename a left join product b on a.oldproductid=b.oldproductid and b.supplyid=$supplyid left join producttype c on a.prodtype=c.rowid where mainrowid=".$rowid;
	$rs = $db->Execute($sql);
	$rs_a = $rs->GetArray();
	$flagdingjia=false;
    if (count($rs_a) != 0) 
    {
    	
    	$allnum=0;
    	$allmoney=0;
    	$class="";
        for($i=0;$i<count($rs_a);$i++)
        {
        	$allnum=$allnum+$rs_a[$i]['num'];
        	$allmoney=$allmoney+round($rs_a[$i]['num']*$rs_a[$i]['price'],2);
        	if($i%2==1)
        		$class="TableLine1";
        	else
        		$class="TableLine2";
        	$jine=round($rs_a[$i]['price']*$rs_a[$i]['zhekou']*$rs_a[$i]['num'],2);
        	
?>
            <tr class=<?php echo $class?>>
            	<td align="center"><?php echo $rs_a[$i]['typename']?></td>
            	<td align="center"><?php echo $rs_a[$i]['oldproductid']?></td>
            	<td align="center"><?php if($rs_a[$i]['productid']=='')echo $rs_a[$i]['prodid']; else echo $rs_a[$i]['productid']?></td>
              <td align="center"><?php echo $rs_a[$i]['prodname']?></td>
              <td align="center"><?php echo $rs_a[$i]['danwei']?></td>
              
                <td align="right"><?php echo number_format($rs_a[$i]['price'],2)?></td>
           
                <td align="right"><?php echo $rs_a[$i]['num']?></td>
                <td align="right"><?php echo number_format($rs_a[$i]['price']*$rs_a[$i]['num'],2)?></td>
                <td align="center"><input class="SmallInput" size=15 id="beizhu_<?php echo $rs_a[$i]['id']?>" value="<?php echo $rs_a[$i]['beizhu']?>" onchange="updateMemo(<?php echo $rs_a[$i]['id']?>,this.value)"></td>
                
                <td align="center">
                <?php if($rs_a[$i]['productid']==''){?>
                <input class="SmallInput" size=10 id="sellprice_<?php echo $rs_a[$i]['id']?>" onkeydown="focusNext(event)" onKeyPress="return inputFloat(event)" value="<?php echo $rs_a[$i]['sellprice']?>" onchange="updateSellPrice(<?php echo $rs_a[$i]['id']?>,this.value)">
                <?php
				$flagdingjia=true;
                }else{ echo number_format($rs_a[$i]['psellprice'],2);}?>
                
                </td>
            </tr>
            <?php 
        }
        ?>
        <tr class=TableHeader >
             <td align=center>总计</td>
             <td></td><td></td><td></td><td></td><td></td>
             <td align="center"><div id="allamount"><?php echo $allnum?></div></td>
             <td align="center"><div id="allmoney"><?php echo $allmoney?> 元</div></td>
             <td></td><td align='center'>
             <?php if($flagdingjia) echo "<input type='button' value='批量定价' class='SmallButton' onclick='PopDingJia()'>"?>
             </td>
        <?php
    } else {
        ?>
        <tr>
            <td colspan="8" style="height:50px" align="center">您还没有选择任何产品</td>
        </tr>
        <?php
    }
?>

</table>
</form>
<?php }?>