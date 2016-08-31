<?php
require_once('CaiWu.php');
class Store
{
	var $db;
	function __construct($db) {
		$this->db=&$db;
	}
	//新增发货单
	function insertFaHuo($chukubillid)
	{
		$stockinfo=returntablefield("stockoutmain", "billid", $chukubillid, "dingdanbillid,totalnum,totalmoney,outtype");
		$dingdanid=$stockinfo['dingdanbillid'];
		$totalnum=$stockinfo['totalnum'];
		$totalmoney=$stockinfo['totalmoney'];
		$outtype=$stockinfo['outtype'];
		if($outtype=="销售出库")
		{
			$sellinfo=returntablefield("sellplanmain", "billid", $dingdanid, "supplyid,linkman,address,mobile,fahuodan,fahuotype,fahuoyunfei,yunfeitype");
			$customerid=$sellinfo['supplyid'];
			$linkman=$sellinfo['linkman'];
			$address=$sellinfo['address'];
			$mobile=$sellinfo['mobile'];
			$fahuodan=$sellinfo['fahuodan'];
			$fahuotype=$sellinfo['fahuotype'];
			$fahuoyunfei=floatval($sellinfo['fahuoyunfei']);
			$yunfeitype=$sellinfo['yunfeitype'];
			$shouhuoren=returntablefield("linkman","ROWID" , $linkman, "linkmanname");
		}
		else if($outtype=="返厂出库")
		{
			$buyinfo=returntablefield("buyplanmain", "billid", $dingdanid, "supplyid,linkman");
			$supplyinfo=returntablefield("supply", "rowid", $buyinfo['supplyid'], "chargesection,phone");
			$linkmaninfo=returntablefield("supplylinkman", "ROWID", $buyinfo['linkman'], "supplyname,phone");
			$customerid=$buyinfo['supplyid'];
			$shouhuoren=$linkmaninfo['supplyname'];
			$address=$supplyinfo['chargesection'];
			$mobile=$supplyinfo['phone']." ".$linkmaninfo['phone'];
			
		}
		$sql = "select * from fahuodan where billid=".$chukubillid;
		$rs = $this->db->Execute($sql);
		$rs_a = $rs->GetArray();
		if(count($rs_a)!=0)
		throw  new Exception("对应的发货单已存在");
		$sql="insert into fahuodan (billid,customerid,dingdanbillid,shouhuoren,tel,address,state,totalnum,totalmoney,outtype) values("
		.$chukubillid.",".$customerid.",".$dingdanid.",'".$shouhuoren."','".$mobile."','".$address."','未发货',$totalnum,$totalmoney,'$outtype')";
		$this->db->Execute($sql);
		$sql = "select * from stockoutmain_detail where mainrowid=".$chukubillid;
		$rs = $this->db->Execute($sql);
		$rs_detail = $rs->GetArray();

		for($i=0;$i<sizeof($rs_detail);$i++)
		{
			$sql = "select max(id) as maxid from fahuodan_detail";
			$rs = $this->db->Execute($sql);
			$rs_a = $rs->GetArray();
			$maxid=$rs_a[0]['maxid']+1;
			$sql="insert into fahuodan_detail (id,prodid,prodname,prodguige,prodxinghao,proddanwei,price,zhekou,num,beizhu,mainrowid,jine) values('"
			.$maxid."','".$rs_detail[$i]['prodid']."','".$rs_detail[$i]['prodname']."','".$rs_detail[$i]['prodguige']."','".$rs_detail[$i]['prodxinghao']."','"
			.$rs_detail[$i]['proddanwei']."',".$rs_detail[$i]['price'].",".$rs_detail[$i]['zhekou'].",".$rs_detail[$i]['num'].",'".$rs_detail[$i]['beizhu']."',".$chukubillid.",".$rs_detail[$i]['jine'].")";
			$this->db->Execute($sql);
		}
		if($outtype=="销售出库")
			$this->updatesellplanmainfahuo($dingdanid);

	}
	//确认发货单
	function confirmFaHuo($billid,$fahuodan,$shouhuoren,$address,$tel,$mailcode,$fahuotype,$package,$weight,$yunfei,$jiesuantype,$beizhu)
	{
		//更新发货单
		$sql="update fahuodan set fahuodan='".$fahuodan."',shouhuoren='".$shouhuoren."',address='".$address."',tel='".$tel."',mailcode='".$mailcode.
		"',fahuotype='".$fahuotype."',package=".$package.",weight=".$weight.",yunfei=".$yunfei.",jiesuantype=".$jiesuantype.",beizhu='".$beizhu.
		"',fahuoren='".$_SESSION['LOGIN_USER_ID']."',fahuodate='".date("Y-m-d H:i:s")."',state='已发货' where billid=".$billid;
		$this->db->Execute($sql);
		//更新出库单状态
		$sql="update stockoutmain set state='已发货' where billid=".$billid;
		$this->db->Execute($sql);
		
		//更新订单发货金额
		$outtype=returntablefield("fahuodan", "billid", $billid, "outtype");
		if($outtype=="销售出库")
		{
			$dingdanid=returntablefield("stockoutmain", "billid", $billid, "dingdanbillid");
			$jine=returntablefield("stockoutmain", "billid", $billid, "totalmoney");
			$sql="update sellplanmain set fahuojine=fahuojine+".$jine." where billid=".$dingdanid;
			$this->db->Execute($sql);
			$this->updatesellplanmainfahuo($dingdanid);
		}
		return $dingdanid;
	}
	//撤销发货单
	function cancelFaHuo($billid)
	{
		$outinfo=returntablefield("stockoutmain", "billid",$billid,"totalmoney,outtype");
		$jine=$outinfo['totalmoney'];
		$outtype=$outinfo['outtype'];
		//发货单
		$sql="update fahuodan set state='未发货' where billid=".$billid;
		$this->db->Execute($sql);
		//更新出库单状态
		$sql="update stockoutmain set state='已出库' where billid=".$billid;
		$this->db->Execute($sql);
		$dingdanid=returntablefield("stockoutmain", "billid", $billid, "dingdanbillid");
		//更新订单发货金额
		if($outtype=='销售出库')
		{
			$sql="update sellplanmain set fahuojine=round(fahuojine-".$jine.",2) where billid=".$dingdanid;
			$this->db->Execute($sql);
			$this->updatesellplanmainfahuo($dingdanid);
		}
	}
	//更新订单表的发货状态
	function updatesellplanmainfahuo($dingdanid)
	{
		$sellplaninfo=returntablefield("sellplanmain", "billid", $dingdanid, "fahuostate,totalmoney,ifpay,huikuanjine,oddment,fahuojine,kaipiaostate,kaipiaojine,user_flag,billtype");

		$fahuojine=$sellplaninfo['fahuojine'];
		$fahuostate=$sellplaninfo['fahuostate'];
		$totalmoney=$sellplaninfo['totalmoney'];
		$sql="select sum(jine) as tuihuojine from sellplanmain_detail where num<0 and mainrowid=$dingdanid";
		$rs = $this->db->Execute($sql);
		$rs_a = $rs->GetArray();
		if($rs_a[0]['tuihuojine']<0)
			$totalmoney=$totalmoney-$rs_a[0]['tuihuojine'];
		if($fahuostate>-1)
		{
			if($totalmoney==$fahuojine)
				$fahuostate=4;	//发货状态=全部
			else
			{
				$billid=returntablefield("stockoutmain", "dingdanbillid", $dingdanid, "billid","state","未出库");
				if($billid!='')
					$fahuostate=1;//发货状态=待出库
				else 
				{
					$billid=returntablefield("fahuodan", "dingdanbillid", $dingdanid, "billid","state","未发货");
					if($billid!='')
						$fahuostate=2;//发货状态=需发货
					else 
					{
						if($fahuojine!=0)
							$fahuostate=3;	//发货状态=部分
						else
							$fahuostate=0;
					}
				}
			}
		}
		
		$sql="update sellplanmain set fahuostate=$fahuostate where billid=$dingdanid";
		$this->db->Execute($sql);
		$CaiWu=new CaiWu($this->db);
		$CaiWu->updatesellplanmainFlag($dingdanid);
	}
	
