<?php
/*
 ��Ȩ����:֣�ݵ���Ƽ�������޹�˾;
 ��ϵ��ʽ:0371-69663266;
 ��˾��ַ:����֣�ݾ��ü��������������־�����·ͨ�Ų�ҵ԰��¥����;
 ��˾���:֣�ݵ���Ƽ�������޹�˾λ���й��в�����-֣��,������2007��1��,�����ڰѻ����Ƚ���Ϣ����������ͨ�ż���������ѹ�����ҵ��ʵ���ռ���������ҵ�ͻ��Ĺ�����ҵ���»�У�ȫ���ṩ��������֪ʶ��Ȩ�Ľ�����������������������������в�������ĸ�У���������������СѧУ��������ṩ�̡�Ŀǰ�����ж�Ҹ�ְ����ְ��ԺУʹ��ͨ���в��з����Ŀ���������ͷ���;

 �������:����Ƽ�������������Լܹ�ƽ̨,�Լ��������֮����չ���κ��������Ʒ;
 ����Э��:���ֻ�У԰��ƷΪ��ҵ���,�������ΪLICENSE��ʽ;����CRMϵͳ��SunshineCRMϵͳΪGPLV3Э�����,GPLV3Э����������뵽�ٶ�����;
 ��������:�����ʹ�õ�ADODB��,PHPEXCEL��,SMTARY���ԭ��������,���´���������������;
 */
ini_set('display_errors', 1);
ini_set('error_reporting', E_ALL);
error_reporting(E_WARNING | E_ERROR);
require_once('lib.inc.php');
$GLOBAL_SESSION=returnsession();

//print_r($_GET);exit;
validateMenuPriv("�ͻ�������Ϣ");
if($_GET['action']=="add_default")
	$ADDINIT=array("sysuser"=>$_SESSION['LOGIN_USER_ID']);
if($_GET['action']=="edit_default_data"||$_GET['action']=="add_default_data")		{
	if($_POST['amtagio']<=0 || $_POST['amtagio']>1)
	{
		//print "<script language='javascript'>alert('�ۿ۱������0-1֮��');window.history.back(-1);</script>";
		//exit;
		$_POST['amtagio']=1;
	}
	$_POST['calling'] = ����תƴ������ĸ($_POST['supplyname']);
	
}


