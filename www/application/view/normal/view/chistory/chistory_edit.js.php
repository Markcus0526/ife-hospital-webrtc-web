<script type="text/javascript">
function onBoothComplete(img_path) {
	$('#img_cavartar').attr('src', img_path);
	$('#cavartar').val(img_path);
	if ($('#cavartar-error').length > 0)
		$("#form").validate().element( "#cavartar" );
}

function onUploadComplete(files, upload_id, upload_type)
{
	if (upload_id == 'cavartar') {
		var files = files.split(';');
		if (files.length) {
			var fs = files[0].split(':');
			$('#img_cavartar').attr('src', fs[0]);
			$('#cavartar').val(fs[0]);
			$("#form").validate().element( "#cavartar" );
		}
	}
	else {
		attaches = $('#' + upload_id).val();
		if (attaches != "") attaches += ";";
		$('#' + upload_id).val(attaches + files);
		$("#form").validate().element( "#" + upload_id);

		refreshAttaches(upload_id);
	}
}

function refreshAttaches(upload_id)
{
	var lks = "";
	var attaches = $('#' + upload_id).val();
	if (attaches != "" && attaches != undefined)
	{
		var as = attaches.split(';');
		for (var i = 0; i < as.length; i ++)
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
	var form = $('#form').validate($.extend({
		ignore: '',
		rules : {
			chistory_name: {
				required: true
			},
			patient_name: {
				required: true
			},
			patient_sex: {
				required: true
			},
			birthday: {
				required: true
			},
			<?php if (!$mChistory->trans_flag) { ?>
			home_address: {
				required: true
			},
			passports: {
				required: true
			},
			<?php } ?>
			cavartar: {
				required: true
			},
			disease_id: {
				required: true
			},
			want_resolve_problem: {
				required: true
			},
			sensitive_medicine: {
				required: true
			},
			smoking_drinking: {
				required: true
			},
			chronic_disease: {
				required: true
			},
			family_disease: {
				required: true
			}
		},

		// Messages for form validation
		messages : {
			chistory_name: {
				required: '<?php l('请输入病历名称。');?>'
			},
			patient_name: {
				required: '<?php l('请输入姓名。');?>'
			},
			patient_sex: {
				required: '<?php l('请选择性别。');?>'
			},
			birthday: {
				required: '<?php l('请输入出生日期。');?>'
			},
			<?php if (!$mChistory->trans_flag) { ?>
			home_address: {
				required: '<?php l('请输入家庭住址。');?>'
			},
			passports: {
				required: '<?php l('请上传身份证件。');?>'
			},
			<?php } ?>
			cavartar: {
				required: '<?php l('请拍摄头像。');?>'
			},
			disease_id: {
				required: '<?php l('请选择疾病种类。');?>'
			},
			want_resolve_problem: {
				required: '<?php l('请输入内容。');?>'
			},
			sensitive_medicine: {
				required: '<?php l('请输入内容。');?>'
			},
			smoking_drinking: {
				required: '<?php l('请输入内容。');?>'
			},
			chronic_disease: {
				required: '<?php l('请输入内容。');?>'
			},
			family_disease: {
				required: '<?php l('请输入内容。');?>'
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
					alertBox("<?php l('提示');?>", "<?php l('保存病历信息成功。');?>", function() {
						<?php if (_my_type() == UTYPE_INTERPRETER) { ?>
						goto_url("interview");
						<?php } else { ?>
						goto_url("chistory/index/<?php p($this->patient_id); ?>");
						<?php } ?>
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

	// init attach
	function refreshCattaches(cattaches)
	{
		var hrow = null;
		$('.cattaches-container').html('');
		if (cattaches.length > 0) {
			for (var i = 0; i < cattaches.length; i ++)
			{
				cattach = cattaches[i];
				files_id = "cattach_files_" + i;
				c = $('#cattach_template > div').clone();
				c.find('.control-label').html(cattach.dtemplate_name + ' <span class="required">*</span> :');
				c.find('input.cattach-files')
					.attr("id", files_id)
					.attr("name", files_id)
					.val(cattach.files);
				c.find('.attach-list').attr('id', 'ul_' + files_id);
				c.find('.btn-upload').attr('href', 'common/upload/' + files_id);

				c.find('input.cattach-id')
					.attr("name", "cattach_id_" + i)
					.val(cattach.cattach_id);
				c.find('input.dtemplate-id')
					.attr("name", "dtemplate_id_" + i)
					.val(cattach.dtemplate_id);

				html = "";
				template_file = cattach.template_file;
				if (template_file !== null && template_file !== "") {
					ts = template_file.split(';');
					for (var j = 0; j < ts.length; j ++)
					{
						fs = ts[j].split(':');
						down_url = "common/down/" + fs[0] + "/" + fs[1];
						html += "<a href='" + down_url + "' class='active'><?php l("下载模板"); ?></a> "
					}
				}
				c.find('.down-dtemplate').html(html);

				if (i % 2 == 0)
					hrow = $('<div class="row chistory-attach-row"></div>');

				hrow.append(c);

				if (i % 2 == 1) {
					$('.cattaches-container').append(hrow);
					hrow = null;
				}
			}

			$('.cattaches-container')
				.find('input.cattach-files')
				.each(function() {
					$(this).rules('add', { required: true, 
						messages: { required: '<?php l("请上传文件。"); ?>'}});	
				});

			if (hrow) {
				$('.cattaches-container').append(hrow);
			}

			App.initFancybox(".cattaches-container");
			
			for (i = 0; i < cattaches.length; i ++)
			{
				files_id = "cattach_files_" + i;
				refreshAttaches(files_id);
			}
		}
		else {
			msg = $('#cattach_template .alert-sel-disease').clone();
			$('.cattaches-container').append(msg);
			$('#sel_disease').unbind('click')
				.bind('click', function() {
					setTimeout(function () {
				        $('.group-disease .bootstrap-select button').trigger('click');
				        $('#row_select_disease').scroll();
				    }, 100);
					
				});
		}
	}

    $('#disease_id').selectpicker()
    	.on('change', function() { 
    		$(this).valid();
    		chistory_id = $('#chistory_id').val();
    		disease_id = $('#disease_id').val();

			App.callAPI("api/chistory/get_cattaches",
				{
					chistory_id: chistory_id,
					disease_id: disease_id
				}
			)
			.done(function(res) {
				cattaches = res.cattaches;

				refreshCattaches(cattaches);
			})
	        .fail(function(res) {
	        	errorBox("<?php l('错误发生');?>", res.err_msg);
	        });

    	});

    refreshAttaches("passports");
    refreshCattaches(<?php p(_json_encode($mChistory->cattaches)); ?>);    

    function resizeLayout()
    {
    	profile = $('#patient_profile');
    	h = profile.height();
    	profile.find('.photo-booth').parent().height(h);
    }
    resizeLayout();

	$('#guide_link').click(function() {
		$('#guide_view').modal('show');
	});

	$('#btn_save').click(function() {
		if ($('#form').valid())
		{
			<?php if ($mChistory->trans_flag) { ?>
				confirmBox('<?php l('提交');?>', '<?php l('确定提交该翻译版病历？提交后无法再修改。');?>', 
					function() {
						// 提交
						$('#post_flag').val(1);
						$('#form').submit();
					},
					function() {
						// 保存
						$('#post_flag').val(0);
						$('#form').submit();
					}
				);
			<?php } else { ?>
				confirmBox('<?php l('提交');?>', '<?php l('确定提交该病历？');?>', 
					function() {
						// 提交
						$('#form').submit();
					},
					function() {
					}
				);
			<?php } ?>
		}
	});
});

</script>