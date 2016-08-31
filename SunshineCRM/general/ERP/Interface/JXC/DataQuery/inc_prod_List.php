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
    //����window.XMLHttpRequest�����Ƿ����ʹ�ò�ͬ�Ĵ�����ʽ
    var xmlHttp;
    if (window.XMLHttpRequest) {
       xmlHttp = new XMLHttpRequest();                  //FireFox��Opera�������֧�ֵĴ�����ʽ
    } else {
       xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");//IE�����֧�ֵĴ�����ʽ
    }
    return xmlHttp;
}
//����������Ͳ�������
function sendRequest(params) {
	
	var xmlHttp=createXmlHttp();                        //����XmlHttpRequest����
    xmlHttp.onreadystatechange =function() {showCartInfo(xmlHttp)};   
    xmlHttp.open("GET", "inc_prod_detail_update.php?tablename=<?php echo $tablename?>&rowid=<?php echo $_GET['rowid']?>" + params, true);
    xmlHttp.send(null);
}
//����������Ӧ��Ϣд�빺�ﳵdiv��
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
//ˢ���б�
function refreshCart(rowid) {
    sendRequest("&rowid="+rowid);
}

//����б�
function emptyCart(rowid) {
	if(confirm('�Ƿ�ȷ�������ϸ��'))
    	sendRequest("&action=empty&rowid"+rowid);
}

