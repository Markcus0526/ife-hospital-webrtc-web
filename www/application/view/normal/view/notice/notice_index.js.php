<script type="text/javascript">

$(function() {	
	var form = $('#list_form').validate($.extend({
		rules : {
			content: {
				required: true
			}
		},

		// Messages for form validation
		messages : {
			content: {
				required: '<?php l('请输入内容。');?>'
			}
		}
	}, getValidationRules()));

	$('#list_form').ajaxForm({
		dataType : 'json',
		beforeSubmit: function() {
			$('[type="submit"]').prop('disabled', true);
		},
		success: function(res, statusText, xhr, form) {
			try {
				if (res.err_code == 0)
				{
					alertBox("<?php l('提示');?>", "<?php l('保存广播成功。');?>", function() {
						App.reload();
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

	function show_edit_notice(notice_id)
	{
		org_notice_id = $('#notice_id').val();
		if (org_notice_id != "") {
			$('tr[notice_id=' + org_notice_id + ']').show();
		}

		$('#edit_notice .td-no').text('+');
		$('#notice_id').val('');
		$('#content').val('');

		if (notice_id != "") {
			App.callAPI("api/notice/get",{ notice_id: notice_id })
			.done(function(res) {
				$('#edit_notice .td-no').text($('tr[notice_id=' + notice_id + '] .td-no').text());
				$('#notice_id').val(res.notice.notice_id);
				$('#content').val(res.notice.content);
				$('#edit_notice').insertAfter('tr[notice_id=' + notice_id + ']');
				$('tr[notice_id=' + notice_id + ']').hide();
				$('#edit_notice').removeClass('hide');
				$('#content').focus();
	        })
	        .fail(function(res) {
	        	errorBox("<?php l('错误发生');?>", res.err_msg);
	        });
		}
		else {
			$('#edit_notice').prependTo('#notice_list tbody');
			$('#edit_notice').removeClass('hide');
			$('#content').focus();
		}
	}

	$('#insert_notice').click(function() {
		show_edit_notice('');
	});

	function cancel_edit_notice()
	{
		org_notice_id = $('#notice_id').val();
		if (org_notice_id != "") {
			$('tr[notice_id=' + org_notice_id + ']').show();
		}
		$('#edit_notice').addClass('hide');
	}

	$('#content').on('keydown', function(ev) {
		if (ev.keyCode === 27) { // escape
			cancel_edit_notice();
		}
	});

	$('#btn_cancel_notice').click(cancel_edit_notice);

	$('.notice .btn-edit').click(function() {
		show_edit_notice($(this).parents('tr').attr('notice_id'));
	});
	$('.notice .editable').dblclick(function() {
		show_edit_notice($(this).parents('tr').attr('notice_id'));
	});

	$('.notice .btn-delete').click(function() {
		notice_id = $(this).parents('tr').attr('notice_id');
		
		confirmBox('<?php l('删除');?>', '<?php l('是否确定删除该广播？');?>', function() {
			App.callAPI("api/notice/delete",
				{
					notice_id: notice_id
				}
			)
			.done(function(res) {
	        	alertBox('<?php l('删除成功');?>', '<?php l('删除广播成功。');?>', function() {
	        		App.reload();
	        	});
	        })
	        .fail(function(res) {
	        	errorBox("<?php l('错误发生');?>", res.err_msg);
	        });
		});
	});

	$('#language_form').ajaxForm({
        dataType : 'json',
        success: function(res, statusText, xhr, form) {
            try {
                if (res.err_code == 0)
                {
                    alertBox("<?php l('提示');?>", "<?php l('成功设置。');?>", function() {
                    	App.reload();
                    });

                    $('#language_form').modal('hide');
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

	function showLangForm(mode, id, name, name_l) {
		if (mode == 'content')
			$('#language_form [name="name_field"]').val('content');
		else
			return;
		$('#language_form [name="id"]').val(id);
		$('#language_form #name').text(name);
		$('#language_form .name_l').val('');

		if (name_l != '') {
			try {
				name_l = JSON.parse(name_l);

				$.each(name_l, function(key, val) {
					$('#language_form [name="name_l[\'' + key + '\']"').val(val);
				});
			}
			catch(e) {

			}
		}

		$('#language_form').modal('show');
	}

	$('.editable-language, .btn-edit-language').click(function() {
		content = $(this).attr('content');
		if (content != '' && content != null) {
			notice_id = $(this).parents('tr[notice_id]').attr('notice_id');
			content_l = $(this).attr('content_l');
			showLangForm('content', notice_id, content, content_l);
			return;
		}
	});
});
</script>