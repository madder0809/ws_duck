<?php 
class CaiWu
{
	var $db;
	function __construct($db) {
       $this->db=&$db;
   }
	
	//���¶������״̬
	function updatesellplanmainhuikuan($dingdanid)
	{
		$sellplaninfo=returntablefield("sellplanmain", "billid", $dingdanid, "totalmoney,ifpay,huikuanjine,kaipiaojine,oddment,fahuostate,kaipiaostate,user_flag,billtype");
		$huikuanjine=$sellplaninfo['huikuanjine'];
		$kaipiaojine=$sellplaninfo['kaipiaojine'];
		$oddment=$sellplaninfo['oddment'];
		$totalmoney=$sellplaninfo['totalmoney'];
		$kaipiaostate=$sellplaninfo['kaipiaostate'];
		if($totalmoney==$huikuanjine+$oddment)
			$ifpay=2;
		else
		{
			if($huikuanjine+$oddment!=0)
				$ifpay=1;
			else
				$ifpay=0;
		}
		if($totalmoney==$kaipiaojine)
			$kaipiaostate=4;
		else 
		{
			if($kaipiaojine!=0)
				$kaipiaostate=3;
			else
			{
				if($kaipiaostate!=-1)
					$kaipiaostate=0; 
			}
		}
		$sql="update sellplanmain set ifpay=$ifpay,kaipiaostate=$kaipiaostate where billid=$dingdanid";
		$this->db->Execute($sql);
		$this->updatesellplanmainFlag($dingdanid);
			
	}
	function updatesellplanmainFlag($dingdanid)
	{
		$sellplaninfo=returntablefield("sellplanmain", "billid", $dingdanid, "totalmoney,ifpay,huikuanjine,kaipiaojine,oddment,fahuostate,kaipiaostate,user_flag,billtype");
		$ifpay=$sellplaninfo['ifpay'];
		$fahuostate=$sellplaninfo['fahuostate'];
		$kaipiaostate=$sellplaninfo['kaipiaostate'];
		$user_flag=$sellplaninfo['user_flag'];
		$oldflag=$user_flag;
		if($user_flag!=-1)
		{
			if($sellplaninfo['billtype']==3)
			{
				if($fahuostate==-1)
				{
					$ifchuku=false;
					$billid=returntablefield("stockoutmain", "dingdanbillid", $dingdanid, "billid","state","�ѳ���","outtype","���۳���");
					if($billid!='')
						$ifchuku=true;
					$billid=returntablefield("stockinmain", "caigoubillid", $dingdanid, "billid","state","�����","intype","�˻����");
					if($billid!='')
						$ifchuku=true;
					if($ifpay==2 && $ifchuku)
						$user_flag=2;
					else if($ifpay==0 && !$ifchuku && $kaipiaostate<=0)
						$user_flag=0;
					else 
						$user_flag=1;
				}
				else 
				{
					if($ifpay==2 && $fahuostate==4)
						$user_flag=2;
					else if($fahuostate==0 && $ifpay==0 && $kaipiaostate<=0)
						$user_flag=0;
					else 
						$user_flag=1;
				}
			}
			else 
			{
				if($fahuostate==4 && $ifpay==2)
					$user_flag=2;
				else if($fahuostate==0 && $ifpay==0 && $kaipiaostate==0)
					$user_flag=0;
				else 
					$user_flag=1;
			}
		}
		$sql="update sellplanmain set user_flag=$user_flag where billid=$dingdanid";
		$this->db->Execute($sql);
		if(($oldflag==0 && $user_flag>0) || ($oldflag>0 && $user_flag==0))
			$this->updatesellplanmainJifen($dingdanid,$user_flag);
	}
	//���»���
	function updatesellplanmainJifen($dingdanid,$user_flag)
	{

		@$global_config_ini_file = @parse_ini_file(DOCUMENT_ROOT.'general/ERP/Interface/Framework/global_config.ini',true);
		$exchange=$global_config_ini_file['section']['integral'];
		if($exchange != 0){
			$sql = "select integral,totalmoney,supplyid from sellplanmain where billid=$dingdanid";
			$rs = $this->db->Execute($sql);
			$rs_a = $rs->GetArray();
			
			if($user_flag > 0){
				$integral = intval($rs_a[0]['totalmoney']/$exchange);
				if($integral!=0)
				{
					$sql="update sellplanmain set integral=$integral where billid=$dingdanid";
					$this->db->Execute($sql);
					$sql="update customer set integral=IFNULL(`customer`.`integral`,0)+$integral where ROWID=".$rs_a[0][supplyid];
					$this->db->Execute($sql);	
				}			
			}else
			{
				$integral = $rs_a[0]['integral'];
				if($integral!=0)
				{
					$sql="update sellplanmain set integral=0 where billid=$dingdanid";
					$this->db->Execute($sql);
					$sql="update customer set integral=IFNULL(`customer`.`integral`,0)-$integral where ROWID=".$rs_a[0][supplyid];
					$this->db->Execute($sql);	
				}							
			}
		}
	}
	//���²ɹ�����Ļؿ�״̬
	function updatebuyplanmainfukuan($caigoubillid)
	{
		$sellplaninfo=returntablefield("buyplanmain", "billid", $caigoubillid, "totalmoney,ifpay,paymoney,shoupiaomoney,oddment,state,shoupiaostate,user_flag");
		$paymoney=$sellplaninfo['paymoney'];
		$oddment=$sellplaninfo['oddment'];
		$totalmoney=$sellplaninfo['totalmoney'];
		$shoupiaomoney=$sellplaninfo['shoupiaomoney'];
		$fahuostate=$sellplaninfo['state'];
		if($totalmoney==$paymoney+$oddment)
			$ifpay=2;
		else
		{
			if($paymoney+$oddment!=0)
				$ifpay=1;
			else
				$ifpay=0;
		}
		if($totalmoney==$shoupiaomoney)
			$shoupiaostate=4;
		else
		{
			if($shoupiaomoney!=0)
				$shoupiaostate=3;
			else 
				$shoupiaostate=0;
		}
		$sql="update buyplanmain set ifpay=$ifpay,shoupiaostate=$shoupiaostate where billid=$caigoubillid";
		$this->db->Execute($sql);
		$this->updatebuyplanmainFlag($caigoubillid);
	
	}
	function updatebuyplanmainFlag($caigoubillid)
	{
		$sellplaninfo=returntablefield("buyplanmain", "billid", $caigoubillid, "ifpay,state,shoupiaostate,user_flag");
		$ifpay=$sellplaninfo['ifpay'];
		$fahuostate=$sellplaninfo['state'];
		$shoupiaostate=$sellplaninfo['shoupiaostate'];
		$user_flag=$sellplaninfo['user_flag'];
		if($user_flag!=-1)
		{
			
			
				if($fahuostate==5 && $ifpay==2)
					$user_flag=2;
				else if($fahuostate==1 && $ifpay==0 && $shoupiaostate==0)
					$user_flag=0;
				else 
					$user_flag=1;
			
		}
		$sql="update buyplanmain set user_flag=$user_flag where billid=$caigoubillid";
		$this->db->Execute($sql);
	
	}
	//�����ؿ��¼
	function insertShoukuanReocord($customerid,$billid,$shoukuan,$accountid,$createman,$opertype,$oddment,$qici="1",$guanlianplanid="")
	{
		$id=returnAutoIncrement("id", "huikuanrecord");
		$sql="insert into huikuanrecord (id,customerid,dingdanbillid,paydate,jine,accountid,createman,createtime,oddment,qici,guanlianplanid)
		values(".$id.",".$customerid.",".$billid.",'".date("Y-m-d")."',".$shoukuan.",".$accountid.",'".$createman."','".date("Y-m-d H:i:s")."',$oddment,'$qici','$guanlianplanid')";
		$this->db->Execute($sql);
		if($shoukuan!=0)
		{
			if($accountid==0)
			{
				//�ۼ�Ԥ�տ�
				$id = returnAutoIncrementUnitBillid("preshoubillid");
				$yuchuzhi=returntablefield("customer","rowid",$customerid,"yuchuzhi");
				if($yuchuzhi<$shoukuan)
					throw new Exception("Ԥ��ֵ����");
				$sql="update customer set yuchuzhi=yuchuzhi-$shoukuan where rowid=$customerid";
				$this->db->Execute($sql);
				$sql="insert into accesspreshou (id,customerid,curchuzhi,jine,opertype,guanlianbillid,createman,createtime) values(
				".$id.",".$customerid.",".$yuchuzhi.",".-$shoukuan.",'$opertype',".$billid.",'".$createman."','".date("Y-m-d H:i:s")."')";
				$this->db->Execute($sql);
			}
			else 
			{
				//�˻��������
				$oldjine=returntablefield("bank", "rowid", $accountid, "jine");
				$sql="update bank set jine=jine+(".$shoukuan.") where rowid=".$accountid;
				$this->db->Execute($sql);
				$sql="insert into accessbank (bankid,oldjine,jine,opertype,guanlianbillid,createman,createtime) values(
				".$accountid.",".$oldjine.",".$shoukuan.",'$opertype',".$billid.",'".$createman."','".date("Y-m-d H:i:s")."')";
				$this->db->Execute($sql);
			}	
			$sql="update sellplanmain set huikuanjine=huikuanjine+($shoukuan) where billid=".$billid;
			$this->db->Execute($sql);
		}
		if($oddment!=0)
			$this->insertFeiYong(8,$oddment,$accountid,$_SESSION['LOGIN_USER_ID'],-1,$billid,"sellplanmain");
		$this->updatesellplanmainhuikuan($billid);
	}
	//���������¼
	function insertFukuanReocord($supplyid,$billid,$fukuan,$accountid,$createman,$opertype,$oddment,$qici,$beizhu,$guanlianplanid="")
	{
		$id=returnAutoIncrement("id", "fukuanrecord");
		$sql="insert into fukuanrecord (id,supplyid,caigoubillid,paydate,jine,accountid,createman,createtime,oddment,qici,beizhu,guanlianplanid)
		values(".$id.",".$supplyid.",".$billid.",'".date("Y-m-d")."',".$fukuan.",".$accountid.",'".$createman."','".date("Y-m-d H:i:s")."',$oddment,'$qici','$beizhu','$guanlianplanid')";
		$this->db->Execute($sql);
		
		//�˻�������
		
		if($accountid==0)
		{
			//�ۼ�Ԥ����
			$id = returnAutoIncrementUnitBillid("prepaybillid");
			$yufukuan=returntablefield("supply","rowid",$supplyid,"yufukuan");
			if($yufukuan<$fukuan)
				throw new Exception("Ԥ�������");
			$sql="update supply set yufukuan=yufukuan-$fukuan where rowid=$supplyid";
			$this->db->Execute($sql);
			$sql="insert into accessprepay (id,supplyid,curchuzhi,jine,opertype,guanlianbillid,createman,createtime) values(
			".$id.",".$supplyid.",".$yufukuan.",".-$fukuan.",'$opertype',".$billid.",'".$createman."','".date("Y-m-d H:i:s")."')";
			$this->db->Execute($sql);
		}
		else 
		{
			//���˻���֧��
			$accountinfo=returntablefield("bank", "rowid", $accountid, "jine,syslock");
			$oldjine=$accountinfo['jine'];
			$sql="update bank set jine=jine-(".$fukuan.") where rowid=".$accountid;
			$this->db->Execute($sql);
			$sql="insert into accessbank (bankid,oldjine,jine,opertype,guanlianbillid,createman,createtime) values(
			".$accountid.",".$oldjine.",".-$fukuan.",'$opertype',".$billid.",'".$createman."','".date("Y-m-d H:i:s")."')";
			$this->db->Execute($sql);
		}
		
		$sql="update buyplanmain set paymoney=paymoney+($fukuan) where billid=".$billid;
		$this->db->Execute($sql);
		if($oddment!=0)
			$this->insertFeiYong(1,$oddment,$accountid,$_SESSION['LOGIN_USER_ID'],1,$billid,"buyplanmain");
		$this->updatebuyplanmainfukuan($billid);
	}
	
	function deleteShoukuanReocord($id)
	{
		$shoukuaninfo=returntablefield("huikuanrecord", "id", $id, "customerid,dingdanbillid,accountid,jine,oddment");
		$dingdanbillid=$shoukuaninfo['dingdanbillid'];
		$accountid=$shoukuaninfo['accountid'];
		$shoukuan=$shoukuaninfo['jine'];
		$oddment=$shoukuaninfo['oddment'];
		$customerid=$shoukuaninfo['customerid'];
		$sql="delete from huikuanrecord  where id=$id";
		$this->db->Execute($sql);
		if($shoukuan!=0)
		{
			if($accountid=='0')
			{
				//����Ԥ�տ�
				$id = returnAutoIncrementUnitBillid("preshoubillid");
				$yuchuzhi=returntablefield("customer","rowid",$customerid,"yuchuzhi");
				if($yuchuzhi+$shoukuan<0)
					throw new Exception("Ԥ��ֵ����");
				$sql="update customer set yuchuzhi=yuchuzhi+$shoukuan where rowid=$customerid";
				$this->db->Execute($sql);
				$sql="insert into accesspreshou (id,customerid,curchuzhi,jine,opertype,guanlianbillid,createman,createtime) values(
				".$id.",".$customerid.",".$yuchuzhi.",".$shoukuan.",'������ȡ',".$dingdanbillid.",'".$_SESSION['LOGIN_USER_ID']."','".date("Y-m-d H:i:s")."')";
				$this->db->Execute($sql);
			}
			else 
			{
				//�˻��������
				$oldjine=returntablefield("bank", "rowid", $accountid, "jine");
				$sql="update bank set jine=jine-(".$shoukuan.") where rowid=".$accountid;
				$this->db->Execute($sql);
				
				$sql="insert into accessbank (bankid,oldjine,jine,opertype,guanlianbillid,createman,createtime) values(
				".$accountid.",".$oldjine.",".-$shoukuan.",'������ȡ',".$dingdanbillid.",'".$_SESSION['LOGIN_USER_ID']."','".date("Y-m-d H:i:s")."')";
				$this->db->Execute($sql);
			}
			$sql="update sellplanmain set huikuanjine=huikuanjine-($shoukuan) where billid=".$dingdanbillid;
			$this->db->Execute($sql);
		}
		if($oddment!=0)
		{
			$this->insertFeiYong(8,-$oddment,$accountid,$_SESSION['LOGIN_USER_ID'],-1,$dingdanbillid,"sellplanmain");
		}
		$this->updatesellplanmainhuikuan($dingdanbillid);
	}
	//ɾ�������¼
	function deleteFukuanReocord($id)
	{
		$fukuaninfo=returntablefield("fukuanrecord", "id", $id, "id,supplyid,caigoubillid,jine,oddment,accountid,guanlianplanid");	
		$caigoubillid=$fukuaninfo['caigoubillid'];
		$fukuan=$fukuaninfo['jine'];
		$oddment=$fukuaninfo['oddment'];
		$accountid=$fukuaninfo['accountid'];
		$supplyid=$fukuaninfo['supplyid'];
		
		$sql="delete from fukuanrecord  where id=$id";
		$this->db->Execute($sql);
		
		//�˻��������
		
		if($accountid=='0')
		{
			//����Ԥ����
			$id = returnAutoIncrementUnitBillid("prepaybillid");
			$yufukuan=returntablefield("supply","rowid",$supplyid,"yufukuan");
			if($yufukuan+$fukuan<0)
				throw new Exception("Ԥ�������");
			$sql="update supply set yufukuan=yufukuan+$fukuan where supplyid=$supplyid";
			$this->db->Execute($sql);
			$sql="insert into accessprepay (id,supplyid,curchuzhi,jine,opertype,guanlianbillid,createman,createtime) values(
			".$id.",".$supplyid.",".$yufukuan.",".$fukuan.",'����֧��',".$caigoubillid.",'".$_SESSION['LOGIN_USER_ID']."','".date("Y-m-d H:i:s")."')";
			$this->db->Execute($sql);
		}
		else 
		{
			$accountinfo=returntablefield("bank", "rowid", $accountid, "jine,syslock");
			$oldjine=$accountinfo['jine'];
			$sql="update bank set jine=jine+(".$fukuan.") where rowid=".$accountid;
			$this->db->Execute($sql);
			
			$sql="insert into accessbank (bankid,oldjine,jine,opertype,guanlianbillid,createman,createtime) values(
			".$accountid.",".$oldjine.",".$fukuan.",'����֧��',".$caigoubillid.",'".$_SESSION['LOGIN_USER_ID']."','".date("Y-m-d H:i:s")."')";
			$this->db->Execute($sql);
		}
		$sql="update buyplanmain set paymoney=paymoney-($fukuan) where billid=".$caigoubillid;
		$this->db->Execute($sql);
		
		if($oddment!=0)
		{
			$this->insertFeiYong(1,-$oddment,$accountid,$_SESSION['LOGIN_USER_ID'],1,$caigoubillid,"buyplanmain");
		}
		$this->updatebuyplanmainfukuan($caigoubillid);
	}
	//��������
	function insertFeiYongAccount($feiyongtype,$jine,$accountid,$createman,$kind,$chanshengdate="",$beizhu="")
	{
		if($chanshengdate=="")
			$chanshengdate=date("Y-m-d");
		$feiyongbillid = returnAutoIncrementUnitBillid("feiyongbillid");
		
		$sql="insert into feiyongrecord (billid,typeid,jine,accountid,chanshengdate,createman,createtime,kind,beizhu) values($feiyongbillid,$feiyongtype,$jine,$accountid,'".$chanshengdate."','".$createman."','".date("Y-m-d H:i:s")."',$kind,'$beizhu')";
		$this->db->Execute($sql);
		$oldjine=returntablefield("bank","rowid",$accountid,"jine");
	    $sql="update bank set jine=jine+(".$jine*$kind.") where rowid=$accountid";
	    $this->db->Execute($sql);
	    if($kind==1)
	    	$feiyongname='��������';
	    else 
	    	$feiyongname='����֧��';
	    $sql="insert into accessbank (bankid,oldjine,jine,opertype,guanlianbillid,createman,createtime) values ($accountid,$oldjine,".$jine*$kind.
	    ",'$feiyongname',$feiyongbillid,'".$_SESSION['LOGIN_USER_ID']."','".date("Y-m-d H:i:s")."')";
	    $this->db->Execute($sql);
	}
	//����Ĩ�����
	function insertFeiYong($feiyongtype,$jine,$accountid,$createman,$kind,$billid,$table)
	{
		$feiyongbillid = returnAutoIncrementUnitBillid("feiyongbillid");
		$sql="insert into feiyongrecord (billid,typeid,jine,accountid,chanshengdate,createman,createtime,kind) values($feiyongbillid,$feiyongtype,$jine,$accountid,'".date("Y-m-d")."','".$createman."','".date("Y-m-d H:i:s")."',$kind)";
		$this->db->Execute($sql);
		if($table!='')
		{
			$sql="update $table set oddment=oddment+($jine) where billid=".$billid;
			$this->db->Execute($sql);
		}
	}
	
	//ɾ�����õ�
	function deleteFeiYongAccount($selectid)
	{
		$feiyonginfo=returntablefield("feiyongrecord","billid",$selectid,"accountid,jine,typeid,kind");
		$accountid=$feiyonginfo['accountid'];
		$jine=$feiyonginfo['jine'];
		$kind=$feiyonginfo['kind'];
	    $sql="delete from feiyongrecord where billid=".$selectid;
	    $this->db->Execute($sql);
	    $oldjine=returntablefield("bank","rowid",$accountid,"jine");
	    $sql="update bank set jine=jine-(".$jine.") where rowid=".$accountid;
	    $this->db->Execute($sql);
	    if($kind==1)
	    	$feiyongname='��������';
	    else 
	    	$feiyongname='����֧��';
	    $sql="insert accessbank (bankid,oldjine,jine,opertype,guanlianbillid,createman,createtime) values (".$accountid.",$oldjine,".-$jine.
	    ",'$feiyongname',$selectid,'".$_SESSION['LOGIN_USER_ID']."','".date("Y-m-d H:i:s")."')";
	    $this->db->Execute($sql);
	}
	//������Ʊ��¼
	function insertKaiPiao($customerid,$billid,$fapiaoneirong,$fapiaotype,$fapiaono,$jine,$createman,$kaipiaodate='')
	{
		if($kaipiaodate=='')
			$kaipiaodate=date("Y-m-d");
		$id=returnAutoIncrement("id", "kaipiaorecord");
		$sql="insert into kaipiaorecord (id,customerid,dingdanbillid,kaipiaoneirong,piaojutype,fapiaono,piaojujine,kaipiaodate,kaipiaoren,createtime)
		values (".$id.",".$customerid.",".$billid.",'".$fapiaoneirong."',".$fapiaotype.",'".$fapiaono."',".$jine.",'".$kaipiaodate."','".$createman."','".date("Y-m-d H:i:s")."')";
		$this->db->Execute($sql);
		$sql="update sellplanmain set kaipiaojine=kaipiaojine+($jine) where billid=".$billid;
		$this->db->Execute($sql);
		$this->updatesellplanmainhuikuan($billid);
	}
	//������Ʊ��¼
	function insertShouPiao($supplyid,$billid,$fapiaoneirong,$fapiaotype,$fapiaono,$jine,$createman,$qici,$beizhu,$kaipiaodate='')
	{
		if($kaipiaodate=='')
			$kaipiaodate=date("Y-m-d");
		$id=returnAutoIncrement("id", "shoupiaorecord");
		$sql="insert into shoupiaorecord (id,supplyid,caigoubillid,kaipiaoneirong,piaojutype,fapiaono,piaojujine,kaipiaodate,kaipiaoren,createtime,qici,beizhu)
		values (".$id.",".$supplyid.",".$billid.",'".$fapiaoneirong."',".$fapiaotype.",'".$fapiaono."',".$jine.",'".$kaipiaodate."','".$createman."','".date("Y-m-d H:i:s")."','$qici','$beizhu')";
		$this->db->Execute($sql);
		$sql="update buyplanmain set shoupiaomoney=shoupiaomoney+($jine) where billid=".$billid;
		$this->db->Execute($sql);
		$this->updatebuyplanmainfukuan($billid);
	}
	//ɾ����Ʊ��¼
	function deleteKaiPiao($id)
	{
		$dingdanbillid=returntablefield("kaipiaorecord", "id",$id,"dingdanbillid");
		$sql="delete from kaipiaorecord where id=$id";
		$this->db->Execute($sql);
		$sql="select sum(piaojujine) as allmoney from kaipiaorecord where dingdanbillid=$dingdanbillid";
		$rs=$this->db->Execute($sql);
		$allmoney=floatvalue($rs->fields[0]['allmoney']);
		$sql="update sellplanmain set kaipiaojine=$allmoney where billid=".$dingdanbillid;
		$this->db->Execute($sql);
		$this->updatesellplanmainhuikuan($dingdanbillid);
	}
	//ɾ����Ʊ��¼
	function deleteShouPiao($id)
	{
		$caigoubillid=returntablefield("shoupiaorecord", "id",$id,"caigoubillid");
		$sql="delete from shoupiaorecord where id=$id";
		$this->db->Execute($sql);
		$sql="select sum(piaojujine) as allmoney from shoupiaorecord where caigoubillid=$caigoubillid";
		$rs=$this->db->Execute($sql);
		$allmoney=floatvalue($rs->fields[0]['allmoney']);
		$sql="update buyplanmain set shoupiaomoney=$allmoney where billid=".$caigoubillid;
		$this->db->Execute($sql);
		$this->updatebuyplanmainfukuan($caigoubillid);
	}
	
	//����Ԥ�����¼
	function insertYuFukuanReocord($supplyid,$linkmanid,$jine,$accountid,$createman,$opertype,$beizhu)
	{
		$id = returnAutoIncrementUnitBillid("prepaybillid");
		$curchuzhi=floatvalue(returntablefield("supply", "rowid", $supplyid, "yufukuan"));
		if($linkmanid=='')
			$linkmanid='null';
		$sql="insert into accessprepay (id,supplyid,linkmanid,curchuzhi,jine,accountid,createman,createtime,opertype,beizhu)
		values(".$id.",".$supplyid.",".$linkmanid.",".$curchuzhi.",".$jine.",".$accountid.",'".$createman."','".date("Y-m-d H:i:s")."','$opertype','$beizhu')";
		$this->db->Execute($sql);
		//�˻�������
		$oldjine=returntablefield("bank", "rowid", $accountid, "jine");
		$sql="update bank set jine=jine-(".$jine.") where rowid=".$accountid;
		$this->db->Execute($sql);
		$sql="insert into accessbank (bankid,oldjine,jine,opertype,guanlianbillid,createman,createtime) values(
		".$accountid.",".$oldjine.",".-$jine.",'$opertype',".$id.",'".$createman."','".date("Y-m-d H:i:s")."')";
		$this->db->Execute($sql);
		$sql="update supply set yufukuan=yufukuan+($jine) where rowid=".$supplyid;
		$this->db->Execute($sql);
	}
	//ɾ��Ԥ�����¼
	function deleteYuFukuanReocord($id)
	{
		$fukuaninfo=returntablefield("accessprepay", "id", $id, "supplyid,accountid,jine,opertype");	
		$supplyid=$fukuaninfo['supplyid'];
		$accountid=$fukuaninfo['accountid'];
		$jine=$fukuaninfo['jine'];
		$opertype=$fukuaninfo['opertype'];
		
		$sql="delete from accessprepay where id=$id";
		$this->db->Execute($sql);
		
		//�˻��������
		$oldjine=returntablefield("bank", "rowid", $accountid, "jine");
		$sql="update bank set jine=jine+(".$jine.") where rowid=".$accountid;
		$this->db->Execute($sql);
		
		$sql="insert into accessbank (bankid,oldjine,jine,opertype,guanlianbillid,createman,createtime) values(
		".$accountid.",".$oldjine.",".$jine.",'$opertype',".$id.",'".$_SESSION['LOGIN_USER_ID']."','".date("Y-m-d H:i:s")."')";
		$this->db->Execute($sql);
		
		$sql="update supply set yufukuan=yufukuan-($jine) where rowid=".$supplyid;
		$this->db->Execute($sql);
		
	}
	//����Ԥ�տ��¼
	function insertYuShoukuanReocord($customerid,$linkmanid,$jine,$accountid,$createman,$opertype,$beizhu)
	{
		
		$id = returnAutoIncrementUnitBillid("preshoubillid");
		$curchuzhi=floatvalue(returntablefield("customer", "ROWID", $customerid, "yuchuzhi"));
		$sql="insert into accesspreshou (id,customerid,linkman,curchuzhi,jine,accountid,createman,createtime,opertype,beizhu)
		values(".$id.",".$customerid.",'".$linkmanid."',".$curchuzhi.",".$jine.",".$accountid.",'".$createman."','".date("Y-m-d H:i:s")."','$opertype','$beizhu')";
		
		$this->db->Execute($sql);
		//�˻��������
		$oldjine=returntablefield("bank", "rowid", $accountid, "jine");
		$sql="update bank set jine=jine+(".$jine.") where rowid=".$accountid;
		$this->db->Execute($sql);
		$sql="insert into accessbank (bankid,oldjine,jine,opertype,guanlianbillid,createman,createtime) values(
		".$accountid.",".$oldjine.",".$jine.",'$opertype',".$id.",'".$createman."','".date("Y-m-d H:i:s")."')";
		$this->db->Execute($sql);
		$sql="update customer set yuchuzhi=yuchuzhi+($jine) where rowid=".$customerid;
		$this->db->Execute($sql);
	}
