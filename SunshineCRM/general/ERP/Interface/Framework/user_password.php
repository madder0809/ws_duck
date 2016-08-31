<?php
//######################教育组件-权限较验部分##########################
SESSION_START();
require_once("lib.inc.php");
$GLOBAL_SESSION=returnsession();
require_once("systemprivateinc.php");
////CheckSystemPrivate("数字化校园系统设置-数字化校园权限");
//######################教育组件-权限较验部分##########################
//print "<script type=\"text/javascript\" language=\"javascript\" src=\"".ROOT_DIR."general/ERP/Enginee/jquery/jquery.js\"></script>";
if($_GET['action']=="")			{

	page_css("我的个人参数设置");
	print "<SCRIPT>
	function FormCheck()
	{
		if (document.form1.新密码.value == \"\") {
			alert(\"新密码没有填写\");
			return false;
		}
		if (document.form1.确认新密码.value == \"\") {
			alert(\"确认新密码没有填写\");
			return false;
		}
		if (document.form1.确认新密码.value != document.form1.新密码.value) {
			alert(\"两次输入密码不一致\");
			return false;
		}
	}
	</SCRIPT>";
	print "<FORM name=form1 onsubmit=\"return FormCheck();\"  action=\"?action=DataDeal&pageid=1\" method=post encType=multipart/form-data>";
	table_begin("80%");
	print "<tr class=TableHeader><td colspan=2>&nbsp;我的密码修改</td></tr>";
	print "<tr class=TableData><td width=40%>&nbsp;原密码:</td><td>&nbsp;<INPUT type=password class=SmallInput maxLength=20  name=原密码 value=\"\"  >&nbsp;(输入你的原密码)</td></tr>";
	print "<tr class=TableData><td width=40%>&nbsp;新密码:</td><td>&nbsp;<INPUT type=password class=SmallInput maxLength=20  name=新密码 value=\"\"  >&nbsp;(至少六位字母或数字)</td></tr>";
	print "<tr class=TableData><td width=40%>&nbsp;确认新密码:</td><td>&nbsp;<INPUT type=password class=SmallInput maxLength=20  name=确认新密码 value=\"\"  >&nbsp;(至少六位字母或数字)</td></tr>";
	
	print_submit("提交");
	table_end();
	form_end();

	print "<BR>";

	//insert into system_log(loginaction,DATE,REMOTE_ADDR,HTTP_USER_AGENT,QUERY_STRING,SCRIPT_NAME,USERID,SQLTEXT)
	$sql = "select * from system_log where loginaction='用户修改密码' and USERID='".$_SESSION['LOGIN_USER_ID']."'";
	$rs = $db->Execute($sql);
	$rs_a = $rs->GetArray();
	table_begin("80%");
	print "<tr class=TableHeader><td colspan=3>&nbsp;密码修改日志</td></tr>";
	print "<tr class=TableContent><td>&nbsp;修改时间</td><td>&nbsp;远程IP</td><td>&nbsp;操作类型</td></tr>";
	for($i=0;$i<sizeof($rs_a);$i++)		{
		print "<tr class=TableData><td>&nbsp;".$rs_a[$i]['DATE']."</td><td>&nbsp;".$rs_a[$i]['REMOTE_ADDR']."</td><td>&nbsp;".$rs_a[$i]['SQLTEXT']."</td></tr>";

	}
	table_end();

	exit;
}






if($_GET['action']=="DataDeal"){

	page_css("我的密码修改");

	$原密码 = $_POST['原密码'];
	$新密码 = $_POST['新密码'];
	$确认新密码 = $_POST['确认新密码'];

	if(strlen($新密码)<6)		{
		print_infor("您输入的新密码长度太短!",'',"location='?'");
		exit;
	}

	if($新密码!=$确认新密码)		{
		print_infor("您两次输入的密码不一致!",'',"location='?'");
		exit;
	}

	$SQL			= "SELECT PASSWORD FROM user WHERE USER_ID = '".$_SESSION['LOGIN_USER_ID']."'";
	$rs				= $db->Execute($SQL);
	$rs_a			= $rs->GetArray();
	$PASSWORDTEXT = $rs_a[0]['PASSWORD'];
	if(crypt($原密码,$PASSWORDTEXT) == $PASSWORDTEXT){
		$新密码密文 = crypt($确认新密码);
		$sql = "update user set PASSWORD='$新密码密文' WHERE USER_ID = '".$_SESSION['LOGIN_USER_ID']."'";
		$db->Execute($sql);
		记录用户修改密码行为($_SESSION['LOGIN_USER_ID'],$sql,"成功修改密码");
		print_infor("您的密码修改成功!",'',"location='?'");
		exit;
	}else{
		记录用户修改密码行为($_SESSION['LOGIN_USER_ID'],$sql,"修改密码时密码输入错误");
		print_infor("您输入的原密码错误!",'',"location='?'");
		exit;
	}
}



function 记录用户修改密码行为($userid,$sql,$type="修改成功")	{
	global $db;
	$sql = ereg_replace("'",'&#039;',$sql);
	$sql = "insert into system_log(loginaction,DATE,REMOTE_ADDR,HTTP_USER_AGENT,QUERY_STRING,SCRIPT_NAME,USERID,SQLTEXT)
			values('用户修改密码'
			,'".date("Y-m-d H:i:s")."'
			,'".$_SERVER['REMOTE_ADDR']."'
			,'".$_SERVER['HTTP_USER_AGENT']."'
			,'".$_SERVER['QUERY_STRING']."'
			,'".$_SERVER['SCRIPT_NAME']."'
			,'$userid'
			,'$type'
			);";
	$db->Execute($sql);
}

?>
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
?>