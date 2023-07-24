<script type="text/javascript">
$(function() {
	$('.checkbox[value="all"]').change(function() {
		group = $(this).attr('group');	
		checked = $(this).prop('checked');
		$('[name="' + group + '[]"]').each(function() {
			if ($(this).val() != "-1")
				$(this).prop('checked', checked);
		});
	});

	$('.checkbox.there-is-all').change(function() {
		check_list = $(this).parents('.checkbox-list');
		checkbox_all = check_list.find('.checkbox[value="all"]');
		checked = true;
		check_list.find('.there-is-all').each(function() {
			if (!$(this).prop('checked'))
				checked = false;
		});
		checkbox_all.prop('checked', checked);
	});

	$('#form').ajaxForm({
		dataType : 'json',
		success: function(res, statusText, xhr, form) {
			try {
				if (res.err_code == 0)
				{
					alertBox("<?php l('提示');?>", "<?php l('成功保存。');?>", function() {
						goto_url('user/admins');
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