<?php
ini_set('display_errors',1);
ini_set('error_reporting',E_ALL);
error_reporting(E_WARNING | E_ERROR);


require_once("lib.inc.php");
$GLOBAL_SESSION=returnsession();
$deelname=$_GET['deelname'];
$tablename=$_GET['tablename'];
?>
<style type="text/css" media="all" rel="stylesheet">

  <!--
 

  #productSel {
    width:400px;
    position:absolute;
    left:600px;
    top:100px;
    background:#EFEFFF;
    text-align:left;

  }
  #ChatHead {
	text-align:right;
    font-size:14px;
    cursor:move;
  	background:url("<?php echo ROOT_DIR?>theme/3/list_hd_bg.png");
    border:1px #b8d1e2 solid;
    font-weight:bold;
    color:#476074;
    line-height:23px;
    padding:0px;
  }
  #ChatHead a:link,#ChatHead a:visited, {
    font-size:14px;
    font-weight:bold;
    padding:3px;
  }
  #ChatBody {
    border:1px solid #003399;
    border-top:none;
    padding:2px;
  	filter:Alpha(opacity=80)
  }
  #ChatContent {
    height:200px;
    padding:6px;
    overflow-y:scroll;
    word-break: break-all
  }
  #ChatBtn {
    border-top:1px solid #003399;
    padding:2px
  }
  -->
  </style>
<LINK href="<?php echo ROOT_DIR?>theme/3/style.css" type=text/css rel=stylesheet>
<script type="text/javascript" language="javascript" src="<?php echo ROOT_DIR?>general/ERP/Enginee/lib/common.js"></script>
<script type="text/javascript" language="javascript" src="<?php echo ROOT_DIR?>general/ERP/Enginee/popup/js/popup.js"></script>
<script type="text/javascript" language="javascript" src="<?php echo ROOT_DIR?>general/ERP/Enginee/popup/js/popupclass.js"></script>
<script type="text/javascript" language="javascript" src="<?php echo ROOT_DIR?>general/ERP/Enginee/jquery/jquery.js"></script>
<script type="text/javascript">
var backurl="";
<?php
		if($tablename=="customerproduct_detail")
			print "backurl='../customerproduct_newai.php';";
		else if($tablename=="sellplanmain_detail")
			print "backurl='../sellplanmain_newai.php';";
		else if($tablename=="buyplanmain_detail")
			print "backurl='../buyplanmain_newai.php';";
		else if($tablename=="stockinnmain_detail")
			print "backurl='../stockinmain_newai.php';";
		else if($tablename=="stockchangemain_detail")
			print "backurl='../stockchangemain_newai.php';";
		else if($tablename=="storecheck_detail")
			print "backurl='../storecheck_newai.php';";
		else if($tablename=="productzuzhuang_detail" || $tablename=="productzuzhuang2_detail" )
			print "backurl='../productzuzhuang_newai.php';";
		else if($tablename=='v_sellonedetail')
			print "backurl='../sellonemain_newai.php';";
		
		
?>

function createXmlHttp() {
    //根据window.XMLHttpRequest对象是否存在使用不同的创建方式
    var xmlHttp;
    if (window.XMLHttpRequest) {
       xmlHttp = new XMLHttpRequest();                  //FireFox、Opera等浏览器支持的创建方式
    } else {
       xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");//IE浏览器支持的创建方式
    }
    return xmlHttp;
}
//向服务器发送操作请求
function sendRequest(params) {
	
	var xmlHttp=createXmlHttp();                        //创建XmlHttpRequest对象
    xmlHttp.onreadystatechange =function() {showCartInfo(xmlHttp)};   
    xmlHttp.open("GET", "inc_prod_detail_update.php?tablename=<?php echo $tablename?>&rowid=<?php echo $_GET['rowid']?>" + params, true);
    xmlHttp.send(null);
}
//将服务器响应信息写入购物车div中
function showCartInfo(xmlHttp) {
    if (xmlHttp.readyState == 4) {
        var res=xmlHttp.responseText;
        if(res.indexOf("<table")!=-1)
        {
        	shoppingcart.innerHTML = xmlHttp.responseText;
        	//document.getElementById("savebutton").focus();
        	scrollBy(0,999999);
        }
        else
        {
            alert(xmlHttp.responseText);
            location.reload();
        }
            
    }
}
//刷新列表
function refreshCart(rowid) {
    sendRequest("&rowid="+rowid);
}

