<?php

require_once( "lib.inc.php" );
$GLOBAL_SESSION = returnsession( );
$file_ini = parse_ini_file( "../Interface/Framework/system_config.ini" );
$BANNER_TEXT = $file_ini['CompanyName'];
?>
<html>
<head>
<title><?php echo $IE_TITLE?></title>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">

<script language="JavaScript">
<!--  将当前窗口缩小为0  -->
self.moveTo(0,0);
<!--  将当前窗口设置为屏幕大小  -->
self.resizeTo(screen.availWidth,screen.availHeight);
<!--   -->
self.focus();   

// 状态栏显示文字
window.defaultStatus="<?php echo $IE_TITLE?>"; 
</script>
</head>

<frameset rows="51,27,*,20" cols="*" frameborder="NO" border="0" framespacing="0" id="frame1">    <!-- 上下方式分割为3块 -->
  <frame src="index_top.php" name="topFrame" scrolling="NO" noresize >                         <!--//顶部页面  -->
  <frame src="index_head.php" name="headFrame" scrolling="NO" noresize >                         <!--//顶部下页面  -->
  <frameset rows="*" cols="7,190,5,9,*,0" framespacing="0" frameborder="NO" border="0" id="frame2"><!--//中部再分为几块,左右方式分割 -->
        <frame src="menu_leftbar.php" name="menu_leftbar" scrolling="NO" noresize>                 <!- //菜单左边条 -->
	<frame src="function_panel_index.php" name="function_panel_index" scrolling="NO" noresize>   <!--//左边的菜单页 -->
        <frame src="menu_rightbar.php" name="menu_rightbar" scrolling="NO" noresize>  <!-//菜单右边条 -->
	<frame src="controlmenu.php" name="controlmenu" scrolling="no" frameborder="0" noresize>   <!--//中间页，控制左边菜单的显隐 --> 
	<frame src="table_index.php" name="table_index"  scrolling="no" frameborder="0" noresize>   <!--//右边的内容页面，显示菜单点击页面 -->
	 <frame src="table_right.php" name="table_right" scrolling="no" frameborder="0" noresize>  <!-- //右边条 -->        
  </frameset>
  
</frameset>

<noframes>您的浏览器不支持框架页面，请使用IE6.0以上的浏览器！</noframes>

</html>