//ɾ��Ԥ�տ��¼
	function deleteYuShoukuanReocord($id)
	{
		$fukuaninfo=returntablefield("accesspreshou", "id", $id, "customerid,accountid,jine,opertype");	
		$customerid=$fukuaninfo['customerid'];
		$accountid=$fukuaninfo['accountid'];
		$jine=$fukuaninfo['jine'];
		$opertype=$fukuaninfo['opertype'];
		
		$sql="delete from accesspreshou where id=$id";
		$this->db->Execute($sql);
		
		//�˻�������
		$oldjine=returntablefield("bank", "rowid", $accountid, "jine");
		$sql="update bank set jine=jine-(".$jine.") where rowid=".$accountid;
		$this->db->Execute($sql);
		
		$sql="insert into accessbank (bankid,oldjine,jine,opertype,guanlianbillid,createman,createtime) values(
		".$accountid.",".$oldjine.",".-$jine.",'$opertype',".$id.",'".$_SESSION['LOGIN_USER_ID']."','".date("Y-m-d H:i:s")."')";
		$this->db->Execute($sql);
		
		$sql="update customer set yuchuzhi=yuchuzhi-($jine) where rowid=".$customerid;
		$this->db->Execute($sql);
		
	}
//�����ʽ�ע�뵥
	function insertBankZhuruAccount($jine,$accountid,$memo,$inouttype)
	{
		
		$billid = returnAutoIncrement("billid","bankzhuru");
		if($inouttype==23)
			$jine=-$jine;
		$sql="insert into bankzhuru (billid,jine,accountid,memo,userid,opertime,inouttype) values($billid,$jine,$accountid,'$memo','".$_SESSION['LOGIN_USER_ID']."','".date("Y-m-d H:i:s")."','$inouttype')";
		$this->db->Execute($sql);
		$oldjine=returntablefield("bank","rowid",$accountid,"jine");
	    $sql="update bank set jine=jine+(".$jine.") where rowid=$accountid";
	    $this->db->Execute($sql);
	    $accesstype=returntablefield("accesstype","id",$inouttype,"name");
	    $sql="insert into accessbank (bankid,oldjine,jine,opertype,guanlianbillid,createman,createtime) values ($accountid,$oldjine,".$jine.
	    ",'$accesstype','$billid,','".$_SESSION['LOGIN_USER_ID']."','".date("Y-m-d H:i:s")."')";
	    $this->db->Execute($sql);
	}
