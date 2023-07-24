<script type="text/javascript">
<?php 
$my_type = _my_type();
?>
function onUploadComplete(files, upload_id, upload_type)
{
	if (upload_id == 'avartar') {
		var files = files.split(';');
		if (files.length) {
			var fs = files[0].split(':');
			if (fs[0] != '') {
				$('#img_avartar').attr('src', fs[0]);
				$('#avartar').val(fs[0]);	
				$('#form').submit();
			}
		}
	}
}

<?php if ($my_type == UTYPE_PATIENT || $my_type == UTYPE_DOCTOR || $my_type == UTYPE_INTERPRETER || $my_type == UTYPE_ADMIN) { ?>
function refreshAttaches(upload_id)
{
	var lks = "";
	var attaches = $('#' + upload_id).val();
	if (attaches != "" && attaches != undefined)
	{
		var as = attaches.split(';');
		for (i = 0; i < as.length; i ++)
		{
			var fs = as[i].split(':');
			lks += "<li>";
			lks += downLink(fs[0], fs[1], fs[2], "<?php l("下载"); ?>");
			lks += "</li>";
		}
		$('#ul_' + upload_id).html(lks);
	}
	else {
		$('#ul_' + upload_id).html(lks);
	}
}

$(function() {

	var form = $('#form').validate($.extend({
		rules : {
			avartar: {
				required: true
			},
		},

		// Messages for form validation
		messages : {
			avartar: {
				required: '<?php l('请上传头像。');?>'
			},
		}
	}, getValidationRules()));

	$('#form').ajaxForm({
		dataType : 'json',
		success: function(res, statusText, xhr, form) {
			try {
				if (res.err_code == 0)
				{
					alertBox("<?php l('提示');?>", "<?php l('成功上传头像');?>", function() {
						App.reload();
					});
					return;
				}
				else {
					errorBox("<?php l('错误发生');?>", res.err_msg);
				}
			}
			finally {
			}
		}
	});	

	// init attach
	refreshAttaches('diplomas');
	refreshAttaches('passports');
});
<?php } ?>
</script>