<?php

ini_set('date.timezone','Asia/Shanghai');
function addShortCutByDate($datefield,$showText='')					{
	if($showText=='')
		$showText=$datefield;
	global $db,$SYSTEM_ADD_SQL,$SYSTEM_PRINT_SQL;
	global $���ӶԲ�ѯ���ڿ�ݷ�ʽ��֧��_�Ƿ�����;
	$���ӶԲ�ѯ���ڿ�ݷ�ʽ��֧��_�Ƿ����� = 1;
	$_SESSION['���ӶԲ�ѯ���ڿ�ݷ�ʽ��֧��']=="";
	if($_SESSION['���ӶԲ�ѯ���ڿ�ݷ�ʽ��֧��']=='')					{
		$_SESSION['���ӶԲ�ѯ���ڿ�ݷ�ʽ��֧��'] = '����Ϊ1';
	}
	if($_GET['���ӶԲ�ѯ���ڿ�ݷ�ʽ��֧��GET']=="����Ϊ0")					{
		$_SESSION['���ӶԲ�ѯ���ڿ�ݷ�ʽ��֧��'] = '����Ϊ0';
	}
	elseif($_GET['���ӶԲ�ѯ���ڿ�ݷ�ʽ��֧��GET']=="����Ϊ1")					{
		$_SESSION['���ӶԲ�ѯ���ڿ�ݷ�ʽ��֧��'] = '����Ϊ1';
	}
	//print $_SESSION['���ӶԲ�ѯ���ڿ�ݷ�ʽ��֧��'];

	if(($_GET['action']==""||$_GET['action']=="init_default")&&$_SESSION['���ӶԲ�ѯ���ڿ�ݷ�ʽ��֧��']=='����Ϊ1')			{
		print "<form name=formadd><table class=TableBlock width=100% ><tr><td nowrap class=TableContent align=left>";
		if($_GET['��ǰ������ʽ']=="����")	$_GET['��ǰ������ʽ'] = "����&nbsp;&nbsp;&nbsp;&nbsp;";
		if($_GET['��ǰ������ʽ']=="")		$_GET['��ǰ������ʽ'] = "û��ѡ��";
		print "<font color=green>�� ".$showText." ����:".$_GET['��ǰ������ʽ']."";

		$FormPageAction = FormPageAction("��ʼʱ��ADD",date("Y-m-d",mktime(0,0,1,date("m"),date("d"),date("Y"))),
											"����ʱ��ADD",date("Y-m-d",mktime(0,0,1,date("m"),date("d"),date("Y"))),
											'',
											"��ǰ������ʽ","����"
										);
		print "&nbsp;&nbsp;<a href=\"?$FormPageAction\">����</a>";

		$FormPageAction = FormPageAction("��ʼʱ��ADD",date("Y-m-d",mktime(0,0,1,date("m"),date("d")-3,date("Y"))),
											"����ʱ��ADD",date("Y-m-d",mktime(0,0,1,date("m"),date("d"),date("Y"))),
											'',
											"��ǰ������ʽ","�������"
										);
		print "&nbsp;&nbsp;<a href=\"?$FormPageAction\">�������</a>";

		$FormPageAction = FormPageAction("��ʼʱ��ADD",date("Y-m-d",mktime(0,0,1,date("m"),date("d")-7,date("Y"))),
											"����ʱ��ADD",date("Y-m-d",mktime(0,0,1,date("m"),date("d"),date("Y"))),
											'',
											"��ǰ������ʽ","���һ��"
										);
		print "&nbsp;&nbsp;<a href=\"?$FormPageAction\">���һ��</a>";

		$FormPageAction = FormPageAction("��ʼʱ��ADD",date("Y-m-d",mktime(0,0,1,date("m"),date("d")-15,date("Y"))),
											"����ʱ��ADD",date("Y-m-d",mktime(0,0,1,date("m"),date("d"),date("Y"))),
											'',
											"��ǰ������ʽ","�������"
										);
		print "&nbsp;&nbsp;<a href=\"?$FormPageAction\">�������</a>";

		$FormPageAction = FormPageAction("��ʼʱ��ADD",date("Y-m-d",mktime(0,0,1,date("m")-1,date("d"),date("Y"))),
											"����ʱ��ADD",date("Y-m-d",mktime(0,0,1,date("m"),date("d"),date("Y"))),
											'',
											"��ǰ������ʽ","���һ��"
										);
		print "&nbsp;&nbsp;<a href=\"?$FormPageAction\">���һ��</a>";

		$FormPageAction = FormPageAction("��ʼʱ��ADD",date("Y-m-d",mktime(0,0,1,date("m")-2,date("d"),date("Y"))),
											"����ʱ��ADD",date("Y-m-d",mktime(0,0,1,date("m"),date("d"),date("Y"))),
											'',
											"��ǰ������ʽ","�������"
										);
		print "&nbsp;&nbsp;<a href=\"?$FormPageAction\">�������</a>";

		$FormPageAction = FormPageAction("��ʼʱ��ADD",date("Y-m-d",mktime(0,0,1,date("m")-3,date("d"),date("Y"))),
											"����ʱ��ADD",date("Y-m-d",mktime(0,0,1,date("m"),date("d"),date("Y"))),
											'',
											"��ǰ������ʽ","�������"
										);
		print "&nbsp;&nbsp;<a href=\"?$FormPageAction\">�������</a>";

		$FormPageAction = FormPageAction("��ʼʱ��ADD",date("Y-m-d",mktime(0,0,1,date("m")-6,date("d"),date("Y"))),
											"����ʱ��ADD",date("Y-m-d",mktime(0,0,1,date("m"),date("d"),date("Y"))),
											'',
											"��ǰ������ʽ","�������"
										);
		print "&nbsp;&nbsp;<a href=\"?$FormPageAction\">�������</a>";

		print "&nbsp;&nbsp;<a href='?".FormPageAction("���ӶԲ�ѯ���ڿ�ݷ�ʽ��֧��GET","����Ϊ0")."'><font color=gray>�ر���ʾ</font></a>";

		print "</font></td></tr></table></form><BR>";
		if($_GET['��ʼʱ��ADD']!=""&&$_GET['����ʱ��ADD']!="")				{
			$SYSTEM_ADD_SQL .= "and $datefield>='".$_GET['��ʼʱ��ADD']." 00:00:00' and $datefield<='".$_GET['����ʱ��ADD']." 23:59:59'";
		}

	}

	//$SYSTEM_PRINT_SQL = "1";

}

