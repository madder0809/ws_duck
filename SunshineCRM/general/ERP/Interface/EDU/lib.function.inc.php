<?php

ini_set('date.timezone','Asia/Shanghai');
function addShortCutByDate($datefield,$showText='')					{
	if($showText=='')
		$showText=$datefield;
	global $db,$SYSTEM_ADD_SQL,$SYSTEM_PRINT_SQL;
	global $增加对查询日期快捷方式的支持_是否启用;
	$增加对查询日期快捷方式的支持_是否启用 = 1;
	$_SESSION['增加对查询日期快捷方式的支持']=="";
	if($_SESSION['增加对查询日期快捷方式的支持']=='')					{
		$_SESSION['增加对查询日期快捷方式的支持'] = '设置为1';
	}
	if($_GET['增加对查询日期快捷方式的支持GET']=="设置为0")					{
		$_SESSION['增加对查询日期快捷方式的支持'] = '设置为0';
	}
	elseif($_GET['增加对查询日期快捷方式的支持GET']=="设置为1")					{
		$_SESSION['增加对查询日期快捷方式的支持'] = '设置为1';
	}
	//print $_SESSION['增加对查询日期快捷方式的支持'];

	if(($_GET['action']==""||$_GET['action']=="init_default")&&$_SESSION['增加对查询日期快捷方式的支持']=='设置为1')			{
		print "<form name=formadd><table class=TableBlock width=100% ><tr><td nowrap class=TableContent align=left>";
		if($_GET['当前搜索方式']=="当天")	$_GET['当前搜索方式'] = "当天&nbsp;&nbsp;&nbsp;&nbsp;";
		if($_GET['当前搜索方式']=="")		$_GET['当前搜索方式'] = "没有选择";
		print "<font color=green>按 ".$showText." 搜索:".$_GET['当前搜索方式']."";

		$FormPageAction = FormPageAction("开始时间ADD",date("Y-m-d",mktime(0,0,1,date("m"),date("d"),date("Y"))),
											"结束时间ADD",date("Y-m-d",mktime(0,0,1,date("m"),date("d"),date("Y"))),
											'',
											"当前搜索方式","当天"
										);
		print "&nbsp;&nbsp;<a href=\"?$FormPageAction\">当天</a>";

		$FormPageAction = FormPageAction("开始时间ADD",date("Y-m-d",mktime(0,0,1,date("m"),date("d")-3,date("Y"))),
											"结束时间ADD",date("Y-m-d",mktime(0,0,1,date("m"),date("d"),date("Y"))),
											'',
											"当前搜索方式","最近三天"
										);
		print "&nbsp;&nbsp;<a href=\"?$FormPageAction\">最近三天</a>";

		$FormPageAction = FormPageAction("开始时间ADD",date("Y-m-d",mktime(0,0,1,date("m"),date("d")-7,date("Y"))),
											"结束时间ADD",date("Y-m-d",mktime(0,0,1,date("m"),date("d"),date("Y"))),
											'',
											"当前搜索方式","最近一周"
										);
		print "&nbsp;&nbsp;<a href=\"?$FormPageAction\">最近一周</a>";

		$FormPageAction = FormPageAction("开始时间ADD",date("Y-m-d",mktime(0,0,1,date("m"),date("d")-15,date("Y"))),
											"结束时间ADD",date("Y-m-d",mktime(0,0,1,date("m"),date("d"),date("Y"))),
											'',
											"当前搜索方式","最近半月"
										);
		print "&nbsp;&nbsp;<a href=\"?$FormPageAction\">最近半月</a>";

		$FormPageAction = FormPageAction("开始时间ADD",date("Y-m-d",mktime(0,0,1,date("m")-1,date("d"),date("Y"))),
											"结束时间ADD",date("Y-m-d",mktime(0,0,1,date("m"),date("d"),date("Y"))),
											'',
											"当前搜索方式","最近一月"
										);
		print "&nbsp;&nbsp;<a href=\"?$FormPageAction\">最近一月</a>";

		$FormPageAction = FormPageAction("开始时间ADD",date("Y-m-d",mktime(0,0,1,date("m")-2,date("d"),date("Y"))),
											"结束时间ADD",date("Y-m-d",mktime(0,0,1,date("m"),date("d"),date("Y"))),
											'',
											"当前搜索方式","最近两月"
										);
		print "&nbsp;&nbsp;<a href=\"?$FormPageAction\">最近两月</a>";

		$FormPageAction = FormPageAction("开始时间ADD",date("Y-m-d",mktime(0,0,1,date("m")-3,date("d"),date("Y"))),
											"结束时间ADD",date("Y-m-d",mktime(0,0,1,date("m"),date("d"),date("Y"))),
											'',
											"当前搜索方式","最近三月"
										);
		print "&nbsp;&nbsp;<a href=\"?$FormPageAction\">最近三月</a>";

		$FormPageAction = FormPageAction("开始时间ADD",date("Y-m-d",mktime(0,0,1,date("m")-6,date("d"),date("Y"))),
											"结束时间ADD",date("Y-m-d",mktime(0,0,1,date("m"),date("d"),date("Y"))),
											'',
											"当前搜索方式","最近六月"
										);
		print "&nbsp;&nbsp;<a href=\"?$FormPageAction\">最近六月</a>";

		print "&nbsp;&nbsp;<a href='?".FormPageAction("增加对查询日期快捷方式的支持GET","设置为0")."'><font color=gray>关闭显示</font></a>";

		print "</font></td></tr></table></form><BR>";
		if($_GET['开始时间ADD']!=""&&$_GET['结束时间ADD']!="")				{
			$SYSTEM_ADD_SQL .= "and $datefield>='".$_GET['开始时间ADD']." 00:00:00' and $datefield<='".$_GET['结束时间ADD']." 23:59:59'";
		}

	}

	//$SYSTEM_PRINT_SQL = "1";

}

