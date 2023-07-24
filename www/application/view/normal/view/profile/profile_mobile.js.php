<script type="text/javascript">
$(function() {
	var form = $('#form').validate($.extend({
		rules : {
			mobile_passkey: {
				passkey: true,
				required: true
			},
			new_mobile: {
				required: true,
				mobile: true
			},
			new_mobile_passkey: {
				passkey: true,
				required: true
			}
		},

		// Messages for form validation
		messages : {
			mobile_passkey: {
				passkey: function() {
					if ($('#mobile_passkey').prop('valid_passkey') === null)
						return '';
					return '<?php l('请输入正确的验证码。');?>'
				},
				required: '<?php l('请输入验证码。');?>'
			},
			new_mobile: {
				required: '<?php l('请输入新绑定手机号。');?>',
				mobile: '<?php l('请输入正确的手机号码。');?>'
			},
			new_mobile_passkey: {
				passkey: function() {
					if ($('#new_mobile_passkey').prop('valid_passkey') === null)
						return '';
					return '<?php l('请输入正确的验证码。');?>'
				},
				required: '<?php l('请输入验证码。');?>'
			}
		}
	}, getValidationRules()));

	$('[name="mobile_passkey"],[name="new_mobile_passkey"]').change(function() {
		var $this = $(this);
		var form = $this.closest('form');
		var mobile_field = $this.attr('mobile');
		var mobile_control = form.find('[name="' + mobile_field + '"]');
		var mobile = mobile_control.val();
		intlTelInput = mobile_control.data('plugin_intlTelInput');
		if (intlTelInput) {
			mobile = intlTelInput.getNumber();
		}
		var passkey = $this.val();
		if (passkey == '') {
			$this.prop('valid_passkey', false)
			$this.valid();;	
			return;
		}
		$this.prop('valid_passkey', null);
		App.callAPI("api/common/check_passkey",
			{
				phone_num: mobile,
				passkey: passkey
			}
		)
		.done(function(res) {
			$this.prop('valid_passkey', res.valid);
			$this.closest('.form-group').find('.help-block-info').addClass('hide');
			$this.valid();
		})
        .fail(function(res) {
        	errorBox("<?php l('错误发生');?>", res.err_msg);
        });
	});

	$('#form').ajaxForm({
		dataType : 'json',
		success: function(res, statusText, xhr, form) {
			try {
				if (res.err_code == 0)
				{
					alertBox("<?php l('提示');?>", "<?php l('更改绑定手机成功。');?>", function() {
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
});

</script>