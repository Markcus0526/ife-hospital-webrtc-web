<?php $my_type = _my_type(); ?>
<script type="text/javascript">
	
$(function() {
	$('#form').ajaxForm({
		dataType : 'json',
        beforeSubmit: function() {
            $('.button-submit').prop('disabled', true);
        },
		success: function(res, statusText, xhr, form) {
			try {
				if (res.err_code == 0)
				{
					modalAlert("<?php l('提示');?>", "<?php l('您已重新预约成功，无需再次支付！');?>", "<?php l('确定');?>", function() {
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
        	case 1:
        		need = $('#need_interpreter').val();
                d_cost = $('#d_cost').val() * 1;
                i_cost = $('#i_cost').val() * 1;
				if (need == 1) {
                    cost = d_cost;
                    if (cost < 0)
                        cost = 0;
                }
				else {
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
});
</script>