<?php
//print_r($_GET);
//print_r(base64_decode($_SERVER['QUERY_STRING']));exit;

ini_set('display_errors', 1);
ini_set('error_reporting', E_ALL);
error_reporting(E_WARNING | E_ERROR);
require_once('lib.inc.php');

$GLOBAL_SESSION=returnsession();
validateMenuPriv("��������");
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


switch ($_GET['ordername'])
{
	case 'name':
		$order = 'b.USER_NAME';break;
	case 'num':
		$order = 'count(0)';break;
	case 'huikuanjine':
		$order = 'sum(huikuanjine)';break;
	case 'fahuojine':
		$order = 'sum(fahuojine)';break;
	case 'kaipiaojine':
		$order = 'sum(kaipiaojine)';break;
	default:
		$order = 'sum(totalmoney)';
}

$show_type=$_GET['show_type'];
if($show_type == 'graph'){
	$type=$_GET['type'];
	if($type=='zhu' || $type == ''){
		$swf = 'Column3D.swf';
	}elseif ($type=='bing'){
		$swf = 'Pie2D.swf';
	}
}

if($_GET['doubletime'] == 2){
	$sc = 'asc';
	$order_img = '<img src="images/arrow_up.gif" border="0">';
}else{
	$sc = 'desc';
	$order_img = '<img src="images/arrow_down.gif" border="0">';
}

//print_r($_GET);
$sql="select a.qianyueren,`b`.`USER_NAME` AS `name`,sum(`a`.`totalmoney`) AS `totalmoney`,sum(`a`.`huikuanjine`) AS `huikuanjine`,sum(`a`.`fahuojine`) AS `fahuojine`,sum(`a`.`kaipiaojine`) AS `kaipiaojine`,count(0) AS `num`,b.UID from `sellplanmain` `a` left join `user` `b` on a.qianyueren=b.USER_ID  where a.user_flag>0 and a.createtime>='".$start_time."' and a.createtime<='".$end_time."'  group by `a`.`qianyueren` order by ".$order.' '.$sc;
//exit($sql);
$rs=$db->Execute($sql);
$rs_a = $rs->GetArray();

$head=array("name"=>"����Ա","num"=>"��������","totalmoney"=>"���׽��","huikuanjine"=>"�ؿ���","fahuojine"=>"�������","kaipiaojine"=>"��Ʊ���");
$headtype=array("name"=>"char","num"=>"int","totalmoney"=>"float","huikuanjine"=>"float","fahuojine"=>"float","kaipiaojine"=>"float");
$title="��������";
$sumcol=array("num"=>"","totalmoney"=>"","huikuanjine"=>"","fahuojine"=>"","kaipiaojine"=>"");
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
			<td nowrap='' colspan="13">
			<form action='' method="get">
			<table width="100%" class="Small" border="0">
				<thead>
					<tr>
						<td class='nowrap'>ʱ��Σ� <input class="SmallInput" size="19"
							name="start_time" value="<?php echo $start_time; ?>"
							onclick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'})"
							readonly=""> �� <input class="SmallInput" size="19"
							name="end_time" value="<?php echo $end_time; ?>"
							onclick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'})"
							readonly="">
							
							��ʾ��ʽ:
							<select class="SmallSelect" name="show_type">
							<option <?php echo ($show_type=='table')?'selected':''; ?> value="table">���</option>
							<option <?php echo ($show_type=='graph')?'selected':''; ?> value="graph">ͼ��</option>
							</select>	
						
							<?php if($show_type=='graph'){?>
							ͼ������:
							<select class="SmallSelect" name="type">
							<option <?php echo ($type=='zhu')?'selected':''; ?> value="zhu">��״ͼ</option>
							<option <?php echo ($type=='bing')?'selected':''; ?> value="bing">��״ͼ</option>
							</select>	
							<?php }?>
							
							 <input class="SmallButtonA" type="submit"
							accesskey="f" value="��ѯ" name="button" id="searchbtn"></td>
					</tr>
				</thead>
			</table>
			</form>
			</td>
		</tr>
		<tr>
			<td colspan="11" class="TableHeader">&nbsp;<?php echo $title?></td>
		</tr>
	</thead>

