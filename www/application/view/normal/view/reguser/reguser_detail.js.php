<script type="text/javascript">
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
			lks += " </li>";
		}
	}
	$('#ul_' + upload_id).html(lks);
}

$(function() {
	$('#btn_accept').click(function() {
		confirmBox('<?php l('审核通过');?>', '<?php l('确定审核通过该用户？');?>', 
			function(reject_note) {
				App.callAPI("api/reguser/accept",
					{
						reguser_id: '<?php p($mReguser->reguser_id); ?>'
					}
				)
				.done(function(res) {
		        	alertBox('<?php l('审核通过');?>', '<?php l('审核通过成功。');?>', function() {
					<?php if($this->from == "ud") { ?>
						goto_url("user/doctors/?<?php p(CLEAR_REQSESS_KEY); ?>");
					<?php } ?>
					<?php if($this->from == "ui") { ?>
						goto_url("user/interpreters/?<?php p(CLEAR_REQSESS_KEY); ?>");
					<?php } ?>
		        		
		        	});
		        })
		        .fail(function(res) {
		        	errorBox("<?php l('错误发生');?>", res.err_msg);
		        });
		});

	});

	// reject form
    var reject_form = $('#reject_form').validate($.extend({
        rules : {
            reject_note: {
                required: true
            }
        },

        // Messages for form validation
        messages : {
            reject_note: {
                required: '<?php l('请输入未通过原因。');?>'
            }
        }
    }, getValidationRules()));

    $('#reject_form').on('submit', function() {
        if ($(this).valid())
            $(this).modal('hide');
    });
    $('#reject_form').ajaxForm({
        dataType : 'json',
        success: function(res, statusText, xhr, form) {
            try {
                if (res.err_code == 0)
                {
                    alertBox("<?php l('提示');?>", "<?php l('审核未通过成功。');?>", function() {
                    <?php if($this->from == "ud") { ?>
						goto_url("reguser/doctors/<?php p(RSTATUS_REJECT); ?>");
					<?php } ?>
					<?php if($this->from == "ui") { ?>
						goto_url("reguser/interpreters/<?php p(RSTATUS_REJECT); ?>");
					<?php } ?>
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

	$('#btn_reject').click(function() {
        $('#reject_form').modal('show');
	});

	refreshAttaches('diplomas');

	refreshAttaches('passports');
});
</script>