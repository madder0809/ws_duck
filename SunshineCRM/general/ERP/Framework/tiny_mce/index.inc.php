<!-- TinyMCE -->
<?php 

	$jsfilename=ROOT_DIR."general/ERP/Framework/tiny_mce/tiny_mce.js";
	$cssfilename=ROOT_DIR."theme/3/style.css";
	
?>
<script type="text/javascript" src="<?php echo $jsfilename?>"></script>
<script type="text/javascript">
tinyMCE.init({
	language: "ch",
	mode : "exact",
	elements : "elm1", 
	theme : "advanced",
	plugins : "safari,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,wordcount",
    theme_advanced_buttons1: "forecolor,backcolor,separator,bold,italic,underline,strikethrough,separator,bullist,numlist,separator, justifyleft, justifycenter, justifyright,justifyleft,justifycenter,justifyright,outdent,indent,removeformat,separator,link,unlink,image,quote,code,fullscreen,insertCode",
    theme_advanced_buttons2: "",
    theme_advanced_buttons3: "",
	theme_advanced_toolbar_location : "top",
	theme_advanced_toolbar_align : "left",
	theme_advanced_statusbar_location : "bottom",
	theme_advanced_resizing : true,
	convert_fonts_to_spans: true,
    remove_trailing_nbsp: true,
    convert_newlines_to_brs: false,
    force_br_newlines: false,
    force_p_newlines: false,
    remove_linebreaks: false,
    relative_urls: false,
	content_css : "content.css"
});
</script>
<!-- /TinyMCE -->
<div>
	<textarea id="elm1" name="<?php echo $var?>" rows="15" cols="70" style="width: 585px">
	<?php echo $var_value?>
	</textarea>
</div>