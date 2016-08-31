<?php
ini_set('display_errors', 1);
ini_set('error_reporting', E_ALL);
error_reporting(E_WARNING | E_ERROR);
require_once('lib.inc.php');
$GLOBAL_SESSION=returnsession();
validateMenuPriv("产品流动表");
global $db;
if(empty($_GET['start_time'])) {
	$start_time = date("Y-m-d 00:00:00",strtotime("last month"));
}else{
	$start_time = $_GET['start_time'];
}

if(empty($_GET['end_time'])) {
	$end_time = date("Y-m-d 23:59:59");
}else{
	$end_time = $_GET['end_time'];
}

if(!empty($_GET['productid'])) {
	$productid_where = " productid = '".trim($_GET['productid'])."' and (ruku<>'' or chuku<>'' or sunyi<>'' or diaobo<>'' or zuzhuang<>'' or nowkucun<>'')";
}else{
	$productid_where = " ruku<>'' or chuku<>'' or sunyi<>'' or diaobo<>'' or zuzhuang<>'' or nowkucun<>''";
}

if(!empty($_GET['storeid'])) {
	$bb_storeid = ' and bb.storeid='.trim($_GET['storeid']);
	$cc_storeid = ' and cc.storeid='.trim($_GET['storeid']);
	$dd_storeid = ' and dd.storeid='.trim($_GET['storeid']);
	$h_storeid = ' and h.storeid='.trim($_GET['storeid']);
	$zz_instoreid = ' and zz.instoreid='.trim($_GET['storeid']);
	$ee_outstoreid = ' and ee.outstoreid='.trim($_GET['storeid']);
	$yy_instoreid = ' and yy.instoreid='.trim($_GET['storeid']);
	$xx_outstoreid = ' and xx.outstoreid='.trim($_GET['storeid']);
}else{
	$storeid_where = '';
}
switch ($_GET['ordername'])
{
	case 'nowkucun':
		$order ='nowkucun';break; 
	case 'productname':
		$order = 'productname';break;
	case 'ruku':
		$order = 'ruku';break;
	case 'chuku':
		$order = 'chuku';break;
	case 'sunyi':
		$order = 'sunyi';break;
	case 'diaobo':
		$order = 'diaobo';break;
	case 'zuzhuang':
		$order = 'zuzhuang';break;
	default:
		$order = 'productid';
}

if($_GET['doubletime'] == 2){
	$sc = 'asc';
	$order_img = '<img src="images/arrow_up.gif" border="0">';
}else{
	$sc = 'desc';
	$order_img = '<img src="images/arrow_down.gif" border="0">';
}

$sql="select name,ROWID from stock";
//exit($sql);
$rs=$db->Execute($sql);
$store_array = $rs->GetArray();


$sql="select * from(SELECT a.productid,a.productname,(SELECT SUM(b.num) FROM stockinmain_detail b LEFT JOIN stockinmain bb on b.mainrowid=bb.billid WHERE b.prodid=a.productid and bb.indate>'".$start_time."' and bb.indate<'".$end_time."'".$bb_storeid.") as ruku,(SELECT SUM(c.num) FROM stockoutmain_detail c LEFT JOIN stockoutmain cc on c.mainrowid=cc.billid WHERE c.prodid=a.productid and cc.outdate>'".$start_time."' and cc.outdate<'".$end_time."'".$cc_storeid.") as chuku,(SELECT SUM(d.num) FROM storecheck_detail d LEFT JOIN storecheck dd on d.mainrowid=dd.billid WHERE d.prodid=a.productid and dd.createtime>'".$start_time."' and dd.createtime<'".$end_time."'".$dd_storeid.") as sunyi,((SELECT SUM(z.num) FROM stockchangemain_detail z LEFT JOIN stockchangemain zz on z.mainrowid=zz.billid WHERE z.prodid=a.productid and zz.inshenhetime>'".$start_time."' and zz.inshenhetime<'".$end_time."'".$zz_instoreid.") - (SELECT SUM(e.num) FROM stockchangemain_detail e LEFT JOIN stockchangemain ee on e.mainrowid=ee.billid WHERE e.prodid=a.productid and ee.outshenhetime>'".$start_time."' and ee.outshenhetime<'".$end_time."'".$ee_outstoreid.")) as diaobo,((SELECT SUM(y.num) FROM productzuzhuang_detail y LEFT JOIN productzuzhuang yy on y.mainrowid=yy.billid WHERE y.prodid=a.productid and yy.inshenhetime>'".$start_time."' and yy.inshenhetime<'".$end_time."'".$yy_instoreid.") - (SELECT SUM(x.num) FROM productzuzhuang_detail x LEFT JOIN productzuzhuang xx on x.mainrowid=xx.billid WHERE x.prodid=a.productid and xx.outshenhetime>'".$start_time."' and xx.outshenhetime<'".$end_time."'".$xx_outstoreid.")) as zuzhuang,(SELECT SUM(h.num) FROM store h WHERE h.prodid=a.productid  ".$h_storeid.") as nowkucun FROM product a ) as nn where ".$productid_where." order by ".$order;

