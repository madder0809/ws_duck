<?php
ini_set('display_errors', 1);
ini_set('error_reporting', E_ALL);
error_reporting(E_WARNING | E_ERROR);
require_once('lib.inc.php');
$GLOBAL_SESSION=returnsession();
global $db;
$sql="select `a`.`billid`,`b`.`ROWID` AS `rowid`,`b`.`supplyname` AS `supplyname`,`a`.`caigoudate`,`a`.`daohuodate`,`a`.`createtime`,`a`.`danhao`,`a`.`zhuti`,`a`.`totalmoney`,`a`.`paymoney`,`a`.`oddment`,`b`.`yufukuan` AS `yufukuan`,(`a`.`totalmoney` - `a`.`paymoney` - `a`.`oddment`) AS `own` ,`d`.`USER_NAME` AS `createman` from (`buyplanmain` `a` join `supply` `b` on`a`.`supplyid` = `b`.`ROWID` left join `user` `d` on`a`.`createman` = `d`.`USER_ID`) where (`a`.`ifpay` < 2 and `a`.`supplyid`=".$_GET['supplyid']." )";
$rs=$db->Execute($sql);
$rs_a = $rs->GetArray();
$yufukuan=0;

if(sizeof($rs_a)>0)
	$yufukuan=$rs_a[0]['yufukuan'];


$head=array("billid"=>"单号","supplyname"=>"供货商","zhuti"=>"采购主题","createman"=>"采购人","caigoudate"=>"采购时间","totalmoney"=>"总金额","paymoney"=>"已支付金额","oddment"=>"去零金额","own"=>"未支付金额");
$headtype=array("billid"=>"string","supplyname"=>"string","zhuti"=>"string","createman"=>"string","caigoudate"=>"string","totalmoney"=>"float","paymoney"=>"float","oddment"=>"float","own"=>"float");
$title="应付款明细";
$sumcol=array("totalmoney"=>"","paymoney"=>"","oddment"=>"","own"=>"");
if($_GET['out_excel'] == 'true'){
	export_XLS($head,$rs_a,$title,$sumcol);
	exit;
}
?>
<html>
<head>
<?php page_css($title); ?>
<script language="javascript" src="../LODOP60/LodopFuncs.js"></script>
<object id="LODOP" classid="clsid:2105C259-1E0C-4534-8141-A753534CB4CA"
	width=0 height=0> <embed id="LODOP_EM" type="application/x-print-lodop"
		width=0 height=0></embed> </object>
</head>
<body class=bodycolor topMargin=5>
<div id='con'>
<table class=TableBlock align=center width=100%>
<tr><td colspan="11" class="TableHeader">&nbsp;<?php echo $title?>——<?php echo $rs_a[0][supplyname];?>(预付款：<font color=red><?php echo number_format($yufukuan,0,".",",")?></font>元)</td></tr>
	<tr class=TableHeader>
		<?php 
	foreach ($head as $key=>$val)
	{
?>
		<td nowrap align=center><?php echo $val?></td>
<?php 
	}
?></tr>
	<?php
	foreach($rs_a as $row)
	{
		echo "<tr class=TableData>";
		foreach ($head as $key=>$val)
		{
			if($headtype[$key]=="int" || $headtype[$key]=="float")
				$align="right";
			else if($headtype[$key]=="char")
				$align="center";
			else
				$align="left";
			echo "<td nowrap align='".$align."'>";
			if($headtype[$key]=="float")
				echo number_format($row[$key],2,".",",");
			else
				echo $row[$key];
			echo "</td>";
			foreach ($sumcol as $sumkey=>$sumval)
			{
				if($sumkey==$key)
					$sumcol[$sumkey]+=$row[$key];
			}
		}
		echo "</tr>";
	}
	?>
	<tr class="TableHeader">
<?php 
	$i=0;
	foreach ($head as $key=>$val)
	{
		if($i==0)
			print "<td>合计 <b>".sizeof($rs_a)."</b> 条记录</td>";
		else
		{
			print "<td align=right><b>";
			foreach ($sumcol as $sumkey=>$sumval)
			{
				if($sumkey==$key)
				{
					if(is_float($sumval))
						echo number_format($sumval,2,".",",");
					else 
						echo $sumval;
				}
			}
			print "</b></td>";
		}
		$i++;
	}
?>	</tr>
</table>
</div>
<form>
<p align="center"><input type="button" class="SmallButton" value=" 打印 "
	onclick="javascript:prn_print();"> <input type="button"
	class="SmallButton" value="导出"
	onclick="location='v_supplyownmoney_mingxi.php?supplyid=<?php echo $_GET[supplyid];?>&out_excel=true';">
<input type="button" class="SmallButton" value=" 返回 "
	onclick="location='v_supplyownmoney_newai.php';"></p>
</form>

</body>
<script language="javascript" type="text/javascript">   
    var LODOP; //声明为全局变量 
	function prn_print() {		
		CreateOneFormPage();
		LODOP.PREVIEW();
		//LODOP.PRINT();	
	};

	function CreateOneFormPage(){
	
		LODOP=getLodop(document.getElementById('LODOP'),document.getElementById('LODOP_EM'));  

		LODOP.PRINT_INIT("打印应付款明细");
		
		LODOP.SET_PRINT_PAGESIZE(2,0,0,"");
		LODOP.ADD_PRINT_TABLE("10%","10%","80%","80%",document.documentElement.innerHTML);
		LODOP.SET_PRINT_MODE("PRINT_PAGE_PERCENT","Auto-Width");
	};              
	

</script>

</html>
