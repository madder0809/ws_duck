<?php
/*
 版权归属:郑州单点科技软件有限公司;
 联系方式:0371-69663266;
 公司地址:河南郑州经济技术开发区第五大街经北三路通信产业园四楼西南;
 公司简介:郑州单点科技软件有限公司位于中国中部城市-郑州,成立于2007年1月,致力于把基于先进信息技术（包括通信技术）的最佳管理与业务实践普及到教育行业客户的管理与业务创新活动中，全面提供具有自主知识产权的教育管理软件、服务与解决方案，是中部最优秀的高校教育管理软件及中小学校管理软件提供商。目前己经有多家高职和中职类院校使用通达中部研发中心开发的软件和服务;

 软件名称:单点科技软件开发基础性架构平台,以及在其基础之上扩展的任何性软件作品;
 发行协议:数字化校园产品为商业软件,发行许可为LICENSE方式;单点CRM系统即SunshineCRM系统为GPLV3协议许可,GPLV3协议许可内容请到百度搜索;
 特殊声明:软件所使用的ADODB库,PHPEXCEL库,SMTARY库归原作者所有,余下代码沿用上述声明;
 */
ini_set('display_errors', 1);
ini_set('error_reporting', E_ALL);
error_reporting(E_WARNING | E_ERROR);
require_once('lib.inc.php');
$GLOBAL_SESSION=returnsession();

//print_r($_GET);exit;
validateMenuPriv("客户基本信息");
if($_GET['action']=="add_default")
	$ADDINIT=array("sysuser"=>$_SESSION['LOGIN_USER_ID']);
if($_GET['action']=="edit_default_data"||$_GET['action']=="add_default_data")		{
	if($_POST['amtagio']<=0 || $_POST['amtagio']>1)
	{
		//print "<script language='javascript'>alert('折扣必须介于0-1之间');window.history.back(-1);</script>";
		//exit;
		$_POST['amtagio']=1;
	}
	$_POST['calling'] = 汉字转拼音首字母($_POST['supplyname']);
	
}