//清空列表
function emptyCart(rowid) {
	if(confirm('是否确认清空明细？'))
    	sendRequest("&action=empty&rowid"+rowid);
}

//删除列表内单件产品
function delProduct(id) {
	if(confirm('是否确认删除本行？'))
    	sendRequest("&action=del&id=" + id);
}
//保存并返回
function saveAndReturn(rowid)
{
	var boolflag=true;
	$("img", document.forms[0]).each(function()
	{	
		var imgsrc=this.src;
		if(imgsrc.indexOf('sepangray.gif')>-1)
		{
			alert('还有产品未进行颜色分配');
			boolflag=false;
			return boolflag;
		}
		
	}); 
	if(boolflag)
	{
		var sbtn=document.getElementsByName('submit');
		for(i=0;i<sbtn.length;i++)
		{
			sbtn[i].value='提交中';
			sbtn[i].disabled=true;
		}
	
		var xmlHttp=createXmlHttp();    
		xmlHttp.onreadystatechange = function() {submitPostCallBack(xmlHttp)};   
		var url="inc_prod_detail_update.php?tablename=<?php echo $tablename?>&action=Save"+
						 "&rowid="+rowid;
		
		xmlHttp.open("GET", url, true);   
		xmlHttp.send();
	}

}
//返回
function Returnback(rowid)
{
	parent.location=backurl;

}
//更新备注
function updateMemo(id, memo)
{
	var xmlHttp=createXmlHttp();    
	xmlHttp.onreadystatechange = function() {submitPostCallBack(xmlHttp)};   
	var url="inc_prod_detail_update.php?tablename=<?php echo $tablename?>&action=updateMemo"+
					 "&id="+id+
	                 "&beizhu=" + memo;
	xmlHttp.open("GET", url, true);   
	xmlHttp.send();
}
//更新金额
function updateMoney(id, money)
{
	//用于数据校验的临时对象
	try
	{
		money=eval(money);
	}
	catch(err)
	{
		alert(err.description);
		return false;
	}
	
	if(IsFloat(money)==false)
	{
		alert("金额必须是浮点数");
		return false;
	}
	else
	{
		var xmlHttp=createXmlHttp();    
    	xmlHttp.onreadystatechange = function() {submitPostCallBack(xmlHttp)};   
		var url="inc_prod_detail_update.php?tablename=<?php echo $tablename?>&action=updateMoney"+
    					 "&id="+id+
    	                 "&jine=" + money;
		xmlHttp.open("GET", url, true);   
		xmlHttp.send();
	}
}
//更新折扣
function updateZhekou(id, zhekou)
{
	//用于数据校验的临时对象
	try
	{
		zhekou=eval(zhekou);
	}
	catch(err)
	{
		alert(err.description);
		return false;
	}
	
	if(Number(zhekou)<0)
	{
		alert("折扣不能小于0");
		return false;	
	}
	if(IsInteger(zhekou)==false)
	{
		alert("折扣必须是整数");
		return false;
	}
	else
	{
		var xmlHttp=createXmlHttp();    
    	xmlHttp.onreadystatechange = function() {submitPostCallBack(xmlHttp)};   
		var url="inc_prod_detail_update.php?tablename=<?php echo $tablename?>&action=updateZhekou"+
    					 "&id="+id+
    	                 "&zhekou=" + zhekou;
		xmlHttp.open("GET", url, true);   
		xmlHttp.send();
	}
}
//更新数量
function updateAmount(id, newamount)
{
	//用于数据校验的临时对象
	try
	{
		newamount=eval(newamount);
	}
	catch(err)
	{
		alert(err.description);
		return false;
	}
	
	if(IsInteger(newamount)==false)
	{
		alert("数量必须是整数");
		return false;
	}
	else
	{
		var xmlHttp=createXmlHttp();    
    	xmlHttp.onreadystatechange = function() {submitPostCallBack(xmlHttp)};   
		var url="inc_prod_detail_update.php?tablename=<?php echo $tablename?>&action=updateAmount"+
    					 "&id="+id+
    	                 "&amount=" + newamount;
		xmlHttp.open("GET", url, true);   
		xmlHttp.send();
	}
}
//更新单价
function updatePrice(id, newprice)
{
	//用于数据校验的临时对象
	try
	{
		newprice=eval(newprice);
	}
	catch(err)
	{
		alert(err.description);
		return false;
	}
	if(Number(newprice)<0)
	{
		alert("单价不能小于0");
		return false;	
	}
	if(IsFloat(newprice)==false)
	{
		alert("单价必须是浮点数");
		return false;
	}
	else
	{
		var xmlHttp=createXmlHttp();    
    	xmlHttp.onreadystatechange = function() {submitPostCallBack(xmlHttp)};   
		var url="inc_prod_detail_update.php?tablename=<?php echo $tablename?>&action=updatePrice"+
    					 "&id="+id+
    	                 "&price=" + newprice;
		xmlHttp.open("GET", url, true);   
		xmlHttp.send();
	}
}
//ajax返回结果
function submitPostCallBack(xmlHttp) 
{
	
    if (xmlHttp.readyState == 4) {
        if (xmlHttp.status == 200) 
    	{
        	
    		var res=xmlHttp.responseText.split("|");
    		if(res[0]=='updateAmount' || res[0]=='updatePrice' || res[0]=='updateMoney' || res[0]=='updateZhekou')
    		{
	   			var amount=document.getElementById('num_'+res[1]);
	   			var price=document.getElementById('price_'+res[1]);
	   			var money=document.getElementById('jine_'+res[1]);
	   			var zhekou=document.getElementById('zhekou_'+res[1]);
	   			var warning=document.getElementById('warning_'+res[1]);
	   			amount.value=res[2];
	   			price.value=res[3];
	   			zhekou.value=eval(res[4])*100;
	   			money.value=Math.round(eval(res[2]*res[3]*res[4])*100)/100;
	   			var allamount=document.getElementById('allamount');
	   			var allmoney=document.getElementById('allmoney');
	   			allamount.innerHTML=res[5];
	   			allmoney.innerHTML=res[6]+" 元";
	   			
	   			if(res[7]!='')
	   			{
		   			var tablename='<?php echo $tablename?>';
		   			
		   			if(res[7]>0 && (tablename=="sellplanmain_detail" || tablename=="v_sellonedetail"))
	   					warning.innerHTML="<img src='../../../Framework/images/warning.gif' title='折后价比成本价低"+res[7]+"元'>";
		   			else if(res[7]<0 && (tablename=="buyplanmain_detail"))
	   					warning.innerHTML="<img src='../../../Framework/images/warning.gif' title='折后价比成本价高"+(-res[7])+"元'>";
		   			else
		   				warning.innerHTML="";
	   			}
	   			else
	   				warning.innerHTML="";
	   			if(res[0]=='updateAmount')
	   			{	
	   				autoFocusNextInput(amount,"form2");
	   			}
	   			else if (res[0]=='updatePrice')
	   			{
	   				autoFocusNextInput(price,"form2");
	   			}
	   			else if (res[0]=='updateZhekou')
	   			{
	   				autoFocusNextInput(zhekou,"form2");
	   			}
	   			else if (res[0]=='updateMoney')
	   			{
	   				autoFocusNextInput(money,"form2");
	   			}
	   			
	   		}	
    		else if(res[0]=='updateMemo')
    		{
    			//var amount=document.getElementById('beizhu_'+res[1]);
    			//autoFocusNextInput(amount,"form2");
    		}
    		else if(res[0]=='Save')
    		{
    			parent.location=backurl;
    		}
    		else
    		{
        		alert(xmlHttp.responseText);
        		window.location.reload();
        	}
        }
    }
}
function PopColorInput(id,tablename)
{
	
	ShowIframe('颜色分配','../colorinput.php?tablename='+tablename+'&id='+id,600,200);
	document.getElementById('dialogBoxClose').src="../../../Enginee/popup/js/closewin.gif";
}
</script>

