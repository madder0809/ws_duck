<?php
/*
版权归属:郑州单点科技软件有限公司;
联系方式:0371-69663266;
公司地址:河南郑州经济技术开发区第五大街经北三路通信产业园四楼西南;
公司简介:郑州单点科技软件有限公司位于中国中部城市-郑州,成立于2007年1月,致力于把基于先进信息技术（包括通信技术）的最佳管理与业务实践普及到教育行业客户的管理与业务创新活动中，全面提供具有自主知识产权的教育管理软件、服务与解决方案，是中部最优秀的高校教育管理软件及中小学校管理软件提供商。目前己经有多家高职和中职类院校使用通达中部研发中心开发的软件和服务;

软件名称:单点科技软件开发基础性架构平台,以及在其基础之上扩展的任何性软件作品;
发行协议:数字化校园产品为商业软件,发行许可为LICENSE方式;单点CRM系统即SunshineCRM系统为GPLV3协议许可,GPLV3协议许可内容请到百度搜索;
特殊声明:软件所使用的ADODB库,PHPEXCEL库,SMTARY库归原作者所有,余下代码沿用上述声明;
*/

function CustomerLinkmanChancePriv_add($fields, $i )
{
	
global $db,$_SESSION,$common_html;
$notnull=trim($fields['null'][$i]['inputtype']);
$notnull=='notnull'?$notnulltext=$common_html['common_html']['mustinput']:$notnulltext='';
$fieldname1=$fields['name'][$i];
$fieldname2=$fields['input'][$i][0];
$fieldname3=$fields['input'][$i][1];

$j = array_search($fieldname2,$fields['name'],true);
$notnull=trim($fields['null'][$j]['inputtype']);
$notnull=='notnull'?$notnulltext1=$common_html['common_html']['mustinput']:$notnulltext1='';


$n = array_search($fieldname3,$fields['name'],true);
$notnull=trim($fields['null'][$n]['inputtype']);
$notnull=='notnull'?$notnulltext2=$common_html['common_html']['mustinput']:$notnulltext2='';

$class = "SmallSelect";


print "<script type=\"text/javascript\" language=\"javascript\" src=\"".ROOT_DIR."general/ERP/Enginee/jquery/jquery.js\"></script>";
print "<script language=javascript>
var $$ = jQuery.noConflict();
var subcat=new Array();
function sendRequest(action,params) {

    $$.ajax({ 
		  type:'GET', 
		  url:'getLinkmanBycustomer.php?action='+action+'&' + params, 
		  dataType: 'xml', 
		  cache:false,
		  success:function(data) 
		  { 
		
		  		var yuchuzhi=$$(data).find('chuzhi').text();
		  		$$('#yuchuzhi').html(yuchuzhi+' 元');";
				
				if($notnulltext1=='')
   		  			print "document.form1.$fieldname2.options[document.form1.$fieldname2.length] = new Option('','');";
   		  		print "
   		  		var i=0;
	   		  	$$(data).find('linkman').each(function() {
                   
						var rowid=$$(this).find('ROWID').text();
						var name=$$(this).find('linkmanname').text();
						var mobile='';
						if($$(this).find('mobile').text()!=null)
							mobile=$$(this).find('mobile').text();
						 
						var address='';
						if($$(this).find('address').text()!=null)
							address=$$(this).find('address').text();
						subcat[i]=new Array();
						subcat[i][0]=rowid;
						subcat[i][1]=name;
						subcat[i][2]=mobile;
						subcat[i][3]=address;
						 
						document.form1.$fieldname2.options[document.form1.$fieldname2.length] = new Option(name, rowid);
						if(rowid=='".$fields['value'][$fieldname2]."')
							document.form1.$fieldname2.options[document.form1.$fieldname2.length-1].selected=true;
						i++;
                    });	";
   		  		if($notnulltext2=='')
   		  			print "document.form1.$fieldname3.options[document.form1.$fieldname3.length] = new Option('','');";
   		  		print "		
   		  		$$(data).find('chance').each(function() {
                   
						var rowid=$$(this).find('id').text();
						var zhuti=$$(this).find('zhuti').text();
						
						document.form1.$fieldname3.options[document.form1.$fieldname3.length] = new Option(zhuti, rowid);
						if(rowid=='".$fields['value'][$fieldname3]."')
							document.form1.$fieldname3.options[document.form1.$fieldname3.length-1].selected=true;
                    });		
		  },
		  error:function(XmlHttpRequest,textStatus, errorThrown)
	      {
			  var errorPage = XmlHttpRequest.responseText;  
			  alert('获取联系人出错：'+errorThrown);
	      }
	});
}

function changelocation(locationid)
{
	
	document.form1.$fieldname2.length = 0;
	document.form1.$fieldname3.length = 0;
	if(locationid!='')
    	sendRequest('linkmanchance','customerid='+locationid);
}
function setAddressMobile(linkmanid)
{
	var address=document.getElementById('address');
	var mobile=document.getElementById('mobile');
	var i;
	for(i=0;i<subcat.length;i++)
	{
		if(subcat[i][0]==linkmanid)
			break;
	}
	if(address!=null)
	{
		address.value=subcat[i][3];
	}
	if(mobile!=null)
	{
		mobile.value=subcat[i][2];
	}
}
";
if($fields['value'][$fieldname1]!='')
{
	print "window.onload=function(){
	changelocation(".$fields['value'][$fieldname1].");
	}
	";
}
print "
</SCRIPT>\n";
print "<TR><TD class=TableData noWrap>客户:</TD><TD class=TableData noWrap>\n";
$customername=returntablefield("customer", "rowid", $fields['value'][$fieldname1], "supplyname");
print "<input type='hidden' name='$fieldname1' value='".$fields['value'][$fieldname1]."' onchange=\"changelocation(this.value)\">";
print "<input type='text' name='".$fieldname1."_ID' value='".$customername."' readonly class='SmallStatic' size='25'>";
print "&nbsp;<input type=\"button\" title='' value=\"选择\" class=\"SmallButton\" onClick=\"SelectAllInforSingle('../../Enginee/Module/kehu_select_single/index.php','','".$fieldname1."_ID', '$fieldname1');\" >&nbsp;";
print "</SELECT>&nbsp;$notnulltext</TD></TR>\n";
print "<TR><TD class=TableData noWrap>当前预储值:</TD><TD class=TableData noWrap><div id='yuchuzhi'></div></TD></TR>\n";


print "<TR><TD class=TableData noWrap>客户联系人:</TD><TD class=TableData noWrap>\n";
print "<SELECT name='$fieldname2' class=\"$class\"  onkeydown=\"if(event.keyCode==13)event.keyCode=9\" onchange=setAddressMobile(this.value);>\n";

print "</SELECT>&nbsp;$notnulltext1</TD></TR>\n";


print "<TR><TD class=TableData noWrap>销售机会:</TD><TD class=TableData noWrap>\n";

print "<SELECT id='$fieldname3' name='$fieldname3' onkeydown=\"if(event.keyCode==13)event.keyCode=9\" class=\"$class\"> \n";
print "</SELECT>&nbsp;$notnulltext2</TD></TR>\n";

}


function CustomerLinkmanChancePriv_value_PRIV( $fieldvalue, $fields, $i )
{

}

?>
