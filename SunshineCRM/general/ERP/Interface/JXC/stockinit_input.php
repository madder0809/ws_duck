<?php
ini_set('display_errors',1);
ini_set('error_reporting',E_ALL);
error_reporting(E_WARNING | E_ERROR);
require_once("lib.inc.php");
$GLOBAL_SESSION=returnsession();
?>
<html>
<head>
<LINK href="<?php echo ROOT_DIR?>theme/3/style.css" type=text/css rel=stylesheet>
<script type="text/javascript" language="javascript" src="<?php echo ROOT_DIR?>general/ERP/Enginee/lib/common.js"></script>
<script type="text/javascript">


function submitFormCheck() 
{
	//用于数据校验的临时对象
	for(var i=0;i<form1.elements.length;i++)
	{
		if(form1.elements[i].type=="text" && form1.elements[i].name.substring(0,4)=="num_")
		{
			var newamount=form1.elements[i].value;
			try
			{
				newamount=eval(newamount);
			}
			catch(err)
			{
				alert(err.description);
				return false;
			}
			<?php if($_SESSION['numzero']==0){?>
			if(form1.elements[i].value!='' && IsInteger(newamount)==false)
			{
				alert(form1.elements[i].value+"，数量必须是整数");
				return false;
			}
			<?php }else{?>
			if(IsFloat(newamount)==false)
			{
				alert("数量必须是浮点数");
				return false;
			}
			<?php }?>
			if(newamount!='' && newamount<0)
			{
				alert("数量必须大于等于0");
				return false;
			}
				
		}
		if(form1.elements[i].type=="text" && form1.elements[i].name.substring(0,4)=="price_")
		{
			var newamount=form1.elements[i].value;
			try
			{
				newamount=eval(newamount);
			}
			catch(err)
			{
				alert(err.description);
				return false;
			}
		
			if(IsFloat(newamount)==false)
			{
				alert("金额必须是浮点数");
				return false;
			}
			if(newamount<0)
			{
				alert("金额必须大于等于0");
				return false;
			}
		}
	}
	var allnum=document.getElementById("allamount").innerText;
	if(eval(allnum)==0)
	{
		alert("总数量必须大于0");
		return false;
	}	
}
function CountRecJine(id,obj)
{
	
	var num=form1.elements("num_"+id).value;
	var price=form1.elements("price_"+id).value;
	var jine=form1.elements("jine_"+id).value;
	
	if(obj.name=="num_"+id || obj.name=="price_"+id)
	{
		jine=Math.round(price*num*100)/100;
		form1.elements("jine_"+id).value=jine;
	}
	else
	{
		if(num!=0)
		{
			price=Math.round(jine/num*100)/100;
			form1.elements("price_"+id).value=price;
			jine=Math.round(price*num*100)/100;
			form1.elements("jine_"+id).value=jine;
		}
	}
	CountAllJine();
}
function CountAllJine()
{
	var allnum=0;
	var allmoney=0;
	for(var i=0;i<form1.elements.length;i++)
	{
		if(form1.elements[i].name.substring(0,4)=="num_")
		{
			allnum=eval(allnum+Number(form1.elements[i].value));
		}
		if(form1.elements[i].name.substring(0,5)=="jine_")
		{
			allmoney=eval(allmoney+Number(form1.elements[i].value));
		}
	}
	allmoney=Math.round(allmoney*100)/100;
	document.getElementById("allamount").innerText=allnum;
	document.getElementById("allmoney").innerText=allmoney;

}
</script>
</head>
<?php 
	global $storeid;
	global $db;
	$storename= returntablefield( "stock", "rowid", $storeid, "name");
	
?>
<body class=bodycolor topMargin=5>
<table id=listtable align=center class=TableBlock width=100% border=0>
<TR><TD colspan=9 class=TableHeader height=30>&nbsp;库存初始化&nbsp;（仓库：<?php echo $storename?>）</TD></TR>
</table>
<div id="shoppingcart">
<form name="form1" method="post" action="stockinit_newai.php?action=save&storeid=<?php echo $storeid?>" onsubmit="return submitFormCheck();">
<table align=center class=TableBlock width=100% border=0 id="table1">
<tr >
	<td align=center class=TableHeader>产品编号</td>
    <td align=center class=TableHeader>产品名称</td>
    <td align=center class=TableHeader>规格</td>
    <td align=center class=TableHeader>型号</td>
    <td align=center class=TableHeader>单位</td>
    <td align=center class=TableHeader>类别</td>
    <td align=center class=TableHeader>价格</td>
    <td align=center class=TableHeader>数量</td>
    <td align=center class=TableHeader>金额</td>
    <td align=center class=TableHeader>备注</td>