//ɾ���ʽ�ע�뵥
	function deleteBankZhuruAccount($billid)
	{
		$sql="select * from bankzhuru where billid=$billid";
		$rs=$this->db->Execute($sql);
		$rs_a=$rs->GetArray();
		$accountid=$rs_a[0]['accountid'];
		$jine=$rs_a[0]['jine'];
		$inouttype=$rs_a[0]['inouttype'];
		
		$sql="delete from  bankzhuru where billid=$billid";
		$this->db->Execute($sql);
		$oldjine=returntablefield("bank","rowid",$accountid,"jine");
	    $sql="update bank set jine=jine-(".$jine.") where rowid=$accountid";
	    $this->db->Execute($sql);
	    $accesstype=returntablefield("accesstype","id",$inouttype,"name");
	    $sql="insert into accessbank (bankid,oldjine,jine,opertype,guanlianbillid,createman,createtime) values ($accountid,$oldjine,".-$jine.
	    ",'$accesstype','$billid,','".$_SESSION['LOGIN_USER_ID']."','".date("Y-m-d H:i:s")."')";
	    $this->db->Execute($sql);
	}
	//�ۼ��˻�
	function operateAccount($accountid,$totalmoney,$accesstype,$billid)
	{
		$yue=returntablefield("bank", "ROWID", $accountid, "jine");
		if($yue<$totalmoney)
		    	throw new Exception("�˻�������֧�� $totalmoney");
		$sql="update bank set jine=jine-$totalmoney where rowid=$accountid";
		$this->db->Execute($sql);
		$sql="insert into accessbank (bankid,oldjine,jine,opertype,guanlianbillid,createman,createtime) values ($accountid,$yue,".-$totalmoney.
	    ",'$accesstype','$billid,','".$_SESSION['LOGIN_USER_ID']."','".date("Y-m-d H:i:s")."')";
	    $this->db->Execute($sql);
		
	}
	//�ۼ�Ԥ����
	function operatePrepay($supplyid,$totalmoney,$opertype,$billid)
	{
		$yue=returntablefield("supply", "ROWID", $supplyid, "yufukuan");
		if($yue<$totalmoney)
		    	throw new Exception("Ԥ����������֧�� $totalmoney");
		$sql="update supply set yufukuan=yufukuan-$totalmoney where rowid=$supplyid";
		$this->db->Execute($sql);
		$id = returnAutoIncrementUnitBillid("prepaybillid");
		$sql="insert into accessprepay (id,supplyid,curchuzhi,jine,opertype,guanlianbillid,createman,createtime) values(
		".$id.",".$supplyid.",".$yue.",".-$totalmoney.",'$opertype',".$billid.",'".$_SESSION['LOGIN_USER_ID']."','".date("Y-m-d H:i:s")."')";
		$this->db->Execute($sql);
		
	}
	//ͨ��������ɾ���ؿ��¼
	function deleteShoukuanReocordByBillid($billid)
	{
		$sql="select id from huikuanrecord where dingdanbillid=$billid";
		$rs=$this->db->Execute($sql);
		$rs_a=$rs->GetArray();
		for($i=0;$i<sizeof($rs_a);$i++)
		{
			$this->deleteShoukuanReocord($rs_a[$i]['id']);
		}
	}
	//ͨ���ɹ�����ɾ�������¼
	function deleteFukuanReocordByBillid($billid)
	{
		$sql="select id from fukuanrecord where caigoubillid=$billid";
		$rs=$this->db->Execute($sql);
		$rs_a=$rs->GetArray();
		for($i=0;$i<sizeof($rs_a);$i++)
		{
			$this->deleteFukuanReocord($rs_a[$i]['id']);
		}
	}
	//ͨ��������ɾ����Ʊ��¼
	function deletekaipiaoByBillid($billid)
	{
		$sql="select id from kaipiaorecord where dingdanbillid=$billid";
		$rs=$this->db->Execute($sql);
		$rs_a=$rs->GetArray();
		for($i=0;$i<sizeof($rs_a);$i++)
		{
			$this->deleteKaiPiao($rs_a[$i]['id']);
		}
	}
	//ͨ���ɹ�����ɾ����Ʊ��¼
	function deleteshoupiaoByBillid($billid)
	{
		$sql="select id from shoupiaorecord where caigoubillid=$billid";
		$rs=$this->db->Execute($sql);
		$rs_a=$rs->GetArray();
		for($i=0;$i<sizeof($rs_a);$i++)
		{
			$this->deleteShouPiao($rs_a[$i]['id']);
		}
	}
}
?>