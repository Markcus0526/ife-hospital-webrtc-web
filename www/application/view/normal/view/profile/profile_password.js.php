<script type="text/javascript">
$(function () {
	var $form = $('#form').validate($.extend({
		rules : {
			old_password: {
				required: true
			},
			new_password: {
				required: true,
				pwd_min_length: <?php p(PASSWORD_MIN_LENGTH); ?>
				<?php if (PASSWORD_STRENGTH == 1) { ?>
				,pwd_strength: true
				<?php } ?>
			},
			confirm_new_password: {
				equalTo: $('#new_password')
			}
		},

		// Messages for form validation
		messages : {
			old_password : {
				required : '<?php l('请输入旧密码。');?>'
			},
			new_password : {
				required : '<?php l('请输入新密码。');?>',
				pwd_min_length: '<?php l("密码的最小大小是" . PASSWORD_MIN_LENGTH . "个字符。");?>'
				<?php if (PASSWORD_STRENGTH == 1) { ?>
				,pwd_strength: '<?php l("密码由8~16位数字、字母、符号组成，不允许有空格"); ?>'
				<?php } ?>
			},
			confirm_new_password: {
				equalTo : '<?php l('请重新输入新密码。');?>'
			}
		}
	}, getValidationRules()));

	$('#form').ajaxForm({
		dataType : 'json',
		success: function(res, statusText, xhr, form) {
			try {
				if (res.err_code == 0)
				{	
					alertBox("<?php l('提示成功');?>", "<?php l('更改密码成功。');?>", function() {
						goto_url("profile");
					});
				}
				else if (res.err_msg != "")
				{
					errorBox("<?php l('错误发生');?>", res.err_msg);
				}
			}
			finally {
			}
		}
	});

});
</script>