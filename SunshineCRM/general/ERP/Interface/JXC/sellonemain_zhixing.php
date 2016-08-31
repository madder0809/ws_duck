<?php
ini_set('display_errors',1);
ini_set('error_reporting',E_ALL);
error_reporting(E_WARNING | E_ERROR);
require_once("lib.inc.php");
$GLOBAL_SESSION=returnsession();
$billid=$_GET['billid'];
?>
<head>
<style>
.attention{
	
	border-top: 1pt none ;
	border-right: 1pt none;
	border-bottom: 1pt solid #000066;
	border-left: 1pt none;
	font-size:12pt;
	font-weight: bold;
	}
</style>
<?php 
print "<script type=\"text/javascript\" language=\"javascript\" src=\"".ROOT_DIR."general/ERP/Enginee/lib/common.js\"></script>";
print "<script type=\"text/javascript\" language=\"javascript\" src=\"".ROOT_DIR."general/ERP/Enginee/jquery/jquery.js\"></script>";

?>

<script type="text/javascript">
$(document).ready(function(){
	showorhide();
	
	$("input[name='ifpay']").click(function(){
		
		showorhide();
	});

});
function showorhide()
{
if($("input[name='ifpay'][checked]").val()==1)
{
	
	 $("#divshoukuan").html('收款');
	 $("#divzhaoling").html('找零');
}
 else
 {

	 $("#divshoukuan").html('押金');
	 $("#divzhaoling").html('尚欠');
 }
$("#quling").val('');
$("#shoukuan").val('');
$("#yingshou").html('');
$("#zhaoling").html('');
}
function inputFloat(event)
{
	if (event.keyCode!=46 && event.keyCode!=45 && (event.keyCode<48 || event.keyCode>57)) 
		event.returnValue=false
}
function focusNext(event)
{
	if(event.keyCode==13)
		event.keyCode=9
}
function funquling(allmoney,ql)
{
	if(allmoney>0 && ql>allmoney)
	{
		alert('去零金额不能大于总金额');
		form1.quling.focus();
		form1.quling.select();
		return false;
	}
	$("#yingshou").html('<font size=+2 color=red>'+(allmoney-ql)+'</font>');
	if($("input[name='ifpay'][checked]").val()==1)
		form1.shoukuan.value=allmoney-ql;
}
function funshoukuan(allmoney,sk)
{
	var ql=form1.quling.value;
	var yingshou=allmoney-ql;
	if($("input[name='ifpay'][checked]").val()==1)
	{
		if(sk>=yingshou)
		{
			$("#zhaoling").html('<font size=+2 color=red>'+(sk-yingshou)+'</font>');
		}
		else
		{
			form1.quling.value=allmoney-sk;
			funquling(allmoney,allmoney-sk);
		}
	}
	else
	{
		if(sk>yingshou)
		{
			alert('押金不能大于'+yingshou);
			form1.shoukuan.focus();
			form1.shoukuan.select();
			return false;
		}
		else
		{
			$("#zhaoling").html('<font size=+2 color=red>'+(yingshou-sk)+'</font>');
		}
	}
}
function GetRadioValue(RadioName)
{
    var obj;    
    obj=document.getElementsByName(RadioName);
    if(obj!=null){
        var i;
        for(i=0;i<obj.length;i++){
            if(obj[i].checked){
                return obj[i].value;            
            }
        }
    }
}
function submitFormCheck(allmoney)
{
	if(form1.quling.value=='')
		form1.quling.value='0';
	if(form1.shoukuan.value=='')
		form1.shoukuan.value='0';
	if(parseFloat(form1.quling.value)>0 && parseFloat(form1.quling.value)>allmoney/10)
	{
		if(!confirm('去零金额大于总金额的十分之一，是否确认执行？'))
			return false;
	}
	if(GetRadioValue('ifpay')==1 && parseFloat(form1.shoukuan.value)==0)
	{
		if(!confirm('收款金额为零，是否确认执行？'))
			return false;
	}
	var sbtn=document.getElementsByName('submit');
	for(i=0;i<sbtn.length;i++)
	{
		sbtn[i].value='提交中';
		sbtn[i].disabled=true;
	}
	return true;
}