$rs=$db->Execute($sql);
$rs_a = $rs->GetArray();

$head=array("productid"=>"产品ID","productname"=>"产品名称","ruku"=>"入库","chuku"=>"出库","sunyi"=>"损益","diaobo"=>"调拨","zuzhuang"=>"组装","nowkucun"=>"当前库存");
$headtype=array("productid"=>"string","productname"=>"string","ruku"=>"int","chuku"=>"int","sunyi"=>"int","diaobo"=>"int","zuzhuang"=>"int","nowkucun"=>"int");
$title="产品流动表";
$sumcol=array("ruku"=>"","chuku"=>"","sunyi"=>"","diaobo"=>"","zuzhuang"=>"","nowkucun"=>"");
if($_GET['out_excel'] == 'true'){
	
	export_XLS($head,$rs_a,$title,$sumcol);
	exit;

}
?>
<html>
<head>
<?php page_css($title); ?>
<script language="javascript" src="../LODOP60/LodopFuncs.js"></script>
<SCRIPT src="../../Enginee/WdatePicker/WdatePicker.js"></SCRIPT>
<object id="LODOP" classid="clsid:2105C259-1E0C-4534-8141-A753534CB4CA"
	width=0 height=0> <embed id="LODOP_EM" type="application/x-print-lodop"
		width=0 height=0></embed> </object>
</head>
<body class=bodycolor topMargin=5>
<div id='con'>
<table class=TableBlock align=center width=100%>
	<thead>
		<tr>
			<td colspan="13">
			<form action='' method="get">
			<table width="100%" class="Small" border="0">
				<thead>
					<tr>
						<td class='nowrap'>时间段： <input class="SmallInput" size="19"
							name="start_time" value="<?php echo $start_time; ?>"
							onclick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'})"> — <input
							class="SmallInput" size="19" name="end_time"
							value="<?php echo $end_time; ?>"
							onclick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'})"> 
							
							产品编号:
							<input type='text'   class="SmallInput" name='productid' value='<?php if(!empty($_GET['productid'])){ echo $_GET['productid'];} ?>' />

							仓库:
							<select class="SmallSelect" name="storeid">
							<option value=''>全部</option>
							<?php foreach ($store_array as $row){ ?>
								<option <?php echo ($_GET['storeid']==$row[ROWID])?'selected':''; ?> value=<?php echo $row[ROWID]; ?>><?php echo $row[name];?></option>
							<?php } ?>
							</select>																					
							<input
							class="SmallButtonA" type="submit" accesskey="f" value="查询"
							name="button" id="searchbtn"></td>
					</tr>
				</thead>
			</table>
			</form>
			</td>
		</tr>
		<tr>
			<td colspan="11" class="TableHeader">&nbsp;产品流动表</td>
		</tr>
	</thead>
	<tr class=TableHeader>

		<?php 
	foreach ($head as $key=>$val)
	{
?>
		<td nowrap align=center
			ondblclick="location='?start_time=<?php echo $start_time?>&end_time=<?php echo $end_time;?>&doubletime=<?php echo ($_GET['doubletime']==1)?2:1;?>&ordername=<?php echo $key?>'"><?php echo $val?><?php echo ($_GET['ordername']==$key)?$order_img:''; ?></td>
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
	onclick="javascript:prn_print();">&nbsp;<input type="button"
	class="SmallButton" value="导出"
	onclick="location='?out_excel=true&start_time=<?php echo $_GET[start_time];?>&end_time=<?php echo $_GET[end_time];?>&ordername=<?php echo $_GET['ordername']?>&doubletime=<?php  echo $_GET['doubletime'] ?>&storeid=<?php echo $_GET['storeid'];?>&productid=<?php echo $_GET['productid'];?>';"></p>
</form>
<script language="javascript" type="text/javascript">   
    var LODOP; //声明为全局变量 
	function prn_print() {		
		CreateOneFormPage();
		LODOP.PREVIEW();
		//LODOP.PRINT();	
	};

	function CreateOneFormPage(){
	
		LODOP=getLodop(document.getElementById('LODOP'),document.getElementById('LODOP_EM'));  

		LODOP.PRINT_INIT("<?php echo $title?>");
		LODOP.SET_PRINT_PAGESIZE(2,0,0,"");
		document.getElementById("searchbtn").style.display="none";
		LODOP.ADD_PRINT_TABLE("10%","10%","80%","100%",document.documentElement.innerHTML);
		document.getElementById("searchbtn").style.display="";
		LODOP.SET_PRINT_MODE("PRINT_PAGE_PERCENT","Auto-Width");
		
	};              
	

</script>

</body>

</html>