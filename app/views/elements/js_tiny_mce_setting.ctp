<script src="<?php echo Router::url('/',true).'js/libs/tiny_mce/jquery.tinymce.js';?>"  type="text/javascript"></script>
<script type="text/javascript">
jQuery(document).ready(function($) {
    $.fn.ftinyMce = function() {
		$(this).tinymce( {
			// Location of TinyMCE script
			script_url: __cfg('path_relative') + 'js/libs/tiny_mce/tiny_mce.js',
			mode: "textareas",
		   // General options
			theme: "advanced",
			plugins: "safari,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",
		   // Theme options
			theme_advanced_buttons1: "newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect",
			theme_advanced_buttons2: "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
			theme_advanced_buttons3: "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,ltr,rtl,|,fullscreen",
			theme_advanced_buttons4: "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,pagebreak",
			theme_advanced_toolbar_location: "top",
			theme_advanced_toolbar_align: "left",
			theme_advanced_statusbar_location: "bottom",
			theme_advanced_resizing: true,
		  // Example content CSS (should be your site CSS)
			content_css: "css/content.css",
		   // Drop lists for link/image/media/template dialogs
			template_external_list_url: "lists/template_list.js",
			external_link_list_url: "lists/link_list.js",
			external_image_list_url: "lists/image_list.js",
			media_external_list_url: "lists/media_list.js",
			height: "400px",
			width: "98%",
			relative_urls : false,
			remove_script_host : false,
			setup: function(ed) {
				ed.onChange.add(function(ed) {
					tinyMCE.triggerSave();
				});
			}
		});
    };
	$('.js-editor').livequery(function() {		 
		 $(this).ftinyMce();
	});
});
</script>