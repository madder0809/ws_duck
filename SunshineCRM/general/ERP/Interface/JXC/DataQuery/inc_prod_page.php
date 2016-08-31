<?php
header("Content-type:text/html;charset=gb2312");
ini_set('display_errors', 1);
ini_set('error_reporting', E_ALL);

// display warnings and errors
error_reporting(E_WARNING | E_ERROR);

require_once("lib.inc.php");
?>
<html>
<head>
<link rel="stylesheet" href="<?php echo ROOT_DIR?>theme/3/style.css" type="text/css">
<script language=javascript>
var xmlHttp;    //用于保存XMLHttpRequest对象的全局变量

//用于创建XMLHttpRequest对象
function createXmlHttp() {
    //根据window.XMLHttpRequest对象是否存在使用不同的创建方式
    if (window.XMLHttpRequest) {
       xmlHttp = new XMLHttpRequest();                  //FireFox、Opera等浏览器支持的创建方式
    } else {
       xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");//IE浏览器支持的创建方式
    }
}
//向服务器发送操作请求
function sendRequest(params) {
    createXmlHttp();                        //创建XmlHttpRequest对象
    xmlHttp.onreadystatechange = showCartInfo;
    xmlHttp.open("GET", "inc_prod_detail_update.php?timestamp=" + new Date().getTime() + params, true);
    xmlHttp.send(null);
}

//将服务器响应信息写入购物车div中
function showCartInfo() {
    if (xmlHttp.readyState == 4) {
        parent.edu_main.shoppingcart.innerHTML = xmlHttp.responseText;
        
    }
}
//向购物车添加产品
function addProduct(pid,action,im,addnum) {
	
	var params="&action=add&productId="+pid+"&im="+im+"&addnum="+addnum;
	window.parent.window.frames['edu_main'].sendRequest(params);
}
<!--
function $(d){return document.getElementById(d);}
function gs(d){var t=$(d);if (t){return t.style;}else{return null;}}
function gs2(d,a){
  if (d.currentStyle){ 
    var curVal=d.currentStyle[a]
  }else{ 
    var curVal=document.defaultView.getComputedStyle(d, null)[a]
  } 
  return curVal;
}
function ChatHidden(){gs("ChatBody").display = "none";}
function ChatShow(){gs("ChatBody").display = "";}
function ChatClose(){gs("main").display = "none";}
function ChatSend(obj){
  var o = obj.ChatValue;
  if (o.value.length>0){
    $("ChatContent").innerHTML += "<strong>Akon说：</strong>"+o.value+"<br/>";
    o.value='';
  }
}
if  (document.getElementById){
  (
    function(){
      if (window.opera){ document.write("<input type='hidden' id='Q' value=' '>"); }
    
      var n = 500;
      var dragok = false;
      var y,x,d,dy,dx;
      
      function move(e)
      {
        if (!e) e = window.event;
        if (dragok){
          d.style.left = dx + e.clientX - x + "px";
          d.style.top  = dy + e.clientY - y + "px";
          return false;
        }
      }
      
      function down(e){
        if (!e) e = window.event;
        var temp = (typeof e.target != "undefined")?e.target:e.srcElement;
        if (temp.tagName != "HTML"|"BODY" && temp.className != "dragclass"){
          temp = (typeof temp.parentNode != "undefined")?temp.parentNode:temp.parentElement;
        }
        if('TR'==temp.tagName){
          temp = (typeof temp.parentNode != "undefined")?temp.parentNode:temp.parentElement;
          temp = (typeof temp.parentNode != "undefined")?temp.parentNode:temp.parentElement;
          temp = (typeof temp.parentNode != "undefined")?temp.parentNode:temp.parentElement;
        }
      
        if (temp.className == "dragclass"){
          if (window.opera){ document.getElementById("Q").focus(); }
          dragok = true;
          temp.style.zIndex = n++;
          d = temp;
          dx = parseInt(gs2(temp,"left"))|0;
          dy = parseInt(gs2(temp,"top"))|0;
          x = e.clientX;
          y = e.clientY;
          document.onmousemove = move;
          return false;
        }
      }
      
      function up(){
        dragok = false;
        document.onmousemove = null;
      }
      
      document.onmousedown = down;
      document.onmouseup = up;
    
    }
  )();
}
-->
</script>
</head>
<body>
<?php require_once("prod_top.php");?>
<?php require_once("inc_product_tree.php");?>

</body>
</html>