function 定时执行函数($函数名称='同步教学计划学分信息到成绩数据表之中',$间隔时间='30')			{
	//进行从主数据库中同步数据
	$变量名称 = "定时执行函数_".$函数名称;
	//session_unregister($变量名称);//测试使用的行代码
	if(!isset($_SESSION[$变量名称]))		{
	
		$_SESSION[$变量名称] = time();
	}
	$现在时间线 = time();
	$时间差 = $现在时间线 - $_SESSION[$变量名称];
	$时间差CEIL = ceil($时间差/60);
	//print_R($时间差);
	//print $变量名称.":".$时间差." ".date("H:i",$_SESSION[$变量名称])."<BR>";
	//print $PHP_SELF_BEGIN."<BR>";
	//当时间超过某一值,或是头一次访问的时候,需要执行此过程
	if($时间差CEIL>=$间隔时间||$时间差==0)							{
		//执行参数传递过来的参数
		$函数名称();
		//更新标记时间
		$_SESSION[$变量名称] = time();
	}//exit;
}



//返回数组的名次信息
function returnArrayMingCi($Result='')				{
	//排名信息
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


//子菜单权限管理部分,同时在FRAMEWORK和EDU下面进行定义
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
	//三个都为空时的情况判断
	if($DEPT_NAME==""&&$USER_NAME==""&&$ROLE_NAME=="")		{
		$return = 1;
	}
	//全体部门
	if($DEPT_NAME=="ALL_DEPT")			{
		$return = 1.5;
	}
	//用户判断
	$LOGIN_USER_ID = $_SESSION['LOGIN_USER_ID'];
	$LOGIN_USER_ID_ARRAY = explode(',',$USER_NAME);
	if(in_array($LOGIN_USER_ID,$LOGIN_USER_ID_ARRAY))		{
		$return = 2;
	}
	//部门判断
	$LOGIN_DEPT_ID = $_SESSION['LOGIN_DEPT_ID'];
	$LOGIN_DEPT_ID_ARRAY = explode(',',$DEPT_NAME);
	if(in_array($LOGIN_DEPT_ID,$LOGIN_DEPT_ID_ARRAY))		{
		$return = 3;
	}
	//角色判断
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
