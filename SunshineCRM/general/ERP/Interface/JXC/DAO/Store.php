<?php
require_once('CaiWu.php');
class Store
{
	var $db;
	function __construct($db) {
		$this->db=&$db;
	}
	//����������
	function insertFaHuo($chukubillid)
	{
		$stockinfo=returntablefield("stockoutmain", "billid", $chukubillid, "dingdanbillid,totalnum,totalmoney,outtype");
		$dingdanid=$stockinfo['dingdanbillid'];
		$totalnum=$stockinfo['totalnum'];
		$totalmoney=$stockinfo['totalmoney'];
		$outtype=$stockinfo['outtype'];
		if($outtype=="���۳���")
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
		else if($outtype=="��������")
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
		throw  new Exception("��Ӧ�ķ������Ѵ���");
		$sql="insert into fahuodan (billid,customerid,dingdanbillid,shouhuoren,tel,address,state,totalnum,totalmoney,outtype) values("
		.$chukubillid.",".$customerid.",".$dingdanid.",'".$shouhuoren."','".$mobile."','".$address."','δ����',$totalnum,$totalmoney,'$outtype')";
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
		if($outtype=="���۳���")
			$this->updatesellplanmainfahuo($dingdanid);

	}
	//ȷ�Ϸ�����
	function confirmFaHuo($billid,$fahuodan,$shouhuoren,$address,$tel,$mailcode,$fahuotype,$package,$weight,$yunfei,$jiesuantype,$beizhu)
	{
		//���·�����
		$sql="update fahuodan set fahuodan='".$fahuodan."',shouhuoren='".$shouhuoren."',address='".$address."',tel='".$tel."',mailcode='".$mailcode.
		"',fahuotype='".$fahuotype."',package=".$package.",weight=".$weight.",yunfei=".$yunfei.",jiesuantype=".$jiesuantype.",beizhu='".$beizhu.
		"',fahuoren='".$_SESSION['LOGIN_USER_ID']."',fahuodate='".date("Y-m-d H:i:s")."',state='�ѷ���' where billid=".$billid;
		$this->db->Execute($sql);
		//���³��ⵥ״̬
		$sql="update stockoutmain set state='�ѷ���' where billid=".$billid;
		$this->db->Execute($sql);
		
		//���¶����������
		$outtype=returntablefield("fahuodan", "billid", $billid, "outtype");
		if($outtype=="���۳���")
		{
			$dingdanid=returntablefield("stockoutmain", "billid", $billid, "dingdanbillid");
			$jine=returntablefield("stockoutmain", "billid", $billid, "totalmoney");
			$sql="update sellplanmain set fahuojine=fahuojine+".$jine." where billid=".$dingdanid;
			$this->db->Execute($sql);
			$this->updatesellplanmainfahuo($dingdanid);
		}
		return $dingdanid;
	}
	//����������
	function cancelFaHuo($billid)
	{
		$outinfo=returntablefield("stockoutmain", "billid",$billid,"totalmoney,outtype");
		$jine=$outinfo['totalmoney'];
		$outtype=$outinfo['outtype'];
		//������
		$sql="update fahuodan set state='δ����' where billid=".$billid;
		$this->db->Execute($sql);
		//���³��ⵥ״̬
		$sql="update stockoutmain set state='�ѳ���' where billid=".$billid;
		$this->db->Execute($sql);
		$dingdanid=returntablefield("stockoutmain", "billid", $billid, "dingdanbillid");
		//���¶����������
		if($outtype=='���۳���')
		{
			$sql="update sellplanmain set fahuojine=round(fahuojine-".$jine.",2) where billid=".$dingdanid;
			$this->db->Execute($sql);
			$this->updatesellplanmainfahuo($dingdanid);
		}
	}
	//���¶�����ķ���״̬
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
				$fahuostate=4;	//����״̬=ȫ��
			else
			{
				$billid=returntablefield("stockoutmain", "dingdanbillid", $dingdanid, "billid","state","δ����");
				if($billid!='')
					$fahuostate=1;//����״̬=������
				else 
				{
					$billid=returntablefield("fahuodan", "dingdanbillid", $dingdanid, "billid","state","δ����");
					if($billid!='')
						$fahuostate=2;//����״̬=�跢��
					else 
					{
						if($fahuojine!=0)
							$fahuostate=3;	//����״̬=����
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
	
	//�������⣬״̬Ϊ=δ����
	function insertDingDanChuKu($dingdanbillid,$storeid,$allnum,$allmoney)
	{
		//��ȡ��ⵥ��
		$billid = returnAutoIncrement("billid","stockoutmain");
		$zhuti=returntablefield("sellplanmain", "billid", $dingdanbillid, "zhuti");
		$sql = "select * from sellplanmain_detail where mainrowid=".$dingdanbillid;
		$rs = $this->db->Execute($sql);
		$rs_detail = $rs->GetArray();
		$stockaccess="���۳���";
		
		//��������ⵥ
		$sql = "insert into stockoutmain (billid,zhuti,storeid,createman,createtime,dingdanbillid,state,totalnum,totalmoney,outtype) values(".
		$billid.",'".$zhuti."',".$storeid.",'".$_SESSION['LOGIN_USER_ID']."','".date("Y-m-d H:i:s")."',"
		.$dingdanbillid.",'δ����',$allnum,$allmoney,'$stockaccess')";
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
	//�������⣬״̬Ϊ=δ����
	function insertDiaoboChuKu($diaoboBillid)
	{
		//��ȡ��ⵥ��
		$billid = returnAutoIncrement("billid","stockoutmain");
		$diaoboInfo=returntablefield("stockchangemain","billid", $diaoboBillid, "zhuti,outstoreid");
		$zhuti=$diaoboInfo['zhuti'];
		$sql = "select * from stockchangemain_detail where num>0 and mainrowid=".$dingdanbillid;
		$rs = $this->db->Execute($sql);
		$rs_detail = $rs->GetArray();
		$stockaccess="���۳���";
		if(sizeof($rs_detail)==0 && $allnum<0)
		{
			$stockaccess="�����˿�";
		}
		//��������ⵥ
		$sql = "insert into stockoutmain (billid,zhuti,storeid,createman,createtime,dingdanbillid,state,totalnum,totalmoney,outtype) values(".
		$billid.",'".$zhuti."',".$storeid.",'".$_SESSION['LOGIN_USER_ID']."','".date("Y-m-d H:i:s")."',"
		.$dingdanbillid.",'δ����',$allnum,$allmoney,'$stockaccess')";
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
	//�ɹ������
	function insertCaiGouRuKu($rowid,$totalnum,$totalmoney,$storeid)
	{
		$sql = "select * from buyplanmain where billid=".$rowid;
		$rs = $this->db->Execute($sql);
		$rs_a = $rs->GetArray();

		if (count($rs_a) !=1)
			throw new Exception("���Ų�����");

		$sql = "select * from buyplanmain_detail where num>0 and mainrowid=".$rowid;
		$rs = $this->db->Execute($sql);
		$rs_detail = $rs->GetArray();
		$billid='';
		for($i=0;$i<sizeof($rs_detail);$i++)
		{
			$num=$_POST["num_".$rs_detail[$i]['id']];
			//���������ϸ
			if($num!=0)
			{
				if($billid=='')
				{
					//��ȡ��ⵥ��
					$billid = returnAutoIncrement("billid","stockinmain");
					//��������ⵥ
					$sql = "insert into stockinmain (billid,zhuti,storeid,createman,createtime,caigoubillid,state,totalnum,totalmoney,intype) values(".
					$billid.",'".$rs_a[0]['zhuti']."',".$storeid.",'".$_SESSION['LOGIN_USER_ID']."','".date("Y-m-d H:i:s")."',"
					.$rowid.",'δ���',$totalnum,$totalmoney,'�ɹ����')";
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
				//������ɫ��
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
		//���±���������ͽ��
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
			//���������ϸ
			if($num!=0)
			{
				if($billid=='')
				{
					//��ȡ���ⵥ��
					$billid = returnAutoIncrement("billid","stockoutmain");
					//�����³��ⵥ
					$sql = "insert into stockoutmain (billid,zhuti,storeid,createman,createtime,dingdanbillid,state,totalnum,totalmoney,outtype) values(".
					$billid.",'".$rs_a[0]['zhuti']."',".$storeid.",'".$_SESSION['LOGIN_USER_ID']."','".date("Y-m-d H:i:s")."',"
					.$rowid.",'δ����',$totalnum,$totalmoney,'��������')";
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
				//������ɫ��
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
		//���±��γ������ͽ��
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
		//�ı�ɹ���״̬
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
			throw new Exception("���Ų�����");
		
		
		$sql="update buyplanmain set totalmoney=rukumoney,paymoney=rukumoney where billid=$rowid";
		$this->db->Execute($sql);
		$sql="update buyplanmain_detail set num=recnum,jine=recnum*zhekou*price where mainrowid=$rowid";
		$this->db->Execute($sql);
		//�ึ���תΪԤ����
		
		if($paymoney>$rukumoney)
		{
			$jine=$paymoney-$rukumoney;
			$id = returnAutoIncrementUnitBillid("prepaybillid");
			$curchuzhi=floatvalue(returntablefield("supply", "rowid", $supplyid, "yufukuan"));
			$sql="insert into accessprepay (id,supplyid,linkmanid,curchuzhi,jine,accountid,createman,createtime,opertype,beizhu)
			values(".$id.",".$supplyid.",'',".$curchuzhi.",".$jine.",'','".$_SESSION['LOGIN_USER_ID']."','".date("Y-m-d H:i:s")."','Ԥ������','�ɹ��� $rowid �ึ�Ľ��תԤ����')";
			$this->db->Execute($sql);
			$sql="update supply set yufukuan=yufukuan+($jine) where rowid=".$supplyid;
			$this->db->Execute($sql);
		}
		
		//�ı�ɹ���״̬
		$this->updatebuyplanmainfahuo($rowid);
		$CaiWu=new CaiWu($this->db);
		$CaiWu->updatebuyplanmainfukuan($rowid);

	}
	//���²ɹ�������״̬
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
			$fahuostate=5;	//���״̬=ȫ��
		else 
		{
			$billid=returntablefield("stockinmain", "caigoubillid", $caigoudanid,"billid","state","δ���","intype","�ɹ����");
			$billid1=returntablefield("stockoutmain", "dingdanbillid", $caigoudanid,"billid","state","δ����","outtype","��������");
			
			if($rukumoney!=0 && $billid=='' && $billid1=='')
				$fahuostate=4;	//���״̬=����
			else
			{
				
				if($billid!='' || $billid1!='')
					$fahuostate=3;//�����
				else 
				{
					$id=returntablefield("buyplanmain_detail", "mainrowid", $caigoudanid,"id");
					if($id!='')
						$fahuostate=2;//��¼��ϸ
					else 
						$fahuostate=1;  //��Ҫ
				}
			}
				
		}

		$sql="update buyplanmain set state=$fahuostate where billid=$caigoudanid";
		$this->db->Execute($sql);
		$CaiWu=new CaiWu($this->db);
		$CaiWu->updatebuyplanmainFlag($caigoudanid);
	}

	//ȷ�����
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
			if($ifkucun=="��")
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
			throw new Exception("���Ϊ��".$rs_detail[$i]['prodid']." �Ĳ�Ʒ��治��");
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
					throw new Exception("���Ϊ��".$rs_detail[$i]['prodid']." �Ĳ�Ʒ����������Ϊ�㣬����Ϊ�㣬�޷������Ȩƽ����");
				}
					
				$sql = "update store set num=num+(".$rs_detail[$i]['num']."),price=".$junjia." where id=$maxid";
				$this->db->Execute($sql);
			}
			//��ɫ����
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

		//������ⵥ״̬
		$sql = "update stockinmain set state='�����',indate='".date("Y-m-d H:i:s")."',instoreshenhe='".$_SESSION['LOGIN_USER_ID']."' where billid=".$rowid;
		$this->db->Execute($sql);

		//��������Ʒ���ɷ��õ�
		if($nokucunjine!=0)
		{
			$feiyongbillid = returnAutoIncrementUnitBillid("feiyongbillid");
			$feiyongtype=returntablefield("feiyongtype", "typename", "���ƿ���Ʒ�ɹ���", "id");
			$sql="insert into feiyongrecord (billid,typeid,jine,accountid,chanshengdate,createman,createtime,kind) values($feiyongbillid,$feiyongtype,$nokucunjine,1,'".date("Y-m-d")."','".$_SESSION['LOGIN_USER_ID']."','".date("Y-m-d H:i:s")."',-1)";
			$this->db->Execute($sql);
		}
		//���²ɹ���״̬
		$caigoubillid=returntablefield("stockinmain","billid",$rowid,"caigoubillid");
		
		if($intype=='�ɹ����')
			$this->UpdateCaigouState($caigoubillid);
		else if($intype=='�˻����')
			$this->updatesellplanmainfahuo($caigoubillid);
		
	}

	//���²ɹ����������
	function UpdateCaigouState($caigoubillid)
	{
		$sql= "select sum(totalmoney) as allmoney from stockinmain where state='�����' and intype='�ɹ����' and caigoubillid=".$caigoubillid;
		$rs=$this->db->Execute($sql);
		$rs_a = $rs->GetArray();
		$ruku=$rs_a[0]['allmoney'];
		$sql= "select sum(totalmoney) as allmoney from stockoutmain where state='�ѳ���' and outtype='��������' and dingdanbillid=".$caigoubillid;
		$rs=$this->db->Execute($sql);
		$rs_a = $rs->GetArray();
		$chuku=$rs_a[0]['allmoney'];
		
		
		$sql = "update buyplanmain set rukumoney=".floatvalue($ruku-$chuku)." where billid=".$caigoubillid;
		$this->db->Execute($sql);
		//���²ɹ�����ϸ
		$sql="select * from buyplanmain_detail where mainrowid=$caigoubillid";
		$rs = $this->db->Execute($sql);
		$rs_detail = $rs->GetArray();
		for($i=0;$i<sizeof($rs_detail);$i++)
		{
			$sql= "select sum(a.num) as allnum from stockinmain_detail a inner join stockinmain b on a.mainrowid=b.billid where b.state='�����' and b.intype='�ɹ����' and b.caigoubillid=$caigoubillid and a.prodid='".$rs_detail[$i]['prodid']."'";
			$rs = $this->db->Execute($sql);
			$rs_sum = $rs->GetArray();
			$ruku=$rs_sum[0]['allnum'];
			$sql= "select sum(a.num) as allnum from stockoutmain_detail a inner join stockoutmain b on a.mainrowid=b.billid where b.state='�ѳ���' and b.outtype='��������' and b.dingdanbillid=$caigoubillid and a.prodid='".$rs_detail[$i]['prodid']."'";
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
			throw new Exception($rs_a[0]['prodid']."������������˲ɹ����������Ƿ��ظ����");
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
		if($rs_detail[0]['state']=='�����')
		{
			//�������
			$sql = "select * from stockinmain_detail where mainrowid=".$rukubillid;
			$rs = $this->db->Execute($sql);
			$rs_detail = $rs->GetArray();
			$nokucunjine=0;
			
			for($j=0;$j<sizeof($rs_detail);$j++)
			{
				
				$ifkucun=returntablefield("product","productid",$rs_detail[$j]['prodid'],"ifkucun");
				if($ifkucun=="��")
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
					throw new Exception("���Ϊ��".$rs_detail[$j]['prodid']." �Ĳ�Ʒ���".$kucun."����");
				}
					


				if($kucun-$rs_detail[$j]['num']!=0)
					$junjia=round(($rs_store[0]['price']*$kucun-$rs_detail[$j]['price']*$rs_detail[$j]['zhekou']*$rs_detail[$j]['num'])/($kucun-$rs_detail[$j]['num']),2);
				else
				{
					if($rs_store[0]['price']*$kucun-$rs_detail[$j]['price']*$rs_detail[$j]['zhekou']*$rs_detail[$j]['num']==0)
						$junjia=0;
					else
						throw new Exception("���Ϊ��".$rs_detail[$j]['prodid']." �Ĳ�Ʒ������������Ϊ�㣬����Ϊ�㣬�޷������Ȩƽ����");
				}
					
				$sql = "update store set num=num-(".$rs_detail[$j]['num']."),price=".$junjia." where storeid=".$storeid." and prodid='".$rs_detail[$j]['prodid']."'";
				$this->db->Execute($sql);
				//��ɫ����
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
				
			//��������Ʒ���ɷ��õ�
			if($nokucunjine!=0)
			{
				$feiyongbillid = returnAutoIncrementUnitBillid("feiyongbillid");
				$feiyongtype=returntablefield("feiyongtype", "typename", "���ƿ���Ʒ�ɹ���", "id");
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
		
			if($intype=='�ɹ����')
				$this->UpdateCaigouState($caigoubillid);
			else if($intype=='�˻����')
				$this->updatesellplanmainfahuo($caigoubillid);
		}
		
	}
	
	//�������۵�����
	function insertSellOneChuKu($billid,$zhuti,$storeid)
	{
		$createman=$_SESSION['LOGIN_USER_ID'];
		//����
		$chukubillid=0;
		$sql="select sum(num) as num,sum(jine) as jine from sellplanmain_detail where num>0 and mainrowid=$billid";
		$rs=$this->db->Execute($sql);
		$rs_a=$rs->GetArray();
		$allnum=$rs_a[0]['num'];
		$allmoney=$rs_a[0]['jine'];
		if($allnum>0)
		{
			$chukubillid = returnAutoIncrement("billid","stockoutmain");
			//�����³��ⵥ
			$sql = "insert into stockoutmain (billid,zhuti,storeid,createman,createtime,dingdanbillid,state,totalnum,totalmoney,outtype,outdate,outstoreshenhe) values(".
			$chukubillid.",'$zhuti',".$storeid.",'".$createman."','".date("Y-m-d H:i:s")."',"
			.$billid.",'�ѳ���',$allnum,$allmoney,'���۳���','".date("Y-m-d H:i:s")."','$createman')";
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
	
				//�ۼ����
				$storearray=returntablefield("product","productid",$rs_detail[$i]['prodid'],"ifkucun,hascolor");
				$ifkucun=$storearray['ifkucun'];
				$hascolor=$storearray['hascolor'];
				$chengben=0;
				if($ifkucun=="��")
				{
					$sql = "select * from store where storeid=".$storeid." and prodid='".$rs_detail[$i]['prodid']."'";
					$rs = $this->db->Execute($sql);
					$rs_store = $rs->GetArray();
					
					if(count($rs_store)==0)
					{
						throw new Exception($rs_detail[$i]['prodid']." ���Ϊ0��");
					}
					else
					{
						
						if($rs_store[0]['num']-$rs_detail[$i]['num']<0)
							throw new Exception($rs_detail[$i]['prodid']." ��治�㣡");
						$chengben=$rs_store[0]['price'];
						$sql = "update store set num=num-(".$rs_detail[$i]['num'].") where storeid=".$storeid." and prodid='".$rs_detail[$i]['prodid']."'";
						$this->db->Execute($sql);
						if($hascolor=="��")
						{
							$sql="select * from sellplanmain_detail_color where id=".$rs_detail[$i]['id'];
							$rs = $this->db->Execute($sql);
							$rs_color = $rs->GetArray();
							foreach ($rs_color as $row)
							{
								$kucun=returntablefield("store_color", "id", $rs_store[0]['id'], "num","color",$row['color']);
								if($kucun<$row['num'])
									throw new Exception($rs_detail[$i]['prodid']." ĳ����ɫ�Ŀ�治�㣡");
								$sql = "update store_color set num=num-(".$row['num'].") where id=".$rs_store[0]['id']." and color='".$row['color']."'";
								$this->db->Execute($sql);
								$sql="insert into stockoutmain_detail_color values($maxid,".$row['color'].",".$row['num'].")";
								$this->db->Execute($sql);
							}
						}
					}
					
					
				}
				//���³�����ϸ
				$sql = "update stockoutmain_detail set avgprice=$chengben,lirun=round((price*zhekou-$chengben)*num,2) where mainrowid=".$chukubillid." and prodid='".$rs_detail[$i]['prodid']."'";
				$this->db->Execute($sql);
	
				$sql="update sellplanmain_detail set chukunum=num,lirun=round((price*zhekou-$chengben)*num,2) where mainrowid=".$billid." and prodid='".$rs_detail[$i]['prodid']."'";
				$this->db->Execute($sql);
			}
		}
		//���
		$sql="select sum(num) as num,sum(jine) as jine from sellplanmain_detail where num<0 and mainrowid=$billid";
		$rs=$this->db->Execute($sql);
		$rs_a=$rs->GetArray();
		$allnum=$rs_a[0]['num'];
		$allmoney=$rs_a[0]['jine'];
		if($allnum<0)
		{
			$rukubillid = returnAutoIncrement("billid","stockinmain");
			//��������ⵥ
			$sql = "insert into stockinmain(billid,zhuti,storeid,createman,createtime,caigoubillid,state,totalnum,totalmoney,indate,intype,instoreshenhe) values(".
			$rukubillid.",'$zhuti',".$storeid.",'".$createman."','".date("Y-m-d H:i:s")."',"
			.$billid.",'�����',-$allnum,-$allmoney,'".date("Y-m-d H:i:s")."','�˻����','".$createman."')";
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
	
				//���ӿ��
				$storearray=returntablefield("product","productid",$rs_detail[$i]['prodid'],"ifkucun,hascolor");
				$ifkucun=$storearray['ifkucun'];
				$hascolor=$storearray['hascolor'];
				$chengben=0;
				if($ifkucun=="��")
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
					if($hascolor=="��")
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
	//��������
	function deleteChuKu($chukubillid)
	{
		$stockoutinfo=returntablefield("stockoutmain","billid",$chukubillid,"dingdanbillid,storeid,state,outtype");
		$dingdanbillid=$stockoutinfo['dingdanbillid'];
		$storeid=$stockoutinfo['storeid'];
		$state=$stockoutinfo['state'];
		$outtype=$stockoutinfo['outtype'];
	
		if($state=='�ѳ���')
		{
			//ɾ��������
			$sql = "select * from fahuodan where billid=".$chukubillid;
			$rs = $this->db->Execute($sql);
			$rs_a = $rs->GetArray();
			if(count($rs_a)>0)
			{
				if($rs_a[0]['state']=='�ѷ���')
				throw new Exception("���ⵥ��".$chukubillid." �ѷ��������ȳ�����������");
				$sql = "delete from fahuodan where billid=".$chukubillid;
				$this->db->Execute($sql);
			}
			//��������
			$sql = "select * from stockoutmain_detail where mainrowid=".$chukubillid;
			$rs = $this->db->Execute($sql);
			$rs_detail = $rs->GetArray();
			for($j=0;$j<sizeof($rs_detail);$j++)
			{

				$tmpArray=returntablefield("product","productid",$rs_detail[$j]['prodid'],"ifkucun,hascolor");
				$ifkucun=$tmpArray['ifkucun'];
				$hascolor=$tmpArray['hascolor'];
				if($ifkucun=="��")
				{
					$sql = "select * from store where storeid=".$storeid." and prodid='".$rs_detail[$j]['prodid']."'";
					$rs = $this->db->Execute($sql);
					$rs_store = $rs->GetArray();
					$kucun=0;
					if(sizeof($rs_store)>0)
					{
						$kucun=$rs_store[0]['num'];
						if($kucun+$rs_detail[$j]['num']<0)
							throw new Exception($rs_detail[$j]['prodid']." ��治�㣡");
						if($kucun+$rs_detail[$j]['num']!=0)
							$junjia=round(($rs_store[0]['price']*$kucun+$rs_detail[$j]['avgprice']*$rs_detail[$j]['num'])/($kucun+$rs_detail[$j]['num']),2);
						else
						{
							if($rs_store[0]['price']*$kucun+$rs_detail[$j]['avgprice']*$rs_detail[$j]['num']==0)
								$junjia=0;
							else
								throw new Exception("���Ϊ��".$rs_detail[$j]['prodid']." �Ĳ�Ʒ������������Ϊ�㣬����Ϊ�㣬�޷������Ȩƽ����");
						}
						$sql = "update store set num=num+(".$rs_detail[$j]['num']."),price=".$junjia." where storeid=".$storeid." and prodid='".$rs_detail[$j]['prodid']."'";
						$this->db->Execute($sql);
					}	
					else 
					{
						if($rs_detail[$j]['num']<0)
							throw new Exception($rs_detail[$j]['prodid']." ��治�㣡");
						else 
						{
							$chengben=$rs_detail[$j]['avgprice'];
							$maxid=returnAutoIncrement("id", "store");
							$sql="insert into store (id,prodid,num,price,storeid) values($maxid,'".$rs_detail[$j]['prodid']."',".$rs_detail[$j]['num'].",".$chengben.",".$storeid.")";
							$this->db->Execute($sql);
							$rs_store[0]['id']=$maxid;
						}
					}
					if($hascolor=="��")
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
				if($outtype=='���۳���')
				{
					//���¶�����ϸ
					$sql="update sellplanmain_detail set chukunum=chukunum-(".$rs_detail[$j]['num']."),lirun=lirun-(".$rs_detail[$j]['lirun'].") where mainrowid=$dingdanbillid and prodid='".$rs_detail[$j]['prodid']."'";
					$this->db->Execute($sql);
				}
			}
			$sql="delete from store where num=0";
			$this->db->Execute($sql);
			
		}
		$sql = "delete from stockoutmain where billid=".$chukubillid;
		$this->db->Execute($sql);
		if($outtype=='���۳���')
			$this->updatesellplanmainfahuo($dingdanbillid);
		else if($outtype=='��������')
			$this->UpdateCaigouState($dingdanbillid);
		
			
	}
	//ȷ�ϳ���
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
			if($outtype=='���۳���')
				$num=$_POST["recnum_".$rs_detail[$i]['id']];
			else 
				$num=$rs_detail[$i]['num'];
			
			if( $num>$rs_detail[$i]['num'])
				throw  new Exception("��Ʒ��".$rs_detail[$i]['prodid']."���ĳ��������ܴ���".$rs_detail[$i]['num']);
			
			
			//�ۼ����
			$tmpArray=returntablefield("product","productid",$rs_detail[$i]['prodid'],"ifkucun,hascolor");
			$ifkucun=$tmpArray['ifkucun'];
			$hascolor=$tmpArray['hascolor'];
			if($ifkucun=="��" && $num!=0)
			{
				$sql = "select * from store where storeid=".$storeid." and prodid='".$rs_detail[$i]['prodid']."'";
				$rs = $this->db->Execute($sql);
				$rs_store = $rs->GetArray();
				$kucun=0;
				if(sizeof($rs_store)>0)
					$kucun=$rs_store[0]['num'];
				
				if($kucun-$num<0)
					throw  new Exception("��Ʒ��".$rs_detail[$i]['prodname']."���Ŀ�治��");
				
				$chengben=$rs_store[0]['price'];
				$sql = "update store set num=num-(".$num.") where storeid=".$storeid." and prodid='".$rs_detail[$i]['prodid']."'";
				$this->db->Execute($sql);
				if($hascolor=="��")
				{
					$sql="select * from stockoutmain_detail_color where id=".$rs_detail[$i]['id'];
					$rs = $this->db->Execute($sql);
					$rs_color = $rs->GetArray();
					foreach ($rs_color as $row)
					{
						$kucun=returntablefield("store_color", "id", $rs_store[0]['id'], "num","color",$row['color']);
						if($kucun<$row['num'])
							throw new Exception($rs_detail[$i]['prodid']." ĳ����ɫ�Ŀ�治�㣡");
						$sql = "update store_color set num=num-(".$row['num'].") where id=".$rs_store[0]['id']." and color='".$row['color']."'";
						$this->db->Execute($sql);
					}
				}
			}
			else
				$chengben=0;
			//���³�����ϸ
			if($num!=0)
				$sql = "update stockoutmain_detail set num=$num,avgprice=$chengben,lirun=round((price*zhekou-$chengben)*$num,2) where id=".$rs_detail[$i]['id'];
			else
				$sql = "delete from stockoutmain_detail where id=".$rs_detail[$i]['id'];
			$this->db->Execute($sql);
			
			if($outtype=='���۳���')
			{
				//ȡ������
				$sql="select lirun from stockoutmain_detail where id=".$rs_detail[$i]['id'];
				$rs = $this->db->Execute($sql);
				$rs_store = $rs->GetArray();
				$lirun=0;
				if(sizeof($rs_store)>0)
				$lirun=$rs_store[0]['lirun'];
				//���¶�����ϸ
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
		//�ı���ⵥ״̬
		$sql = "update stockoutmain set state='�ѳ���',totalnum=$allnum,totalmoney=$allmoney,outstoreshenhe='".$_SESSION['LOGIN_USER_ID']."',outdate='".date("Y-m-d H:i:s")."' where billid=".$chukubillid;
		$this->db->Execute($sql);
		
		if($outtype=='��������')
		{
			//�����Ϊ��������
			$sql="select sum(lirun) from stockoutmain_detail where mainrowid=".$dingdanbillid;
			$rs = $this->db->Execute($sql);
			$rs_store = $rs->GetArray();
			$lirun=$rs_store[0]['lirun'];
			if($lirun!=0)
			{
				$kind=1;
				$feiyongname='��������';
				$jine=-$lirun;
				if($lirun>0)
				{
					$kind=-1;
					$feiyongname='��������';
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
	
	//��ͬ����
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
	//ɾ����ͬ����
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
	//���º�ͬ�Ľ���״̬
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
		$fahuostate=4;	//����״̬=ȫ��
		else if($totalmoney>$fahuojine)
		{
			if($fahuojine>0)
			$fahuostate=3;	//����״̬=����
			else
			$fahuostate=0;  //����״̬=�跢��
				
		}
		if($ifpay==2 && $fahuostate==4) //����״̬=���
		$user_flag=2;
		else if($ifpay==0 && $fahuostate==0 && $kaipiaostate<=2) //����״̬=��ʱ��
		$user_flag=0;
		else								//����״̬=ִ����
		$user_flag=1;
		$sql="update sellplanmain set fahuostate=$fahuostate,user_flag=$user_flag where billid=$dingdanid";
		$this->db->Execute($sql);

	}
	//�����̵㵥
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
			throw new Exception("���Ϊ��".$rs_detail[$i]['prodid']." �Ĳ�Ʒ��治��");

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

		$sql="update storecheck set totalmoney=".$allmoney.",state='�̵����' where billid=".$rowid;
		$this->db->Execute($sql);
		$sql="update storecheck_detail set jine=round(price*zhekou*num,2) where mainrowid=".$rowid;
		$this->db->Execute($sql);

	}
	//ɾ���̵㵥
	function deleteStoreCheck($selectid)
	{
		$storeid = returntablefield("storecheck","billid",$selectid,"storeid");
		$state=returntablefield("storecheck","billid",$selectid,"state");

		if($state=='�̵����')
		{
			//�������
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
				throw new Exception("���Ϊ��".$rs_detail[$j]['prodid']." �Ĳ�Ʒ��治��");


				if($kucun-$rs_detail[$j]['num']!=0)
				$junjia=round(($rs_store[0]['price']*$kucun-$rs_detail[$j]['price']*$rs_detail[$j]['num'])/($kucun-$rs_detail[$j]['num']),2);
				else
				{
					if($rs_store[0]['price']*$kucun-$rs_detail[$j]['price']*$rs_detail[$j]['num']==0)
					$junjia=0;
					else
					throw new Exception("���Ϊ��".$rs_detail[$j]['prodid']." �Ĳ�Ʒ������������Ϊ�㣬����Ϊ�㣬�޷������Ȩƽ����");
				}
					
				$sql = "update store set num=num-(".$rs_detail[$j]['num']."),price=".$junjia." where storeid=".$storeid." and prodid='".$rs_detail[$j]['prodid']."'";
				$this->db->Execute($sql);

				//print $sql;exit;

			}
			$sql="delete from store where num=0";
			$this->db->Execute($sql);
		}
		//ɾ���̵㵥
		$sql = "delete from storecheck where billid=".$selectid;
		$this->db->Execute($sql);
	}
	//���³�������
	function updateStockoutAmount($id,$recnum)
	{
		$sql="update stockoutmain_detail set num=$recnum where id=$id";
		$this->db->Execute($sql);
	}
}

?>