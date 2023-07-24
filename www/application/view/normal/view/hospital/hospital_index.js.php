<?php
$my_type = _my_type();
?>
<script type="text/javascript">
$(function() {	
	/********** hcountry *************/
	var form = $('#list_form').validate($.extend({
		rules : {
			hcountry_id: {
				required: true
			},
			country_name: {
				required: true
			},
			hospital_name: {
				required: true
			},
			address: {
				required: true
			}
		},

		// Messages for form validation
		messages : {
			hcountry_id: {
				required: '<?php l('请输入国家。');?>'
			},
			country_name: {
				required: '<?php l('请输入国家名称。');?>'
			},
			hospital_name: {
				required: '<?php l('请输入医院名称。');?>'
			},
			address: {
				required: '<?php l('请输入医院地址。');?>'
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
					cancel_edit_country();
					if ($('#list_form').attr('action') == "api/hcountry/save")
						msg = "<?php l('保存国家信息成功。');?>";
					else
						msg = "<?php l('保存医院信息成功。');?>";
					
					alertBox("<?php l('提示');?>", msg, function() {
						App.reload();
					});
					return;
				}
				else {
					errorBox("<?php l('错误发生');?>", res.err_msg);
					$('[type="submit"]').prop('disabled', false);
				}
			}
			finally {
			}
		}
	});

	function show_edit_country(hcountry_id)
	{
		cancel_edit_country();
		cancel_edit_hospital();

		$('#list_form').attr('action', "api/hcountry/save");

		$('#edit_country .td-no').text('+');
		$('#o_hcountry_id').val('');
		$('#hcountry_id').val('');
		$('#country_name').val('');

		if (hcountry_id != "") {
			// edit
			$('#o_hcountry_id').val(hcountry_id);
			App.callAPI("api/hcountry/get",{ hcountry_id: hcountry_id })
			.done(function(res) {
				$('#edit_country .td-no').text($('tr.hcountry[hcountry_id=' + hcountry_id + '] .td-no').text());
				$('#hcountry_id').val(res.hcountry.hcountry_id);
				$('#country_name').val(res.hcountry.country_name);
				$('#edit_country').insertAfter('tr.hcountry[hcountry_id=' + hcountry_id + ']');
				$('tr.hcountry[hcountry_id=' + hcountry_id + ']').hide();
				$('#edit_country').removeClass('hide');
				$('#country_name').focus();
	        })
	        .fail(function(res) {
	        	errorBox("<?php l('错误发生');?>", res.err_msg);
	        });
		}
		else {
			// insert
			$('#edit_country').prependTo('#country_list tbody');
			$('#edit_country').removeClass('hide');
			$('#country_name').focus();
		}
	}

	$('#insert_country').click(function() {
		show_edit_country('');
	});

	function cancel_edit_country()
	{
		org_country_id = $('#o_hcountry_id').val();
		if (org_country_id != "") {
			$('tr[hcountry_id=' + org_country_id + ']').show();
		}
		$('#edit_country').addClass('hide');
		form.resetForm();
	}

	$('#country_name').on('keydown', function(ev) {
		if (ev.keyCode === 27) { // escape
			cancel_edit_country();
		}
	});

	$('#edit_country .btn-cancel').click(cancel_edit_country);

	$('tr.hcountry .btn-edit').click(function() {
		show_edit_country($(this).parents('tr').attr('hcountry_id'));
	});
	<?php if ($my_type == UTYPE_SUPER) { ?>
	$('tr.hcountry .editable').dblclick(function() {
		show_edit_country($(this).parents('tr').attr('hcountry_id'));
	});
	<?php } ?>

	$('tr.hcountry .btn-delete').click(function() {
		hcountry_id = $(this).parents('tr').attr('hcountry_id');
		
		confirmBox('<?php l('删除');?>', '<?php l('是否确定删除该国家？');?>', function() {
			App.callAPI("api/hcountry/delete",
				{
					hcountry_id: hcountry_id
				}
			)
			.done(function(res) {
	        	alertBox('<?php l('删除成功');?>', '<?php l('删除国家成功。');?>', function() {
	        		App.reload();
	        	});
	        })
	        .fail(function(res) {
	        	errorBox("<?php l('错误发生');?>", res.err_msg);
	        });
		});
	});

	function updateExpand(tr_country) {
		hcountry_id = tr_country.attr('hcountry_id');
		expand = tr_country.attr('expand');

		if (expand == 1) {
			// expand
			$('tr.hospital[hcountry_id=' + hcountry_id + ']').show();
			tr_country.find('.btn-expand .fa').removeClass('fa-caret-up').addClass('fa-caret-down');
		}
		else {
			// collapse
			$('tr.hospital[hcountry_id=' + hcountry_id + ']').hide();
			tr_country.find('.btn-expand .fa').addClass('fa-caret-up').removeClass('fa-caret-down');
		}
	}

	$('.btn-expand').click(function() {
		tr_country = $(this).parents('tr.hcountry');
		expand = tr_country.attr('expand');

		if (expand == 1)
			expand = 0; // collapse
		else 
			expand = 1; // expand

		tr_country.attr('expand', expand);

		updateExpand(tr_country);
	});

	$('.make-switch').on('switchChange.bootstrapSwitch', function() {
		tr_country = $(this).parents('tr.hcountry');
		expand = $(this).prop('checked');

		if (expand)
			expand = 1; // expand
		else 
			expand = 0; // collapse
		
		tr_country.attr('expand', expand);

		updateExpand(tr_country);

		/*
		hcountry_id = $(this).parents('tr.hcountry').attr('hcountry_id');
		expand_flag = $(this).prop('checked');

		if (h_expand_flag) {
			$('.make-switch').each(function() {
				o_country_id = $(this).parents('tr.hcountry').attr('hcountry_id');
				if (hcountry_id != o_country_id) {
					console.log(o_country_id);
					$(this).prop('checked', false).trigger("change.bootstrapSwitch", true);
				}
			});
		}

		App.callAPI("api/hcountry/set_h_expand_flag",
				{
					hcountry_id: hcountry_id,
					h_expand_flag: h_expand_flag
				}
			)
	        .fail(function(res) {
	        	errorBox("<?php l('错误发生');?>", res.err_msg);
	        });
	    */
	});

	// move
	$('tr.hcountry .btn-move-top,tr.hcountry .btn-move-up,tr.hcountry .btn-move-down,tr.hcountry .btn-move-bottom').click(function() {
		hcountry_id = $(this).parents('tr.hcountry').attr('hcountry_id');
		direct = $(this).attr('direct');

		App.callAPI("api/hcountry/move_to",
				{
					hcountry_id: hcountry_id,
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
		$('tr.hcountry').each(function() {
			updateExpand($(this));
		});
	<?php
	}
	?>

	/********** hospital *************/
	$('.insert-hospital').click(function() {
		hcountry_id = $(this).parents('tr').attr('hcountry_id');
		show_edit_hospital('', hcountry_id);
	});

	function show_edit_hospital(hospital_id, hcountry_id, focus_control)
	{
		cancel_edit_hospital();
		cancel_edit_country();

		$('#list_form').attr('action', "api/hospital/save");

		$('#edit_hospital .td-no').text('+');
		$('#hospital_id').val('');
		$('#hcountry_id').val('');
		$('#hospital_name').val('');
		$('#address').val('');

		if (hospital_id != "") {
			// edit
			App.callAPI("api/hospital/get",{ hospital_id: hospital_id })
			.done(function(res) {
				$('#edit_hospital .td-no').text("");
				$('#hospital_id').val(res.hospital.hospital_id);
				$('#hcountry_id').val(res.hospital.hcountry_id);
				$('#hospital_name').val(res.hospital.hospital_name);
				$('#address').val(res.hospital.address);
				$('#edit_hospital').insertAfter('tr.hospital[hospital_id=' + hospital_id + ']');
				$('tr.hospital[hospital_id=' + hospital_id + ']').hide();
				$('#edit_hospital').removeClass('hide');
				if (focus_control == "address")
					$('#address').focus();
				else
					$('#hospital_name').focus();
	        })
	        .fail(function(res) {
	        	errorBox("<?php l('错误发生');?>", res.err_msg);
	        });
		}
		else {
			// insert
			$('#edit_hospital').insertAfter('tr.hcountry[hcountry_id=' + hcountry_id + ']');
			$('#hcountry_id').val(hcountry_id);
			$('#edit_hospital').removeClass('hide');
			$('#hospital_name').focus();
		}
	}

	function cancel_edit_hospital()
	{
		org_hospital_id = $('#hospital_id').val();
		if (org_hospital_id != "") {
			$('tr[hospital_id=' + org_hospital_id + ']').show();
		}
		$('#edit_hospital').addClass('hide');
		form.resetForm();
	}

	$('#hospital_name,#address').on('keydown', function(ev) {
		if (ev.keyCode === 27) { // escape
			cancel_edit_hospital();
		}
	});

	$('#edit_hospital .btn-cancel').click(cancel_edit_hospital);

	$('tr.hospital .btn-edit').click(function() {
		show_edit_hospital($(this).parents('tr').attr('hospital_id'));
	});
	<?php if (_has_priv(CODE_PRIV_HOSPITALS, PRIV_EDIT)) { ?>
	$('tr.hospital .editable').dblclick(function() {
		show_edit_hospital($(this).parents('tr').attr('hospital_id'), null, $(this).attr("for"));
	});
	<?php } ?>

	$('tr.hospital .btn-delete').click(function() {
		hospital_id = $(this).parents('tr').attr('hospital_id');
		
		confirmBox('<?php l('删除');?>', '<?php l('是否确定删除该医院？');?>', function() {
			App.callAPI("api/hospital/delete",
				{
					hospital_id: hospital_id
				}
			)
			.done(function(res) {
	        	alertBox('<?php l('删除成功');?>', '<?php l('删除医院成功。');?>', function() {
	        		App.reload();
	        	});
	        })
	        .fail(function(res) {
	        	errorBox("<?php l('错误发生');?>", res.err_msg);
	        });
		});
	});

	// move
	$('tr.hospital .btn-move-top,tr.hospital .btn-move-up,tr.hospital .btn-move-down,tr.hospital .btn-move-bottom').click(function() {
		hospital_id = $(this).parents('tr.hospital').attr('hospital_id');
		direct = $(this).attr('direct');

		App.callAPI("api/hospital/move_to",
				{
					hospital_id: hospital_id,
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
		if (mode == 'hospital')
		{
			$('#language_form').attr('action', 'api/hospital/save_name');
			$('#language_form [name="name_field"]').val('hospital_name');
		}
		else if (mode == 'hcountry') 
		{
			$('#language_form').attr('action', 'api/hcountry/save_name');
			$('#language_form [name="name_field"]').val('country_name');
		}
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
		hospital_name = $(this).attr('hospital_name');
		if (hospital_name != '' && hospital_name != null) {
			hospital_id = $(this).parents('tr.hospital').attr('hospital_id');
			hospital_name_l = $(this).attr('hospital_name_l');
			showLangForm('hospital', hospital_id, hospital_name, hospital_name_l);
			return;
		}
		country_name = $(this).attr('country_name');
		if (country_name != '' && country_name != null) {
			hcountry_id = $(this).parents('tr.hcountry').attr('hcountry_id');
			country_name_l = $(this).attr('country_name_l');
			showLangForm('hcountry', hcountry_id, country_name, country_name_l);
			return;
		}
	});
});
</script>