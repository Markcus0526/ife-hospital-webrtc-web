<script type="text/javascript">
$(function() {
	var form = $('#form').validate($.extend({
		rules : {
			comment: {
				required: true
			}
		},

		// Messages for form validation
		messages : {
			comment: {
				required: '<?php l('请输入反馈内容。');?>'
			}
		}
	}, getValidationRules()));

	$('#form').ajaxForm({
		dataType : 'json',
		success: function(res, statusText, xhr, form) {
			try {
				if (res.err_code == 0)
				{
					$('#comment').val('');
					alertBox("<?php l('提示');?>", "<?php l('你的反馈意见已成功提交！');?>");
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