	//订单出库，状态为=未出库
	function insertDingDanChuKu($dingdanbillid,$storeid,$allnum,$allmoney)
	{
		//获取入库单号
		$billid = returnAutoIncrement("billid","stockoutmain");
		$zhuti=returntablefield("sellplanmain", "billid", $dingdanbillid, "zhuti");
		$sql = "select * from sellplanmain_detail where mainrowid=".$dingdanbillid;
		$rs = $this->db->Execute($sql);
		$rs_detail = $rs->GetArray();
		$stockaccess="销售出库";
		
		//插入新入库单
		$sql = "insert into stockoutmain (billid,zhuti,storeid,createman,createtime,dingdanbillid,state,totalnum,totalmoney,outtype) values(".
		$billid.",'".$zhuti."',".$storeid.",'".$_SESSION['LOGIN_USER_ID']."','".date("Y-m-d H:i:s")."',"
		.$dingdanbillid.",'未出库',$allnum,$allmoney,'$stockaccess')";
		$this->db->Execute($sql);

		$sql = "select * from sellplanmain_detail where chukunum<>num and mainrowid=".$dingdanbillid;
		$rs = $this->db->Execute($sql);
		$rs_detail = $rs->GetArray();
		for($i=0;$i<sizeof($rs_detail);$i++)
		{
				
			if($rs_detail[$i]['num']==0)
			continue;
			$sql="select max(id) as maxid from stockoutmain_detail";
			$rs = $this->db->Execute($sql);
			$rs_a=$rs->GetArray();
			$maxid=$rs_a[0]['maxid']+1;
			$sql = "insert into stockoutmain_detail (id,prodid,prodname,prodguige,prodxinghao,proddanwei,price,zhekou,num,mainrowid,jine) values('$maxid','".
			$rs_detail[$i]['prodid']."','".$rs_detail[$i]['prodname']."','".$rs_detail[$i]['prodguige']."','".$rs_detail[$i]['prodxinghao'].
	    	"','".$rs_detail[$i]['proddanwei']."',".$rs_detail[$i]['price'].",".$rs_detail[$i]['zhekou'].",".(intval($rs_detail[$i]['num'])-intval($rs_detail[$i]['chukunum'])).",".$billid.",".$rs_detail[$i]['jine'].")";
			$this->db->Execute($sql);
			$sql = "select * from sellplanmain_detail_color where id=".$rs_detail[$i]['id'];
			$rs = $this->db->Execute($sql);
			$rs_detail_color = $rs->GetArray();
			foreach ($rs_detail_color as $row)
			{
				$sql = "select sum(num) as allnum from stockoutmain_detail_color where color=".$row['color']." and id in (select distinct id from stockoutmain_detail a inner join stockoutmain b on a.mainrowid=b.billid where b.dingdanbillid=$dingdanbillid)";
				$rs = $this->db->Execute($sql);
				$rs_b = $rs->GetArray();
			
				$sql = "insert into stockoutmain_detail_color values($maxid,".$row['color'].",".($row['num']-$rs_b[0]['allnum']).")";
				$this->db->Execute($sql);
			}
				
		}
		
		$this->updatesellplanmainfahuo($dingdanbillid);
	}
	/*
	//调拨出库，状态为=未出库
	function insertDiaoboChuKu($diaoboBillid)
	{
		//获取入库单号
		$billid = returnAutoIncrement("billid","stockoutmain");
		$diaoboInfo=returntablefield("stockchangemain","billid", $diaoboBillid, "zhuti,outstoreid");
		$zhuti=$diaoboInfo['zhuti'];
		$sql = "select * from stockchangemain_detail where num>0 and mainrowid=".$dingdanbillid;
		$rs = $this->db->Execute($sql);
		$rs_detail = $rs->GetArray();
		$stockaccess="销售出库";
		if(sizeof($rs_detail)==0 && $allnum<0)
		{
			$stockaccess="销售退库";
		}
		//插入新入库单
		$sql = "insert into stockoutmain (billid,zhuti,storeid,createman,createtime,dingdanbillid,state,totalnum,totalmoney,outtype) values(".
		$billid.",'".$zhuti."',".$storeid.",'".$_SESSION['LOGIN_USER_ID']."','".date("Y-m-d H:i:s")."',"
		.$dingdanbillid.",'未出库',$allnum,$allmoney,'$stockaccess')";
		$this->db->Execute($sql);

		$sql = "select * from sellplanmain_detail where chukunum<>num and mainrowid=".$dingdanbillid;
		$rs = $this->db->Execute($sql);
		$rs_detail = $rs->GetArray();
		for($i=0;$i<sizeof($rs_detail);$i++)
		{
				
			if($rs_detail[$i]['num']==0)
			continue;
			$sql="select max(id) as maxid from stockoutmain_detail";
			$rs = $this->db->Execute($sql);
			$rs_a=$rs->GetArray();
			$maxid=$rs_a[0]['maxid']+1;
			$sql = "insert into stockoutmain_detail (id,prodid,prodname,prodguige,prodxinghao,proddanwei,price,zhekou,num,mainrowid,jine) values('$maxid','".
			$rs_detail[$i]['prodid']."','".$rs_detail[$i]['prodname']."','".$rs_detail[$i]['prodguige']."','".$rs_detail[$i]['prodxinghao'].
	    	"','".$rs_detail[$i]['proddanwei']."',".$rs_detail[$i]['price'].",".$rs_detail[$i]['zhekou'].",".$rs_detail[$i]['num'].",".$billid.",".$rs_detail[$i]['jine'].")";
			$this->db->Execute($sql);
				
		}
		$this->updatesellplanmainfahuo($dingdanbillid);
	}
	*/
	//采购单入库
	function insertCaiGouRuKu($rowid,$totalnum,$totalmoney,$storeid)
	{
		$sql = "select * from buyplanmain where billid=".$rowid;
		$rs = $this->db->Execute($sql);
		$rs_a = $rs->GetArray();

		if (count($rs_a) !=1)
			throw new Exception("单号不存在");

		$sql = "select * from buyplanmain_detail where num>0 and mainrowid=".$rowid;
		$rs = $this->db->Execute($sql);
		$rs_detail = $rs->GetArray();
		$billid='';
		for($i=0;$i<sizeof($rs_detail);$i++)
		{
			$num=$_POST["num_".$rs_detail[$i]['id']];
			//插入入库明细
			if($num!=0)
			{
				if($billid=='')
				{
					//获取入库单号
					$billid = returnAutoIncrement("billid","stockinmain");
					//插入新入库单
					$sql = "insert into stockinmain (billid,zhuti,storeid,createman,createtime,caigoubillid,state,totalnum,totalmoney,intype) values(".
					$billid.",'".$rs_a[0]['zhuti']."',".$storeid.",'".$_SESSION['LOGIN_USER_ID']."','".date("Y-m-d H:i:s")."',"
					.$rowid.",'未入库',$totalnum,$totalmoney,'采购入库')";
					$this->db->Execute($sql);
					$newbill=true;
				}
				$sql="select max(id) as maxid from stockinmain_detail";
				$rs = $this->db->Execute($sql);
				$rs_a=$rs->GetArray();
				$maxid=$rs_a[0]['maxid']+1;
				$sql = "insert into stockinmain_detail (id,prodid,prodname,prodguige,prodxinghao,proddanwei,price,zhekou,num,mainrowid,jine) values('$maxid','".
				$rs_detail[$i]['prodid']."','".$rs_detail[$i]['prodname']."','".$rs_detail[$i]['prodguige']."','".$rs_detail[$i]['prodxinghao'].
	    		"','".$rs_detail[$i]['proddanwei']."',".$rs_detail[$i]['price'].",".$rs_detail[$i]['zhekou'].",".$num.",".$billid.",".round($num*$rs_detail[$i]['price']*$rs_detail[$i]['zhekou'],2).")";
				$this->db->Execute($sql);
				//插入颜色表
				$sql = "select * from buyplanmain_tmp_color where id=".$rs_detail[$i]['id'];
				$rs = $this->db->Execute($sql);
				$rs_b = $rs->GetArray();
				for($j=0;$j<sizeof($rs_b);$j++)
				{
					$sql="insert into stockinmain_detail_color (id,color,num) values($maxid,".$rs_b[$j]['color'].",".$rs_b[$j]['num'].")";
					$this->db->Execute($sql);
					
				}
			}
		}
		//更新本次入库数和金额
		if($billid!='')
		{
			$sql="select sum(num) as allnum,sum(jine) as allmoney from stockinmain_detail where mainrowid=$billid";
			$rs = $this->db->Execute($sql);
			$rs_a=$rs->GetArray();
			$totalnum=$rs_a[0]['allnum'];
			$totalmoney=$rs_a[0]['allmoney'];
		
			$sql = "update stockinmain set totalnum=$totalnum,totalmoney=$totalmoney where billid=$billid";
			$this->db->Execute($sql);
		}
		$sql = "select * from buyplanmain_detail where num<0 and mainrowid=".$rowid;
		$rs = $this->db->Execute($sql);
		$rs_detail = $rs->GetArray();
		$billid='';
		for($i=0;$i<sizeof($rs_detail);$i++)
		{
			$num=$_POST["num_".$rs_detail[$i]['id']];
			//插入入库明细
			if($num!=0)
			{
				if($billid=='')
				{
					//获取出库单号
					$billid = returnAutoIncrement("billid","stockoutmain");
					//插入新出库单
					$sql = "insert into stockoutmain (billid,zhuti,storeid,createman,createtime,dingdanbillid,state,totalnum,totalmoney,outtype) values(".
					$billid.",'".$rs_a[0]['zhuti']."',".$storeid.",'".$_SESSION['LOGIN_USER_ID']."','".date("Y-m-d H:i:s")."',"
					.$rowid.",'未出库',$totalnum,$totalmoney,'返厂出库')";
					$this->db->Execute($sql);
					$newbill=true;
				}
				$sql="select max(id) as maxid from stockoutmain_detail";
				$rs = $this->db->Execute($sql);
				$rs_a=$rs->GetArray();
				$maxid=$rs_a[0]['maxid']+1;
				$sql = "insert into stockoutmain_detail (id,prodid,prodname,prodguige,prodxinghao,proddanwei,price,zhekou,num,mainrowid,jine) values('$maxid','".
				$rs_detail[$i]['prodid']."','".$rs_detail[$i]['prodname']."','".$rs_detail[$i]['prodguige']."','".$rs_detail[$i]['prodxinghao'].
	    		"','".$rs_detail[$i]['proddanwei']."',".$rs_detail[$i]['price'].",".$rs_detail[$i]['zhekou'].",".-$num.",".$billid.",".-round($num*$rs_detail[$i]['price']*$rs_detail[$i]['zhekou'],2).")";
				$this->db->Execute($sql);
				//插入颜色表
				$sql = "select * from buyplanmain_tmp_color where id=".$rs_detail[$i]['id'];
				$rs = $this->db->Execute($sql);
				$rs_b = $rs->GetArray();
				for($j=0;$j<sizeof($rs_b);$j++)
				{
					$sql="insert into stockoutmain_detail_color (id,color,num) values($maxid,".$rs_b[$j]['color'].",".-$rs_b[$j]['num'].")";
					$this->db->Execute($sql);
					
				}
			}
		}
		//更新本次出库数和金额
		if($billid!='')
		{
			$sql="select sum(num) as allnum,sum(jine) as allmoney from stockoutmain_detail where mainrowid=$billid";
			$rs = $this->db->Execute($sql);
			$rs_a=$rs->GetArray();
			$totalnum=$rs_a[0]['allnum'];
			$totalmoney=$rs_a[0]['allmoney'];
		
			$sql = "update stockoutmain set totalnum=$totalnum,totalmoney=$totalmoney where billid=$billid";
			$this->db->Execute($sql);
		}
		$sql = "delete from buyplanmain_tmp_color where id in (select id from buyplanmain_detail where mainrowid=".$rowid.")";
		$this->db->Execute($sql);
		//改变采购单状态
		$this->updatebuyplanmainfahuo($rowid);

	}
	function terminateCaiGou($rowid)
	{
		$sql = "select * from buyplanmain where billid=".$rowid;
		$rs = $this->db->Execute($sql);
		$rs_a = $rs->GetArray();
		$rukumoney=$rs_a[0]['rukumoney'];
		$paymoney=$rs_a[0]['paymoney'];
		$supplyid=$rs_a[0]['supplyid'];
		if (count($rs_a) !=1)
			throw new Exception("单号不存在");
		
		
		$sql="update buyplanmain set totalmoney=rukumoney,paymoney=rukumoney where billid=$rowid";
		$this->db->Execute($sql);
		$sql="update buyplanmain_detail set num=recnum,jine=recnum*zhekou*price where mainrowid=$rowid";
		$this->db->Execute($sql);
		//多付金额转为预付款
		
		if($paymoney>$rukumoney)
		{
			$jine=$paymoney-$rukumoney;
			$id = returnAutoIncrementUnitBillid("prepaybillid");
			$curchuzhi=floatvalue(returntablefield("supply", "rowid", $supplyid, "yufukuan"));
			$sql="insert into accessprepay (id,supplyid,linkmanid,curchuzhi,jine,accountid,createman,createtime,opertype,beizhu)
			values(".$id.",".$supplyid.",'',".$curchuzhi.",".$jine.",'','".$_SESSION['LOGIN_USER_ID']."','".date("Y-m-d H:i:s")."','预付货款','采购单 $rowid 多付的金额转预付款')";
			$this->db->Execute($sql);
			$sql="update supply set yufukuan=yufukuan+($jine) where rowid=".$supplyid;
			$this->db->Execute($sql);
		}
		
		//改变采购单状态
		$this->updatebuyplanmainfahuo($rowid);
		$CaiWu=new CaiWu($this->db);
		$CaiWu->updatebuyplanmainfukuan($rowid);

	}
	//更新采购表的入库状态
	function updatebuyplanmainfahuo($caigoudanid)
	{
		$buyplaninfo=returntablefield("buyplanmain", "billid", $caigoudanid, "state,totalmoney,ifpay,rukumoney,shoupiaostate,user_flag");
		$fahuostate=$buyplaninfo['state'];
		$ifpay=$buyplaninfo['ifpay'];
		$kaipiaostate=$buyplaninfo['shoupiaostate'];
		$rukumoney=$buyplaninfo['rukumoney'];
		$totalmoney=$buyplaninfo['totalmoney'];
		$user_flag=$buyplaninfo['user_flag'];
		if($totalmoney==$rukumoney)
			$fahuostate=5;	//入库状态=全部
		else 
		{
			$billid=returntablefield("stockinmain", "caigoubillid", $caigoudanid,"billid","state","未入库","intype","采购入库");
			$billid1=returntablefield("stockoutmain", "dingdanbillid", $caigoudanid,"billid","state","未出库","outtype","返厂出库");
			
			if($rukumoney!=0 && $billid=='' && $billid1=='')
				$fahuostate=4;	//入库状态=部分
			else
			{
				
				if($billid!='' || $billid1!='')
					$fahuostate=3;//待入库
				else 
				{
					$id=returntablefield("buyplanmain_detail", "mainrowid", $caigoudanid,"id");
					if($id!='')
						$fahuostate=2;//已录明细
					else 
						$fahuostate=1;  //需要
				}
			}
				
		}

		$sql="update buyplanmain set state=$fahuostate where billid=$caigoudanid";
		$this->db->Execute($sql);
		$CaiWu=new CaiWu($this->db);
		$CaiWu->updatebuyplanmainFlag($caigoudanid);
	}

