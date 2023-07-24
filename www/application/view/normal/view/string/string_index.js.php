<script type="text/javascript">

$(function() {	
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
		if (mode == 'string')
			$('#language_form [name="name_field"]').val('string');
		else
			return;
		$('#language_form [name="id"]').val(id);
		name = name.replace(/\n/g, "<br/>");
		$('#language_form #string').html(name);
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
		string = $(this).attr('string');
		if (string != '' && string != null) {
			string_id = $(this).parents('tr[string_id]').attr('string_id');
			string_l = $(this).attr('string_l');
			showLangForm('string', string_id, string, string_l);
			return;
		}
	});
});
</script>