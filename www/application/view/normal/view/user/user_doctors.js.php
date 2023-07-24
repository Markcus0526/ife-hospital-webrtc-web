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
		
		confirmBox('<?php l('删除');?>', '<?php l('是否确定删除该专家？');?>', function() {
			App.callAPI("api/user/delete",
				{
					user_id: user_id
				}
			)
			.done(function(res) {
	        	alertBox('<?php l('删除成功');?>', '<?php l('删除专家成功。');?>', function() {
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

    <?php
    if (_has_priv(CODE_PRIV_DOCTORS, PRIV_SET_FEE)) {
    ?>   
    var cost_form = $('#cost_form').validate($.extend({
        rules : {
            d_fee_usd: {
                required: function() {
                    return $('[name="d_cunit"][value="usd"]').prop('checked');
                },
                number:  function() {
                    return $('[name="d_cunit"][value="usd"]').prop('checked');
                },
                min: 0
            },
            d_fee_rmb: {
                required:  function() {
                    return $('[name="d_cunit"][value="rmb"]').prop('checked');
                },
                number:  function() {
                    return $('[name="d_cunit"][value="rmb"]').prop('checked');
                },
                min: 0
            }
        },

        // Messages for form validation
        messages : {
            d_fee_usd: {
                required: '<?php l('请输入会诊费。');?>',
                number: '<?php l('请输入有效数字。');?>',
                min: '<?php l('请输入0元以上的数值。');?>',
            },
            d_fee_rmb: {
                required: '<?php l('请输入会诊费。');?>',
                number: '<?php l('请输入有效数字。');?>',
                min: '<?php l('请输入0元以上的数值。');?>',
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
                    alertBox("<?php l('提示');?>", "<?php l('成功更改会诊费。');?>", function() {
                    	App.reload();
                    });

                    $('#cost_form').modal('hide');
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

    var showHideDfee = function(d_cunit) {
        $('#cost_form [name="d_fee_usd"]').hide();
        $('#cost_form [name="d_fee_rmb"]').hide();

        if (d_cunit == 'usd')
            $('#cost_form [name="d_fee_usd"]').show();
        if (d_cunit == 'rmb')
            $('#cost_form [name="d_fee_rmb"]').show();
    }

	$('.btn-change-fee').click(function() {
		tr = $(this).parents('tr[user_id]');
		$('#cost_form [name="user_id"]').val(tr.attr('user_id'));
        d_cunit = tr.attr('d_cunit');
        $('[name="d_cunit"]').prop('checked', false);
        $('[name="d_cunit"][value="' + d_cunit + '"]').prop('checked', true);
		d_fee = toFloat(tr.attr('d_fee'));
		if (d_fee == '')
			d_fee = 0;
        $('#cost_form [name="d_fee_usd"]').val('');
        $('#cost_form [name="d_fee_rmb"]').val('');

        if (d_cunit == 'usd')
		  $('#cost_form [name="d_fee_usd"]').val(d_fee);
        else if (d_cunit == 'rmb')
          $('#cost_form [name="d_fee_rmb"]').val(d_fee);

        showHideDfee(d_cunit);

        $('#cost_form').valid();
        $('#cost_form .has-error').removeClass('has-error');
		$('#cost_form').modal('show');
	});
    // $('[name="d_fee_usd"]').keypress(function() {
    //     $('[name="d_cunit"][value="usd"]').prop('checked', true);

    // });
    // $('[name="d_fee_rmb"]').keypress(function() {
    //     $('[name="d_cunit"][value="rmb"]').prop('checked', true);
    // });
    $('[name="d_cunit"]').click(function() {
        d_cunit = $('[name="d_cunit"]:checked').val();
        showHideDfee(d_cunit);
    });

    <?php
    }
    ?>
});
</script>