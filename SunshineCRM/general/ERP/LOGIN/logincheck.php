<?php
header("Content-type:text/html;charset=gb2312");

ini_set('display_errors', 1);
ini_set('error_reporting', E_ALL);

// display warnings and errors
error_reporting(E_WARNING | E_ERROR);

require_once('include.inc.php');
//$��ǰ׺ = "TD_OA.";
//print_R($db);exit;
function logincheck($username,$password)							{
	global $db,$��ǰ׺;
	$SQL		= "SELECT * FROM ".$��ǰ׺."user WHERE USER_ID = '$username'";
	$rs			= $db->Execute($SQL);
	$rs_a		= $rs->GetArray();
	$USER_ID	= $rs_a[0]['USER_ID'];
	$PASSWORDTEXT = $rs_a[0]['PASSWORD'];
	//print crypt('', $PASSWORDTEXT) == $PASSWORDTEXT;exit;
	//print_R($password);print_R($PASSWORDTEXT);exit;
	if($USER_ID!="")												{
		if(crypt($password,$PASSWORDTEXT) == $PASSWORDTEXT)			{
			//������ȷ
			return $rs_a;
			exit;
		}
		else	{
			//�������
			//print_R($password);print_R($username);exit;
			echo "<META HTTP-EQUIV=REFRESH CONTENT='0;URL=notchecked.php'>\n";
			exit;
		}
	}
	else	{
		//�û���������
		//print_R($password);print_R($_POST);exit;
		echo "<META HTTP-EQUIV=REFRESH CONTENT='0;URL=notchecked.php'>\n";
		exit;
	}
	exit;
}
//$array=explode('||',$_GET['checkstring']);//print_R($array);
//$username=$array[0];
//$password=$array[1];
//print $username.$password;exit;

//����������ĸ =
$checkUserName = explode('=',$_REQUEST['username']);
$checkUserPassword = explode('=',$_REQUEST['password']);
if(sizeof($checkUserName)>1||sizeof($checkUserPassword)>1)  {
	echo "<META HTTP-EQUIV=REFRESH CONTENT='0;URL=notchecked.php'>\n";
}
//����������ĸ "
$checkUserName = explode('"',$_REQUEST['username']);
$checkUserPassword = explode('"',$_REQUEST['password']);
if(sizeof($checkUserName)>1||sizeof($checkUserPassword)>1)  {
	echo "<META HTTP-EQUIV=REFRESH CONTENT='0;URL=notchecked.php'>\n";
}
//����������ĸ '
$checkUserName = explode("'",$_REQUEST['username']);
$checkUserPassword = explode("'",$_REQUEST['password']);
if(sizeof($checkUserName)>1||sizeof($checkUserPassword)>1)  {
	echo "<META HTTP-EQUIV=REFRESH CONTENT='0;URL=notchecked.php'>\n";
}

$rs_a	=	logincheck($_REQUEST['username'],$_REQUEST['password']);
if($rs_a[0]['USER_NAME']!='')	{
	session_start();
	$rs_a[0]['THEME'] = '3';
	$_SESSION['LOGIN_UID']		=	$rs_a[0]['UID'];
	$_SESSION['LOGIN_USER_ID']	=	$rs_a[0]['USER_ID'];
	$_SESSION['LOGIN_DEPT_ID']	=	$rs_a[0]['DEPT_ID'];
	$_SESSION['LOGIN_USER_PRIV']=	$rs_a[0]['USER_PRIV'];
	$_SESSION['LOGIN_THEME']	=	$rs_a[0]['THEME'];
	$_SESSION['LOGIN_AVATAR']	=	$rs_a[0]['AVATAR'];
	$_SESSION['LOGIN_USER_NAME']=	$rs_a[0]['USER_NAME'];
	$_SESSION['LOGIN_FUNC_ID_STR'] = $rs_a[0]['FUNC_ID_STR'];
	$_SESSION['LOGIN_USER_MOBILE']	=	$rs_a[0]['MOBIL_NO'];
	$_SESSION['LEFT_MENU']		=	$rs_a[0]['leftmenu'];
	$_SESSION['RIGHT_MENU']		=	$rs_a[0]['rightmenu'];
	$_SESSION[SMTPServerIP]= $rs_a[0][SMTPServerIP];
	$_SESSION[EmailAddress]= $rs_a[0][EmailAddress];
	$_SESSION[EmailPassword]= $rs_a[0][EmailPassword];
		
	$DEPT_ID=$rs_a[0]['DEPT_ID'];
	$sql="select DEPT_NAME from ".$��ǰ׺."department where DEPT_ID='$DEPT_ID'";
	//print $sql;
	$rs_d=$db->Execute($sql);
	$DEPT_NAME=$rs_d->fields['DEPT_NAME'];

	$USER_PRIV=$rs_a[0]['USER_PRIV'];
	$sql="select PRIV_NAME from ".$��ǰ׺."user_priv where USER_PRIV='$USER_PRIV'";
	//print $sql;
	$rs_u=$db->Execute($sql);
	$PRIV_NAME=$rs_u->fields['PRIV_NAME'];
	$_SESSION['LOGIN_DEPT_NAME']	=	$DEPT_NAME;
	$_SESSION[$SUNSHINE_USER_DEPT_NAME_VAR]=$DEPT_NAME;
	$_SESSION[$SUNSHINE_USER_PRIV_NAME_VAR]=$PRIV_NAME;
	//print $SUNSHINE_USER_AVATAR_VAR;

	$goalfile = "../Interface/Framework/global_config.ini";
	@$ini_file = @parse_ini_file( $goalfile );
	$_SESSION['SmsServerIP']=$ini_file[SmsServerIP];
	$_SESSION['SmsLoginID']=$ini_file[SmsLoginID];
	$_SESSION['SmsLoginPWD']=$ini_file[SmsLoginPWD];
	$_SESSION['limitEditDel']=$ini_file[limitEditDel];
	
	
	//����
	$_SESSION['deptid']=1;
	$sql="select * from ".$��ǰ׺."unit where id=".$_SESSION['deptid'];
	//print $sql;
	$rs_d=$db->Execute($sql);
	$_SESSION['numzero']=$rs_d->fields['numzero'];
	$_SESSION['UNIT_NAME']=$rs_d->fields['UNIT_NAME'];
	$_SESSION['TEL_NO']=$rs_d->fields['TEL_NO'];
	$_SESSION['ADDRESS']=$rs_d->fields['ADDRESS'];
	//print_R($_SESSION);print_R($_GET);exit;
	$MENU_TYPE = 0;

	//��־��¼
	system_log_input('��¼�ɹ�',$_SESSION['LOGIN_USER_ID']);
	
	echo "<META HTTP-EQUIV=REFRESH CONTENT='0;URL=../Framework/index.php'>\n";


	//echo "<META HTTP-EQUIV=REFRESH CONTENT='0;URL=Framework/index.php'>\n";
}
else	{
	system_log_input('��¼ʧ��',$_SESSION['LOGIN_USER_ID']);
	echo "<META HTTP-EQUIV=REFRESH CONTENT='0;URL=notchecked.php'>\n";

}
?>