</script>
<LINK href="<?php echo ROOT_DIR?>theme/3/style.css" type=text/css rel=stylesheet>
</head>
<?php 
	global $db;
	$billinfo= returntablefield("sellplanmain", "billid", $billid, "supplyid,totalmoney,ifpay");
	$sql="select sum(num) as num,sum(jine) as jine from sellplanmain_detail where mainrowid=$billid";
	$rs=$db->Execute($sql);
	$rs_a=$rs->GetArray();
	$allnum=$rs_a[0]['num'];
	$allmoney=$rs_a[0]['jine'];
	if($billinfo['totalmoney']!=$allmoney)
	{
		print "<script language='javascript'>alert('单据总金额与明细合计不一致，请编辑明细时保存退出');location='sellonemain_newai.php';</script>";
		exit;
	}
	$customer= returntablefield( "customer", "rowid", $billinfo[supplyid], "supplyname,yuchuzhi");
	
?>
<body class=bodycolor topMargin=5>
<table id=listtable align=center class=TableBlock width=100% border=0>
<TR><TD colspan=9 class=TableHeader height=30>&nbsp;店面销售单执行</TD></TR>
</table>
<div id="shoppingcart">
<form name="form1" method="post" action="sellonemain_newai.php?action=finish&billid=<?php echo $billid?>" onsubmit="return submitFormCheck(<?php echo $allmoney?>);">
<table align=center class=TableBlock width=100% border=0 id="table1" >
<tr ><td align=center class=TableHeader>客户</td><td class=TableLine2><?php echo $customer['supplyname']?></td></tr>
<tr ><td align=center class=TableHeader>预储值</td><td class=TableLine2><?php echo $customer['yuchuzhi']?> 元</td></tr>
<tr ><td align=center class=TableHeader>总数量</td><td class=TableLine2><?php echo $allnum?></td></tr>
<tr ><td align=center class=TableHeader>总金额</td><td class=TableLine2><font size=+2 color=red><?php echo number_format($allmoney, 2, '.', ',');?> </font>元</td></tr>
<tr ><td align=center class=TableHeader>是否付款</td><td class=TableLine2>
<input type="radio"  name="ifpay" value=1 checked>是
<input type="radio"  name="ifpay" value=0 >否
</td></tr>
<tr ><td align=center class=TableHeader>收款账户</td><td class=TableLine2><?php 
print_account_yuchu('accountid','','预收款支付');
?></td></tr>
<tr ><td align=center class=TableHeader>去零</td><td class=TableLine2>
<input type="text" id="quling" name="quling" class="attention"  onkeydown="focusNext(event)" onKeyPress="return inputFloat(event)" onblur="this.value=Math.round(this.value*100)/100;funquling(<?php echo $allmoney?>,this.value);">
（<span id="divyingshou">应收</span>：<span id="yingshou"></span>元）</td></tr>
<tr ><td align=center class=TableHeader><div id="divshoukuan">收款</div></td><td class=TableLine2>
<input type="text" id="shoukuan" name="shoukuan" class="attention"  onkeydown="focusNext(event)" onKeyPress="return inputFloat(event)" onblur="this.value=Math.round(this.value*100)/100;funshoukuan(<?php echo $allmoney?>,this.value);">
（<span id="divzhaoling">找零</span>：<span id="zhaoling"></span>元）</td></tr>
    
</tr>

</table>
</div>
<p align=center><input type=submit name='submit' value=" 保存 " class="SmallButton" >
&nbsp;&nbsp;<input type=button value=" 返回 " class="SmallButton" onclick="location='sellonemain_newai.php';"></p>
</form>
</body>
</html>