	//确认入库
	function confirmRuKu($rowid)
	{
		$stockinfo = returntablefield("stockinmain","billid",$rowid,"storeid,intype");
		$storeid=$stockinfo['storeid'];
		$intype=$stockinfo['intype'];
		$sql = "select * from stockinmain_detail where mainrowid=".$rowid;
		$rs = $this->db->Execute($sql);
		$rs_detail = $rs->GetArray();
		$nokucunjine=0;
		for($i=0;$i<sizeof($rs_detail);$i++)
		{
			$ifkucun=returntablefield("product","productid",$rs_detail[$i]['prodid'],"ifkucun");
			if($ifkucun=="否")
			{
				$nokucunjine=$nokucunjine+($rs_detail[$i]['price']*$rs_detail[$i]['zhekou']*$rs_detail[$i]['num']);
				continue;
			}
			$sql = "select * from store where storeid=".$storeid." and prodid='".$rs_detail[$i]['prodid']."'";
			$rs = $this->db->Execute($sql);
			$rs_store = $rs->GetArray();
			$kucun=0;
			if(sizeof($rs_store)>0)
			$kucun=$rs_store[0]['num'];
			if($kucun+$rs_detail[$i]['num']<0)
			throw new Exception("编号为：".$rs_detail[$i]['prodid']." 的产品库存不足");
			$maxid=0;	
			if(sizeof($rs_store)==0)
			{
				$maxid = returnAutoIncrement("id","store");
				$sql = "insert into store (id,prodid,storeid,num,price) values($maxid,'".
				$rs_detail[$i]['prodid']."',".$storeid.",".$rs_detail[$i]['num'].",".$rs_detail[$i]['price']*$rs_detail[$i]['zhekou'].")";
				$this->db->Execute($sql);
				
			}
			else
			{
				$maxid=$rs_store[0]['id'];
				if($rs_store[0]['num']+$rs_detail[$i]['num']!=0)
					$junjia=round(($rs_store[0]['price']*$rs_store[0]['num']+$rs_detail[$i]['price']*$rs_detail[$i]['zhekou']*$rs_detail[$i]['num'])/($rs_store[0]['num']+$rs_detail[$i]['num']),2);
				else
				{
					if($rs_store[0]['price']*$rs_store[0]['num']+$rs_detail[$i]['price']*$rs_detail[$i]['zhekou']*$rs_detail[$i]['num']==0)
					$junjia=0;
					else
					throw new Exception("编号为：".$rs_detail[$i]['prodid']." 的产品入库后库存数量为零，但金额不为零，无法计算加权平均价");
				}
					
				$sql = "update store set num=num+(".$rs_detail[$i]['num']."),price=".$junjia." where id=$maxid";
				$this->db->Execute($sql);
			}
			//颜色处理
			$sql = "select * from stockinmain_detail_color where id=".$rs_detail[$i]['id'];
			$rs = $this->db->Execute($sql);
			$rs_b = $rs->GetArray();
			for($j=0;$j<sizeof($rs_b);$j++)
			{
				$sql = "select * from store_color where id=$maxid and color=".$rs_b[$j]['color'];
				$rs = $this->db->Execute($sql);
				$rs_c = $rs->GetArray();
				if(sizeof($rs_c)==0)
					$sql="insert into store_color (id,color,num) values($maxid,".$rs_b[$j]['color'].",".$rs_b[$j]['num'].")";
				else 
					$sql="update store_color set num=num+(".$rs_b[$j]['num'].") where id=$maxid and color=".$rs_b[$j]['color'];
				$this->db->Execute($sql);
				
			}
			//print $sql;exit;
				
		}
		$sql="delete from store where num=0";
		$this->db->Execute($sql);

		//更新入库单状态
		$sql = "update stockinmain set state='已入库',indate='".date("Y-m-d H:i:s")."',instoreshenhe='".$_SESSION['LOGIN_USER_ID']."' where billid=".$rowid;
		$this->db->Execute($sql);

		//不及库存产品生成费用单
		if($nokucunjine!=0)
		{
			$feiyongbillid = returnAutoIncrementUnitBillid("feiyongbillid");
			$feiyongtype=returntablefield("feiyongtype", "typename", "不计库存产品采购费", "id");
			$sql="insert into feiyongrecord (billid,typeid,jine,accountid,chanshengdate,createman,createtime,kind) values($feiyongbillid,$feiyongtype,$nokucunjine,1,'".date("Y-m-d")."','".$_SESSION['LOGIN_USER_ID']."','".date("Y-m-d H:i:s")."',-1)";
			$this->db->Execute($sql);
		}
		//更新采购单状态
		$caigoubillid=returntablefield("stockinmain","billid",$rowid,"caigoubillid");
		
		if($intype=='采购入库')
			$this->UpdateCaigouState($caigoubillid);
		else if($intype=='退货入库')
			$this->updatesellplanmainfahuo($caigoubillid);
		
	}

