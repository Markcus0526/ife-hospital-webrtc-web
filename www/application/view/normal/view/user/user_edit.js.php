<script type="text/javascript">
function onBoothComplete(img_path) {
	$('#img_avartar').attr('src', img_path);
	$('#avartar').val(img_path);
}

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
	<?php if ($mUser->user_type == UTYPE_PATIENT || $mUser->user_type == UTYPE_DOCTOR || $mUser->user_type == UTYPE_INTERPRETER) { ?>
	else {
		attaches = $('#' + upload_id).val();
		if (attaches != "") attaches += ";";
		$('#' + upload_id).val(attaches + files);

		refreshAttaches(upload_id);
	}
	<?php } ?>
}

<?php if ($mUser->user_type == UTYPE_PATIENT || $mUser->user_type == UTYPE_DOCTOR || $mUser->user_type == UTYPE_INTERPRETER) { ?>
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
			mobile: {
				required: true,
				mobile: true
			},
			other_tel: {
				mobile: true
			},
			email: {
				required: true,
				email: true
			},
	<?php if ($mUser->user_id == "") { ?>
			password: {
				required: true,
				pwd_min_length: <?php p(PASSWORD_MIN_LENGTH); ?>
				<?php if (PASSWORD_STRENGTH == 1) { ?>
				,pwd_strength: true
				<?php } ?>
			},
	<?php } else {?>
			password: {
				pwd_min_length: <?php p(PASSWORD_MIN_LENGTH); ?>
				<?php if (PASSWORD_STRENGTH == 1) { ?>
				,pwd_strength: true
				<?php } ?>
			},
	<?php } ?>
			confirm_password: {
				equalTo: '#password'
			},
	<?php if ($mUser->user_type == UTYPE_DOCTOR) { ?>
			d_title: {
				required: true
			},
			d_depart: {
				required: true
			},
			'hospitals[]': {
				required: true
			},
			'diseases[]': {
				required: true
			},
	<?php } ?>
	<?php if ($mUser->user_type == UTYPE_INTERPRETER) { ?>
			i_age: {
				required: true
			},
	<?php } ?>
	<?php if ($mUser->user_type == UTYPE_DOCTOR) { ?>
			'languages': {
				required: true
			},
			introduction: {
				required: true
			},
	<?php } else if ($mUser->user_type == UTYPE_INTERPRETER) { ?>
			'languages[]': {
				required: true
			},
			introduction: {
				required: true
			},
	<?php } ?>
	<?php if ($mUser->user_type != UTYPE_DOCTOR) { ?>
			sex: {
				required: true
			},
	<?php } ?>
	<?php if ($mUser->user_type == UTYPE_PATIENT || $mUser->user_type == UTYPE_INTERPRETER) { ?>
			home_address: {
				required: true
			},
			passports: {
				required: true
			},
	<?php } ?>
			diplomas: {
				required: true
			}
		},

		// Messages for form validation
		messages : {
			user_name: {
				required: '<?php l('请输入姓名。');?>',
				real_name: '<?php l('姓名由任意长度的汉字、字母、符号组成。');?>'
			},
			mobile: {
				required: '<?php l('请输入手机号码。');?>',
				mobile: '<?php l('请输入正确的手机号码。'); ?>'
			},
			other_tel: {
				mobile: '<?php l('请输入正确的手机号码。'); ?>'
			},
			email: {
				required: '<?php l('请输入电子邮箱。');?>',
				email: '<?php l('请输入正确的电子邮箱。');?>',
			},
	<?php if ($mUser->user_id == "") { ?>
			password: {
				required: '<?php l("请输入密码。"); ?>',
				<?php if (PASSWORD_STRENGTH == 1) { ?>
				pwd_min_length: '<?php l("密码由8~16位数字、字母、符号组成，不允许有空格"); ?>',
				pwd_strength: '<?php l("密码由8~16位数字、字母、符号组成，不允许有空格"); ?>'
				<?php } else { ?>
				pwd_min_length: '<?php l("密码的最小大小是" . PASSWORD_MIN_LENGTH . "个字符。");?>'
				<?php } ?>
			},
	<?php } else {?>
			password: {
				<?php if (PASSWORD_STRENGTH == 1) { ?>
				pwd_min_length: '<?php l("密码由8~16位数字、字母、符号组成，不允许有空格"); ?>',
				pwd_strength: '<?php l("密码由8~16位数字、字母、符号组成，不允许有空格"); ?>'
				<?php } else { ?>
				pwd_min_length: '<?php l("密码的最小大小是" . PASSWORD_MIN_LENGTH . "个字符。");?>'
				<?php } ?>
			},
	<?php } ?>
			confirm_password: {
				equalTo: '<?php l("请输入一致的密码。"); ?>'
			},
	<?php if ($mUser->user_type == UTYPE_DOCTOR) { ?>
			d_title: {
				required: '<?php l('请输入职称。');?>'
			},
			d_depart: {
				required: '<?php l('请输入所属科室。');?>'
			},
			'hospitals[]': {
				required: '<?php l('请选择所属医院。');?>'
			},
			'diseases[]': {
				required: '<?php l('请选择疾病专长。');?>'
			},
	<?php } ?>
	<?php if ($mUser->user_type == UTYPE_INTERPRETER) { ?>
			i_age: {
				required: '<?php l('请输入译龄。');?>',
				max: '<?php l('请输入有效数字。');?>',
				min: '<?php l('请输入有效数字。');?>'
			},
	<?php } ?>
	<?php if ($mUser->user_type == UTYPE_DOCTOR) { ?>
			'languages': {
				required: '<?php l('请选择精通语言。');?>'
			},
			introduction: {
				required: '<?php l('请输入您的简介。');?>'
			},
	<?php } else if ($mUser->user_type == UTYPE_INTERPRETER) { ?>
			'languages[]': {
				required: '<?php l('请选择精通语言。');?>'
			},
			introduction: {
				required: '<?php l('请输入您的简介。');?>'
			},
	<?php } ?>
	<?php if ($mUser->user_type != UTYPE_DOCTOR) { ?>
			sex: {
				required: '<?php l('请选择性别。');?>'
			},
	<?php } ?>
	<?php if ($mUser->user_type == UTYPE_PATIENT || $mUser->user_type == UTYPE_INTERPRETER) { ?>
			home_address: {
				required: '<?php l('请输入家庭住址。');?>'
			},
			passports: {
				required: '<?php l('请上传身份证件。');?>'
			},
	<?php } ?>
			diplomas: {
				required: '<?php l('请上传资格证书。');?>'
			}
		}
	}, getValidationRules()));

	$('#form').ajaxForm({
		dataType : 'json',
		beforeSubmit: function() {
			$('[type="submit"]').prop('disabled', true);
		},
		success: function(res, statusText, xhr, form) {
			try {
				if (res.err_code == 0)
				{
					alertBox("<?php l('提示');?>", "<?php l('保存用户信息成功。');?>", function() {
						goto_url($('.btn-cancel').attr('href'));
					});
					return;
				}
				else {
					$('[type="submit"]').prop('disabled', false);
					errorBox("<?php l('错误发生');?>", res.err_msg);
				}
			}
			finally {
			}
		}
	});	

	$('[name="languages"]').selectpicker()
		.on('change', function() { $(this).valid();});
	$('[name="languages[]"]').selectpicker()
		.on('change', function() { $(this).valid();});
	$('[name="diseases[]"]').selectpicker()
		.on('change', function() { $(this).valid(); });

	<?php if ($mUser->user_type == UTYPE_DOCTOR) { ?>
	$('[name="hospitals[]"]').selectpicker()
		.on('change', function() { $(this).valid();});
	<?php } ?>

	<?php if ($mUser->user_type == UTYPE_DOCTOR || $mUser->user_type == UTYPE_INTERPRETER) { ?>
	// init attach
	refreshAttaches('diplomas');
	<?php } ?>

	<?php if ($mUser->user_type == UTYPE_PATIENT || $mUser->user_type == UTYPE_DOCTOR || $mUser->user_type == UTYPE_INTERPRETER) { ?>
	// init attach
	refreshAttaches('passports');
	<?php } ?>
});

</script>