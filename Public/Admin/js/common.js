;$(function(){
	
	//弹出框
	$('.modal').click(function(){
		$.get($(this).attr('href'),'',function(data){
			$('body').append(data);
			//document.getElementById("mask_bg").style.display ="block";
			//document.getElementById("mask_box").style.display ="block";
			$("#mask_bg").show();
			$("#mask_box").show();
		},'json');
		return false;
	});
	
	//删除-询问框
	$('.ask_del').on("click",function(){
		var div  = "<div id='mask_box' style='width:400px;height:200px;'>";
				div += "<div id='mask_box_header'>信息</div>";
					div += "<div id='mask_box_content' style='color:#000;text-align:center;margin-top:40px;'>";
					div += "<p style='text-align: center;display: inline;'>是否执行此操作</p>";
					div += "<div id='mask_box_footer'>";
						div += "<input type='submit' onclick=run_click('" + $(this).attr('href') + "') value='确定'>";
						div += "<input type='button' onclick='remove_mask_box();' value='取消'>";
					div += "</div>";
				div += "</div>";
			div += "</div>";
		$('body').append(div);
		document.getElementById("mask_bg").style.display ="block";
		document.getElementById("mask_box").style.display ="block";
		return false;
	});

	//审核-询问框
	$('.ask_audit').on("click",function(){
		var div  = "<div id='mask_box'>";
				div += "<div id='mask_box_header'>信息</div>";
					div += "<div id='mask_box_content' style='text-align:center;margin-top:40px;'>";
					div += "<p style='text-align: center;display: inline;'>是否执行此操作</p>";
					div += "<div id='mask_box_footer'>";
						div += "<input type='submit' onclick=run_click('" + $(this).attr('href') + "&status=1') value='通过'>";
						div += "<input type='submit' onclick=run_click('" + $(this).attr('href') + "&status=2') value='不通过'>";
						div += "<input type='button' onclick='remove_mask_box();' value='取消'>";
					div += "</div>";
				div += "</div>";
			div += "</div>";
		$('body').append(div);
		$("#mask_box").width('500');
		$("#mask_box").height('200');
		document.getElementById("mask_bg").style.display ="block";
		document.getElementById("mask_box").style.display ="block";
		return false;
	})
	
	$('.table tbody tr').hover(function(){
	    $(this).find('td a').show();
    },function(){
    	$(this).find('td a').hide();
    });
	
	$(window).resize(function(){
		resize_main_content();
	});
	
	resize_main_content();
});

function resize_main_content(){
	$top_height = $('.header').height()+$('.details').height()+$('.header_action').height();
	$('.main_content').height($(window).height() - $top_height - 45);
}

function ajax_post(){
	$.post($('.modal_form').attr('action'), $('.modal_form').serialize(), function(data){
		if(data.status == '1'){
			location.href=data.url;
		}else{
			$('#prompt_info').text(data.info);							
		}
	},'json');
	return false;
}

function run_click(url){
	$.get(url, '', function(data){
		if(data.status == 1){
			location.href = data.url;
		}else{
			alert(data.info);
		}
	});
}
function remove_mask_box() {
	 $("#mask_bg").hide();
	 $("#mask_box").remove();
}

//判断是否是正整数
function IsNum(s)
{
	if(s!=null){
		var r,re;
		re = /\d*/i; //\d表示数字,*表示匹配多个数字
		r = s.match(re);
		return (r==s)?true:false;
	}
	return false;
}