</head>
<?php
if($tablename=="customerproduct_detail")
{
	$customerid= returntablefield("customerproduct", "rowid", $_GET['rowid'], "客户");
	$customerInfo= returntablefield( "customer", "rowid", $customerid, "supplyname,state");
	$customerName=$customerInfo['supplyname'];
	$custState=$customerInfo['state'];
	$custprice=returntablefield("customerlever", "rowid",$custState,"relatePrice");
	if($custprice=='')
		$custprice='sellprice';
	$pricename=returntablefield("systemlang", "tablename","product","chinese","fieldname",$custprice);
	$customerName="客户：".$customerName."，适用价格：<font color=red>$pricename</font>";
}
else if($tablename=="sellplanmain_detail" || $tablename=="customerproduct_detail")
{

	$customerid= returntablefield("sellplanmain", "billid", $_GET['rowid'], "supplyid");	
	$customerInfo= returntablefield( "customer", "rowid", $customerid, "supplyname,state");
	$customerName=$customerInfo['supplyname'];
	$custState=$customerInfo['state'];
	$custprice=returntablefield("customerlever", "rowid",$custState,"relatePrice");
	if($custprice=='')
		$custprice='sellprice';
	$pricename=returntablefield("systemlang", "tablename","product","chinese","fieldname",$custprice);
	$customerName="客户：".$customerName."，适用价格：<font color=red>$pricename</font>";
}
else if($tablename=="buyplanmain_detail")
{
	
	$buyplaninfo= returntablefield("buyplanmain", "billid", $_GET['rowid'], "supplyid,totalmoney");
	$customerid=$buyplaninfo['supplyid'];
	$totalmoney=$buyplaninfo['totalmoney'];
	$customerName= returntablefield( "supply", "rowid", $customerid, "supplyname");
	$customerName="供应商：".$customerName;
}
else if($tablename=="stockchangemain_detail")
{
	$storeid= returntablefield("stockchangemain", "billid", $_GET['rowid'], "outstoreid");	
	$customerName= returntablefield( "stock", "rowid", $storeid, "name");
	$customerName="调出仓库：".$customerName;
}
else if($tablename=="storecheck_detail")
{
	$storeid= returntablefield("storecheck", "billid", $_GET['rowid'], "storeid");	
	$customerName= returntablefield( "stock", "rowid", $storeid, "name");
	$customerName="盘点仓库：".$customerName;
}
else if($tablename=="productzuzhuang_detail")
{
	$storeid= returntablefield("productzuzhuang", "billid", $_GET['rowid'], "outstoreid");	
	$customerName= returntablefield( "stock", "rowid", $storeid, "name");
	$customerName="出库仓库：".$customerName;
}
else if($tablename=="productzuzhuang2_detail")
{
	$storeid= returntablefield("productzuzhuang", "billid", $_GET['rowid'], "instoreid");	
	$customerName= returntablefield( "stock", "rowid", $storeid, "name");
	$totalmoney= returntablefield("productzuzhuang", "billid", $_GET['rowid'], "totalmoney");
	$customerName="入库仓库：".$customerName."，本单总金额为：<font color=red>".$totalmoney."</font>元";
}
else if($tablename=='v_sellonedetail')
{
	$customerid= returntablefield("sellplanmain", "billid", $_GET['rowid'], "supplyid");
	$customerInfo= returntablefield( "customer", "rowid", $customerid, "supplyname,state");
	$customerName=$customerInfo['supplyname'];
	$custState=$customerInfo['state'];
	$storeid= returntablefield("sellplanmain", "billid", $_GET['rowid'], "storeid");	
	$storeName= returntablefield( "stock", "rowid", $storeid, "name");
	$custprice=returntablefield("customerlever", "rowid",$custState,"relatePrice");
	if($custprice=='')
		$custprice='sellprice';
	$pricename=returntablefield("systemlang", "tablename","product","chinese","fieldname",$custprice);
	$customerName="客户：".$customerName."，仓库：".$storeName."，适用价格：<font color=red>$pricename</font>";
}
$rowid=$_GET['rowid'];
?>
<body class=bodycolor topMargin=5 onload="refreshCart(<?php echo $rowid?>)" >
<table id=listtable align=center class=TableBlock width=100% border=0>
<TR><TD colspan=9 class=TableHeader height=30>&nbsp;<?php echo $deelname?>&nbsp;（<?php echo $customerName; if($tablename=="buyplanmain_detail") echo "，采购总额 <font color=red>".$totalmoney."</font> 元";?>）</TD></TR>
</table>
<div id="shoppingcart">
</div>
<p align=center>
<?php if($tablename=="buyplanmain_detail"){?>
<input type=button value="返回" class="SmallButton" onclick="parent.location=backurl;">
&nbsp;&nbsp;<?php }?>

<input type=button value="清空列表" class="SmallButton" onclick="emptyCart(<?php echo $rowid?>)">
&nbsp;&nbsp;<input type=button name='submit' value=" 保 存 " id="savebutton" title="快捷键:ALT+s" accesskey="s" class="SmallButton" onclick="saveAndReturn(<?php echo $rowid?>)">

</body>
</html>
