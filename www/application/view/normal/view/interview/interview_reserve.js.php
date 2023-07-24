<?php $my_type = _my_type(); ?>
<script type="text/javascript">
	
$(function() {
	var $form = $('#form').validate($.extend({
		rules : {
            <?php if($my_type != UTYPE_PATIENT) {?>
            patient_id: {
                required: true
            },
            <?php } ?>
			chistory_id: {
				required: true
			},
			planguage_id: {
				required: true
			}
		},

		// Messages for form validation
		messages : {
            <?php if($my_type != UTYPE_PATIENT) {?>
            patient_id: {
                required: "<?php l("请选择患者"); ?>"
            },
            <?php } ?>
			chistory_id: {
				required: "<?php l("请选择病历"); ?>"
			},
			planguage_id: {
				required: "<?php l("请选择精通语言"); ?>"
			}
		}
	}, getValidationRules()));

	$('#form').ajaxForm({
		dataType : 'json',
        beforeSubmit: function() {
            $('.button-submit').prop('disabled', true);
        },
		success: function(res, statusText, xhr, form) {
			try {
				if (res.err_code == 0)
				{
					modalAlert("<?php l('提示');?>", '<span class="text-danger"><?php l('您的预约尚未完成！');?></span><br><?php l('请在<span class=text-danger>%d</span>分钟内完成网上支付，否则订单将自动关闭。', INTERVIEW_PAY_LIMIT); ?>', "<?php l('确定');?>", function() {
						goto_url("interview/");
					});
					return;
				}
				else {
					hideMask();
                    $('.button-submit').prop('disabled', false);
        			errorBox("<?php l('错误发生');?>", res.err_msg);
				}
			}
			finally {
			}
		}
	});

	var handleTab = function(tab, navigation, index) {
        var total = navigation.find('li').length - 1;

        // set done steps
        jQuery('li', $('#form')).removeClass("done");
        var li_list = navigation.find('li');
        for (var i = 0; i < index; i++) {
            jQuery(li_list[i]).addClass("done");
        }

        if (index == 0) {
            $('#form').find('.button-previous').hide();
        } else {
            $('#form').find('.button-previous').show();
        }

        switch(index) {
        	case 2:
				$('#planguage_name').text($("#planguage_id option:selected").text());
        		break;
        	case 3:
				$('#patient_name2').text($('#patient_name').text());
        		need = $('[name="need_interpreter"]:checked').val();
                d_cost = $('#d_cost').val() * 1;
                i_cost = $('#i_cost').val() * 1;
				if (need == 1) {
					$('#need_interpreter_label').text("<?php p(_code_label(CODE_NEED, NEED)); ?>");
                    cost = d_cost;
                    if (cost < 0)
                        cost = 0;
                }
				else {
					$('#need_interpreter_label').text("<?php p(_code_label(CODE_NEED, UNNEED)); ?>");
                    cost = d_cost - i_cost;
                }
                ex_rate = $('#ex_rate').val();
                $('#cost').text(numberFormat(cost));
                ex_cost = roundNumber(cost * ex_rate, 2);
                $('#ex_cost').text(numberFormat(ex_cost));
        		break;
        }

        if (index >= total) {
            $('#form').find('.button-next').hide();
            $('#form').find('.button-submit').show();

        } else {
            $('#form').find('.button-next').show();
            $('#form').find('.button-submit').hide();
        }
    }

    handleTab(null, $('.steps'), 0);

    // default form wizard
    $('#form').bootstrapWizard({
        'tabClass': 'steps',
        'nextSelector': '.button-next',
        'previousSelector': '.button-previous',
        onTabClick: function (tab, navigation, index, clickedIndex) {
            var li_list = navigation.find('li');
            if ($('#form').valid() == false) {
                return false;
            }
            if (!jQuery(li_list[clickedIndex]).hasClass("done") && 
                index + 1 != clickedIndex) {
                return false;
            }
            return handleTab(tab, navigation, clickedIndex);
        },
        onNext: function (tab, navigation, index) {
            if ($('#form').valid() == false) {
                 return false;
            }

            return handleTab(tab, navigation, index);
        },
        onPrevious: function (tab, navigation, index) {
            handleTab(tab, navigation, index);
        },
        onTabShow: function (tab, navigation, index) {
            
        }
    });

    $('#form').find('.button-previous').hide();
    $('#form .button-submit').hide();

    $('.button-submit').click( function() {
    	showMask('');
        $('#form').submit();
    });

    // step 0 
    function check_valid(step)
    {
    	valid = false;
    	if (step == 0) {
    		valid = ($('#reserved_starttime').val() != '' &&
        		$('#reserved_endtime').val() != '');

        	if (valid)
        		$('.button-next').removeAttr('disabled');
        	else
        		$('.button-next').attr('disabled', 'disabled');
    	}

    	return valid;
    }

    var interview_date = null;
    function onChangeDatepicker() {
        date = $('#sel_date').datepicker('getFormattedDate');
        d = $('#sel_date').datepicker('getDate');
        weekday = d.getDay();

        interview_date = date;
        $('#reserved_starttime').val('');
        $('#reserved_endtime').val('');
        check_valid(0);

        refreshTable(date);
    }
	$('#sel_date').datepicker({
		format: 'yyyy-mm-dd',
        language: '<?php p(_lang()); ?>',
		todayHighlight: true,
        startDate: '<?php p(_date($mInterview->startDate));?>',
        weekStart: 0
	}).on('changeDate', function() {
        onChangeDatepicker();
	});

    onChangeDatepicker();

	function refreshTable(date) {
        if (date == "") {
            // clear
            $('#sel_dtimes ul.dtime-list').html('');
            $('#reserved_starttime').val('');
            $('#reserved_endtime').val('');
            return;
        }

		App.callAPI("api/dtime/list",
			{
				date: date,
				doctor_id: $('#doctor_id').val(),
                reserve: 1
			}
		)
		.done(function(res) {
			sel_dtimes = res.sel_dtimes;
			$('#sel_dtimes ul.dtime-list').html('');
			for (i = 0; i < sel_dtimes.length; i ++) {
				d = sel_dtimes[i];
				label = "<span class='state-" + d.state + "'>";
                clickable = "";
				switch(d.state) {
					case 1:
						label += "<?php l("可预约"); ?>";
                        clickable = "clickable";
						break;
                    case 3:
                        label += "<?php l("不可预约"); ?>";
                        break;
                    default:
                        continue;
				}
				label += "</span>";
				$('#sel_dtimes ul.dtime-list')
					.append('<li class="' + clickable + '" start_time="' + d.start_time + 
						'" end_time="' + d.end_time + '" start_time_label="' + d.start_time_label + 
                        '" end_time_label="' + d.end_time_label + '"><p>' + 
                        d.start_time_label + '-' + d.end_time_label + 
                        '</p>' + label + '</li>');
			}
			
			$('#sel_dtimes ul.dtime-list li.clickable').click(function() {
				checked = $(this).attr('check');
				if (checked != 1) {
					$('#sel_dtimes ul.dtime-list li').attr('check', 0);
					$(this).attr('check', 1);

			        $('#reserved_starttime').val($(this).attr('start_time'));
			        $('#reserved_endtime').val($(this).attr('end_time'));

			        interview_datetimes = interview_date + " "+ $(this).attr('start_time_label') + "-" + $(this).attr('end_time_label');
			        $('#interview_datetimes').text(interview_datetimes);
				}

				check_valid(0);
			});
        })
        .fail(function(res) {
        	errorBox("<?php l('错误发生');?>", res.err_msg);
        });
	}

	check_valid(0);

	// step 1
    <?php if($my_type != UTYPE_PATIENT) {?>
    $('#patient_id').select2({
        placeholder: "<?php l("请选择"); ?>",
        minimumInputLength: 0,
        ajax: { 
            url: "api/user/select2",
            dataType: 'json',
            data: function (term, page) {
                return {
                    query: term,
                    user_type: <?php p(UTYPE_PATIENT); ?>
                };
            },
            results: function (data, page) { 
                return {
                    results: data.options
                };
            }
        },
        initSelection: function (element, callback) {
            var id = $(element).val();
            if (id !== "") {
                callback({
                    id: id,
                    text: $(element).attr('text')
                });
            }
        }
    }).change(function() {
        $(this).valid();
        $('#chistory_lang').removeClass('hide');
        $('#chistory_id').select2('val', '');
        $('#patient_name').text('');
        $('#patient_sex_name').text('');
        $('#patient_birthday').text('');
        $('#disease_name').text('');
        $('#disease_id').text('');
    });
    <?php } ?>

    $('#planguage_id').selectpicker()
        .on('change', function() { $(this).valid();});
	$('#chistory_id').select2({
        placeholder: "<?php l("请选择"); ?>",
        minimumInputLength: 0,
        ajax: { 
            url: "api/chistory/select2",
            dataType: 'json',
            data: function (term, page) {
                return {
                    query: term,
                    user_id: $('#patient_id').val()
                };
            },
            results: function (data, page) { 
                return {
                    results: data.options
                };
            }
        },
        initSelection: function (element, callback) {
            var id = $(element).val();
            if (id !== "") {
                callback({
                	id: id,
                	text: $(element).attr('text')
                });
            }
        }
    }).change(function() {
        $(this).valid();
    	App.callAPI("api/chistory/get",
			{
				chistory_id: $('#chistory_id').val()
			}
		)
		.done(function(res) {
			chistory = res.chistory;
			$('#patient_name').text(chistory.patient_name);
			$('#patient_sex_name').text(chistory.patient_sex_name);
			$('#patient_birthday').text(chistory.birthday);
			$('#disease_name').text(chistory.disease_name);
			$('#disease_id').text(chistory.disease_id);
        })
        .fail(function(res) {
        	errorBox("<?php l('错误发生');?>", res.err_msg);
        });
	});
});
</script>