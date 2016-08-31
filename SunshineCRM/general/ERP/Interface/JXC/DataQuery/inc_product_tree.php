
<link rel="stylesheet" href="<?php echo ROOT_DIR?>general/ERP/Framework/inc/demo.css" type="text/css">
<link rel="stylesheet" href="<?php echo ROOT_DIR?>general/ERP/Framework/inc/zTreeStyle.css" type="text/css">
<script type="text/javascript" language="javascript" src="<?php echo ROOT_DIR?>general/ERP/Enginee/jquery/jquery.js"></script>
  <script type="text/javascript" src="<?php echo ROOT_DIR?>general/ERP/Framework/inc/jquery.ztree-2.6.js"></script>
  <SCRIPT LANGUAGE="JavaScript">
  <!--
	var zTree1;
	var setting;

	setting = {
		keepParent: true,
		keepLeaf: true,
		async: true,
//		asyncUrl: "asyncData/node.jsp",
		asyncUrl: "node.php",  
		asyncParam: ["name", "id"],
		callback: {
			click: zTreeOnClick
		}
	};

	$(document).ready(function(){
		refreshTree();
	});

	function zTreeOnClick(event, treeId, treeNode)
	{
		if(!treeNode.isParent)
			addProduct(treeNode.id,'add',3,1);
			//parent.edu_main.location="../Interface/Framework/user_newai.php?FF=FF&DEPT_ID="+treeNode.id;
		
	}

	function refreshTree() {
		zTree1 = $("#treeDemo").zTree(setting);
	}

  //-->
  </SCRIPT>
<div align=left  style="margin: 10px;">
方法三：通过类别选择
		<div>
			<ul id="treeDemo" class="tree"  style="overflow-x:hidden"></ul>
		</div>	
</div>		

