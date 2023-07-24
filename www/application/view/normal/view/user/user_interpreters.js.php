<script type="text/javascript">
$(function() {
	// init country name
	$('.td-country[country_id]').each(function() {
		country_id = $(this).attr('country_id');
		country_name = $('#country_id option[value="' + country_id + '"]').text();
		$(this).text(country_name);
	});

	$('#country_id').change(function() {
		$('#list_form').submit();
	})
	
	$('.btn-delete').click(function() {
		user_id = $(this).parents('tr').attr('user_id');
		
		confirmBox('<?php l('删除');?>', '<?php l('是否确定删除该翻译？');?>', function() {
			App.callAPI("api/user/delete",
				{
					user_id: user_id
				}
			)
			.done(function(res) {
	        	alertBox('<?php l('删除成功');?>', '<?php l('删除翻译成功。');?>', function() {
	        		App.reload();
	        	});
	        })
	        .fail(function(res) {
	        	errorBox("<?php l('错误发生');?>", res.err_msg);
	        });
		});
	});
	
	$('.btn-unlock').click(function() {
		user_id = $(this).parents('tr').attr('user_id');
		
		confirmBox('<?php l('恢复');?>', '<?php l('是否确定恢复该账号?');?>', function() {
			App.callAPI("api/user/unlock",
				{
					user_id: user_id
				}
			)
			.done(function(res) {
	        	alertBox('<?php l('恢复成功');?>', '<?php l('恢复账号成功。');?>', function() {
	        		App.reload();
	        	});
	        })
	        .fail(function(res) {
	        	errorBox("<?php l('错误发生');?>", res.err_msg);
	        });
		});
	});

	<?php if (_my_type() == UTYPE_SUPER) { ?>
	var form = $('#cost_form').validate($.extend({
		rules : {
			interpreter_fee: {
				required: true,
                number: true,
                min: 0,
                max: 100
			}
		},

		// Messages for form validation
		messages : {
			interpreter_fee: {
                required: '<?php l('请输入会诊费。');?>',
                number: '<?php l('请输入有效数字。');?>',
                min: '<?php l('请输入0以上的数值。');?>',
                max: '<?php l('请输入100以下的数值。');?>',
			}
		}
	}, getValidationRules()));

    $('#cost_form').on('submit', function() {
        if ($(this).valid())
            $(this).modal('hide');
    });

	$('#cost_form').ajaxForm({
		dataType : 'json',
		success: function(res, statusText, xhr, form) {
			try {
				if (res.err_code == 0)
				{
					alertBox("<?php l('提示');?>", "<?php l('成功更改翻译费用。');?>", function() {
                    	App.reload(2000);
                    });

                    $('#cost_form').modal('hide')
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

	$('.btn-change-fee').click(function() {
		$('#interpreter_fee').val($('#interpreter_fee').attr('org_fee'));
		$('#cost_form').valid();
		$('#cost_form .has-error').removeClass('has-error');
		$('#cost_form').modal('show');
	})
	<?php } ?>

	$('.btn-select').click(function() {
		user_id = $(this).parents('tr[user_id]').attr('user_id');
		parent.onSelectInterpreter(user_id);
		parent.$.fancybox.close();
	});
});
</script>