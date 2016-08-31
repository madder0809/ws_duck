<?php
ini_set('display_errors', 1);
ini_set('error_reporting', E_ALL);
error_reporting(E_WARNING | E_ERROR);
require_once('lib.inc.php');
$GLOBAL_SESSION=returnsession();
global $db;
validateMenuPriv("客户所有者");
$sql="SELECT COUNT(*) count,b.USER_NAME name FROM customer a LEFT JOIN `user` b on b.USER_ID=a.sysuser GROUP BY sysuser order by count desc";
//exit($sql);
$rs=$db->Execute($sql);
$rs_a = $rs->GetArray();
//print_r($rs_a);exit;
?>
<html>
<head>
<?php page_css("订单生成出库单"); ?>
</head>
<body class=bodycolor topMargin=5>


<!-- START Script Block for Chart index -->
<script type="text/javascript"
	src="../../Framework/FusionCharts/FusionCharts.js"></script>
<div id="indexDiv" align="center">Chart.</div>
<script type="text/javascript"> 
//Instantiate the Chart 
var chart_index = new FusionCharts("../../Framework/FusionCharts/Column3D.swf", "index", "100%", "550", "0", "0");
//chart_index.setTransparent("false");

//Provide entire XML data using dataXML method
chart_index.setDataXML("<graph bgcolor='e1f5ff' caption='客户所有者统计' subCaption='' numberPrefix='' formatNumberScale='1' decimalPrecision='2' baseFontSize='14' numberSuffix='个'><?php foreach ($rs_a as $row){echo "<set name='".$row['name']."' value='".$row['count']."'/>";}?></graph>");
chart_index.render("indexDiv");
</script>
<!-- END Script Block for Chart index -->


</body>
</html>










