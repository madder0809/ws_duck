<?php
//######################�������-Ȩ�޽��鲿��##########################
SESSION_START();
require_once("lib.inc.php");
$GLOBAL_SESSION=returnsession();
require_once("systemprivateinc.php");
////CheckSystemPrivate("���ֻ�У԰ϵͳ����-���ֻ�У԰Ȩ��");
//######################�������-Ȩ�޽��鲿��##########################
//print "<script type=\"text/javascript\" language=\"javascript\" src=\"".ROOT_DIR."general/ERP/Enginee/jquery/jquery.js\"></script>";
if($_GET['action']=="")			{

	page_css("�ҵĸ��˲�������");
	print "<SCRIPT>
	function FormCheck()
	{
		if (document.form1.������.value == \"\") {
			alert(\"������û����д\");
			return false;
		}
		if (document.form1.ȷ��������.value == \"\") {
			alert(\"ȷ��������û����д\");
			return false;
		}
		if (document.form1.ȷ��������.value != document.form1.������.value) {
			alert(\"�����������벻һ��\");
			return false;
		}
	}
	</SCRIPT>";
	print "<FORM name=form1 onsubmit=\"return FormCheck();\"  action=\"?action=DataDeal&pageid=1\" method=post encType=multipart/form-data>";
	table_begin("80%");
	print "<tr class=TableHeader><td colspan=2>&nbsp;�ҵ������޸�</td></tr>";
	print "<tr class=TableData><td width=40%>&nbsp;ԭ����:</td><td>&nbsp;<INPUT type=password class=SmallInput maxLength=20  name=ԭ���� value=\"\"  >&nbsp;(�������ԭ����)</td></tr>";
	print "<tr class=TableData><td width=40%>&nbsp;������:</td><td>&nbsp;<INPUT type=password class=SmallInput maxLength=20  name=������ value=\"\"  >&nbsp;(������λ��ĸ������)</td></tr>";
	print "<tr class=TableData><td width=40%>&nbsp;ȷ��������:</td><td>&nbsp;<INPUT type=password class=SmallInput maxLength=20  name=ȷ�������� value=\"\"  >&nbsp;(������λ��ĸ������)</td></tr>";
	
	print_submit("�ύ");
	table_end();
	form_end();

	print "<BR>";

	//insert into system_log(loginaction,DATE,REMOTE_ADDR,HTTP_USER_AGENT,QUERY_STRING,SCRIPT_NAME,USERID,SQLTEXT)
	$sql = "select * from system_log where loginaction='�û��޸�����' and USERID='".$_SESSION['LOGIN_USER_ID']."'";
	$rs = $db->Execute($sql);
	$rs_a = $rs->GetArray();
	table_begin("80%");
	print "<tr class=TableHeader><td colspan=3>&nbsp;�����޸���־</td></tr>";
	print "<tr class=TableContent><td>&nbsp;�޸�ʱ��</td><td>&nbsp;Զ��IP</td><td>&nbsp;��������</td></tr>";
	for($i=0;$i<sizeof($rs_a);$i++)		{
		print "<tr class=TableData><td>&nbsp;".$rs_a[$i]['DATE']."</td><td>&nbsp;".$rs_a[$i]['REMOTE_ADDR']."</td><td>&nbsp;".$rs_a[$i]['SQLTEXT']."</td></tr>";

	}
	table_end();

	exit;
}






if($_GET['action']=="DataDeal"){

	page_css("�ҵ������޸�");

	$ԭ���� = $_POST['ԭ����'];
	$������ = $_POST['������'];
	$ȷ�������� = $_POST['ȷ��������'];

	if(strlen($������)<6)		{
		print_infor("������������볤��̫��!",'',"location='?'");
		exit;
	}

	if($������!=$ȷ��������)		{
		print_infor("��������������벻һ��!",'',"location='?'");
		exit;
	}

	$SQL			= "SELECT PASSWORD FROM user WHERE USER_ID = '".$_SESSION['LOGIN_USER_ID']."'";
	$rs				= $db->Execute($SQL);
	$rs_a			= $rs->GetArray();
	$PASSWORDTEXT = $rs_a[0]['PASSWORD'];
	if(crypt($ԭ����,$PASSWORDTEXT) == $PASSWORDTEXT){
		$���������� = crypt($ȷ��������);
		$sql = "update user set PASSWORD='$����������' WHERE USER_ID = '".$_SESSION['LOGIN_USER_ID']."'";
		$db->Execute($sql);
		��¼�û��޸�������Ϊ($_SESSION['LOGIN_USER_ID'],$sql,"�ɹ��޸�����");
		print_infor("���������޸ĳɹ�!",'',"location='?'");
		exit;
	}else{
		��¼�û��޸�������Ϊ($_SESSION['LOGIN_USER_ID'],$sql,"�޸�����ʱ�����������");
		print_infor("�������ԭ�������!",'',"location='?'");
		exit;
	}
}



function ��¼�û��޸�������Ϊ($userid,$sql,$type="�޸ĳɹ�")	{
	global $db;
	$sql = ereg_replace("'",'&#039;',$sql);
	$sql = "insert into system_log(loginaction,DATE,REMOTE_ADDR,HTTP_USER_AGENT,QUERY_STRING,SCRIPT_NAME,USERID,SQLTEXT)
			values('�û��޸�����'
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
	��Ȩ����:֣�ݵ���Ƽ�������޹�˾;
	��ϵ��ʽ:0371-69663266;
	��˾��ַ:����֣�ݾ��ü��������������־�����·ͨ�Ų�ҵ԰��¥����;
	��˾���:֣�ݵ���Ƽ�������޹�˾λ���й��в�����-֣��,������2007��1��,�����ڰѻ����Ƚ���Ϣ����������ͨ�ż���������ѹ�����ҵ��ʵ���ռ���������ҵ�ͻ��Ĺ�����ҵ���»�У�ȫ���ṩ��������֪ʶ��Ȩ�Ľ�����������������������������в�������ĸ�У���������������СѧУ��������ṩ�̡�Ŀǰ�����ж�Ҹ�ְ����ְ��ԺУʹ��ͨ���в��з����Ŀ���������ͷ���;

	�������:����Ƽ�������������Լܹ�ƽ̨,�Լ��������֮����չ���κ��������Ʒ;
	����Э��:���ֻ�У԰��ƷΪ��ҵ���,�������ΪLICENSE��ʽ;����CRMϵͳ��SunshineCRMϵͳΪGPLV3Э�����,GPLV3Э����������뵽�ٶ�����;
	��������:�����ʹ�õ�ADODB��,PHPEXCEL��,SMTARY���ԭ��������,���´���������������;
	*/
?>