<?php if($show_type=='table' || empty($show_type)){?>
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
			switch($val){
				case "����Ա����":
					print "<a target='_blank' href='../Framework/user_newai.php?action=view_default&UID=".$row['UID']."'>".$row[$key]."</a>";
					break;

				case "��������":	
					print "<a target='_blank' href='daogoupaihangtongjimingxi.php?qianyueren=".$row['qianyueren']."&start_time=".$start_time."&end_time=".$end_time."'>".$row[$key]."</a>";
					break;	
				case "���׽��":	
					print "<a target='_blank' href='daogoupaihangtongjimingxi.php?qianyueren=".$row['qianyueren']."&start_time=".$start_time."&end_time=".$end_time."'>".number_format($row[$key],2,'.',',')."</a>";
					break;
				case "�ؿ���":
					print "<a target='_blank' href='daogoupaihangtongjimingxi.php?qianyueren=".$row['qianyueren']."&start_time=".$start_time."&end_time=".$end_time."&where=huikuan'>".number_format($row[$key],2,'.',',')."</a>";;
					break;
				case "�������":
					print "<a target='_blank' href='daogoupaihangtongjimingxi.php?qianyueren=".$row['qianyueren']."&start_time=".$start_time."&end_time=".$end_time."&where=fahuo'>".number_format($row[$key],2,'.',',')."</a>";;
					break;
				case "��Ʊ���":
					print "<a target='_blank' href='daogoupaihangtongjimingxi.php?qianyueren=".$row['qianyueren']."&start_time=".$start_time."&end_time=".$end_time."&where=kaipiao'>".number_format($row[$key],2,'.',',')."</a>";;
					break;
				
				default: 
					echo $row[$key];
				
			}
			
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
			print "<td>�ϼ� <b>".sizeof($rs_a)."</b> ����¼</td>";
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
<script language="javascript" type="text/javascript">   
    var LODOP; //����Ϊȫ�ֱ��� 
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
		LODOP.ADD_PRINT_TABLE("10%","10%","80%","80%",document.documentElement.innerHTML);
		document.getElementById("searchbtn").style.display="";
		LODOP.SET_PRINT_MODE("PRINT_PAGE_PERCENT","Auto-Width");
		
	};              
	

</script>
<form>
<p align="center"><input type="button" class="SmallButton" value=" ��ӡ "
	onclick="javascript:prn_print();">&nbsp;<input type="button"
	class="SmallButton" value="����"
	onclick="location='?out_excel=true&start_time=<?php echo $_GET[start_time];?>&end_time=<?php echo $_GET[end_time];?>&ordername=<?php echo $_GET['ordername']?>&doubletime=<?php  echo $_GET['doubletime'] ?>';"></p>
</form>
<?php 

}elseif($show_type=='graph'){

	$mingci = 20;  // ��ǰ������¼����ͳ��
	if(isset($rs_a[$mingci])){
		$sum = 0;
		$len =  count($rs_a);
		for ($i=$mingci;$i<$len;$i++){
			$sum+=$rs_a[$i][totalmoney];
			unset($rs_a[$i]);
		}
		$rs_a[$mingci]['name'] = '����';
		$rs_a[$mingci]['totalmoney'] = $sum;
	}
?>
</table>
</div>

<!-- START Script Block for Chart index -->
<script type="text/javascript"
	src="../../Framework/FusionCharts/FusionCharts.js"></script>
<div id="indexDiv" align="center">Chart.</div>
<script type="text/javascript"> 
//Instantiate the Chart 
var chart_index = new FusionCharts("../../Framework/FusionCharts/<?php echo $swf; ?>", "index", "100%", "550", "0", "0");
//chart_index.setTransparent("false");

//Provide entire XML data using dataXML method
chart_index.setDataXML("<graph bgcolor='e1f5ff' caption='����Ա����' subCaption='��ȷ����λ���������룩' numberPrefix='' formatNumberScale='1' decimalPrecision='2' baseFontSize='14' numberSuffix='��Ԫ'><?php foreach ($rs_a as $row){echo "<set name='".$row['name']."' value='".($row['totalmoney']/10000)."'/>";}?></graph>");
chart_index.render("indexDiv");
</script>
<!-- END Script Block for Chart index -->
<?php
}
?>

</body>

</html>