function ��ʱִ�к���($��������='ͬ����ѧ�ƻ�ѧ����Ϣ���ɼ����ݱ�֮��',$���ʱ��='30')			{
	//���д������ݿ���ͬ������
	$�������� = "��ʱִ�к���_".$��������;
	//session_unregister($��������);//����ʹ�õ��д���
	if(!isset($_SESSION[$��������]))		{
	
		$_SESSION[$��������] = time();
	}
	$����ʱ���� = time();
	$ʱ��� = $����ʱ���� - $_SESSION[$��������];
	$ʱ���CEIL = ceil($ʱ���/60);
	//print_R($ʱ���);
	//print $��������.":".$ʱ���." ".date("H:i",$_SESSION[$��������])."<BR>";
	//print $PHP_SELF_BEGIN."<BR>";
	//��ʱ�䳬��ĳһֵ,����ͷһ�η��ʵ�ʱ��,��Ҫִ�д˹���
	if($ʱ���CEIL>=$���ʱ��||$ʱ���==0)							{
		//ִ�в������ݹ����Ĳ���
		$��������();
		//���±��ʱ��
		$_SESSION[$��������] = time();
	}//exit;
}



//���������������Ϣ
function returnArrayMingCi($Result='')				{
	//������Ϣ
	$ArrayValues = @array_values($Result);
	$NewSortArrayValues = array();
	for($i=0;$i<sizeof($ArrayValues);$i++)		{
		$Values = $ArrayValues[$i];
		if(!in_array($Values,$NewSortArrayValues))	{
			$NewSortArray[$Values] = $i+1;
			array_push($NewSortArrayValues,$Values);
		}
	}
	//print_R($NewSortArray);
	return $NewSortArray;
}


function aksort(&$array,$valrev=false,$keyrev=false) {
  if ($valrev) { arsort($array); } else { asort($array); }
    $vals = array_count_values($array);
    $i = 0;
    foreach ($vals AS $val=>$num) {
        $first = array_splice($array,0,$i);
        $tmp = array_splice($array,0,$num);
        if ($keyrev) { krsort($tmp); } else { ksort($tmp); }
        $array = array_merge($first,$tmp,$array);
        unset($tmp);
        $i = $num;
    }
}


//�Ӳ˵�Ȩ�޹�����,ͬʱ��FRAMEWORK��EDU������ж���
function returnPrivMenu($ModuleName)		{
	global $db,$_SERVER,$_SESSION;
	$PHP_SELF_ARRAY = explode('/',$_SERVER['PHP_SELF']);
	$PHP_SELF = array_pop($PHP_SELF_ARRAY);
	$sql = "select * from systemprivateinc where `FILE`='$PHP_SELF' and `MODULE`='$ModuleName'";
	$rs = $db->Execute($sql);
	$rs_a = $rs->GetArray(); //print_R($rs_a);
	$DEPT_NAME = $rs_a[0]['DEPT_ID'];
	$USER_NAME = $rs_a[0]['USER_ID'];
	$ROLE_NAME = $rs_a[0]['ROLE_ID'];
	$return = 0;
	//������Ϊ��ʱ������ж�
	if($DEPT_NAME==""&&$USER_NAME==""&&$ROLE_NAME=="")		{
		$return = 1;
	}
	//ȫ�岿��
	if($DEPT_NAME=="ALL_DEPT")			{
		$return = 1.5;
	}
	//�û��ж�
	$LOGIN_USER_ID = $_SESSION['LOGIN_USER_ID'];
	$LOGIN_USER_ID_ARRAY = explode(',',$USER_NAME);
	if(in_array($LOGIN_USER_ID,$LOGIN_USER_ID_ARRAY))		{
		$return = 2;
	}
	//�����ж�
	$LOGIN_DEPT_ID = $_SESSION['LOGIN_DEPT_ID'];
	$LOGIN_DEPT_ID_ARRAY = explode(',',$DEPT_NAME);
	if(in_array($LOGIN_DEPT_ID,$LOGIN_DEPT_ID_ARRAY))		{
		$return = 3;
	}
	//��ɫ�ж�
	$LOGIN_USER_PRIV = $_SESSION['LOGIN_USER_PRIV'];
	$LOGIN_USER_PRIV_ARRAY = explode(',',$ROLE_NAME);
	if(in_array($LOGIN_USER_PRIV,$LOGIN_USER_PRIV_ARRAY))		{
		$return = 4;
	}
	//print_R($_SESSION);
	return $return;
}

function base64_encode2($value)		{
	return $value;
}
?>