//ɾ���б��ڵ�����Ʒ
function delProduct(id) {
	if(confirm('�Ƿ�ȷ��ɾ�����У�'))
    	sendRequest("&action=del&id=" + id);
}
//���沢����
function saveAndReturn(rowid)
{
	var boolflag=true;
	$("img", document.forms[0]).each(function()
	{	
		var imgsrc=this.src;
		if(imgsrc.indexOf('sepangray.gif')>-1)
		{
			alert('���в�Ʒδ������ɫ����');
			boolflag=false;
			return boolflag;
		}
		
	}); 
	if(boolflag)
	{
		var sbtn=document.getElementsByName('submit');
		for(i=0;i<sbtn.length;i++)
		{
			sbtn[i].value='�ύ��';
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
//����
function Returnback(rowid)
{
	parent.location=backurl;

}
//���±�ע
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
//���½��
function updateMoney(id, money)
{
	//��������У�����ʱ����
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
		alert("�������Ǹ�����");
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
//�����ۿ�
function updateZhekou(id, zhekou)
{
	//��������У�����ʱ����
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
		alert("�ۿ۲���С��0");
		return false;	
	}
	if(IsInteger(zhekou)==false)
	{
		alert("�ۿ۱���������");
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
//��������
function updateAmount(id, newamount)
{
	//��������У�����ʱ����
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
		alert("��������������");
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
//���µ���
function updatePrice(id, newprice)
{
	//��������У�����ʱ����
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
		alert("���۲���С��0");
		return false;	
	}
	if(IsFloat(newprice)==false)
	{
		alert("���۱����Ǹ�����");
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
//ajax���ؽ��
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
	   			allmoney.innerHTML=res[6]+" Ԫ";
	   			
	   			if(res[7]!='')
	   			{
		   			var tablename='<?php echo $tablename?>';
		   			
		   			if(res[7]>0 && (tablename=="sellplanmain_detail" || tablename=="v_sellonedetail"))
	   					warning.innerHTML="<img src='../../../Framework/images/warning.gif' title='�ۺ�۱ȳɱ��۵�"+res[7]+"Ԫ'>";
		   			else if(res[7]<0 && (tablename=="buyplanmain_detail"))
	   					warning.innerHTML="<img src='../../../Framework/images/warning.gif' title='�ۺ�۱ȳɱ��۸�"+(-res[7])+"Ԫ'>";
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
	
	ShowIframe('��ɫ����','../colorinput.php?tablename='+tablename+'&id='+id,600,200);
	document.getElementById('dialogBoxClose').src="../../../Enginee/popup/js/closewin.gif";
}
</script>

</head>
<?php
if($tablename=="customerproduct_detail")
{
	$customerid= returntablefield("customerproduct", "rowid", $_GET['rowid'], "�ͻ�");
	$customerInfo= returntablefield( "customer", "rowid", $customerid, "supplyname,state");
	$customerName=$customerInfo['supplyname'];
	$custState=$customerInfo['state'];
	$custprice=returntablefield("customerlever", "rowid",$custState,"relatePrice");
	if($custprice=='')
		$custprice='sellprice';
	$pricename=returntablefield("systemlang", "tablename","product","chinese","fieldname",$custprice);
	$customerName="�ͻ���".$customerName."�����ü۸�<font color=red>$pricename</font>";
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
	$customerName="�ͻ���".$customerName."�����ü۸�<font color=red>$pricename</font>";
}
else if($tablename=="buyplanmain_detail")
{
	
	$buyplaninfo= returntablefield("buyplanmain", "billid", $_GET['rowid'], "supplyid,totalmoney");
	$customerid=$buyplaninfo['supplyid'];
	$totalmoney=$buyplaninfo['totalmoney'];
	$customerName= returntablefield( "supply", "rowid", $customerid, "supplyname");
	$customerName="��Ӧ�̣�".$customerName;
}
else if($tablename=="stockchangemain_detail")
{
	$storeid= returntablefield("stockchangemain", "billid", $_GET['rowid'], "outstoreid");	
	$customerName= returntablefield( "stock", "rowid", $storeid, "name");
	$customerName="�����ֿ⣺".$customerName;
}
else if($tablename=="storecheck_detail")
{
	$storeid= returntablefield("storecheck", "billid", $_GET['rowid'], "storeid");	
	$customerName= returntablefield( "stock", "rowid", $storeid, "name");
	$customerName="�̵�ֿ⣺".$customerName;
}
else if($tablename=="productzuzhuang_detail")
{
	$storeid= returntablefield("productzuzhuang", "billid", $_GET['rowid'], "outstoreid");	
	$customerName= returntablefield( "stock", "rowid", $storeid, "name");
	$customerName="����ֿ⣺".$customerName;
}
else if($tablename=="productzuzhuang2_detail")
{
	$storeid= returntablefield("productzuzhuang", "billid", $_GET['rowid'], "instoreid");	
	$customerName= returntablefield( "stock", "rowid", $storeid, "name");
	$totalmoney= returntablefield("productzuzhuang", "billid", $_GET['rowid'], "totalmoney");
	$customerName="���ֿ⣺".$customerName."�������ܽ��Ϊ��<font color=red>".$totalmoney."</font>Ԫ";
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
	$customerName="�ͻ���".$customerName."���ֿ⣺".$storeName."�����ü۸�<font color=red>$pricename</font>";
}
$rowid=$_GET['rowid'];
?>
<body class=bodycolor topMargin=5 onload="refreshCart(<?php echo $rowid?>)" >
<table id=listtable align=center class=TableBlock width=100% border=0>
<TR><TD colspan=9 class=TableHeader height=30>&nbsp;<?php echo $deelname?>&nbsp;��<?php echo $customerName; if($tablename=="buyplanmain_detail") echo "���ɹ��ܶ� <font color=red>".$totalmoney."</font> Ԫ";?>��</TD></TR>
</table>
<div id="shoppingcart">
</div>
<p align=center>
<?php if($tablename=="buyplanmain_detail"){?>
<input type=button value="����" class="SmallButton" onclick="parent.location=backurl;">
&nbsp;&nbsp;<?php }?>

<input type=button value="����б�" class="SmallButton" onclick="emptyCart(<?php echo $rowid?>)">
&nbsp;&nbsp;<input type=button name='submit' value=" �� �� " id="savebutton" title="��ݼ�:ALT+s" accesskey="s" class="SmallButton" onclick="saveAndReturn(<?php echo $rowid?>)">

</body>
</html>
