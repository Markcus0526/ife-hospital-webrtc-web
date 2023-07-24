<script type="text/javascript">
function onUploadComplete(files, upload_id, upload_type)
{
	attaches = $('#' + upload_id).val();
	if (attaches != "") attaches += ";";
	$('#' + upload_id).val(attaches + files);

	refreshAttaches(upload_id);
}

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

$(function() {	
	var form = $('#list_form').validate($.extend({
		rules : {
			disease_name: {
				required: true
			}
		},

		// Messages for form validation
		messages : {
			disease_name: {
				required: '<?php l('请输入病种名称。');?>'
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
					alertBox("<?php l('提示');?>", "<?php l('保存病种成功。');?>", function() {
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

	function show_edit_disease(disease_id)
	{
		cancel_edit_disease();
		cancel_edit_dtemplate();

		$('#list_form').attr('action', "api/disease/save");

		$('#edit_disease .td-no').text('+');
		$('#disease_id').val('');
		$('#disease_name').val('');
		$('#description').val('');

		if (disease_id != "") {
			App.callAPI("api/disease/get",{ disease_id: disease_id })
			.done(function(res) {
				$('#edit_disease .td-no').text($('tr.disease[disease_id=' + disease_id + '] .td-no').text());
				$('#disease_id').val(res.disease.disease_id);
				$('#disease_name').val(res.disease.disease_name);
				$('#description').val(res.disease.description);
				$('#edit_disease').insertAfter('tr.disease[disease_id=' + disease_id + ']');
				$('tr.disease[disease_id=' + disease_id + ']').hide();
				$('#edit_disease').removeClass('hide');
				$('#disease_name').focus();
	        })
	        .fail(function(res) {
	        	errorBox("<?php l('错误发生');?>", res.err_msg);
	        });
		}
		else {
			$('#edit_disease').prependTo('#disease_list tbody');
			$('#edit_disease').removeClass('hide');
			$('#disease_name').focus();
		}
	}

	$('#insert_disease').click(function() {
		show_edit_disease('');
	});

	function cancel_edit_disease()
	{
		org_disease_id = $('#disease_id').val();
		if (org_disease_id != "") {
			$('tr.disease[disease_id=' + org_disease_id + ']').show();
		}
		$('#edit_disease').addClass('hide');
	}

	$('#disease_name,#description').on('keydown', function(ev) {
		if (ev.keyCode === 27) { // escape
			cancel_edit_disease();
		}
	});

	$('#btn_cancel_disease').click(cancel_edit_disease);

	$('.disease .btn-edit').click(function() {
		show_edit_disease($(this).parents('tr').attr('disease_id'));
	});
	$('.disease .editable').dblclick(function() {
		show_edit_disease($(this).parents('tr').attr('disease_id'));
	});

	$('.disease .btn-delete').click(function() {
		disease_id = $(this).parents('tr').attr('disease_id');
		
		confirmBox('<?php l('删除');?>', '<?php l('是否确定删除该病种？');?>', function() {
			App.callAPI("api/disease/delete",
				{
					disease_id: disease_id
				}
			)
			.done(function(res) {
	        	alertBox('<?php l('删除成功');?>', '<?php l('删除病种成功。');?>', function() {
	        		App.reload();
	        	});
	        })
	        .fail(function(res) {
	        	errorBox("<?php l('错误发生');?>", res.err_msg);
	        });
		});
	});

	var dform = $('#dtemplate_form').validate($.extend({
		rules : {
			dtemplate_name: {
				required: true
			}
		},

		// Messages for form validation
		messages : {
			dtemplate_name: {
				required: '<?php l('请输入模板名称。');?>'
			}
		}
	}, getValidationRules()));

	$('#dtemplate_form').ajaxForm({
		dataType : 'json',
		beforeSubmit: function() {
			$('[type="submit"]').prop('disabled', true);
		},
		success: function(res, statusText, xhr, form) {
			try {
				if (res.err_code == 0)
				{
					alertBox("<?php l('提示');?>", "<?php l('保存模板成功。');?>", function() {
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

	$('.insert-dtemplate').click(function() {
		disease_id = $(this).parents('tr').attr('disease_id');

		$('#dtemplate_form [name="disease_id"]').val(disease_id);
		$('#dtemplate_form [name="dtemplate_name"]').val('');
		
		$('#dtemplate_form').valid();
        $('#dtemplate_form .has-error').removeClass('has-error');
		$('#dtemplate_form').modal('show');
	});

	function show_edit_dtemplate(dtemplate_id)
	{
		cancel_edit_disease();
		cancel_edit_dtemplate();

		$('#list_form').attr('action', "api/disease/save_dtemplate");

		$('#edit_dtemplate .td-no').text('+');
		$('#dtemplate_id').val('');
		$('#dtemplate_name').val('');

		if (dtemplate_id != "") {
			App.callAPI("api/disease/get_dtemplate",{ dtemplate_id: dtemplate_id })
			.done(function(res) {
				$('#edit_dtemplate .td-no').text($('tr[dtemplate_id=' + dtemplate_id + '] .td-no').text());
				$('#disease_id').val(res.dtemplate.disease_id);
				$('#dtemplate_id').val(res.dtemplate.dtemplate_id);
				$('#dtemplate_name').val(res.dtemplate.dtemplate_name);
				$('#edit_dtemplate').insertAfter('tr[dtemplate_id=' + dtemplate_id + ']');
				$('tr[dtemplate_id=' + dtemplate_id + ']').hide();
				$('#edit_dtemplate').removeClass('hide');
				$('#dtemplate_name').focus();
	        })
	        .fail(function(res) {
	        	errorBox("<?php l('错误发生');?>", res.err_msg);
	        });
		}
		else {
			$('#edit_dtemplate').prependTo('#disease_list tbody');
			$('#edit_dtemplate').removeClass('hide');
			$('#dtemplate_name').focus();
		}
	}

	function cancel_edit_dtemplate()
	{
		org_dtemplate_id = $('#dtemplate_id').val();
		if (org_dtemplate_id != "") {
			$('tr[dtemplate_id=' + org_dtemplate_id + ']').show();
		}
		$('#edit_dtemplate').addClass('hide');
	}

	$('#dtemplate_name').on('keydown', function(ev) {
		if (ev.keyCode === 27) { // escape
			cancel_edit_dtemplate();
		}
	});

	$('#btn_cancel_dtemplate').click(cancel_edit_dtemplate);

	$('.dtemplate .btn-edit').click(function() {
		show_edit_dtemplate($(this).parents('tr').attr('dtemplate_id'));
	});
	$('.dtemplate .editable').dblclick(function() {
		show_edit_dtemplate($(this).parents('tr').attr('dtemplate_id'));
	});

	$('.dtemplate .btn-delete').click(function() {
		dtemplate_id = $(this).parents('tr').attr('dtemplate_id');
		
		confirmBox('<?php l('删除');?>', '<?php l('是否确定删除该模板？');?>', function() {
			App.callAPI("api/disease/delete_dtemplate",
				{
					dtemplate_id: dtemplate_id
				}
			)
			.done(function(res) {
	        	alertBox('<?php l('删除成功');?>', '<?php l('删除模板成功。');?>', function() {
	        		App.reload();
	        	});
	        })
	        .fail(function(res) {
	        	errorBox("<?php l('错误发生');?>", res.err_msg);
	        });
		});
	});

	function updateExpand(tr_disease) {
		disease_id = tr_disease.attr('disease_id');
		expand = tr_disease.attr('expand');

		if (expand == 1) {
			// expand
			$('tr.dtemplate[disease_id=' + disease_id + ']').show();
			tr_disease.find('.btn-expand .fa').removeClass('fa-caret-up').addClass('fa-caret-down');
		}
		else {
			// collapse
			$('tr.dtemplate[disease_id=' + disease_id + ']').hide();
			tr_disease.find('.btn-expand .fa').addClass('fa-caret-up').removeClass('fa-caret-down');
		}
	}

	$('.btn-expand').click(function() {
		tr_disease = $(this).parents('tr.disease');
		expand = tr_disease.attr('expand');

		if (expand == 1)
			expand = 0; // collapse
		else 
			expand = 1; // expand

		tr_disease.attr('expand', expand);

		updateExpand(tr_disease);
	});

	$('.make-switch').on('switchChange.bootstrapSwitch', function() {
		tr_disease = $(this).parents('tr.disease');
		expand = $(this).prop('checked');

		if (expand)
			expand = 1; // expand
		else 
			expand = 0; // collapse
		
		tr_disease.attr('expand', expand);

		updateExpand(tr_disease);
	});

	// move
	$('tr.disease .btn-move-top,tr.disease .btn-move-up,tr.disease .btn-move-down,tr.disease .btn-move-bottom').click(function() {
		disease_id = $(this).parents('tr.disease').attr('disease_id');
		direct = $(this).attr('direct');

		App.callAPI("api/disease/move_to",
				{
					disease_id: disease_id,
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

	// move
	$('tr.dtemplate .btn-move-up,tr.dtemplate .btn-move-down').click(function() {
		dtemplate_id = $(this).parents('tr.dtemplate').attr('dtemplate_id');
		direct = $(this).attr('direct');

		App.callAPI("api/disease/move_to_dtemplate",
				{
					dtemplate_id: dtemplate_id,
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

	<?php 
	if ($this->expand==0) {
	?>
		$('tr.dtemplate').each(function() {
			updateExpand($(this));
		});
	<?php
	}
	?>

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
		if (mode == 'disease')
			$('#language_form [name="name_field"]').val('disease_name');
		else if (mode == 'dtemplate')
			$('#language_form [name="name_field"]').val('dtemplate_name');
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
		disease_name = $(this).attr('disease_name');
		if (disease_name != '' && disease_name != null) {
			disease_id = $(this).parents('tr[disease_id]').attr('disease_id');
			disease_name_l = $(this).attr('disease_name_l');
			showLangForm('disease', disease_id, disease_name, disease_name_l);
			return;
		}
		dtemplate_name = $(this).attr('dtemplate_name');
		if (dtemplate_name != '' && dtemplate_name != null) {
			dtemplate_id = $(this).parents('tr[dtemplate_id]').attr('dtemplate_id');
			dtemplate_name_l = $(this).attr('dtemplate_name_l');
			showLangForm('dtemplate', dtemplate_id, dtemplate_name, dtemplate_name_l);
			return;
		}
	});

	$('.btn-upload-template').click(function() {
		dtemplate_id = $(this).parents('tr[dtemplate_id]').attr('dtemplate_id');
		$('#upload_form [name="dtemplate_id"]').val(dtemplate_id);
		template_file = $(this).attr('template_file');
		if (template_file != '')
			template_file_l = JSON.parse(template_file);
		else
			template_file_l = {};
		<?php 
		foreach ($mLanguages as $key => $lang) {
			$lang_code = $lang["language_code"];
			?>
		$('#template_file_<?php p($lang_code); ?>').val(template_file_l['<?php p($lang_code); ?>']);
		refreshAttaches('template_file_<?php p($lang_code); ?>');
			<?php
		}
		?>

		$('#upload_form').modal('show');
	});

	$('#upload_form').ajaxForm({
        dataType : 'json',
        success: function(res, statusText, xhr, form) {
            try {
                if (res.err_code == 0)
                {
                    alertBox("<?php l('提示');?>", "<?php l('成功上传。');?>", function() {
                    	App.reload();
                    });

                    $('#upload_form').modal('hide');
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