	//更新采购单已入库金额
	function UpdateCaigouState($caigoubillid)
	{
		$sql= "select sum(totalmoney) as allmoney from stockinmain where state='已入库' and intype='采购入库' and caigoubillid=".$caigoubillid;
		$rs=$this->db->Execute($sql);
		$rs_a = $rs->GetArray();
		$ruku=$rs_a[0]['allmoney'];
		$sql= "select sum(totalmoney) as allmoney from stockoutmain where state='已出库' and outtype='返厂出库' and dingdanbillid=".$caigoubillid;
		$rs=$this->db->Execute($sql);
		$rs_a = $rs->GetArray();
		$chuku=$rs_a[0]['allmoney'];
		
		
		$sql = "update buyplanmain set rukumoney=".floatvalue($ruku-$chuku)." where billid=".$caigoubillid;
		$this->db->Execute($sql);
		//更新采购单明细
		$sql="select * from buyplanmain_detail where mainrowid=$caigoubillid";
		$rs = $this->db->Execute($sql);
		$rs_detail = $rs->GetArray();
		for($i=0;$i<sizeof($rs_detail);$i++)
		{
			$sql= "select sum(a.num) as allnum from stockinmain_detail a inner join stockinmain b on a.mainrowid=b.billid where b.state='已入库' and b.intype='采购入库' and b.caigoubillid=$caigoubillid and a.prodid='".$rs_detail[$i]['prodid']."'";
			$rs = $this->db->Execute($sql);
			$rs_sum = $rs->GetArray();
			$ruku=$rs_sum[0]['allnum'];
			$sql= "select sum(a.num) as allnum from stockoutmain_detail a inner join stockoutmain b on a.mainrowid=b.billid where b.state='已出库' and b.outtype='返厂出库' and b.dingdanbillid=$caigoubillid and a.prodid='".$rs_detail[$i]['prodid']."'";
			$rs = $this->db->Execute($sql);
			$rs_sum = $rs->GetArray();
			$chuku=$rs_sum[0]['allnum'];
			$sql= "update buyplanmain_detail set recnum=".floatvalue($ruku-$chuku)." where prodid='".$rs_detail[$i]['prodid']."' and mainrowid=$caigoubillid";
			$this->db->Execute($sql);
		}
		$sql="select * from buyplanmain_detail where recnum>num and num>0 and mainrowid=$caigoubillid";
		$rs = $this->db->Execute($sql);
		$rs_a = $rs->GetArray();
		if(sizeof($rs_a)>0)
		{
			throw new Exception($rs_a[0]['prodid']."的入库数超过了采购数，请检查是否重复入库");
		}
		$this->updatebuyplanmainfahuo($caigoubillid);

	}
	