if($_GET['action']=="view_default")	{
	global $db;
	$billid = $_GET['ROWID'];
	$sql = "SELECT a.*,b.`name` as state,c.`name` as enterstype,a.style,e.USER_NAME as blong,e.USER_NAME,f.`name` as origin,g.`name` as salemode,h.`name` as property,a.yuchuzhi,a.createdate,a.integral,a.explainStr FROM customer a
		LEFT JOIN customerlever b on a.state=b.ROWID LEFT JOIN unitprop c on a.enterstype=c.ROWID  LEFT JOIN `user` ee on a.user_id=ee.USER_ID  LEFT JOIN `user` e on a.sysuser=e.USER_ID LEFT JOIN customerorigin f on a.origin=f.ROWID LEFT JOIN salemode g on a.salemode=g.ROWID LEFT JOIN property h on a.property=h.ROWID WHERE a.ROWID=".$billid;
	$rs=$db->Execute($sql);
	$rs_a = $rs->GetArray();
	page_css("�ͻ��ۺ���ͼ");
	$custname=$rs_a[0][supplyname];
	$UID=returntablefield("user", "user_id", $rs_a[$i]['user_id'], "uid");
	$UID1=returntablefield("user", "user_id", $rs_a[$i]['sysuser'], "uid");
	$editrole=false;
/*
	if(ifHasRoleCust($billid))
	{
		if($_SESSION['limitEditDel']=='1')
			$editrole=true;
		else
		{
			if($rs_a[$i]['sysuser']==$_SESSION['LOGIN_USER_ID'] || $_SESSION['LOGIN_USER_ID']=='admin')
				$editrole=true;
		}
	}
*/	
	?>



<table class="TableBlock" align="center" width="80%">
	<tr>
		<td class="TableHeader" align="left" colspan="4">&nbsp;�鿴�ͻ���Ϣ</td>
	</tr>
	<tr>
		<td class="TableControl" nowrap="" align="middle" colspan="4">
		<div align="left"><input type="button" accesskey="p" name="print"
			value=" ��ӡ " class="SmallButtonA"
			onclick="document.execCommand('Print');" title="��ݼ�:ALT+p"> 
			<input
			type="button" accesskey="m" name="record" value="�޸���־"
			class="SmallButton"
			onclick="window.open('modifyrecord_newai.php?<?php echo base64_encode("tablename=customer&keyfield=rowid&keyvalue=$billid")?>');"
			title="��ݼ�:ALT+r">
			<input
			type="button" accesskey="c" name="cancel" value=" ���� "
			class="SmallButtonA"
			onclick="if(history.length==0) window.close();else history.back();"
			title="��ݼ�:ALT+c"></div>
		</td>
	</tr>
	<tr>
		<td nowrap="" class="TableContent" width="20%">�ͻ�����:</td>
		<td class="TableData" colspan="1"><?php if($editrole) print "<a href='customer_newai.php?".base64_encode("action=edit_default&ROWID=".$billid)."' target='_blank'><img src='../Framework/images/edit1.gif' title='�༭'></a>"; echo $rs_a[0][supplyname];?></td>
		<td nowrap="" class="TableContent" width="20%">��˾��ַ:</td>
		<td class="TableData" colspan="1"><?php echo $rs_a[0][contactaddress] ?></td>
	</tr>
	<tr>
		<td nowrap="" class="TableContent" width="20%">�ۿ�:</td>
		<td class="TableData" colspan="1"><?php echo $rs_a[0][amtagio] ?></td>
		<td nowrap="" class="TableContent" width="20%">��Ա��:</td>
		<td class="TableData" colspan="1"><?php echo $rs_a[0][membercard] ?></td>
	</tr>
	<tr>
		<td class="TableContent" nowrap="">�ͻ�״̬:</td>
		<td class="TableData" nowrap="" colspan="1"><?php echo $rs_a[0][state] ?></td>
		<td nowrap="" class="TableContent" width="20%">�绰:</td>
		<td class="TableData" colspan="1"><?php
				
					$regex="/1[34568]+\\d{9}/";
					preg_match_all($regex,$rs_a[0][phone], $phones);
				
					if(strlen($phones[0][0])==11)
						echo "<a href='../JXC/sms_sendlist_newai.php?".base64_encode("action=add_default&sendlist=".$phones[0][0])."' target='_blank'>".$phones[0][0]."</a>";
				
					else
						echo  $rs_a[0][phone]?></td>
	</tr>
	<tr>
		<td nowrap="" class="TableContent" width="20%">����:</td>
		<td class="TableData" colspan="1"><?php echo $rs_a[0][fax] ?></td>
		<td nowrap="" class="TableContent" width="20%">EMail:</td>
		<td class="TableData" colspan="1"><?php print "<a href='../CRM/email_newai.php?".base64_encode("action=add_default&sendlist=".$rs_a[0][email])."&fromsrc=' target='_blank' >".$rs_a[0][email]."</a>";?></td>
	</tr>
	<tr>
		<td nowrap="" class="TableContent" width="20%">��ַ:</td>
		<?php 
		$urlwangzhi=$rs_a[0][netword];
		$flag=stripos($rs_a[0][netword], "http://");
		if($flag===false)
				$urlwangzhi="http://".$urlwangzhi;
		?>
		<td class="TableData" colspan="1"><a href='<?php echo $urlwangzhi ?>' target='_blank'><?php echo $rs_a[0][netword] ?></a></td>
		<td nowrap="" class="TableContent" width="20%">������:</td>
		<td class="TableData" colspan="1"><?php echo $rs_a[0][bank] ?></td>
	</tr>
	<tr>
		<td nowrap="" class="TableContent" width="20%">�����ʻ�:</td>
		<td class="TableData" colspan="1"><?php echo $rs_a[0]['accountnumber'] ?></td>
		<td class="TableContent" nowrap="">��������:</td>
		<td class="TableData" nowrap="" colspan="1"><?php echo $rs_a[0][enterstype] ?></td>
	</tr>
	<tr>
		<td class="TableContent" nowrap="">��������:</td>
		<td class="TableData" nowrap="" colspan="1"><?php
			$shengname='';
			$cityname='';
			$townname='';
			$shengcode=substr($rs_a[0][style],0,2);
			if($shengcode!='00')
			{
				$shengcode.="0000";
				$shengname=returntablefield("customerarea", "rowid", $shengcode, "name");
			}
			$citycode=substr($rs_a[0][style],0,4);
			if(substr($citycode,-2)!='00')
			{
				$citycode.="00";
				$cityname=returntablefield("customerarea", "rowid", $citycode, "name");
			}
			if(substr($rs_a[0][style],-2)!='00')
				$townname=returntablefield("customerarea", "rowid", $rs_a[0][style], "name");
		echo $shengname.$cityname.$townname ?></td>
		<td class="TableContent" nowrap="">������:</td>
		<td class="TableData" nowrap="" colspan="1"><a target='_blank' href='../Framework/user_newai.php?<?php echo base64_encode("action=view_default&UID=$UID")?>'><?php echo $rs_a[0][blong] ?></a></td>
	</tr>
	<tr>
		<td class="TableContent" nowrap="">������:</td>
		<td class="TableData" nowrap="" colspan="1"><a target='_blank' href='../Framework/user_newai.php?<?php echo base64_encode("action=view_default&UID=$UID1")?>'><?php echo $rs_a[0][USER_NAME] ?></a></td>
		
		<td class="TableContent" nowrap="">�ͻ���Դ:</td>
		<td class="TableData" nowrap="" colspan="1"><?php echo $rs_a[0][origin] ?></td>
	</tr>
	<tr>
		<td class="TableContent" nowrap="">���۷�ʽ:</td>
		<td class="TableData" nowrap="" colspan="1"><?php echo $rs_a[0][salemode] ?></td>
		<td class="TableContent" nowrap="">��ҵ����:</td>
		<td class="TableData" nowrap="" colspan="1"><?php echo $rs_a[0][property] ?></td>
	</tr>
	<tr>
		<td nowrap="" class="TableContent" width="20%">Ԥ��ֵ:</td>
		<td class="TableData" colspan="1"><?php echo $rs_a[0][yuchuzhi] ?></td>
		<td nowrap="" class="TableContent" width="20%">����ʱ��:</td>
		<td class="TableData" colspan="1"><?php echo $rs_a[0][createdate] ?></td>
	</tr>
	<tr>
		<td nowrap="" class="TableContent" width="20%">���л���:</td>
		<td class="TableData" style="word-break: break-all" colspan="1"><?php echo $rs_a[0][integral] ?></td>
		<td nowrap="" class="TableContent" width="20%">������ַ:</td>
		<td class="TableData" style="word-break: break-all" colspan="1"><?php echo $rs_a[0][supplycn] ?></td>
	</tr>
	<tr>
		<td nowrap="" class="TableContent" width="20%">��ע:</td>
		<td class="TableData" style="word-break: break-all" colspan="3"><?php echo $rs_a[0][explainStr] ?></td>
	</tr>
<style> 
.tablecontent td {
background: #F0F0F0;
border-top: 1px #CFDDEA solid;
border-bottom: 1px #CFDDEA solid;
}
</style>
	<?php
//��ϵ��
	$sql = "SELECT a.rowid,a.linkmanname,a.gender,a.mobile,a.phone,a.email,a.fax,a.business,a.postcode,a.businessexpian,a.mark FROM linkman a WHERE a.customerid=".$billid;
	$rs = $db->Execute($sql);
	$rs_a = $rs->GetArray();
	print("<tr class='TableData1'><td colspan='32' nowrap='' width='100%'>
				<table class='TableBlock' align='center' width='99.6%'>
					<tr class='TableContent'>
						<td colspan='10' nowrap=''><b  style='display:block;float:left;'><a href='linkman_newai.php?".base64_encode("action=init_default_search&searchfield=customerid&searchvalue=$custname")."' target='_blank'>��ϵ���б�</a></b>						
						<input style='float:right;display:block;' type='button' accesskey='n' value=' �½� ' class='SmallButtonA' onclick=\"window.open('linkman_newai.php?action=add_default&customerid=".$_GET['ROWID']."');\" title='��ݼ�:ALT+n'>
						</td>
					</tr>");			
	print("<tr class='TableContent'> 
						<td nowrap=''>����</td>
						<td nowrap=''>�Ա�</td>
						<td nowrap=''>ְ��</td>						
						<td nowrap=''>�绰</td>
						<td nowrap=''>�ֻ�</td>
						<td nowrap=''>E-mail</td>
						<td nowrap=''>QQ</td>
						<td nowrap=''>����</td>
						<td nowrap=''>��������</td>
						<td nowrap=''>��ע</td>
					</tr>");

	if(empty($rs_a)){
		print("<tr class='TableData1'  height='100%'><td colspan='10' nowrap=''>����</td></tr>");
	}else{
		foreach($rs_a as $row){
			print "<tr class='TableData1'>";
			if($editrole) 
				print "<td nowrap=''><a href='linkman_newai.php?".base64_encode("action=edit_default&ROWID=".$row['rowid'])."' target='_blank'><img src='../Framework/images/edit1.gif' title='�༭'></a>".$row['linkmanname']."</td>";
			else
				print "	<td nowrap=''>".$row['linkmanname']."</td>";
			print "	<td nowrap=''>".($row['gender']==1?'��':'Ů')."</td>
					<td nowrap=''>".$row['business']."</td>
					<td nowrap=''>".$row['phone']."</td>
					<td nowrap=''><a href='sms_sendlist_newai.php?".base64_encode("action=add_default&sendlist=".$row['mobile'])."' target='_blank' >".$row['mobile']."</a></td>
					<td nowrap=''><a href='../CRM/email_newai.php?".base64_encode("action=add_default&sendlist=".$row['rowid'])."&fromsrc=customer' target='_blank' >".$row['email']."</a></td>
					<td nowrap=''>".(!empty($row['fax'])?"<a target=\"_blank\" href=\"http://wpa.qq.com/msgrd?v=3&amp;uin=".$row[fax]."&amp;site=qq&amp;menu=yes\"><img border=\"0\" src=\"http://wpa.qq.com/pa?p=2:".$row["fax"].":45\" onerror=\"this.src='".ROOT_DIR."general/ERP/Framework/images/help3.gif';this.title='��QQ������ܲ�����'\" title=\"������﷢��Ϣ\">".$row["fax"]."</a>":'')."</td>
					<td nowrap=''>".$row['postcode']."</td>
					<td nowrap=''>".$row['businessexpian']."</td>
					<td nowrap=''>".$row['mark']."</td>
					</tr>";		
		}
	}
	print("</table>");
	print("</td></tr>");
	
	
//���ۻ���	

	$sql = "SELECT a.`������`,a.`��ϵ��`,a.`���`,b.USER_NAME,a.`��������`,a.`�ͻ�����`,c.linkmanname,a.`����ʱ��`,d.`�׶�` FROM crm_chance a
LEFT JOIN `user` b on a.`������`=b.USER_ID 
LEFT JOIN linkman c on a.`��ϵ��`=c.ROWID 
LEFT JOIN crm_jieduan d on a.`��ǰ�׶�`=d.`���`  WHERE a.`�ͻ�����`=".$billid."  ORDER BY a.`����ʱ��` DESC  LIMIT 0,5";
	$rs = $db->Execute($sql);
	$rs_a = $rs->GetArray();
	print("<tr class='TableData1'><td colspan='32' nowrap='' width='100%'>
	<table class=\"TableBlock\" align=\"center\" width=\"2\" style=\"float:left;\"><tbody><tr class=\"TableContent\"></tr></table>
				<table class='TableBlock' align='center' width='49%' style='float:left;height: auto;'>
					<tr class='TableContent'>
						<td colspan='5' nowrap=''><b  style='display:block;float:left;'><a href='crm_chance_newai.php?".base64_encode("action=init_default_search&searchfield=�ͻ�����&searchvalue=$custname")."' target='_blank'>���ۻ���</a></b>						
						<input style='float:right;display:block;' type='button' accesskey='n' value=' �½� ' class='SmallButtonA' onclick=\"window.open('crm_chance_newai.php?action=add_default&customerid=".$_GET['ROWID']."');\" title='��ݼ�:ALT+n'>
						</td>
					</tr>");			
	print("<tr class='TableContent'> 
						<td nowrap=''>����ʱ��</td>
						<td nowrap=''>��������</td>
						<td nowrap=''>��ϵ��</td>
						<td nowrap=''>������</td>
						<td nowrap=''>��ǰ�׶�</td>
					</tr>");

	if(empty($rs_a)){
		print("<tr class='TableData1'  height='100%'><td colspan='5' nowrap=''>����</td></tr>");
		print("<tr class='TableData1'  height='100%'><td colspan='5' nowrap=''>&nbsp;</td></tr>");
		print("<tr class='TableData1'  height='100%'><td colspan='5' nowrap=''>&nbsp;</td></tr>");
		print("<tr class='TableData1'  height='100%'><td colspan='5' nowrap=''>&nbsp;</td></tr>");
		print("<tr class='TableData1'  height='100%'><td colspan='5' nowrap=''>&nbsp;</td></tr>");
	}else{
		for($i=0;$i<5;$i++){
			if(!isset($rs_a[$i])){
				print("<tr class='TableData1'  height='50%'><td colspan='5' nowrap=''>&nbsp;</td></tr>");
				continue;
			}
			$UID=returntablefield("user", "user_id", $rs_a[$i]['������'], "uid");
			print("<tr class='TableData1'>
						<td nowrap=''>".$rs_a[$i]['����ʱ��']."</td>
						<td nowrap=''><a target='_blank' href='crm_chance_newai.php?".base64_encode("action=view_default&���=".$rs_a[$i]['���'])."' title='".cutStr($rs_a[$i]['�ͻ�����'],50)."'>".cutStr($rs_a[$i]['��������'],18)."</a></td>
						<td nowrap=''><a target='_blank' href='../JXC/linkman_newai.php?".base64_encode("action=view_default&ROWID=".$rs_a[$i]['��ϵ��'])."'>".$rs_a[$i]['linkmanname']."</a></td>
						<td nowrap=''><a target='_blank' href='../Framework/user_newai.php?".base64_encode("action=view_default&UID=".$UID)."'>".$rs_a[$i]['USER_NAME']."</a></td>
						
						<td nowrap=''>".$rs_a[$i]['�׶�']."</td>						
					</tr>");		
		}
	}
	print("</table>");
	print('<table class="TableBlock" align="center" width="5" style="float:left;"><tbody><tr class="TableContent"></tr></table>');
	//print("</td></tr>");

//���ټ�¼

	$sql = "SELECT a.chance,a.linkmanid,a.id,a.contacttime,a.describes,b.`�׶�`,c.`����`,d.linkmanname  FROM crm_contact a LEFT JOIN crm_jieduan b on a.stage=b.`���` left join crm_dict_servicesources c on a.contact=c.`���` left join linkman d on a.linkmanid=d.ROWID  WHERE  a.customerid=".$billid."  ORDER BY a.contacttime DESC  LIMIT 0,5";;
		//exit($sql);
	$rs = $db->Execute($sql);
	$rs_a = $rs->GetArray();
	print("<table class='TableBlock' align='center' width='50%' style='float:left;height: auto;'>
					<tr class='TableContent'>
						<td colspan='5' nowrap=''><b   style='display:block;float:left;'><a href='crm_contact_newai.php?".base64_encode("action=init_default_search&searchfield=customerid&searchvalue=$custname")."' target='_blank'>���ټ�¼</a></b>
							<input style='float:right;' type='button' accesskey='n' value=' �½� ' class='SmallButtonA' onclick=\"window.open('crm_contact_newai.php?action=add_default&customerid=".$_GET['ROWID']."');\" title='��ݼ�:ALT+n'>						
						</td>
					</tr>");			
	print("<tr class='TableContent'>
						<td nowrap=''>��ϵʱ��</td>
						<td nowrap=''>����</td>
						<td nowrap=''>�׶�</td>
						<td nowrap=''>��ϵ��ʽ</td>
						<td nowrap=''>��ϵ��</td>
					</tr>");
	if(empty($rs_a)){
		print("<tr class='TableData1'  height='100%'><td colspan='5' nowrap=''>����</td></tr>");
		print("<tr class='TableData1'  height='100%'><td colspan='5' nowrap=''>&nbsp;</td></tr>");
		print("<tr class='TableData1'  height='100%'><td colspan='5' nowrap=''>&nbsp;</td></tr>");
		print("<tr class='TableData1'  height='100%'><td colspan='5' nowrap=''>&nbsp;</td></tr>");
		print("<tr class='TableData1'  height='100%'><td colspan='5' nowrap=''>&nbsp;</td></tr>");
	}else{
		for($i=0;$i<5;$i++){
			if(!isset($rs_a[$i])){
				print("<tr class='TableData1'  height='100%'><td colspan='5' nowrap=''>&nbsp;</td></tr>");
				continue;
			}
			$zhuti=returntablefield("crm_chance", "���", $rs_a[$i]['chance'], '��������,�ͻ�����');
			print("<tr class='TableData1'>
						<td nowrap=''>".$rs_a[$i]['contacttime']."</td>
						<td nowrap=''><a target='_blank' href='crm_chance_newai.php?".base64_encode("action=view_default&���=".$rs_a[$i]['chance'])."' title='".cutStr($zhuti['�ͻ�����'],50)."'>".cutStr($zhuti['��������'],15)."</a></td>
						<td nowrap=''><a target='_blank' href='crm_contact_newai.php?".base64_encode("action=view_default&id=".$rs_a[$i]['id'])."' title='".cutStr($rs_a[$i]['describes'],50)."'>".$rs_a[$i]['�׶�']."</a></td>
						<td nowrap=''>".$rs_a[$i]['����']."</td>
						<td nowrap=''><a target='_blank' href='../JXC/linkman_newai.php?".base64_encode("action=view_default&ROWID=".$rs_a[$i]['linkmanid'])."'>".$rs_a[$i][linkmanname]."</a></td>						
					</tr>");		
		}
	}
	print("</table>");
	print("</td></tr>");

//�����¼

	$sql = "SELECT * FROM customer_xuqiu where customerid=".$billid."  ORDER BY createtime DESC LIMIT 0,5";
	//exit($sql);
	$rs = $db->Execute($sql);
	$rs_a = $rs->GetArray();
	print("<tr class='TableData1'><td colspan='32' nowrap='' width='100%'>
	<table class=\"TableBlock\" align=\"center\" width=\"2\" style=\"float:left;\"><tbody><tr class=\"TableContent\"></tr></table>
				<table class='TableBlock' align='center' width='49%' style='float:left;height: auto;'>
					<tr class='TableContent'>
						<td colspan='5' nowrap=''><b   style='display:block;float:left;'><a href='customer_xuqiu_newai.php?".base64_encode("action=init_default_search&searchfield=customerid&searchvalue=$custname")."' target='_blank'>�����뷽��</a></b>
								<input style='float:right;' type='button' accesskey='n' value=' �½� ' class='SmallButtonA' onclick=\"window.open('customer_xuqiu_newai.php?action=add_default&customerid=".$_GET['ROWID']."');\" title='��ݼ�:ALT+n'>						
						</td>
					</tr>");			
	print("<tr class='TableContent'>
						<td nowrap=''>��������</td>
						<td nowrap=''>�ṩ��</td>
						<td nowrap=''>��Ӧ����</td>
						<td nowrap=''>��Ҫ�̶�</td>
						<td nowrap=''>���ʱ��</td>
						
					</tr>");
	if(empty($rs_a)){
		print("<tr class='TableData1'  height='100%'><td colspan='5' nowrap=''>����</td></tr>");
		print("<tr class='TableData1'  height='100%'><td colspan='5' nowrap=''>&nbsp;</td></tr>");
		print("<tr class='TableData1'  height='100%'><td colspan='5' nowrap=''>&nbsp;</td></tr>");
		print("<tr class='TableData1'  height='100%'><td colspan='5' nowrap=''>&nbsp;</td></tr>");
		print("<tr class='TableData1'  height='100%'><td colspan='5' nowrap=''>&nbsp;</td></tr>");
	}else{
		for($i=0;$i<5;$i++){
			if(!isset($rs_a[$i])){
				print("<tr class='TableData1'  height='100%'><td colspan='5' nowrap=''>&nbsp;</td></tr>");
				continue;
			}
			$chacename=returntablefield("crm_chance", "���", $rs_a[$i]['chanceid'], "��������");
			$important=returntablefield("important", "id", $rs_a[$i]['import'], "name");
			print("<tr class='TableData1'>
						<td nowrap=''><a target='_blank' href='customer_xuqiu_newai.php?".base64_encode("action=view_default&id=".$rs_a[$i]['id'])."'>".cutStr($rs_a[$i]['zhuti'],12)."</a></td>
						<td nowrap=''>".$rs_a[$i]['tigongren']."</td>
						<td nowrap=''><a target='_blank' href='crm_chance_newai.php?".base64_encode("action=view_default&���=".$rs_a[$i]['chanceid'])."'>".cutstr($chacename,12)."</a></td>		
						<td nowrap=''>$important</td>
						<td nowrap=''>".cutStr($rs_a[$i][createtime],10)."</td>
										
					</tr>");		
		}
	}



	print("</table>");
	print('<table class="TableBlock" align="center" width="5" style="float:left;"><tbody><tr class="TableContent"></tr></table>');
	//print("</td></tr>");
//���ü�¼
	$sql = "SELECT * FROM crm_feiyong_sq WHERE �ͻ�����=".$billid." order BY ����ʱ�� desc LIMIT 0,5";
	//exit($sql);
	$rs = $db->Execute($sql);
	$rs_a = $rs->GetArray();
	print("<table class='TableBlock' align='center' width='50%' style='float:left;height: auto;'>
					<tr class='TableContent'>
						<td colspan='6' nowrap=''><b  style='display:block;float:left;'><a href='../CRM/crm_feiyong_sq_newai.php?".base64_encode("action=init_default_search&searchfield=�ͻ�����&searchvalue=$custname")."' target='_blank'>���ü�¼</a></b>
						<input style='float:right;' type='button' value=' �½� ' class='SmallButtonA' onclick=\"window.open('../CRM/crm_feiyong_sq_newai.php?action=add_default&�ͻ�����=".$_GET['ROWID']."');\"></span>									
						</td>
					</tr>");			
	print("<tr class='TableContent'>
						<td nowrap=''>��������</td>
						<td nowrap=''>���</td>
						<td nowrap=''>������;</td>
						<td nowrap=''>��������</td>
						<td nowrap=''>������ </td>
						<td nowrap=''>�Ƿ���� </td>
					</tr>");

	if(empty($rs_a)){
		print("<tr class='TableData1'  height='100%'><td colspan='7' nowrap=''>����</td></tr>");
		print("<tr class='TableData1'  height='100%'><td colspan='7' nowrap=''>&nbsp;</td></tr>");
		print("<tr class='TableData1'  height='100%'><td colspan='7' nowrap=''>&nbsp;</td></tr>");
		print("<tr class='TableData1'  height='100%'><td colspan='7' nowrap=''>&nbsp;</td></tr>");
		print("<tr class='TableData1'  height='100%'><td colspan='7' nowrap=''>&nbsp;</td></tr>");
	
	}else{
		for($i=0;$i<5;$i++){
			if(!isset($rs_a[$i])){
				print("<tr class='TableData1'  height='100%'><td colspan='7' nowrap=''>&nbsp;</td></tr>");
				continue;
			}
			$UID=returntablefield("user", "user_id", $rs_a[$i]['¼��Ա'], "uid,user_name");
			$feiyongtype=returntablefield("v_feiyongbaoxiao", "id", $rs_a[$i]['��������'], "typename");
			$shenhe=returntablefield("crm_shenhezhuangtai", "id", $rs_a[$i]['�Ƿ����'], "name");
			
			print("<tr class='TableData1'>
						<td nowrap=''>$feiyongtype</td>
						<td nowrap=''>".$rs_a[$i]['���']."</td>
						<td nowrap=''>".$rs_a[$i]['������;']."</td>
						<td nowrap=''>".$rs_a[$i]['��������']."</td>
						<td nowrap=''><a target='_blank' href='../Framework/user_newai.php?".base64_encode("action=view_default&UID=".$UID['uid'])."'>".$UID['user_name']."</a></td>
						<td nowrap=''>".$shenhe."</td>	
												
					</tr>");		
		}
	}
	print("</table>");
	print("</td></tr>");

//������Ʒ

	$sql = "SELECT * FROM competeproduct where customerid=".$billid."  ORDER BY createtime DESC LIMIT 0,5";
	//exit($sql);
	$rs = $db->Execute($sql);
	$rs_a = $rs->GetArray();
	print("<tr class='TableData1'><td colspan='32' nowrap='' width='100%'>
	<table class=\"TableBlock\" align=\"center\" width=\"2\" style=\"float:left;\"><tbody><tr class=\"TableContent\"></tr></table>
				<table class='TableBlock' align='center' width='49%' style='float:left;height: auto;'>
					<tr class='TableContent'>
						<td colspan='5' nowrap=''><b   style='display:block;float:left;'><a href='competeproduct_newai.php?".base64_encode("action=init_default_search&searchfield=customerid&searchvalue=$custname")."' target='_blank'>������Ʒ</a></b>
								<input style='float:right;' type='button' accesskey='n' value=' �½� ' class='SmallButtonA' onclick=\"window.open('competeproduct_newai.php?action=add_default&customerid=".$_GET['ROWID']."');\" title='��ݼ�:ALT+n'>						
						</td>
					</tr>");			
	print("<tr class='TableContent'>
						<td nowrap=''>������</td>
						<td nowrap=''>��Ӧ����</td>
						<td nowrap=''>������Ʒ</td>
						<td nowrap=''>��˾����</td>
						<td nowrap=''>����ʱ��</td>
						
					</tr>");
	if(empty($rs_a)){
		print("<tr class='TableData1'  height='100%'><td colspan='5' nowrap=''>����</td></tr>");
		print("<tr class='TableData1'  height='100%'><td colspan='5' nowrap=''>&nbsp;</td></tr>");
		print("<tr class='TableData1'  height='100%'><td colspan='5' nowrap=''>&nbsp;</td></tr>");
		print("<tr class='TableData1'  height='100%'><td colspan='5' nowrap=''>&nbsp;</td></tr>");
		print("<tr class='TableData1'  height='100%'><td colspan='5' nowrap=''>&nbsp;</td></tr>");
	}else{
		for($i=0;$i<5;$i++){
			if(!isset($rs_a[$i])){
				print("<tr class='TableData1'  height='100%'><td colspan='5' nowrap=''>&nbsp;</td></tr>");
				continue;
			}
			$chacename=returntablefield("crm_chance", "���", $rs_a[$i]['productid'], "��������");
			$UID=returntablefield("user", "user_id", $rs_a[$i]['user_id'], "uid,user_name");
			print("<tr class='TableData1'>
					<td nowrap=''><a target='_blank' href='../Framework/user_newai.php?".base64_encode("action=view_default&UID=".$UID['uid'])."'>".$UID['user_name']."</a></td>
					<td nowrap=''><a target='_blank' href='crm_chance_newai.php?".base64_encode("action=view_default&���=".$rs_a[$i]['productid'])."'>".cutstr($chacename,9)."</a></td>
						<td nowrap=''><a target='_blank' href='competeproduct_newai.php?".base64_encode("action=view_default&ROWID=".$rs_a[$i]['ROWID'])."'>".cutStr($rs_a[$i]['comproduct'],9)."</a></td>
						<td nowrap=''>".cutStr($rs_a[$i]['infofrom'],9)."</td>
						<td nowrap=''>".cutStr($rs_a[$i]['createtime'],10)."</td>
										
					</tr>");		
		}
	}



	print("</table>");
	print('<table class="TableBlock" align="center" width="5" style="float:left;"><tbody><tr class="TableContent"></tr></table>');
	//print("</td></tr>");
//���ۼ�¼
$sql = "SELECT  a.���ۻ���,a.������,a.`������`,a.ROWID,a.`����ʱ��`,a.`����`,b.USER_NAME,c.linkmanname,d.`��������` FROM customerproduct a LEFT JOIN `user` b on a.`������`=b.USER_ID LEFT JOIN linkman c on a.`������`=c.ROWID LEFT JOIN crm_chance d on a.`���ۻ���`=d.`���` WHERE a.`�ͻ�`=".$billid."  ORDER BY a.`����ʱ��` DESC LIMIT 0,5";
	//exit($sql);
	$rs = $db->Execute($sql);
	$rs_a = $rs->GetArray();
	print("<table class='TableBlock' align='center' width='50%' style='float:left;height: auto;'>
					<tr class='TableContent'>
						<td colspan='5' nowrap=''><b  style='display:block;float:left;'><a href='customerproduct_newai.php?".base64_encode("action=init_default_search&searchfield=�ͻ�&searchvalue=$custname")."' target='_blank'>���ۼ�¼</a></b>
						<input style='float:right;' type='button' value=' �½� ' class='SmallButtonA' onclick=\"window.open('customerproduct_newai.php?action=add_default&customerid=".$_GET['ROWID']."');\"></span>									
						</td>
					</tr>");	
			
	print("<tr class='TableContent'>
						<td nowrap=''>����ʱ��</td>
						<td nowrap=''>����</td>
						<td nowrap=''>������</td>
						<td nowrap=''>������</td>
						<td nowrap=''>���ۻ���</td>
						
					</tr>");
	if(empty($rs_a)){
		print("<tr class='TableData1'  height='100%'><td colspan='5' nowrap=''>����</td></tr>");
		print("<tr class='TableData1'  height='100%'><td colspan='5' nowrap=''>&nbsp;</td></tr>");
		print("<tr class='TableData1'  height='100%'><td colspan='5' nowrap=''>&nbsp;</td></tr>");
		print("<tr class='TableData1'  height='100%'><td colspan='5' nowrap=''>&nbsp;</td></tr>");
		print("<tr class='TableData1'  height='100%'><td colspan='5' nowrap=''>&nbsp;</td></tr>");
	}else{
		for($i=0;$i<5;$i++){
			if(!isset($rs_a[$i])){
				print("<tr class='TableData1'  height='100%'><td colspan='5' nowrap=''>&nbsp;</td></tr>");
				continue;
			}
			$UID=returntablefield("user", "user_id", $rs_a[$i]['������'], "uid");
			print("<tr class='TableData1'>
						<td nowrap=''>".$rs_a[$i]['����ʱ��']."</td>
						<td nowrap=''><a target='_blank' href='customerproduct_newai.php?".base64_encode("action=view_default&ROWID=".$rs_a[$i]['ROWID'])."'>".cutStr($rs_a[$i]['����'],12)."</a></td>
						<td nowrap=''><a target='_blank' href='../Framework/user_newai.php?".base64_encode("action=view_default&UID=".$UID)."'>".$rs_a[$i]['USER_NAME']."</a></td>
						<td nowrap=''><a target='_blank' href='../JXC/linkman_newai.php?".base64_encode("action=view_default&ROWID=".$rs_a[$i]['������'])."'>".$rs_a[$i][linkmanname]."</a></td>
						<td nowrap=''><a target='_blank' href='crm_chance_newai.php?".base64_encode("action=view_default&���=".$rs_a[$i]['���ۻ���'])."'>".cutstr($rs_a[$i]['��������'],12)."</a></td>						
					</tr>");		
		}
	}
	
	print("</table>");
	print("</td></tr>");
	

//��Ŀ����
$sql = "SELECT * FROM crm_shenqingbaobei WHERE customerid=".$billid." order BY createtime desc LIMIT 0,5";
	//exit($sql);
	$rs = $db->Execute($sql);
	$rs_a = $rs->GetArray();
	print("<tr class='TableData1'><td colspan='32' nowrap='' width='100%'>
	<table class=\"TableBlock\" align=\"center\" width=\"2\" style=\"float:left;\"><tbody><tr class=\"TableContent\"></tr></table>
				<table class='TableBlock' align='center' width='49%' style='float:left;height: auto;'>
					<tr class='TableContent'>
						<td colspan='5' nowrap=''><b   style='display:block;float:left;'><a href='crm_shenqingbaobei_newai.php?".base64_encode("action=init_default_search&searchfield=customerid&searchvalue=$custname")."' target='_blank'>��Ŀ����</a></b>
								<input style='float:right;' type='button' accesskey='n' value=' �½� ' class='SmallButtonA' onclick=\"window.open('crm_shenqingbaobei_newai.php?action=add_default&customerid=".$_GET['ROWID']."');\" title='��ݼ�:ALT+n'>						
						</td>
					</tr>");	
		
	print("<tr class='TableContent'>
						<td nowrap=''>������</td>
						<td nowrap=''>��Ӧ����</td>
						<td nowrap=''>����ʱ��</td>
						<td nowrap=''>���״̬</td>
						<td nowrap=''>�����</td>
					</tr>");

	if(empty($rs_a)){
		print("<tr class='TableData1'  height='100%'><td colspan='7' nowrap=''>����</td></tr>");
		print("<tr class='TableData1'  height='100%'><td colspan='7' nowrap=''>&nbsp;</td></tr>");
		print("<tr class='TableData1'  height='100%'><td colspan='7' nowrap=''>&nbsp;</td></tr>");
		print("<tr class='TableData1'  height='100%'><td colspan='7' nowrap=''>&nbsp;</td></tr>");
		print("<tr class='TableData1'  height='100%'><td colspan='7' nowrap=''>&nbsp;</td></tr>");
	}else{
		for($i=0;$i<5;$i++){
			if(!isset($rs_a[$i])){
				print("<tr class='TableData1'  height='100%'><td colspan='7' nowrap=''>&nbsp;</td></tr>");
				continue;
			}
			$UID=returntablefield("user", "user_id", $rs_a[$i]['createman'], "uid,user_name");
			$UID1=returntablefield("user", "user_id", $rs_a[$i]['shenheman'], "uid,user_name");
			$chacename=returntablefield("crm_chance", "���", $rs_a[$i]['chanceid'], "��������");
			$shenhestate=returntablefield("crm_shenhezhuangtai", "id", $rs_a[$i]['state'], "name");
			print("<tr class='TableData1'>
					<td nowrap=''><a target='_blank' href='../Framework/user_newai.php?".base64_encode("action=view_default&UID=".$UID['uid'])."'>".$UID['user_name']."</a></td>
					<td nowrap=''><a target='_blank' href='crm_chance_newai.php?".base64_encode("action=view_default&���=".$rs_a[$i]['chanceid'])."'>".cutstr($chacename,15)."</a></td>
					<td nowrap=''>".cutStr($rs_a[$i]['createtime'],10)."</td>
					<td nowrap=''>".$shenhestate."</td>
					<td nowrap=''><a target='_blank' href='../Framework/user_newai.php?".base64_encode("action=view_default&UID=".$UID1['uid'])."'>".$UID1['user_name']."</a></td>
						
						
												
					</tr>");		
		}
	}

	print("</table>");
	print('<table class="TableBlock" align="center" width="5" style="float:left;"><tbody><tr class="TableContent"></tr></table>');
	//print("</td></tr>");

//�ɽ���¼
	$sql = "SELECT a.billtype,a.user_id,a.billid,a.zhuti,a.totalmoney,a.huikuanjine,a.fahuojine,b.USER_NAME,a.createtime FROM sellplanmain a LEFT JOIN user b on a.user_id=b.USER_ID WHERE a.supplyid=".$billid." order BY a.createtime desc LIMIT 0,5";
	//exit($sql);
	$rs = $db->Execute($sql);
	$rs_a = $rs->GetArray();
	print("<table class='TableBlock' align='center' width='50%' style='float:left;height: auto;'>
					<tr class='TableContent'>
						<td colspan='7' nowrap=''><b  style='display:block;float:left;'><a href='sellplanmain_newai.php?".base64_encode("action=init_default_search&searchfield=supplyid&searchvalue=$custname")."' target='_blank'>�ɽ���¼</a></b>
						<input style='float:right;' type='button' value=' �½� ' class='SmallButtonA' onclick=\"window.open('sellplanmain_newai.php?action=add_default&customerid=".$_GET['ROWID']."');\"></span>									
						</td>
					</tr>");			
	print("<tr class='TableContent'>
						<td nowrap=''>����</td>
						<td nowrap=''>����</td>
						<td nowrap=''>�ܽ��</td>
						<td nowrap=''>�ؿ�</td>
						<td nowrap=''>����/����</td>
						<td nowrap=''>������</td>
						<td nowrap=''>��������</td>
						
					</tr>");

	if(empty($rs_a)){
		print("<tr class='TableData1'  height='100%'><td colspan='7' nowrap=''>����</td></tr>");
		print("<tr class='TableData1'  height='100%'><td colspan='7' nowrap=''>&nbsp;</td></tr>");
		print("<tr class='TableData1'  height='100%'><td colspan='7' nowrap=''>&nbsp;</td></tr>");
		print("<tr class='TableData1'  height='100%'><td colspan='7' nowrap=''>&nbsp;</td></tr>");
		print("<tr class='TableData1'  height='100%'><td colspan='7' nowrap=''>&nbsp;</td></tr>");
	}else{
		for($i=0;$i<5;$i++){
			if(!isset($rs_a[$i])){
				print("<tr class='TableData1'  height='100%'><td colspan='7' nowrap=''>&nbsp;</td></tr>");
				continue;
			}
			$UID=returntablefield("user", "user_id", $rs_a[$i]['user_id'], "uid");
			$urlname='sellplanmain';
			if($rs_a[$i]['billtype']=='1')
				$urlname='sellcontract';
			else if($rs_a[$i]['billtype']=='2')
				$urlname='sellplanmain';
			else 
				$urlname='sellonemain';
			print("<tr class='TableData1'>
						<td nowrap=''>".$rs_a[$i][billid]."</td>
						
						<td nowrap=''><a target='_blank' href='".$urlname."_newai.php?".base64_encode("action=view_default&billid=".$rs_a[$i]['billid'])."'>".cutStr($rs_a[$i]['zhuti'],15)."</a></td>
						<td nowrap=''>".$rs_a[$i]['totalmoney']."</td>
						<td nowrap=''>".$rs_a[$i][huikuanjine]."</td>	
						<td nowrap=''>".$rs_a[$i][fahuojine]."</td>	
						<td nowrap=''><a target='_blank' href='../Framework/user_newai.php?".base64_encode("action=view_default&UID=".$UID)."'>".$rs_a[$i]['USER_NAME']."</a></td>
						<td nowrap=''>".$rs_a[$i][createtime]."</td>						
					</tr>");		
		}
	}
	print("</table>");
	print("</td></tr>");

//���ֶһ���¼
	$sql = "SELECT a.createman,a.ROWID,a.prodid,prodname,a.integral,a.exchangenum,b.USER_NAME,a.createtime FROM exchange a LEFT JOIN `user` b on a.createman=b.USER_ID WHERE a.customid=".$billid." ORDER BY a.createtime desc LIMIT 0,5";
	//exit($sql);
	$rs = $db->Execute($sql);
	$rs_a = $rs->GetArray();
	print("<tr class='TableData1'><td colspan='32' nowrap='' width='100%'>
	<table class=\"TableBlock\" align=\"center\" width=\"2\" style=\"float:left;\"><tbody><tr class=\"TableContent\"></tr></table>
				<table class='TableBlock' align='center' width='49%' style='float:left;height: auto;'>
					<tr class='TableContent'>
						<td colspan='5' nowrap=''><b  style='display:block;float:left;'><a href='exchange_newai.php?".base64_encode("action=init_default_search&searchfield=customid&searchvalue=$custname")."' target='_blank'>���ֶһ���¼</a></b>
							<input style='float:right;' type='button' accesskey='n' value=' �½� ' class='SmallButtonA' onclick=\"window.open('exchange_newai.php?action=add_default&customerid=".$_GET['ROWID']."');\" title='��ݼ�:ALT+n'>					
								</td>
					</tr>");			
	print("<tr class='TableContent'>						
						<td nowrap=''>��Ʒ����</td>
						<td nowrap=''>���ѻ���</td>
						<td nowrap=''>�һ�����</td>
						<td nowrap=''>�һ���</td>
						<td nowrap=''>�һ�ʱ��</td>						
					</tr>");

	if(empty($rs_a)){
		print("<tr class='TableData1'  height='100%'><td colspan='5' nowrap=''>����</td></tr>");
		print("<tr class='TableData1'  height='100%'><td colspan='5' nowrap=''>&nbsp;</td></tr>");
		print("<tr class='TableData1'  height='100%'><td colspan='5' nowrap=''>&nbsp;</td></tr>");
		print("<tr class='TableData1'  height='100%'><td colspan='5' nowrap=''>&nbsp;</td></tr>");
		print("<tr class='TableData1'  height='100%'><td colspan='5' nowrap=''>&nbsp;</td></tr>");
	}else{

		for($i=0;$i<5;$i++){
			if(!isset($rs_a[$i])){
				print("<tr class='TableData1'  height='100%'><td colspan='5' nowrap=''>&nbsp;</td> </tr>");
				continue;
			}
			$UID=returntablefield("user", "user_id", $rs_a[$i]['createman'], "uid");
			print("<tr class='TableData1'>
						
						<td nowrap=''><a target='_blank' href='product_newai.php?".base64_encode("action=view_default&productid=".$rs_a[$i]['prodid'])."'>".$rs_a[$i]['prodname']."</a></td>
						<td nowrap=''>".$rs_a[$i]['integral']."</td>
						<td nowrap=''>".$rs_a[$i]['exchangenum']."</td>
						<td nowrap=''><a target='_blank' href='../Framework/user_newai.php?".base64_encode("action=view_default&UID=".$UID)."'>".$rs_a[$i]['USER_NAME']."</a></td>
						
						<td nowrap=''>".$rs_a[$i]['createtime']."</td>						
					</tr>");		
		}
	}

	


	print("</table>");
	print('<table class="TableBlock" align="center" width="5" style="float:left;"><tbody><tr class="TableContent"></tr></table>');
	//print("</td></tr>");

//�����¼
	$sql = "SELECT a.`��ϵ��` as linkmanid,a.`���`,b.`����` as ��������,c.`����` as ����ʽ,a.`�������` as ��ʼ����,a.`�������` as ����ʱ��,d.linkmanname as ��ϵ��,e.`����` as ״̬
FROM crm_service a 
LEFT JOIN crm_dict_servicetypes b on a.`������`=b.`���`
LEFT JOIN crm_dict_servicesources c on a.`����׶�`=c.`���`
LEFT JOIN linkman d on a.`��ϵ��`=d.ROWID
LEFT JOIN crm_dict_servicestatus e on a.`���س̶�`=e.`���`  where a.`�ͻ�����`=".$billid." ORDER BY a.`�������` desc LIMIT 0,5";
	//exit($sql);
	$rs = $db->Execute($sql);
	$rs_a = $rs->GetArray();
	print("<table class='TableBlock' align='center' width='50%' style='float:left;height: auto;'>
					<tr class='TableContent'>
						<td colspan='6' nowrap=''><b  style='display:block;float:left;'><a href='crm_service_newai.php?".base64_encode("action=init_default_search&searchfield=�ͻ�����&searchvalue=$custname")."' target='_blank'>�����¼</a></b>
														<input style='float:right;' type='button' accesskey='n' value=' �½� ' class='SmallButtonA' onclick=\"window.open('crm_service_newai.php?action=add_default&customerid=".$_GET['ROWID']."');\" title='��ݼ�:ALT+n'>						
						
						
						</td>
					</tr>");			
	print("<tr class='TableContent'>
						<td nowrap=''>��������</td>
						<td nowrap=''>����ʽ</td>
						<td nowrap=''>��ʼ����</td>
						<td nowrap=''>����ʱ��</td>
						<td nowrap=''>��ϵ��</td>
						<td nowrap=''>״̬</td>
					</tr>");
	if(empty($rs_a)){
		print("<tr class='TableData1'><td colspan='6' nowrap=''>����</td></tr>");
		print("<tr class='TableData1'  height='100%'><td colspan='6' nowrap=''>&nbsp;</td></tr>");
		print("<tr class='TableData1'  height='100%'><td colspan='6' nowrap=''>&nbsp;</td></tr>");
		print("<tr class='TableData1'  height='100%'><td colspan='6' nowrap=''>&nbsp;</td></tr>");
		print("<tr class='TableData1'  height='100%'><td colspan='6' nowrap=''>&nbsp;</td></tr>");
	}else{
		for($i=0;$i<5;$i++){
			if(!isset($rs_a[$i])){
				print("<tr class='TableData1'  height='100%'><td colspan='6' nowrap=''>&nbsp;</td></tr>");
				continue;
			}
			print("<tr class='TableData1'>
						<td nowrap=''><a target='_blank' href='crm_service_newai.php?".base64_encode("action=view_default&���=".$rs_a[$i]['���'])."'>".$rs_a[$i]['��������']."</a></td>
						<td nowrap=''>".$rs_a[$i]['����ʽ']."</td>
						<td nowrap=''>".$rs_a[$i]['��ʼ����']."</td>
						<td nowrap=''>".$rs_a[$i]['����ʱ��']."</td>		
						<td nowrap=''><a target='_blank' href='../JXC/linkman_newai.php?".base64_encode("action=view_default&ROWID=".$rs_a[$i]['linkmanid'])."'>".$rs_a[$i]['��ϵ��']."</a></td>	
						<td nowrap=''>".$rs_a[$i]['״̬']."</td>							
					</tr>");		
		}
	}
	print("</table>");
	print("</td></tr>");
	?>
	<tr>
		<td class="TableControl" nowrap="" align="middle" colspan="4">
		<div align="left"><input type="button" accesskey="p" name="print"
			value=" ��ӡ " class="SmallButtonA"
			onclick="document.execCommand('Print');" title="��ݼ�:ALT+p"> 
			<input
			type="button" accesskey="m" name="record" value="�޸���־"
			class="SmallButton"
			onclick="window.open('modifyrecord_newai.php?tablename=customer&keyfield=rowid&keyvalue=<?php echo $billid?>');"
			title="��ݼ�:ALT+r">
			<input
			type="button" accesskey="c" name="cancel" value=" ���� "
			class="SmallButtonA"
			onclick="if(history.length==0) window.close();else history.back();"
			title="��ݼ�:ALT+c"></div>
		</td>
	</tr>
</table>
	<?php
	exit();
}

if($_GET['action']=="operation_yijiao")	{
	validateMenuPriv("�ͻ��ƽ�");
	$selectid=explode(",",$_GET['selectid']);
	try 
	{
		for($i=0;$i<count($selectid);$i++)
		{
			if($selectid[$i]!='')
			{
				if(!ifHasRoleCust($selectid[$i]))
				{
					$custname=returntablefield("customer","rowid", $selectid[$i], "supplyname");
					throw new Exception("��û��Ȩ���ƽ��ͻ���$custname");
				}
			
			}
		}
		print "<script>location.href='../CRM/inc_crm_tools.php?action=add_yijiao&custlist=".$_GET['selectid']."';</script>";
		exit;
		
	}
	catch (Exception $e)
	{
		print "<script language='javascript'>alert('��������".$e->getMessage()."');window.history.back(-1);</script>";
		exit;
	}
	
	
	
}
if($_GET['action']=="delete_array")
{
	$selectid=explode(",",$_GET['selectid']);
	
	if($_SESSION['LOGIN_USER_PRIV']=='3')
	{
		for($i=0;$i<count($selectid);$i++)
		{
			if($selectid[$i]!='')
			{
				$sql="update customer set datascope=-1 where rowid=".$selectid[$i];
				$db->Execute($sql);
			}
		}
		$return=FormPageAction("action","init_default");
		print_infor("�ͻ�������ɾ��",'trip',"location='?$return'","?$return",0);
		exit;
	}
	for($i=0;$i<count($selectid);$i++)
	{
		if($selectid[$i]!='')
		{
			$billid=returntablefield("sellplanmain", "supplyid", $selectid[$i], "billid");
			if($billid!='')
			{
				$customername=returntablefield("customer", "rowid", $selectid[$i], "supplyname");
				print "<script language='javascript'>alert('".$customername." ���ں�ͬ�����۵���¼������ɾ����ص���');window.history.back(-1);</script>";
				exit;
			}
		

		}
	}

}
if($_GET['action']=="addlinkman")
{
	print "<script>location.href='linkman_newai.php?action=add_default&customerid=".$_GET['ROWID']."';</script>";
	exit;
}
if($_GET['action']=="addcrmvisit")
{
	print "<script>location.href='crm_contact_newai.php?action=add_default&customerid=".$_GET['ROWID']."';</script>";
	exit;
}

$SYSTEM_ADD_SQL=getCustomerRoleByUser($SYSTEM_ADD_SQL,"sysuser");

$limitEditDelUser='sysuser';
addShortCutByDate("createdate","����ʱ��");
//print_r($_SESSION);
//$SYSTEM_ADD_SQL = " and ((sysuser='".$_SESSION['LOGIN_USER_ID']."' and datascope=0) or datascope=1)";
//$SYSTEM_PRINT_SQL=1;
$filetablename = "customer";
require_once( "include.inc.php" );
systemhelpcontent( "�ͻ�����", "100%" );

?>