</tr>

<?php 
	$sql="select * from store_init where storeid=$storeid and flag=0";
	$rs = $db->Execute($sql);
	$rs_detail = $rs->GetArray();
	if(count($rs_detail)==0)
	{
		$sql = "select a.*,b.name as typename from product a inner join producttype b on a.producttype=b.rowid where ifkucun='是'";
		$rs = $db->Execute($sql);
		$rs_a = $rs->GetArray();
   	 	for($i=0;$i<count($rs_a);$i++)
   	 	{
   	 		$sql="select max(id) as maxid from store_init";
   	 		$rs=$db->Execute($sql);
   	 		$rs_b=$rs->GetArray();
   	 		$maxid=$rs_b[0]['maxid']+1;
   	 		$sql="insert into store_init (id,prodid,storeid,prodname,guige,xinghao,danwei,typename) values('$maxid','".$rs_a[$i]['productid'].
   	 		"',$storeid,'".$rs_a[$i]['productname']."','".$rs_a[$i]['standard']."','".$rs_a[$i]['mode']."','".$rs_a[$i]['measureid']."','".$rs_a[$i]['typename']."')";
   	 		$db->Execute($sql);
   	 	} 
	}
	$sql = "select * from store_init where storeid=$storeid and flag=0";
	$rs = $db->Execute($sql);
	$rs_a = $rs->GetArray();
    if (count($rs_a) != 0) 
    {
    	$class="";
        for($i=0;$i<count($rs_a);$i++)
        {
        	
        	if($i%2==1)
        		$class="TableLine1";
        	else
        		$class="TableLine2";
        	
?>
            <tr class=<?php echo $class?>>
            	<td><?php echo $rs_a[$i]['prodid']?></td>
                <td><?php echo $rs_a[$i]['prodname']?></td>
                <td align="center"><?php echo $rs_a[$i]['guige']?></td>
                <td align="center"><?php echo $rs_a[$i]['xinghao']?></td>
                <td align="center"><?php echo $rs_a[$i]['danwei']?></td>
                <td align="center"><?php echo $rs_a[$i]['typename']?></td>
                <td align="center" ><input class="SmallInput" size=10 name="price_<?php echo $rs_a[$i]['id']?>" onkeydown="focusNext(event)" onKeyPress="return inputFloat(event)" value="" onchange="CountRecJine(<?php echo $rs_a[$i]['id']?>,this)"></td>
                <td align="center" ><input class="SmallInput" size=10 name="num_<?php echo $rs_a[$i]['id']?>" onkeydown="focusNext(event)" onKeyPress="return inputFloat(event)" value="" onchange="CountRecJine(<?php echo $rs_a[$i]['id']?>,this)"></td>
                <td align="right"> <Input class="SmallInput" size=10 name="jine_<?php echo $rs_a[$i]['id']?>" onkeydown="focusNext(event)" onKeyPress="return inputFloat(event)" value="" onchange="CountRecJine(<?php echo $rs_a[$i]['id']?>,this)"></td>
                <td align="center"><input class="SmallInput" size=15 name="beizhu_<?php echo $rs_a[$i]['id']?>" onkeydown="focusNext(event)"></td>
               
            </tr>
            <?php 
        }
        ?>
        <tr class=TableHeader >
             <td align=center>总计</td>
             <td></td><td></td><td></td><td></td><td></td><td></td>
             <td align="right"><div id="allamount"></div></td>
             <td align="right"><div id="allmoney"></div></td>
             <td align="right"></td>
             <td></td>
        <?php
    } else {
        ?>
        <tr>
            <td colspan="9" style="height:50px" align="center">请先在“商品维护”菜单中完善产品库</td>
        </tr>
        <?php
    }
?>

</table>
</div>
<p align=center><input type=submit value=" 保存 " class="SmallButton" >
&nbsp;&nbsp;<input type=button value=" 返回 " class="SmallButton" onclick="location='stockinit_newai.php';"></p>
</form>
</body>
</html>