	function deleteRuKu($rukubillid)
	{
		$storeid = returntablefield("stockinmain","billid",$rukubillid,"storeid");
		$sql = "select * from stockinmain where billid=".$rukubillid;
		$rs = $this->db->Execute($sql);
		$rs_detail = $rs->GetArray();
		$intype=$rs_detail[0]['intype'];
		if($rs_detail[0]['state']=='已入库')
		{
			//撤销库存
			$sql = "select * from stockinmain_detail where mainrowid=".$rukubillid;
			$rs = $this->db->Execute($sql);
			$rs_detail = $rs->GetArray();
			$nokucunjine=0;
			
			for($j=0;$j<sizeof($rs_detail);$j++)
			{
				
				$ifkucun=returntablefield("product","productid",$rs_detail[$j]['prodid'],"ifkucun");
				if($ifkucun=="否")
				{
					$nokucunjine=$nokucunjine+($rs_detail[$j]['price']*$rs_detail[$j]['zhekou']*$rs_detail[$j]['num']);
					continue;
				}
					
				$sql = "select * from store where storeid=".$storeid." and prodid='".$rs_detail[$j]['prodid']."'";
				$rs = $this->db->Execute($sql);
				$rs_store = $rs->GetArray();
				$kucun=0;
				$maxid=0;
				
				if(sizeof($rs_store)>0)
				{
					$kucun=$rs_store[0]['num'];
					$maxid=$rs_store[0]['id'];
				}
				if($kucun-$rs_detail[$j]['num']<0)
				{
					throw new Exception("编号为：".$rs_detail[$j]['prodid']." 的产品库存".$kucun."不足");
				}
					


				if($kucun-$rs_detail[$j]['num']!=0)
					$junjia=round(($rs_store[0]['price']*$kucun-$rs_detail[$j]['price']*$rs_detail[$j]['zhekou']*$rs_detail[$j]['num'])/($kucun-$rs_detail[$j]['num']),2);
				else
				{
					if($rs_store[0]['price']*$kucun-$rs_detail[$j]['price']*$rs_detail[$j]['zhekou']*$rs_detail[$j]['num']==0)
						$junjia=0;
					else
						throw new Exception("编号为：".$rs_detail[$j]['prodid']." 的产品撤销后库存数量为零，但金额不为零，无法计算加权平均价");
				}
					
				$sql = "update store set num=num-(".$rs_detail[$j]['num']."),price=".$junjia." where storeid=".$storeid." and prodid='".$rs_detail[$j]['prodid']."'";
				$this->db->Execute($sql);
				//颜色处理
				$sql = "select * from stockinmain_detail_color where id=".$rs_detail[$j]['id'];
				$rs = $this->db->Execute($sql);
				$rs_b = $rs->GetArray();
				for($m=0;$m<sizeof($rs_b);$m++)
				{
					$sql = "select * from store_color where id=$maxid and color=".$rs_b[$m]['color'];
					$rs = $this->db->Execute($sql);
					$rs_c = $rs->GetArray();
					$sql="update store_color set num=num-(".$rs_b[$m]['num'].") where id=$maxid and color=".$rs_b[$m]['color'];
					$this->db->Execute($sql);
				}
				

			}
			$sql="delete from store where num=0";
			$this->db->Execute($sql);
				
			//不及库存产品生成费用单
			if($nokucunjine!=0)
			{
				$feiyongbillid = returnAutoIncrementUnitBillid("feiyongbillid");
				$feiyongtype=returntablefield("feiyongtype", "typename", "不计库存产品采购费", "id");
				$sql="insert into feiyongrecord (billid,typeid,jine,accountid,chanshengdate,createman,createtime,kind) values($feiyongbillid,$feiyongtype,-$nokucunjine,1,'".date("Y-m-d")."','".$_SESSION['LOGIN_USER_ID']."','".date("Y-m-d H:i:s")."',-1)";
				$this->db->Execute($sql);
			}
		}
		$caigoubillid=returntablefield("stockinmain","billid",$rukubillid,"caigoubillid");
		$sql = "delete from stockinmain where billid=".$rukubillid;
		$this->db->Execute($sql);
		
		
		if($caigoubillid==0)
		{
			$sql="delete from store_init where storeid=$storeid and flag=1";
			$this->db->Execute($sql);
		}
		else 
		{
		
			if($intype=='采购入库')
				$this->UpdateCaigouState($caigoubillid);
			else if($intype=='退货入库')
				$this->updatesellplanmainfahuo($caigoubillid);
		}
		
	}
	
