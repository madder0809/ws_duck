<?php

function dept_tree_list( $DEPT_ID, $PRIV_OP )
{
    global $DEEP_COUNT;
    global $connection;
    $query = "SELECT * from producttype where parentid='".$DEPT_ID."'";
    if ($_GET['MODULE_ID']!="")
    	$query.=" and rowid<>'".$_GET['MODULE_ID']."'";
    //只能选择目录
	if ($_GET['MANAGE_FLAG']=="1")
    	$query.=" and rowid not in (select distinct producttype from product)";
    $query.="order by rowid";
    //print $query;exit;
    $cursor = exequery( $connection, $query );
    $OPTION_TEXT = "";
    $DEEP_COUNT1 = $DEEP_COUNT;
    $DEEP_COUNT .= "　　";
    while ( $ROW = mysql_fetch_array( $cursor ) )
    {
        ++$COUNT;
        $DEPT_ID = $ROW['ROWID'];
        $DEPT_NAME = $ROW['name'];
        $DEPT_PARENT = $ROW['parentid'];
        $DEPT_NAME = str_replace( "<", "&lt", $DEPT_NAME );
        $DEPT_NAME = str_replace( ">", "&gt", $DEPT_NAME );
        $DEPT_NAME = stripslashes( $DEPT_NAME );
        if ( $PRIV_OP == 1 )
        {
            $DEPT_PRIV = is_dept_priv( $DEPT_ID );
        }
        else
        {
            $DEPT_PRIV = 1;
        }
        $image="<img src='../../../Framework/images/endnode.gif'>";
        $onclick="onclick=javascript:click_dept('".$DEPT_ID."') style=cursor:pointer";
        //判断是否下面有产品
        $query1 = "SELECT * from product where producttype='".$DEPT_ID."'";
        $cursor1 = exequery( $connection, $query1 );
		$ROW1 = mysql_fetch_array( $cursor1 ); 
		
		if(sizeof($ROW1)>1)
		{
			$image="<img width=16 height=16 src='../../../Framework/images/ts.gif'>";
		}
		else 
			$title="title='此目录为空'";
        //只能选择终端节点
        if ($_GET['MANAGE_FLAG']=="2")
        {
	        $query1 = "SELECT * from producttype where parentid='".$DEPT_ID."'";
	        $cursor1 = exequery( $connection, $query1 );
			$ROW1 = mysql_fetch_array( $cursor1 ); 
			if(sizeof($ROW1)>1)
			{
				$onclick="";
				$title="";
			}
        }	
        
        $OPTION_TEXT_CHILD = dept_tree_list( $DEPT_ID, $PRIV_OP );
        if ( $DEPT_PRIV == 1 )
        {
            $OPTION_TEXT .= "     <tr class=TableData>       <td class='menulines' id='".$DEPT_ID."' title='".$DEPT_NAME."' ".$onclick." ".$title.">".$DEEP_COUNT1.$image." ".$DEPT_NAME."</td>     </tr>";
        }
        if ( $OPTION_TEXT_CHILD != "" )
        {
            $OPTION_TEXT .= $OPTION_TEXT_CHILD;
        }
    }
    $DEEP_COUNT = $DEEP_COUNT1;
    return $OPTION_TEXT;
}


session_start();

include_once( "../user_select/setting.inc.php" );

if ( $TO_ID == "" || $TO_ID == "undefined" )
{
    $TO_ID = "TO_ID";
    $TO_NAME = "TO_NAME";
}
if ( $PRIV_OP == "undefined" )
{
    $PRIV_OP = "";
}
echo "
<html>
<head>
<title>选择产品类别</title>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=gb2312\">
<link rel=\"stylesheet\" type=\"text/css\" href=\"".ROOT_DIR."theme/$LOGIN_THEME/style.css\" />
<style>
	.menulines{}
</style>
<script type=\"text/javascript\" language=\"javascript\" src=\"".ROOT_DIR."general/ERP/Enginee/lib/common.js\"></script>
<script Language=\"JavaScript\">\r\n
function getOpenner()
{
   if(is_moz)
   {
      return parent.opener.document;
   }
   else
      return parent.dialogArguments.document;
}
var parent_window = getOpenner();
function click_dept(dept_id)   {
	targetelement=document.getElementById(dept_id);
	dept_name=targetelement.title;
	parent_window.form1.";
	echo $TO_ID;
	echo ".value=dept_id;
	parent_window.form1.";
	echo $TO_NAME;
	echo ".value=dept_name;
	window.close();
}
function borderize_on(targetelement)	{
	color=\"#003FBF\";
	targetelement.style.borderColor=\"black\";
	targetelement.style.backgroundColor=color;
	targetelement.style.color=\"white\";
	targetelement.style.fontWeight=\"bold\";
}

function begin_set()					{
	TO_VAL=parent_window.form1.";
	echo $TO_ID;
	echo ".value;";
	echo "
	var allElements=document.getElementsByTagName('td');
	for (step_i=0; step_i<allElements.length; step_i++)			{
		if(allElements[step_i].className==\"menulines\")			{
			dept_id=allElements[step_i].id;
			if(TO_VAL==dept_id)
				borderize_on(allElements[step_i]);
		}


	}



}
</script>
</head>
<body topmargin=\"1\" leftmargin=\"0\" class=\"bodycolor\" onload=\"begin_set()\">";
if ( $DEPT_ID == "" )
{
    $DEPT_ID = 0;
}
$OPTION_TEXT = dept_tree_list( $DEPT_ID, $PRIV_OP );
if ( $OPTION_TEXT == "" )
{
    message( "提示", "未定义或无可管理的类别", "blank" );
    
}
else
{
    echo "<table class=\"TableBlock\" width=\"95%\">   ";
    echo $OPTION_TEXT;
}
echo "</body></html>   ";
?>