if($_GET['action']=="view_default")	{
	global $db;
	$billid = $_GET['ROWID'];
	$sql = "SELECT a.*,b.`name` as state,c.`name` as enterstype,a.style,e.USER_NAME as blong,e.USER_NAME,f.`name` as origin,g.`name` as salemode,h.`name` as property,a.yuchuzhi,a.createdate,a.integral,a.explainStr FROM customer a
		LEFT JOIN customerlever b on a.state=b.ROWID LEFT JOIN unitprop c on a.enterstype=c.ROWID  LEFT JOIN `user` ee on a.user_id=ee.USER_ID  LEFT JOIN `user` e on a.sysuser=e.USER_ID LEFT JOIN customerorigin f on a.origin=f.ROWID LEFT JOIN salemode g on a.salemode=g.ROWID LEFT JOIN property h on a.property=h.ROWID WHERE a.ROWID=".$billid;
	$rs=$db->Execute($sql);
	$rs_a = $rs->GetArray();
	page_css("客户综合视图");
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
		<td class="TableHeader" align="left" colspan="4">&nbsp;查看客户信息</td>
	</tr>
	<tr>
		<td class="TableControl" nowrap="" align="middle" colspan="4">
		<div align="left"><input type="button" accesskey="p" name="print"
			value=" 打印 " class="SmallButtonA"
			onclick="document.execCommand('Print');" title="快捷键:ALT+p"> 
			<input
			type="button" accesskey="m" name="record" value="修改日志"
			class="SmallButton"
			onclick="window.open('modifyrecord_newai.php?<?php echo base64_encode("tablename=customer&keyfield=rowid&keyvalue=$billid")?>');"
			title="快捷键:ALT+r">
			<input
			type="button" accesskey="c" name="cancel" value=" 返回 "
			class="SmallButtonA"
			onclick="if(history.length==0) window.close();else history.back();"
			title="快捷键:ALT+c"></div>
		</td>
	</tr>
	<tr>
		<td nowrap="" class="TableContent" width="20%">客户名称:</td>
		<td class="TableData" colspan="1"><?php if($editrole) print "<a href='customer_newai.php?".base64_encode("action=edit_default&ROWID=".$billid)."' target='_blank'><img src='../Framework/images/edit1.gif' title='编辑'></a>"; echo $rs_a[0][supplyname];?></td>
		<td nowrap="" class="TableContent" width="20%">公司地址:</td>
		<td class="TableData" colspan="1"><?php echo $rs_a[0][contactaddress] ?></td>
	</tr>
	<tr>
		<td nowrap="" class="TableContent" width="20%">折扣:</td>
		<td class="TableData" colspan="1"><?php echo $rs_a[0][amtagio] ?></td>
		<td nowrap="" class="TableContent" width="20%">会员卡:</td>
		<td class="TableData" colspan="1"><?php echo $rs_a[0][membercard] ?></td>
	</tr>
	<tr>
		<td class="TableContent" nowrap="">客户状态:</td>
		<td class="TableData" nowrap="" colspan="1"><?php echo $rs_a[0][state] ?></td>
		<td nowrap="" class="TableContent" width="20%">电话:</td>
		<td class="TableData" colspan="1"><?php
				
					$regex="/1[34568]+\\d{9}/";
					preg_match_all($regex,$rs_a[0][phone], $phones);
				
					if(strlen($phones[0][0])==11)
						echo "<a href='../JXC/sms_sendlist_newai.php?".base64_encode("action=add_default&sendlist=".$phones[0][0])."' target='_blank'>".$phones[0][0]."</a>";
				
					else
						echo  $rs_a[0][phone]?></td>
	</tr>
	<tr>
		<td nowrap="" class="TableContent" width="20%">传真:</td>
		<td class="TableData" colspan="1"><?php echo $rs_a[0][fax] ?></td>
		<td nowrap="" class="TableContent" width="20%">EMail:</td>
		<td class="TableData" colspan="1"><?php print "<a href='../CRM/email_newai.php?".base64_encode("action=add_default&sendlist=".$rs_a[0][email])."&fromsrc=' target='_blank' >".$rs_a[0][email]."</a>";?></td>
	</tr>
	<tr>
		<td nowrap="" class="TableContent" width="20%">网址:</td>
		<?php 
		$urlwangzhi=$rs_a[0][netword];
		$flag=stripos($rs_a[0][netword], "http://");
		if($flag===false)
				$urlwangzhi="http://".$urlwangzhi;
		?>
		<td class="TableData" colspan="1"><a href='<?php echo $urlwangzhi ?>' target='_blank'><?php echo $rs_a[0][netword] ?></a></td>
		<td nowrap="" class="TableContent" width="20%">开户行:</td>
		<td class="TableData" colspan="1"><?php echo $rs_a[0][bank] ?></td>
	</tr>
	<tr>
		<td nowrap="" class="TableContent" width="20%">银行帐户:</td>
		<td class="TableData" colspan="1"><?php echo $rs_a[0]['accountnumber'] ?></td>
		<td class="TableContent" nowrap="">经济类型:</td>
		<td class="TableData" nowrap="" colspan="1"><?php echo $rs_a[0][enterstype] ?></td>
	</tr>
	<tr>
		<td class="TableContent" nowrap="">所属地域:</td>
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
		<td class="TableContent" nowrap="">创建者:</td>
		<td class="TableData" nowrap="" colspan="1"><a target='_blank' href='../Framework/user_newai.php?<?php echo base64_encode("action=view_default&UID=$UID")?>'><?php echo $rs_a[0][blong] ?></a></td>
	</tr>
	<tr>
		<td class="TableContent" nowrap="">归属人:</td>
		<td class="TableData" nowrap="" colspan="1"><a target='_blank' href='../Framework/user_newai.php?<?php echo base64_encode("action=view_default&UID=$UID1")?>'><?php echo $rs_a[0][USER_NAME] ?></a></td>
		
		<td class="TableContent" nowrap="">客户来源:</td>
		<td class="TableData" nowrap="" colspan="1"><?php echo $rs_a[0][origin] ?></td>
	</tr>
	<tr>
		<td class="TableContent" nowrap="">销售方式:</td>
		<td class="TableData" nowrap="" colspan="1"><?php echo $rs_a[0][salemode] ?></td>
		<td class="TableContent" nowrap="">行业属性:</td>
		<td class="TableData" nowrap="" colspan="1"><?php echo $rs_a[0][property] ?></td>
	</tr>
	<tr>
		<td nowrap="" class="TableContent" width="20%">预储值:</td>
		<td class="TableData" colspan="1"><?php echo $rs_a[0][yuchuzhi] ?></td>
		<td nowrap="" class="TableContent" width="20%">创建时间:</td>
		<td class="TableData" colspan="1"><?php echo $rs_a[0][createdate] ?></td>
	</tr>
	<tr>
		<td nowrap="" class="TableContent" width="20%">现有积分:</td>
		<td class="TableData" style="word-break: break-all" colspan="1"><?php echo $rs_a[0][integral] ?></td>
		<td nowrap="" class="TableContent" width="20%">工厂地址:</td>
		<td class="TableData" style="word-break: break-all" colspan="1"><?php echo $rs_a[0][supplycn] ?></td>
	</tr>
	<tr>
		<td nowrap="" class="TableContent" width="20%">备注:</td>
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
//联系人
	$sql = "SELECT a.rowid,a.linkmanname,a.gender,a.mobile,a.phone,a.email,a.fax,a.business,a.postcode,a.businessexpian,a.mark FROM linkman a WHERE a.customerid=".$billid;
	$rs = $db->Execute($sql);
	$rs_a = $rs->GetArray();
	print("<tr class='TableData1'><td colspan='32' nowrap='' width='100%'>
				<table class='TableBlock' align='center' width='99.6%'>
					<tr class='TableContent'>
						<td colspan='10' nowrap=''><b  style='display:block;float:left;'><a href='linkman_newai.php?".base64_encode("action=init_default_search&searchfield=customerid&searchvalue=$custname")."' target='_blank'>联系人列表</a></b>						
						<input style='float:right;display:block;' type='button' accesskey='n' value=' 新建 ' class='SmallButtonA' onclick=\"window.open('linkman_newai.php?action=add_default&customerid=".$_GET['ROWID']."');\" title='快捷键:ALT+n'>
						</td>
					</tr>");			
	print("<tr class='TableContent'> 
						<td nowrap=''>姓名</td>
						<td nowrap=''>性别</td>
						<td nowrap=''>职务</td>						
						<td nowrap=''>电话</td>
						<td nowrap=''>手机</td>
						<td nowrap=''>E-mail</td>
						<td nowrap=''>QQ</td>
						<td nowrap=''>爱好</td>
						<td nowrap=''>工作描述</td>
						<td nowrap=''>备注</td>
					</tr>");

	if(empty($rs_a)){
		print("<tr class='TableData1'  height='100%'><td colspan='10' nowrap=''>暂无</td></tr>");
	}else{
		foreach($rs_a as $row){
			print "<tr class='TableData1'>";
			if($editrole) 
				print "<td nowrap=''><a href='linkman_newai.php?".base64_encode("action=edit_default&ROWID=".$row['rowid'])."' target='_blank'><img src='../Framework/images/edit1.gif' title='编辑'></a>".$row['linkmanname']."</td>";
			else
				print "	<td nowrap=''>".$row['linkmanname']."</td>";
			print "	<td nowrap=''>".($row['gender']==1?'男':'女')."</td>
					<td nowrap=''>".$row['business']."</td>
					<td nowrap=''>".$row['phone']."</td>
					<td nowrap=''><a href='sms_sendlist_newai.php?".base64_encode("action=add_default&sendlist=".$row['mobile'])."' target='_blank' >".$row['mobile']."</a></td>
					<td nowrap=''><a href='../CRM/email_newai.php?".base64_encode("action=add_default&sendlist=".$row['rowid'])."&fromsrc=customer' target='_blank' >".$row['email']."</a></td>
					<td nowrap=''>".(!empty($row['fax'])?"<a target=\"_blank\" href=\"http://wpa.qq.com/msgrd?v=3&amp;uin=".$row[fax]."&amp;site=qq&amp;menu=yes\"><img border=\"0\" src=\"http://wpa.qq.com/pa?p=2:".$row["fax"].":45\" onerror=\"this.src='".ROOT_DIR."general/ERP/Framework/images/help3.gif';this.title='此QQ号码可能不存在'\" title=\"点击这里发消息\">".$row["fax"]."</a>":'')."</td>
					<td nowrap=''>".$row['postcode']."</td>
					<td nowrap=''>".$row['businessexpian']."</td>
					<td nowrap=''>".$row['mark']."</td>
					</tr>";		
		}
	}
	print("</table>");
	print("</td></tr>");
	
	
//销售机会	

	$sql = "SELECT a.`创建人`,a.`联系人`,a.`编号`,b.USER_NAME,a.`机会主题`,a.`客户需求`,c.linkmanname,a.`发现时间`,d.`阶段` FROM crm_chance a
LEFT JOIN `user` b on a.`创建人`=b.USER_ID 
LEFT JOIN linkman c on a.`联系人`=c.ROWID 
LEFT JOIN crm_jieduan d on a.`当前阶段`=d.`编号`  WHERE a.`客户名称`=".$billid."  ORDER BY a.`发现时间` DESC  LIMIT 0,5";
	$rs = $db->Execute($sql);
	$rs_a = $rs->GetArray();
	print("<tr class='TableData1'><td colspan='32' nowrap='' width='100%'>
	<table class=\"TableBlock\" align=\"center\" width=\"2\" style=\"float:left;\"><tbody><tr class=\"TableContent\"></tr></table>
				<table class='TableBlock' align='center' width='49%' style='float:left;height: auto;'>
					<tr class='TableContent'>
						<td colspan='5' nowrap=''><b  style='display:block;float:left;'><a href='crm_chance_newai.php?".base64_encode("action=init_default_search&searchfield=客户名称&searchvalue=$custname")."' target='_blank'>销售机会</a></b>						
						<input style='float:right;display:block;' type='button' accesskey='n' value=' 新建 ' class='SmallButtonA' onclick=\"window.open('crm_chance_newai.php?action=add_default&customerid=".$_GET['ROWID']."');\" title='快捷键:ALT+n'>
						</td>
					</tr>");			
	print("<tr class='TableContent'> 
						<td nowrap=''>发现时间</td>
						<td nowrap=''>机会主题</td>
						<td nowrap=''>联系人</td>
						<td nowrap=''>创建人</td>
						<td nowrap=''>当前阶段</td>
					</tr>");

	if(empty($rs_a)){
		print("<tr class='TableData1'  height='100%'><td colspan='5' nowrap=''>暂无</td></tr>");
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
			$UID=returntablefield("user", "user_id", $rs_a[$i]['创建人'], "uid");
			print("<tr class='TableData1'>
						<td nowrap=''>".$rs_a[$i]['发现时间']."</td>
						<td nowrap=''><a target='_blank' href='crm_chance_newai.php?".base64_encode("action=view_default&编号=".$rs_a[$i]['编号'])."' title='".cutStr($rs_a[$i]['客户需求'],50)."'>".cutStr($rs_a[$i]['机会主题'],18)."</a></td>
						<td nowrap=''><a target='_blank' href='../JXC/linkman_newai.php?".base64_encode("action=view_default&ROWID=".$rs_a[$i]['联系人'])."'>".$rs_a[$i]['linkmanname']."</a></td>
						<td nowrap=''><a target='_blank' href='../Framework/user_newai.php?".base64_encode("action=view_default&UID=".$UID)."'>".$rs_a[$i]['USER_NAME']."</a></td>
						
						<td nowrap=''>".$rs_a[$i]['阶段']."</td>						
					</tr>");		
		}
	}
	print("</table>");
	print('<table class="TableBlock" align="center" width="5" style="float:left;"><tbody><tr class="TableContent"></tr></table>');
	//print("</td></tr>");

//跟踪记录

	$sql = "SELECT a.chance,a.linkmanid,a.id,a.contacttime,a.describes,b.`阶段`,c.`名称`,d.linkmanname  FROM crm_contact a LEFT JOIN crm_jieduan b on a.stage=b.`编号` left join crm_dict_servicesources c on a.contact=c.`编号` left join linkman d on a.linkmanid=d.ROWID  WHERE  a.customerid=".$billid."  ORDER BY a.contacttime DESC  LIMIT 0,5";;
		//exit($sql);
	$rs = $db->Execute($sql);
	$rs_a = $rs->GetArray();
	print("<table class='TableBlock' align='center' width='50%' style='float:left;height: auto;'>
					<tr class='TableContent'>
						<td colspan='5' nowrap=''><b   style='display:block;float:left;'><a href='crm_contact_newai.php?".base64_encode("action=init_default_search&searchfield=customerid&searchvalue=$custname")."' target='_blank'>跟踪记录</a></b>
							<input style='float:right;' type='button' accesskey='n' value=' 新建 ' class='SmallButtonA' onclick=\"window.open('crm_contact_newai.php?action=add_default&customerid=".$_GET['ROWID']."');\" title='快捷键:ALT+n'>						
						</td>
					</tr>");			
	print("<tr class='TableContent'>
						<td nowrap=''>联系时间</td>
						<td nowrap=''>机会</td>
						<td nowrap=''>阶段</td>
						<td nowrap=''>联系方式</td>
						<td nowrap=''>联系人</td>
					</tr>");
	if(empty($rs_a)){
		print("<tr class='TableData1'  height='100%'><td colspan='5' nowrap=''>暂无</td></tr>");
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
			$zhuti=returntablefield("crm_chance", "编号", $rs_a[$i]['chance'], '机会主题,客户需求');
			print("<tr class='TableData1'>
						<td nowrap=''>".$rs_a[$i]['contacttime']."</td>
						<td nowrap=''><a target='_blank' href='crm_chance_newai.php?".base64_encode("action=view_default&编号=".$rs_a[$i]['chance'])."' title='".cutStr($zhuti['客户需求'],50)."'>".cutStr($zhuti['机会主题'],15)."</a></td>
						<td nowrap=''><a target='_blank' href='crm_contact_newai.php?".base64_encode("action=view_default&id=".$rs_a[$i]['id'])."' title='".cutStr($rs_a[$i]['describes'],50)."'>".$rs_a[$i]['阶段']."</a></td>
						<td nowrap=''>".$rs_a[$i]['名称']."</td>
						<td nowrap=''><a target='_blank' href='../JXC/linkman_newai.php?".base64_encode("action=view_default&ROWID=".$rs_a[$i]['linkmanid'])."'>".$rs_a[$i][linkmanname]."</a></td>						
					</tr>");		
		}
	}
	print("</table>");
	print("</td></tr>");

//需求记录

	$sql = "SELECT * FROM customer_xuqiu where customerid=".$billid."  ORDER BY createtime DESC LIMIT 0,5";
	//exit($sql);
	$rs = $db->Execute($sql);
	$rs_a = $rs->GetArray();
	print("<tr class='TableData1'><td colspan='32' nowrap='' width='100%'>
	<table class=\"TableBlock\" align=\"center\" width=\"2\" style=\"float:left;\"><tbody><tr class=\"TableContent\"></tr></table>
				<table class='TableBlock' align='center' width='49%' style='float:left;height: auto;'>
					<tr class='TableContent'>
						<td colspan='5' nowrap=''><b   style='display:block;float:left;'><a href='customer_xuqiu_newai.php?".base64_encode("action=init_default_search&searchfield=customerid&searchvalue=$custname")."' target='_blank'>需求与方案</a></b>
								<input style='float:right;' type='button' accesskey='n' value=' 新建 ' class='SmallButtonA' onclick=\"window.open('customer_xuqiu_newai.php?action=add_default&customerid=".$_GET['ROWID']."');\" title='快捷键:ALT+n'>						
						</td>
					</tr>");			
	print("<tr class='TableContent'>
						<td nowrap=''>需求主题</td>
						<td nowrap=''>提供人</td>
						<td nowrap=''>对应机会</td>
						<td nowrap=''>重要程度</td>
						<td nowrap=''>提出时间</td>
						
					</tr>");
	if(empty($rs_a)){
		print("<tr class='TableData1'  height='100%'><td colspan='5' nowrap=''>暂无</td></tr>");
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
			$chacename=returntablefield("crm_chance", "编号", $rs_a[$i]['chanceid'], "机会主题");
			$important=returntablefield("important", "id", $rs_a[$i]['import'], "name");
			print("<tr class='TableData1'>
						<td nowrap=''><a target='_blank' href='customer_xuqiu_newai.php?".base64_encode("action=view_default&id=".$rs_a[$i]['id'])."'>".cutStr($rs_a[$i]['zhuti'],12)."</a></td>
						<td nowrap=''>".$rs_a[$i]['tigongren']."</td>
						<td nowrap=''><a target='_blank' href='crm_chance_newai.php?".base64_encode("action=view_default&编号=".$rs_a[$i]['chanceid'])."'>".cutstr($chacename,12)."</a></td>		
						<td nowrap=''>$important</td>
						<td nowrap=''>".cutStr($rs_a[$i][createtime],10)."</td>
										
					</tr>");		
		}
	}



	print("</table>");
	print('<table class="TableBlock" align="center" width="5" style="float:left;"><tbody><tr class="TableContent"></tr></table>');
	//print("</td></tr>");
//费用记录
	$sql = "SELECT * FROM crm_feiyong_sq WHERE 客户名称=".$billid." order BY 创建时间 desc LIMIT 0,5";
	//exit($sql);
	$rs = $db->Execute($sql);
	$rs_a = $rs->GetArray();
	print("<table class='TableBlock' align='center' width='50%' style='float:left;height: auto;'>
					<tr class='TableContent'>
						<td colspan='6' nowrap=''><b  style='display:block;float:left;'><a href='../CRM/crm_feiyong_sq_newai.php?".base64_encode("action=init_default_search&searchfield=客户名称&searchvalue=$custname")."' target='_blank'>费用记录</a></b>
						<input style='float:right;' type='button' value=' 新建 ' class='SmallButtonA' onclick=\"window.open('../CRM/crm_feiyong_sq_newai.php?action=add_default&客户名称=".$_GET['ROWID']."');\"></span>									
						</td>
					</tr>");			
	print("<tr class='TableContent'>
						<td nowrap=''>费用类型</td>
						<td nowrap=''>金额</td>
						<td nowrap=''>费用用途</td>
						<td nowrap=''>产生日期</td>
						<td nowrap=''>申请人 </td>
						<td nowrap=''>是否审核 </td>
					</tr>");

	if(empty($rs_a)){
		print("<tr class='TableData1'  height='100%'><td colspan='7' nowrap=''>暂无</td></tr>");
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
			$UID=returntablefield("user", "user_id", $rs_a[$i]['录单员'], "uid,user_name");
			$feiyongtype=returntablefield("v_feiyongbaoxiao", "id", $rs_a[$i]['费用类型'], "typename");
			$shenhe=returntablefield("crm_shenhezhuangtai", "id", $rs_a[$i]['是否审核'], "name");
			
			print("<tr class='TableData1'>
						<td nowrap=''>$feiyongtype</td>
						<td nowrap=''>".$rs_a[$i]['金额']."</td>
						<td nowrap=''>".$rs_a[$i]['费用用途']."</td>
						<td nowrap=''>".$rs_a[$i]['申请日期']."</td>
						<td nowrap=''><a target='_blank' href='../Framework/user_newai.php?".base64_encode("action=view_default&UID=".$UID['uid'])."'>".$UID['user_name']."</a></td>
						<td nowrap=''>".$shenhe."</td>	
												
					</tr>");		
		}
	}
	print("</table>");
	print("</td></tr>");

//竞争产品

	$sql = "SELECT * FROM competeproduct where customerid=".$billid."  ORDER BY createtime DESC LIMIT 0,5";
	//exit($sql);
	$rs = $db->Execute($sql);
	$rs_a = $rs->GetArray();
	print("<tr class='TableData1'><td colspan='32' nowrap='' width='100%'>
	<table class=\"TableBlock\" align=\"center\" width=\"2\" style=\"float:left;\"><tbody><tr class=\"TableContent\"></tr></table>
				<table class='TableBlock' align='center' width='49%' style='float:left;height: auto;'>
					<tr class='TableContent'>
						<td colspan='5' nowrap=''><b   style='display:block;float:left;'><a href='competeproduct_newai.php?".base64_encode("action=init_default_search&searchfield=customerid&searchvalue=$custname")."' target='_blank'>竞争产品</a></b>
								<input style='float:right;' type='button' accesskey='n' value=' 新建 ' class='SmallButtonA' onclick=\"window.open('competeproduct_newai.php?action=add_default&customerid=".$_GET['ROWID']."');\" title='快捷键:ALT+n'>						
						</td>
					</tr>");			
	print("<tr class='TableContent'>
						<td nowrap=''>创建人</td>
						<td nowrap=''>对应机会</td>
						<td nowrap=''>竞争产品</td>
						<td nowrap=''>公司名称</td>
						<td nowrap=''>创建时间</td>
						
					</tr>");
	if(empty($rs_a)){
		print("<tr class='TableData1'  height='100%'><td colspan='5' nowrap=''>暂无</td></tr>");
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
			$chacename=returntablefield("crm_chance", "编号", $rs_a[$i]['productid'], "机会主题");
			$UID=returntablefield("user", "user_id", $rs_a[$i]['user_id'], "uid,user_name");
			print("<tr class='TableData1'>
					<td nowrap=''><a target='_blank' href='../Framework/user_newai.php?".base64_encode("action=view_default&UID=".$UID['uid'])."'>".$UID['user_name']."</a></td>
					<td nowrap=''><a target='_blank' href='crm_chance_newai.php?".base64_encode("action=view_default&编号=".$rs_a[$i]['productid'])."'>".cutstr($chacename,9)."</a></td>
						<td nowrap=''><a target='_blank' href='competeproduct_newai.php?".base64_encode("action=view_default&ROWID=".$rs_a[$i]['ROWID'])."'>".cutStr($rs_a[$i]['comproduct'],9)."</a></td>
						<td nowrap=''>".cutStr($rs_a[$i]['infofrom'],9)."</td>
						<td nowrap=''>".cutStr($rs_a[$i]['createtime'],10)."</td>
										
					</tr>");		
		}
	}



	print("</table>");
	print('<table class="TableBlock" align="center" width="5" style="float:left;"><tbody><tr class="TableContent"></tr></table>');
	//print("</td></tr>");
//报价记录
$sql = "SELECT  a.销售机会,a.报价人,a.`接收人`,a.ROWID,a.`报价时间`,a.`主题`,b.USER_NAME,c.linkmanname,d.`机会主题` FROM customerproduct a LEFT JOIN `user` b on a.`报价人`=b.USER_ID LEFT JOIN linkman c on a.`接收人`=c.ROWID LEFT JOIN crm_chance d on a.`销售机会`=d.`编号` WHERE a.`客户`=".$billid."  ORDER BY a.`报价时间` DESC LIMIT 0,5";
	//exit($sql);
	$rs = $db->Execute($sql);
	$rs_a = $rs->GetArray();
	print("<table class='TableBlock' align='center' width='50%' style='float:left;height: auto;'>
					<tr class='TableContent'>
						<td colspan='5' nowrap=''><b  style='display:block;float:left;'><a href='customerproduct_newai.php?".base64_encode("action=init_default_search&searchfield=客户&searchvalue=$custname")."' target='_blank'>报价记录</a></b>
						<input style='float:right;' type='button' value=' 新建 ' class='SmallButtonA' onclick=\"window.open('customerproduct_newai.php?action=add_default&customerid=".$_GET['ROWID']."');\"></span>									
						</td>
					</tr>");	
			
	print("<tr class='TableContent'>
						<td nowrap=''>报价时间</td>
						<td nowrap=''>主题</td>
						<td nowrap=''>报价人</td>
						<td nowrap=''>接收人</td>
						<td nowrap=''>销售机会</td>
						
					</tr>");
	if(empty($rs_a)){
		print("<tr class='TableData1'  height='100%'><td colspan='5' nowrap=''>暂无</td></tr>");
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
			$UID=returntablefield("user", "user_id", $rs_a[$i]['报价人'], "uid");
			print("<tr class='TableData1'>
						<td nowrap=''>".$rs_a[$i]['报价时间']."</td>
						<td nowrap=''><a target='_blank' href='customerproduct_newai.php?".base64_encode("action=view_default&ROWID=".$rs_a[$i]['ROWID'])."'>".cutStr($rs_a[$i]['主题'],12)."</a></td>
						<td nowrap=''><a target='_blank' href='../Framework/user_newai.php?".base64_encode("action=view_default&UID=".$UID)."'>".$rs_a[$i]['USER_NAME']."</a></td>
						<td nowrap=''><a target='_blank' href='../JXC/linkman_newai.php?".base64_encode("action=view_default&ROWID=".$rs_a[$i]['接收人'])."'>".$rs_a[$i][linkmanname]."</a></td>
						<td nowrap=''><a target='_blank' href='crm_chance_newai.php?".base64_encode("action=view_default&编号=".$rs_a[$i]['销售机会'])."'>".cutstr($rs_a[$i]['机会主题'],12)."</a></td>						
					</tr>");		
		}
	}
	
	print("</table>");
	print("</td></tr>");
	

//项目报备
$sql = "SELECT * FROM crm_shenqingbaobei WHERE customerid=".$billid." order BY createtime desc LIMIT 0,5";
	//exit($sql);
	$rs = $db->Execute($sql);
	$rs_a = $rs->GetArray();
	print("<tr class='TableData1'><td colspan='32' nowrap='' width='100%'>
	<table class=\"TableBlock\" align=\"center\" width=\"2\" style=\"float:left;\"><tbody><tr class=\"TableContent\"></tr></table>
				<table class='TableBlock' align='center' width='49%' style='float:left;height: auto;'>
					<tr class='TableContent'>
						<td colspan='5' nowrap=''><b   style='display:block;float:left;'><a href='crm_shenqingbaobei_newai.php?".base64_encode("action=init_default_search&searchfield=customerid&searchvalue=$custname")."' target='_blank'>项目报备</a></b>
								<input style='float:right;' type='button' accesskey='n' value=' 新建 ' class='SmallButtonA' onclick=\"window.open('crm_shenqingbaobei_newai.php?action=add_default&customerid=".$_GET['ROWID']."');\" title='快捷键:ALT+n'>						
						</td>
					</tr>");	
		
	print("<tr class='TableContent'>
						<td nowrap=''>申请人</td>
						<td nowrap=''>对应机会</td>
						<td nowrap=''>申请时间</td>
						<td nowrap=''>审核状态</td>
						<td nowrap=''>审核人</td>
					</tr>");

	if(empty($rs_a)){
		print("<tr class='TableData1'  height='100%'><td colspan='7' nowrap=''>暂无</td></tr>");
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
			$chacename=returntablefield("crm_chance", "编号", $rs_a[$i]['chanceid'], "机会主题");
			$shenhestate=returntablefield("crm_shenhezhuangtai", "id", $rs_a[$i]['state'], "name");
			print("<tr class='TableData1'>
					<td nowrap=''><a target='_blank' href='../Framework/user_newai.php?".base64_encode("action=view_default&UID=".$UID['uid'])."'>".$UID['user_name']."</a></td>
					<td nowrap=''><a target='_blank' href='crm_chance_newai.php?".base64_encode("action=view_default&编号=".$rs_a[$i]['chanceid'])."'>".cutstr($chacename,15)."</a></td>
					<td nowrap=''>".cutStr($rs_a[$i]['createtime'],10)."</td>
					<td nowrap=''>".$shenhestate."</td>
					<td nowrap=''><a target='_blank' href='../Framework/user_newai.php?".base64_encode("action=view_default&UID=".$UID1['uid'])."'>".$UID1['user_name']."</a></td>
						
						
												
					</tr>");		
		}
	}

	print("</table>");
	print('<table class="TableBlock" align="center" width="5" style="float:left;"><tbody><tr class="TableContent"></tr></table>');
	//print("</td></tr>");

//成交记录
	$sql = "SELECT a.billtype,a.user_id,a.billid,a.zhuti,a.totalmoney,a.huikuanjine,a.fahuojine,b.USER_NAME,a.createtime FROM sellplanmain a LEFT JOIN user b on a.user_id=b.USER_ID WHERE a.supplyid=".$billid." order BY a.createtime desc LIMIT 0,5";
	//exit($sql);
	$rs = $db->Execute($sql);
	$rs_a = $rs->GetArray();
	print("<table class='TableBlock' align='center' width='50%' style='float:left;height: auto;'>
					<tr class='TableContent'>
						<td colspan='7' nowrap=''><b  style='display:block;float:left;'><a href='sellplanmain_newai.php?".base64_encode("action=init_default_search&searchfield=supplyid&searchvalue=$custname")."' target='_blank'>成交记录</a></b>
						<input style='float:right;' type='button' value=' 新建 ' class='SmallButtonA' onclick=\"window.open('sellplanmain_newai.php?action=add_default&customerid=".$_GET['ROWID']."');\"></span>									
						</td>
					</tr>");			
	print("<tr class='TableContent'>
						<td nowrap=''>单号</td>
						<td nowrap=''>主题</td>
						<td nowrap=''>总金额</td>
						<td nowrap=''>回款</td>
						<td nowrap=''>交付/发货</td>
						<td nowrap=''>创建人</td>
						<td nowrap=''>创建日期</td>
						
					</tr>");

	if(empty($rs_a)){
		print("<tr class='TableData1'  height='100%'><td colspan='7' nowrap=''>暂无</td></tr>");
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

//积分兑换记录
	$sql = "SELECT a.createman,a.ROWID,a.prodid,prodname,a.integral,a.exchangenum,b.USER_NAME,a.createtime FROM exchange a LEFT JOIN `user` b on a.createman=b.USER_ID WHERE a.customid=".$billid." ORDER BY a.createtime desc LIMIT 0,5";
	//exit($sql);
	$rs = $db->Execute($sql);
	$rs_a = $rs->GetArray();
	print("<tr class='TableData1'><td colspan='32' nowrap='' width='100%'>
	<table class=\"TableBlock\" align=\"center\" width=\"2\" style=\"float:left;\"><tbody><tr class=\"TableContent\"></tr></table>
				<table class='TableBlock' align='center' width='49%' style='float:left;height: auto;'>
					<tr class='TableContent'>
						<td colspan='5' nowrap=''><b  style='display:block;float:left;'><a href='exchange_newai.php?".base64_encode("action=init_default_search&searchfield=customid&searchvalue=$custname")."' target='_blank'>积分兑换记录</a></b>
							<input style='float:right;' type='button' accesskey='n' value=' 新建 ' class='SmallButtonA' onclick=\"window.open('exchange_newai.php?action=add_default&customerid=".$_GET['ROWID']."');\" title='快捷键:ALT+n'>					
								</td>
					</tr>");			
	print("<tr class='TableContent'>						
						<td nowrap=''>产品名称</td>
						<td nowrap=''>消费积分</td>
						<td nowrap=''>兑换数量</td>
						<td nowrap=''>兑换人</td>
						<td nowrap=''>兑换时间</td>						
					</tr>");

	if(empty($rs_a)){
		print("<tr class='TableData1'  height='100%'><td colspan='5' nowrap=''>暂无</td></tr>");
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

//服务记录
	$sql = "SELECT a.`联系人` as linkmanid,a.`编号`,b.`名称` as 服务类型,c.`名称` as 服务方式,a.`最后期限` as 开始日期,a.`服务概述` as 花费时间,d.linkmanname as 联系人,e.`名称` as 状态
FROM crm_service a 
LEFT JOIN crm_dict_servicetypes b on a.`服务编号`=b.`编号`
LEFT JOIN crm_dict_servicesources c on a.`服务阶段`=c.`编号`
LEFT JOIN linkman d on a.`联系人`=d.ROWID
LEFT JOIN crm_dict_servicestatus e on a.`严重程度`=e.`编号`  where a.`客户名称`=".$billid." ORDER BY a.`最后期限` desc LIMIT 0,5";
	//exit($sql);
	$rs = $db->Execute($sql);
	$rs_a = $rs->GetArray();
	print("<table class='TableBlock' align='center' width='50%' style='float:left;height: auto;'>
					<tr class='TableContent'>
						<td colspan='6' nowrap=''><b  style='display:block;float:left;'><a href='crm_service_newai.php?".base64_encode("action=init_default_search&searchfield=客户名称&searchvalue=$custname")."' target='_blank'>服务记录</a></b>
														<input style='float:right;' type='button' accesskey='n' value=' 新建 ' class='SmallButtonA' onclick=\"window.open('crm_service_newai.php?action=add_default&customerid=".$_GET['ROWID']."');\" title='快捷键:ALT+n'>						
						
						
						</td>
					</tr>");			
	print("<tr class='TableContent'>
						<td nowrap=''>服务类型</td>
						<td nowrap=''>服务方式</td>
						<td nowrap=''>开始日期</td>
						<td nowrap=''>花费时间</td>
						<td nowrap=''>联系人</td>
						<td nowrap=''>状态</td>
					</tr>");
	if(empty($rs_a)){
		print("<tr class='TableData1'><td colspan='6' nowrap=''>暂无</td></tr>");
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
						<td nowrap=''><a target='_blank' href='crm_service_newai.php?".base64_encode("action=view_default&编号=".$rs_a[$i]['编号'])."'>".$rs_a[$i]['服务类型']."</a></td>
						<td nowrap=''>".$rs_a[$i]['服务方式']."</td>
						<td nowrap=''>".$rs_a[$i]['开始日期']."</td>
						<td nowrap=''>".$rs_a[$i]['花费时间']."</td>		
						<td nowrap=''><a target='_blank' href='../JXC/linkman_newai.php?".base64_encode("action=view_default&ROWID=".$rs_a[$i]['linkmanid'])."'>".$rs_a[$i]['联系人']."</a></td>	
						<td nowrap=''>".$rs_a[$i]['状态']."</td>							
					</tr>");		
		}
	}
	print("</table>");
	print("</td></tr>");
	?>
	<tr>
		<td class="TableControl" nowrap="" align="middle" colspan="4">
		<div align="left"><input type="button" accesskey="p" name="print"
			value=" 打印 " class="SmallButtonA"
			onclick="document.execCommand('Print');" title="快捷键:ALT+p"> 
			<input
			type="button" accesskey="m" name="record" value="修改日志"
			class="SmallButton"
			onclick="window.open('modifyrecord_newai.php?tablename=customer&keyfield=rowid&keyvalue=<?php echo $billid?>');"
			title="快捷键:ALT+r">
			<input
			type="button" accesskey="c" name="cancel" value=" 返回 "
			class="SmallButtonA"
			onclick="if(history.length==0) window.close();else history.back();"
			title="快捷键:ALT+c"></div>
		</td>
	</tr>
</table>
	<?php
	exit();
}

if($_GET['action']=="operation_yijiao")	{
	validateMenuPriv("客户移交");
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
					throw new Exception("你没有权限移交客户：$custname");
				}
			
			}
		}
		print "<script>location.href='../CRM/inc_crm_tools.php?action=add_yijiao&custlist=".$_GET['selectid']."';</script>";
		exit;
		
	}
	catch (Exception $e)
	{
		print "<script language='javascript'>alert('发生错误：".$e->getMessage()."');window.history.back(-1);</script>";
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
		print_infor("客户资料已删除",'trip',"location='?$return'","?$return",0);
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
				print "<script language='javascript'>alert('".$customername." 存在合同或销售单记录，请先删除相关单据');window.history.back(-1);</script>";
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
addShortCutByDate("createdate","创建时间");
//print_r($_SESSION);
//$SYSTEM_ADD_SQL = " and ((sysuser='".$_SESSION['LOGIN_USER_ID']."' and datascope=0) or datascope=1)";
//$SYSTEM_PRINT_SQL=1;
$filetablename = "customer";
require_once( "include.inc.php" );
systemhelpcontent( "客户管理", "100%" );

?>