	//店面销售单出库
	function insertSellOneChuKu($billid,$zhuti,$storeid)
	{
		$createman=$_SESSION['LOGIN_USER_ID'];
		//出库
		$chukubillid=0;
		$sql="select sum(num) as num,sum(jine) as jine from sellplanmain_detail where num>0 and mainrowid=$billid";
		$rs=$this->db->Execute($sql);
		$rs_a=$rs->GetArray();
		$allnum=$rs_a[0]['num'];
		$allmoney=$rs_a[0]['jine'];
		if($allnum>0)
		{
			$chukubillid = returnAutoIncrement("billid","stockoutmain");
			//插入新出库单
			$sql = "insert into stockoutmain (billid,zhuti,storeid,createman,createtime,dingdanbillid,state,totalnum,totalmoney,outtype,outdate,outstoreshenhe) values(".
			$chukubillid.",'$zhuti',".$storeid.",'".$createman."','".date("Y-m-d H:i:s")."',"
			.$billid.",'已出库',$allnum,$allmoney,'销售出库','".date("Y-m-d H:i:s")."','$createman')";
			$this->db->Execute($sql);
			$sql = "select * from sellplanmain_detail where num>0 and mainrowid=".$billid;
			$rs = $this->db->Execute($sql);
			$rs_detail = $rs->GetArray();

			for($i=0;$i<sizeof($rs_detail);$i++)
			{
				$maxid = returnAutoIncrement("id","stockoutmain_detail");
				$sql = "insert into stockoutmain_detail (id,prodid,prodname,prodguige,prodxinghao,proddanwei,price,zhekou,num,mainrowid,jine) values('$maxid','".
				$rs_detail[$i]['prodid']."','".$rs_detail[$i]['prodname']."','".$rs_detail[$i]['prodguige']."','".$rs_detail[$i]['prodxinghao'].
		    	"','".$rs_detail[$i]['proddanwei']."',".$rs_detail[$i]['price'].",".$rs_detail[$i]['zhekou'].",".$rs_detail[$i]['num'].",".$chukubillid.",".$rs_detail[$i]['jine'].")";
				$this->db->Execute($sql);
	
				//扣减库存
				$storearray=returntablefield("product","productid",$rs_detail[$i]['prodid'],"ifkucun,hascolor");
				$ifkucun=$storearray['ifkucun'];
				$hascolor=$storearray['hascolor'];
				$chengben=0;
				if($ifkucun=="是")
				{
					$sql = "select * from store where storeid=".$storeid." and prodid='".$rs_detail[$i]['prodid']."'";
					$rs = $this->db->Execute($sql);
					$rs_store = $rs->GetArray();
					
					if(count($rs_store)==0)
					{
						throw new Exception($rs_detail[$i]['prodid']." 库存为0！");
					}
					else
					{
						
						if($rs_store[0]['num']-$rs_detail[$i]['num']<0)
							throw new Exception($rs_detail[$i]['prodid']." 库存不足！");
						$chengben=$rs_store[0]['price'];
						$sql = "update store set num=num-(".$rs_detail[$i]['num'].") where storeid=".$storeid." and prodid='".$rs_detail[$i]['prodid']."'";
						$this->db->Execute($sql);
						if($hascolor=="是")
						{
							$sql="select * from sellplanmain_detail_color where id=".$rs_detail[$i]['id'];
							$rs = $this->db->Execute($sql);
							$rs_color = $rs->GetArray();
							foreach ($rs_color as $row)
							{
								$kucun=returntablefield("store_color", "id", $rs_store[0]['id'], "num","color",$row['color']);
								if($kucun<$row['num'])
									throw new Exception($rs_detail[$i]['prodid']." 某个颜色的库存不足！");
								$sql = "update store_color set num=num-(".$row['num'].") where id=".$rs_store[0]['id']." and color='".$row['color']."'";
								$this->db->Execute($sql);
								$sql="insert into stockoutmain_detail_color values($maxid,".$row['color'].",".$row['num'].")";
								$this->db->Execute($sql);
							}
						}
					}
					
					
				}
				//更新出库明细
				$sql = "update stockoutmain_detail set avgprice=$chengben,lirun=round((price*zhekou-$chengben)*num,2) where mainrowid=".$chukubillid." and prodid='".$rs_detail[$i]['prodid']."'";
				$this->db->Execute($sql);
	
				$sql="update sellplanmain_detail set chukunum=num,lirun=round((price*zhekou-$chengben)*num,2) where mainrowid=".$billid." and prodid='".$rs_detail[$i]['prodid']."'";
				$this->db->Execute($sql);
			}
		}
		//入库
		$sql="select sum(num) as num,sum(jine) as jine from sellplanmain_detail where num<0 and mainrowid=$billid";
		$rs=$this->db->Execute($sql);
		$rs_a=$rs->GetArray();
		$allnum=$rs_a[0]['num'];
		$allmoney=$rs_a[0]['jine'];
		if($allnum<0)
		{
			$rukubillid = returnAutoIncrement("billid","stockinmain");
			//插入新入库单
			$sql = "insert into stockinmain(billid,zhuti,storeid,createman,createtime,caigoubillid,state,totalnum,totalmoney,indate,intype,instoreshenhe) values(".
			$rukubillid.",'$zhuti',".$storeid.",'".$createman."','".date("Y-m-d H:i:s")."',"
			.$billid.",'已入库',-$allnum,-$allmoney,'".date("Y-m-d H:i:s")."','退货入库','".$createman."')";
			$this->db->Execute($sql);
	
			$sql = "select * from sellplanmain_detail where num<0 and mainrowid=".$billid;
			$rs = $this->db->Execute($sql);
			$rs_detail = $rs->GetArray();
			for($i=0;$i<sizeof($rs_detail);$i++)
			{
				$maxid = returnAutoIncrement("id","stockinmain_detail");
				$rs_detail[$i]['num']=-$rs_detail[$i]['num'];
				$rs_detail[$i]['jine']=-$rs_detail[$i]['jine'];
				$sql = "insert into stockinmain_detail (id,prodid,prodname,prodguige,prodxinghao,proddanwei,price,zhekou,num,mainrowid,jine) values('$maxid','".
				$rs_detail[$i]['prodid']."','".$rs_detail[$i]['prodname']."','".$rs_detail[$i]['prodguige']."','".$rs_detail[$i]['prodxinghao'].
		    	"','".$rs_detail[$i]['proddanwei']."',".$rs_detail[$i]['price'].",".$rs_detail[$i]['zhekou'].",".$rs_detail[$i]['num'].",".$rukubillid.",".$rs_detail[$i]['jine'].")";
				$this->db->Execute($sql);
	
				//增加库存
				$storearray=returntablefield("product","productid",$rs_detail[$i]['prodid'],"ifkucun,hascolor");
				$ifkucun=$storearray['ifkucun'];
				$hascolor=$storearray['hascolor'];
				$chengben=0;
				if($ifkucun=="是")
				{
					$sql = "select * from store where storeid=".$storeid." and prodid='".$rs_detail[$i]['prodid']."'";
					$rs = $this->db->Execute($sql);
					$rs_store = $rs->GetArray();
					
					if(count($rs_store)==0)
					{
							
							$chengben=$rs_detail[$i]['price']*$rs_detail[$i]['zhekou'];
							$maxid=returnAutoIncrement("id", "store");
							$sql="insert into store (id,prodid,num,price,storeid) values($maxid,'".$rs_detail[$i]['prodid']."',".-$rs_detail[$i]['num'].",".$chengben.",".$storeid.")";
							$this->db->Execute($sql);
								
					}
					else
					{
						
						$chengben=round(($rs_store[0]['price']*$rs_store[0]['num']+$rs_detail[$i]['num']*$rs_detail[$i]['zhekou']*$rs_detail[$i]['price'])/($rs_store[0]['num']+$rs_detail[$i]['num']),2);
						$sql = "update store set num=num+(".$rs_detail[$i]['num']."),price=$chengben where storeid=".$storeid." and prodid='".$rs_detail[$i]['prodid']."'";
						$this->db->Execute($sql);
					}
					if($hascolor=="是")
					{
						$sql="select * from sellplanmain_detail_color where id=".$rs_detail[$i]['id'];
						$rs = $this->db->Execute($sql);
						$rs_color = $rs->GetArray();
						foreach ($rs_color as $row)
						{
							$kucun=returntablefield("store_color", "id", $rs_store[0]['id'], "num","color",$row['color']);
							if($kucun=='')
								$sql ="insert store_color values(".$rs_store[0]['id'].",".$row['color'].",".$row['num'].")";
							else
								$sql = "update store_color set num=num-(".$row['num'].") where id=".$rs_store[0]['id']." and color='".$row['color']."'";
							$this->db->Execute($sql);
							$sql="insert into stockinmain_detail_color values($maxid,".$row['color'].",".-$row['num'].")";
							$this->db->Execute($sql);
						}
					}
					
				}
	
				$sql="update sellplanmain_detail set chukunum=num,lirun=0 where mainrowid=".$billid." and prodid='".$rs_detail[$i]['prodid']."'";
				$this->db->Execute($sql);
			}
		}
		$this->updatesellplanmainfahuo($billid);
		return $chukubillid;

	}
	//撤销出库
	function deleteChuKu($chukubillid)
	{
		$stockoutinfo=returntablefield("stockoutmain","billid",$chukubillid,"dingdanbillid,storeid,state,outtype");
		$dingdanbillid=$stockoutinfo['dingdanbillid'];
		$storeid=$stockoutinfo['storeid'];
		$state=$stockoutinfo['state'];
		$outtype=$stockoutinfo['outtype'];
	
		if($state=='已出库')
		{
			//删除发货单
			$sql = "select * from fahuodan where billid=".$chukubillid;
			$rs = $this->db->Execute($sql);
			$rs_a = $rs->GetArray();
			if(count($rs_a)>0)
			{
				if($rs_a[0]['state']=='已发货')
				throw new Exception("出库单：".$chukubillid." 已发货，请先撤销发货单！");
				$sql = "delete from fahuodan where billid=".$chukubillid;
				$this->db->Execute($sql);
			}
			//撤销出库
			$sql = "select * from stockoutmain_detail where mainrowid=".$chukubillid;
			$rs = $this->db->Execute($sql);
			$rs_detail = $rs->GetArray();
			for($j=0;$j<sizeof($rs_detail);$j++)
			{

				$tmpArray=returntablefield("product","productid",$rs_detail[$j]['prodid'],"ifkucun,hascolor");
				$ifkucun=$tmpArray['ifkucun'];
				$hascolor=$tmpArray['hascolor'];
				if($ifkucun=="是")
				{
					$sql = "select * from store where storeid=".$storeid." and prodid='".$rs_detail[$j]['prodid']."'";
					$rs = $this->db->Execute($sql);
					$rs_store = $rs->GetArray();
					$kucun=0;
					if(sizeof($rs_store)>0)
					{
						$kucun=$rs_store[0]['num'];
						if($kucun+$rs_detail[$j]['num']<0)
							throw new Exception($rs_detail[$j]['prodid']." 库存不足！");
						if($kucun+$rs_detail[$j]['num']!=0)
							$junjia=round(($rs_store[0]['price']*$kucun+$rs_detail[$j]['avgprice']*$rs_detail[$j]['num'])/($kucun+$rs_detail[$j]['num']),2);
						else
						{
							if($rs_store[0]['price']*$kucun+$rs_detail[$j]['avgprice']*$rs_detail[$j]['num']==0)
								$junjia=0;
							else
								throw new Exception("编号为：".$rs_detail[$j]['prodid']." 的产品撤销后库存数量为零，但金额不为零，无法计算加权平均价");
						}
						$sql = "update store set num=num+(".$rs_detail[$j]['num']."),price=".$junjia." where storeid=".$storeid." and prodid='".$rs_detail[$j]['prodid']."'";
						$this->db->Execute($sql);
					}	
					else 
					{
						if($rs_detail[$j]['num']<0)
							throw new Exception($rs_detail[$j]['prodid']." 库存不足！");
						else 
						{
							$chengben=$rs_detail[$j]['avgprice'];
							$maxid=returnAutoIncrement("id", "store");
							$sql="insert into store (id,prodid,num,price,storeid) values($maxid,'".$rs_detail[$j]['prodid']."',".$rs_detail[$j]['num'].",".$chengben.",".$storeid.")";
							$this->db->Execute($sql);
							$rs_store[0]['id']=$maxid;
						}
					}
					if($hascolor=="是")
					{
						$sql="select * from stockoutmain_detail_color where id=".$rs_detail[$j]['id'];
						$rs = $this->db->Execute($sql);
						$rs_color = $rs->GetArray();
						foreach ($rs_color as $row)
						{
							
							$sql="select num from store_color where id=".$rs_store[0]['id']." and color=".$row['color'];
							$rs = $this->db->Execute($sql);
							$rs_store_color = $rs->GetArray();
							if(sizeof($rs_store_color)==0)
							{
								$sql="insert store_color (id,color,num) values(".$rs_store[0]['id'].",".$row['color'].",".$row['num'].")";
							}
							else 
							{
								$sql = "update store_color set num=num+(".$row['num'].") where id=".$rs_store[0]['id']." and color='".$row['color']."'";
							}
							$this->db->Execute($sql);
						}
					}
					
				}
				if($outtype=='销售出库')
				{
					//更新订单明细
					$sql="update sellplanmain_detail set chukunum=chukunum-(".$rs_detail[$j]['num']."),lirun=lirun-(".$rs_detail[$j]['lirun'].") where mainrowid=$dingdanbillid and prodid='".$rs_detail[$j]['prodid']."'";
					$this->db->Execute($sql);
				}
			}
			$sql="delete from store where num=0";
			$this->db->Execute($sql);
			
		}
		$sql = "delete from stockoutmain where billid=".$chukubillid;
		$this->db->Execute($sql);
		if($outtype=='销售出库')
			$this->updatesellplanmainfahuo($dingdanbillid);
		else if($outtype=='返厂出库')
			$this->UpdateCaigouState($dingdanbillid);
		
			
	}
	//确认出库
	function confirmChuKu($chukubillid)
	{
		$stockoutinfo=returntablefield("stockoutmain","billid",$chukubillid,"dingdanbillid,storeid,state,outtype");
		$dingdanbillid=$stockoutinfo['dingdanbillid'];
		$storeid=$stockoutinfo['storeid'];
		$outtype=$stockoutinfo['outtype'];
		$sql = "select * from stockoutmain_detail where mainrowid=".$chukubillid;
		$rs = $this->db->Execute($sql);
		$rs_detail = $rs->GetArray();
		for($i=0;$i<sizeof($rs_detail);$i++)
		{
			if($outtype=='销售出库')
				$num=$_POST["recnum_".$rs_detail[$i]['id']];
			else 
				$num=$rs_detail[$i]['num'];
			
			if( $num>$rs_detail[$i]['num'])
				throw  new Exception("产品【".$rs_detail[$i]['prodid']."】的出库数不能大于".$rs_detail[$i]['num']);
			
			
			//扣减库存
			$tmpArray=returntablefield("product","productid",$rs_detail[$i]['prodid'],"ifkucun,hascolor");
			$ifkucun=$tmpArray['ifkucun'];
			$hascolor=$tmpArray['hascolor'];
			if($ifkucun=="是" && $num!=0)
			{
				$sql = "select * from store where storeid=".$storeid." and prodid='".$rs_detail[$i]['prodid']."'";
				$rs = $this->db->Execute($sql);
				$rs_store = $rs->GetArray();
				$kucun=0;
				if(sizeof($rs_store)>0)
					$kucun=$rs_store[0]['num'];
				
				if($kucun-$num<0)
					throw  new Exception("产品【".$rs_detail[$i]['prodname']."】的库存不足");
				
				$chengben=$rs_store[0]['price'];
				$sql = "update store set num=num-(".$num.") where storeid=".$storeid." and prodid='".$rs_detail[$i]['prodid']."'";
				$this->db->Execute($sql);
				if($hascolor=="是")
				{
					$sql="select * from stockoutmain_detail_color where id=".$rs_detail[$i]['id'];
					$rs = $this->db->Execute($sql);
					$rs_color = $rs->GetArray();
					foreach ($rs_color as $row)
					{
						$kucun=returntablefield("store_color", "id", $rs_store[0]['id'], "num","color",$row['color']);
						if($kucun<$row['num'])
							throw new Exception($rs_detail[$i]['prodid']." 某个颜色的库存不足！");
						$sql = "update store_color set num=num-(".$row['num'].") where id=".$rs_store[0]['id']." and color='".$row['color']."'";
						$this->db->Execute($sql);
					}
				}
			}
			else
				$chengben=0;
			//更新出库明细
			if($num!=0)
				$sql = "update stockoutmain_detail set num=$num,avgprice=$chengben,lirun=round((price*zhekou-$chengben)*$num,2) where id=".$rs_detail[$i]['id'];
			else
				$sql = "delete from stockoutmain_detail where id=".$rs_detail[$i]['id'];
			$this->db->Execute($sql);
			
			if($outtype=='销售出库')
			{
				//取得利润
				$sql="select lirun from stockoutmain_detail where id=".$rs_detail[$i]['id'];
				$rs = $this->db->Execute($sql);
				$rs_store = $rs->GetArray();
				$lirun=0;
				if(sizeof($rs_store)>0)
				$lirun=$rs_store[0]['lirun'];
				//更新订单明细
				$sql="update sellplanmain_detail set chukunum=chukunum+$num,lirun=lirun+$lirun where mainrowid=$dingdanbillid and prodid='".$rs_detail[$i]['prodid']."'";
				$this->db->Execute($sql);
			}
				
		}
		$sql="delete from store where num=0";
		$this->db->Execute($sql);

		$sql = "select sum(num) as allnum,sum(price*zhekou*num) as allmoney from stockoutmain_detail where mainrowid=".$chukubillid;
		$rs = $this->db->Execute($sql);
		$rs_all = $rs->GetArray();
		$allnum=$rs_all[0]['allnum'];
		$allmoney=round(floatval($rs_all[0]['allmoney']),2);
		//改变出库单状态
		$sql = "update stockoutmain set state='已出库',totalnum=$allnum,totalmoney=$allmoney,outstoreshenhe='".$_SESSION['LOGIN_USER_ID']."',outdate='".date("Y-m-d H:i:s")."' where billid=".$chukubillid;
		$this->db->Execute($sql);
		
		if($outtype=='返厂出库')
		{
			//利润变为收益或费用
			$sql="select sum(lirun) from stockoutmain_detail where mainrowid=".$dingdanbillid;
			$rs = $this->db->Execute($sql);
			$rs_store = $rs->GetArray();
			$lirun=$rs_store[0]['lirun'];
			if($lirun!=0)
			{
				$kind=1;
				$feiyongname='返货收益';
				$jine=-$lirun;
				if($lirun>0)
				{
					$kind=-1;
					$feiyongname='返货亏损';
					$jine=$lirun;
				}
				$feiyongbillid = returnAutoIncrementUnitBillid("feiyongbillid");
				$feiyongtype=returntablefield("feiyongtype", "typename", $feiyongname, "id");
				$sql="insert into feiyongrecord (billid,typeid,jine,accountid,chanshengdate,createman,createtime,kind) values($feiyongbillid,$feiyongtype,$jine,'','".date("Y-m-d")."','".$_SESSION['LOGIN_USER_ID']."','".date("Y-m-d H:i:s")."',$kind)";
				$this->db->Execute($sql);
			}
			
			$this->UpdateCaigouState($dingdanbillid);
		}
		

	}
	
