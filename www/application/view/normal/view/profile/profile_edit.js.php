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
			$('#img_avartar').attr('src', fs[0]);
			$('#avartar').val(fs[0]);	
		}
	}
	else {
		attaches = $('#' + upload_id).val();
		if (attaches != "") attaches += ";";
		$('#' + upload_id).val(attaches + files);

		refreshAttaches(upload_id);
	}
}

<?php if ($my_type == UTYPE_PATIENT || $my_type == UTYPE_DOCTOR || $my_type == UTYPE_INTERPRETER) { ?>
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
			lks += "<a href='javascript:;' class='remove-attach' no=" + i + " upload_id=" + upload_id + "><i class='mdi-content-remove-circle'></i></a>";
			lks += "</li>";
		}
		$('#ul_' + upload_id).html(lks);

		if (as.length >= 3)
			$('#ul_' + upload_id + " ~ .file-upload").hide();
		else 
			$('#ul_' + upload_id + " ~ .file-upload").show();

		$('.remove-attach').unbind('click').bind('click', 
			function() {
				var no = $(this).attr('no');
				var upload_id = $(this).attr('upload_id');
				var attaches = $('#' + upload_id).val();
				var new_attaches = "";
				if (attaches != "")
				{
					var as = attaches.split(';');
					for (i = 0; i < as.length; i ++)
					{
						if (i != no)
						{
							if (new_attaches != "")
								new_attaches += ";";
							new_attaches += as[i];
						}
					}
				}
				$('#' + upload_id).val(new_attaches);
				refreshAttaches(upload_id);
			});
	}
	else {
		$('#ul_' + upload_id).html(lks);
	}
}
<?php } ?>

$(function() {
	var form = $('#form').validate($.extend({
		rules : {
			user_name: {
				required: true,
				real_name: true
			},
			email: {
				required: true,
				email: true
			},
	<?php if ($my_type == UTYPE_DOCTOR) { ?>
			d_title: {
				required: true
			},
			d_depart: {
				required: true
			},
			hospitals: {
				required: true
			},
	<?php } ?>
	<?php if ($my_type == UTYPE_INTERPRETER) { ?>
			i_age: {
				required: true
			},
	<?php } ?>
	<?php if ($my_type == UTYPE_DOCTOR || $my_type == UTYPE_INTERPRETER) { ?>
			languages: {
				required: true
			},
			introduction: {
				required: true
			},
	<?php } ?>
	<?php if ($my_type == UTYPE_PATIENT || $my_type == UTYPE_DOCTOR || $my_type == UTYPE_INTERPRETER) { ?>
			home_address: {
				required: true
			},
			passports: {
				required: true
			},
	<?php } ?>
	<?php if ($my_type != UTYPE_DOCTOR) { ?>
			sex: {
				required: true
			}
	<?php } ?>
		},

		// Messages for form validation
		messages : {
			user_name: {
				required: '<?php l('请输入姓名。');?>',
				real_name: '<?php l('姓名由任意长度的汉字、字母、符号组成。');?>'
			},
			email: {
				required: '<?php l('请输入电子邮箱。');?>',
				email: '<?php l('请输入正确的电子邮箱。');?>',
			},
	<?php if ($my_type == UTYPE_DOCTOR) { ?>
			d_title: {
				required: '<?php l('请输入职称。');?>'
			},
			d_depart: {
				required: '<?php l('请输入所属科室。');?>'
			},
			hospitals: {
				required: '<?php l('请选择所属医院。');?>'
			},
	<?php } ?>
	<?php if ($my_type == UTYPE_INTERPRETER) { ?>
			i_age: {
				required: '<?php l('请输入译龄。');?>'
			},
	<?php } ?>
	<?php if ($my_type == UTYPE_DOCTOR || $my_type == UTYPE_INTERPRETER) { ?>
			languages: {
				required: '<?php l('请选择精通语言。');?>'
			},
			introduction: {
				required: '<?php l('请输入您的简介。');?>'
			},
	<?php } ?>
	<?php if ($my_type == UTYPE_PATIENT || $my_type == UTYPE_DOCTOR || $my_type == UTYPE_INTERPRETER) { ?>
			home_address: {
				required: '<?php l('请输入家庭住址。');?>'
			},
			passports: {
				required: '<?php l('请上传身份证件。');?>'
			},
	<?php } ?>
	<?php if ($my_type != UTYPE_DOCTOR) { ?>
			sex: {
				required: '<?php l('请选择性别。');?>'
			}
	<?php } ?>
		}
	}, getValidationRules()));

	$('#form').ajaxForm({
		dataType : 'json',
		success: function(res, statusText, xhr, form) {
			try {
				if (res.err_code == 0)
				{
					alertBox("<?php l('提示');?>", "<?php l('更改基本信息成功。');?>", function() {
						goto_url("profile/index");
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

	<?php if ($my_type == UTYPE_DOCTOR) { ?>
	$('[name="hospitals[]"]').selectpicker()
		.on('change', function() { $(this).valid();});
	<?php } ?>

	<?php if ($my_type == UTYPE_DOCTOR || $my_type == UTYPE_INTERPRETER) { ?>
	// init attach
	refreshAttaches('diplomas');
	<?php } ?>

	<?php if ($my_type == UTYPE_PATIENT || $my_type == UTYPE_DOCTOR || $my_type == UTYPE_INTERPRETER) { ?>
	// init attach
	refreshAttaches('passports');
	<?php } ?>
});

</script>