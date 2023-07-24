<script type="text/javascript">
$(function() {	
	var form = $('#list_form').validate($.extend({
		rules : {
			language_name: {
				required: true
			}
		},

		// Messages for form validation
		messages : {
			language_name: {
				required: '<?php l('请输入语言名称。');?>'
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
					alertBox("<?php l('提示');?>", "<?php l('保存语言成功。');?>", function() {
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

	function show_edit_language(language_id)
	{
		$('#list_form').attr('action', "api/language/save");

		org_language_id = $('#language_id').val();
		if (org_language_id != "") {
			$('tr[language_id=' + org_language_id + ']').show();
		}

		$('#edit_language .td-no').text('+');
		$('#language_id').val('');
		$('#language_name').val('');
		$('#language_code').val('');

		if (language_id != "") {
			App.callAPI("api/language/get",{ language_id: language_id })
			.done(function(res) {
				$('#edit_language .td-no').text($('tr[language_id=' + language_id + '] .td-no').text());
				$('#language_id').val(res.language.language_id);
				$('#language_name').val(res.language.language_name);
				$('#language_code').val(res.language.language_code);
				$('#edit_language').insertAfter('tr[language_id=' + language_id + ']');
				$('tr[language_id=' + language_id + ']').hide();
				$('#edit_language').removeClass('hide');
				$('#language_name').focus();
	        })
	        .fail(function(res) {
	        	errorBox("<?php l('错误发生');?>", res.err_msg);
	        });
		}
		else {
			$('#edit_language').prependTo('#language_list tbody');
			$('#edit_language').removeClass('hide');
			$('#language_name').focus();
		}
	}

	$('#insert_language').click(function() {
		show_edit_language('');
	});

	function cancel_edit_language()
	{
		org_language_id = $('#language_id').val();
		if (org_language_id != "") {
			$('tr[language_id=' + org_language_id + ']').show();
		}
		$('#edit_language').addClass('hide');
	}

	$('#language_name,#language_code').on('keydown', function(ev) {
		if (ev.keyCode === 27) { // escape
			cancel_edit_language();
		}
	});

	$('#btn_cancel_language').click(cancel_edit_language);

	$('.language .btn-edit').click(function() {
		show_edit_language($(this).parents('tr').attr('language_id'));
	});
	$('.language .editable').dblclick(function() {
		show_edit_language($(this).parents('tr').attr('language_id'));
	});

	$('.language .btn-delete').click(function() {
		language_id = $(this).parents('tr').attr('language_id');
		
		confirmBox('<?php l('删除');?>', '<?php l('是否确定删除该语言？');?>', function() {
			App.callAPI("api/language/delete",
				{
					language_id: language_id
				}
			)
			.done(function(res) {
	        	alertBox('<?php l('删除成功');?>', '<?php l('删除语言成功。');?>', function() {
	        		App.reload();
	        	});
	        })
	        .fail(function(res) {
	        	errorBox("<?php l('错误发生');?>", res.err_msg);
	        });
		});
	});

	// move
	$('tr.language .btn-move-top,tr.language .btn-move-up,tr.language .btn-move-down,tr.language .btn-move-bottom').click(function() {
		language_id = $(this).parents('tr.language').attr('language_id');
		direct = $(this).attr('direct');

		App.callAPI("api/language/move_to",
				{
					language_id: language_id,
					direct: direct
				}
			)
			.done(function(res) {
	        	App.reload();
	        })
	        .fail(function(res) {
	        	errorBox("<?php l('错误发生');?>", res.err_msg);
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
		if (mode == 'language')
			$('#language_form [name="name_field"]').val('language_name');
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

	$('.editable-language').click(function() {
		language_name = $(this).attr('language_name');
		if (language_name != '' && language_name != null) {
			language_id = $(this).parents('tr[language_id]').attr('language_id');
			language_name_l = $(this).attr('language_name_l');
			showLangForm('language', language_id, language_name, language_name_l);
			return;
		}
	});
});
</script>