	//合同交付
	function HeTongJiaoFu($customerid,$hetongid,$productid,$id,$num,$price,$jieshouren,$jiaofudate,$beizhu,$jine)
	{
		$sql="select max(id) as maxid from sellcontract_jiaofu";
		$rs=$this->db->Execute($sql);
		$rs_a=$rs->GetArray();
		$maxid=$rs_a[0]['maxid']+1;
		$sql="insert into sellcontract_jiaofu (id,customerid,hetongid,productid,planid,num,price,jieshouren,jiaofudate,beizhu,createman,createtime,jine)
		values($maxid,$customerid,$hetongid,'$productid',$id,$num,$price,'$jieshouren','$jiaofudate','$beizhu','".$_SESSION['LOGIN_USER_ID']."','".date('Y-m-d H:i:s')."',$jine)";
		$rs=$this->db->Execute($sql);
		$sql="update sellplanmain_detail set chukunum=chukunum+$num where id=$id";
		$rs=$this->db->Execute($sql);

		$this->updatehetongfahuo($hetongid);
	}
	//删除合同交付
	function deleteHeTongJiaoFu($selectid)
	{
		$planid=returntablefield("sellcontract_jiaofu", "id", $selectid, "planid");
		$sql="delete from sellcontract_jiaofu where id=$selectid";
		$rs=$this->db->Execute($sql);
		$sql="select sum(num) as allnum from sellcontract_jiaofu where planid=$planid";
		$rs=$this->db->Execute($sql);
		$rs_a = $rs->GetArray();
		$num=$rs_a[0]['allnum'];
		$sql="update sellplanmain_detail set chukunum='$num' where id=$planid";
		$rs=$this->db->Execute($sql);
		$hetongid=returntablefield("sellplanmain_detail", "id", $planid, "mainrowid");

		$this->updatehetongfahuo($hetongid);
	}
	//更新合同的交付状态
	function updatehetongfahuo($dingdanid)
	{
		$sql="select sum(jine) as jine from sellcontract_jiaofu where hetongid=$dingdanid";
		$rs=$this->db->Execute($sql);
		$rs_a = $rs->GetArray();
		$jine=floatvalue($rs_a[0]['jine']);
		$sql="update sellplanmain set fahuojine=$jine where billid=$dingdanid";
		$rs=$this->db->Execute($sql);

		$sellplaninfo=returntablefield("sellplanmain", "billid", $dingdanid, "fahuostate,totalmoney,ifpay,fahuojine,kaipiaostate,user_flag");
		$fahuostate=$sellplaninfo['fahuostate'];
		$ifpay=$sellplaninfo['ifpay'];
		$kaipiaostate=$sellplaninfo['kaipiaostate'];
		$fahuojine=$sellplaninfo['fahuojine'];
		$totalmoney=$sellplaninfo['totalmoney'];
		$user_flag=$sellplaninfo['user_flag'];
		if($totalmoney==$fahuojine)
		$fahuostate=4;	//发货状态=全部
		else if($totalmoney>$fahuojine)
		{
			if($fahuojine>0)
			$fahuostate=3;	//发货状态=部分
			else
			$fahuostate=0;  //发货状态=需发货
				
		}
		if($ifpay==2 && $fahuostate==4) //订单状态=完成
		$user_flag=2;
		else if($ifpay==0 && $fahuostate==0 && $kaipiaostate<=2) //订单状态=临时单
		$user_flag=0;
		else								//订单状态=执行中
		$user_flag=1;
		$sql="update sellplanmain set fahuostate=$fahuostate,user_flag=$user_flag where billid=$dingdanid";
		$this->db->Execute($sql);

	}
	//新增盘点单
	function insertStoreCheck($rowid,$allmoney)
	{
		$storeid = returntablefield("storecheck","billid",$rowid,"storeid");
		$sql = "select * from storecheck_detail where mainrowid=".$rowid;
		$rs = $this->db->Execute($sql);
		$rs_detail = $rs->GetArray();
		for($i=0;$i<sizeof($rs_detail);$i++)
		{

			$sql = "select * from store where storeid=".$storeid." and prodid='".$rs_detail[$i]['prodid']."'";

			$rs = $this->db->Execute($sql);
			$rs_store = $rs->GetArray();
			$kucun=0;
			if(sizeof($rs_store)>0)
			$kucun=$rs_store[0]['num'];
			if($kucun+$rs_detail[$i]['num']<0)
			throw new Exception("编号为：".$rs_detail[$i]['prodid']." 的产品库存不足");

			if(sizeof($rs_store)==0)
			{
				$maxid=returnAutoIncrement("id", "store");
				$sql = "insert into store (id,prodid,storeid,num,price) values($maxid,'".
				$rs_detail[$i]['prodid']."',".$storeid.",".$rs_detail[$i]['num'].",".$rs_detail[$i]['price'].")";
				$this->db->Execute($sql);
			}
			else
			{
				$junjia=$rs_store[0]['price'];
				$sql = "update store set num=num+(".$rs_detail[$i]['num'].") where storeid=".$storeid." and prodid='".$rs_detail[$i]['prodid']."'";
				$this->db->Execute($sql);
				$sql = "update storecheck_detail set price=".$junjia." where id=".$rs_detail[$i]['id'];
				$this->db->Execute($sql);

			}

		}
		$sql="delete from store where num=0";
		$this->db->Execute($sql);

		$sql="update storecheck set totalmoney=".$allmoney.",state='盘点结束' where billid=".$rowid;
		$this->db->Execute($sql);
		$sql="update storecheck_detail set jine=round(price*zhekou*num,2) where mainrowid=".$rowid;
		$this->db->Execute($sql);

	}
	//删除盘点单
	function deleteStoreCheck($selectid)
	{
		$storeid = returntablefield("storecheck","billid",$selectid,"storeid");
		$state=returntablefield("storecheck","billid",$selectid,"state");

		if($state=='盘点结束')
		{
			//撤销库存
			$sql = "select * from storecheck_detail where mainrowid=".$selectid;
			$rs = $this->db->Execute($sql);
			$rs_detail = $rs->GetArray();
			for($j=0;$j<sizeof($rs_detail);$j++)
			{

				$sql = "select * from store where storeid=".$storeid." and prodid='".$rs_detail[$j]['prodid']."'";
				$rs = $this->db->Execute($sql);
				$rs_store = $rs->GetArray();
				$kucun=0;
				if(sizeof($rs_store)>0)
				$kucun=$rs_store[0]['num'];
				if($kucun-$rs_detail[$j]['num']<0)
				throw new Exception("编号为：".$rs_detail[$j]['prodid']." 的产品库存不足");


				if($kucun-$rs_detail[$j]['num']!=0)
				$junjia=round(($rs_store[0]['price']*$kucun-$rs_detail[$j]['price']*$rs_detail[$j]['num'])/($kucun-$rs_detail[$j]['num']),2);
				else
				{
					if($rs_store[0]['price']*$kucun-$rs_detail[$j]['price']*$rs_detail[$j]['num']==0)
					$junjia=0;
					else
					throw new Exception("编号为：".$rs_detail[$j]['prodid']." 的产品撤销后库存数量为零，但金额不为零，无法计算加权平均价");
				}
					
				$sql = "update store set num=num-(".$rs_detail[$j]['num']."),price=".$junjia." where storeid=".$storeid." and prodid='".$rs_detail[$j]['prodid']."'";
				$this->db->Execute($sql);

				//print $sql;exit;

			}
			$sql="delete from store where num=0";
			$this->db->Execute($sql);
		}
		//删除盘点单
		$sql = "delete from storecheck where billid=".$selectid;
		$this->db->Execute($sql);
	}
	//更新出库数量
	function updateStockoutAmount($id,$recnum)
	{
		$sql="update stockoutmain_detail set num=$recnum where id=$id";
		$this->db->Execute($sql);
	}
}

?>