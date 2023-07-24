<script type="text/javascript">
	
$(function() {
	var date = null, start_date = null, clickable_start_date = null;
	var s_date = null, e_date = null, first_change = true; 
	var interview_id = $('#interview_id').val();
	var form = $('#form').validate($.extend({
		rules : {
			doctor_sign: {
				required: true
			}
		},

		// Messages for form validation
		messages : {
			doctor_sign: {
				required: '<?php l('请输入');?>'
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
                    alertBox("<?php l('提示');?>", "<?php l('成功更改时间。');?>", function() {
                        goto_url("interview");
                    });
					return;
				}
				else {
					hideMask();
					$('[type="submit"]').prop('disabled', false);
        			errorBox("<?php l('错误发生');?>", res.err_msg);
				}
			}
			finally {
			}
		}
	});

    // step 0 
    function check_valid(alert)
    {
    	var c = $('.week-time-table .time[check=1]');

		valid = c.length > 0;

		if (valid) {
			se = get_start_end_time(c);
			date = se.starttime.substring(0, 10);
			$('#sel_date').datepicker('setDate', date);
			$('#reserved_starttime').val(se.starttime);
			$('#reserved_endtime').val(se.endtime);
    		$('.button-submit').removeAttr('disabled');
    		if (alert) {
    			alertBox("<?php l('提示');?>", "<?php l('请取消已选时间段');?>");
    		}
		}
		else {
			$('#reserved_starttime').val('');
			$('#reserved_endtime').val('');
    		$('.button-submit').attr('disabled', 'disabled');
		}

    	return valid;
    }

    function get_start_end_time(cell)
    {
		d = cell.attr('d');
		h = parseInt(cell.attr('h'));

    	return {
    		starttime: d + ' ' + h + ':00:00',
    		endtime: d + ' ' + (h + 1) + ':00:00'
    	}
    }

    function cancel_check()
    {
    	var c = $('.week-time-table .time[check=1]');

    	o_state = c.attr('o_state');
    	if (o_state == undefined)
    		o_state = 0;
		c.attr('state', o_state)
			.attr('o_state', 0)
			.attr('check', 0);
		first_change = false;
    }

    function onChangeDate(init) {
        var new_date = $('#sel_date').datepicker('getFormattedDate');
        if (date != new_date) {
        	date = new_date;
        	if (init == undefined) {
	        	$('#reserved_starttime').val('');
				$('#reserved_endtime').val('');
				cancel_check();
        	}
        	refreshTable();	
        }
    }

	$('#sel_date').datepicker({
		format: 'yyyy-mm-dd',
        language: '<?php p(_lang()); ?>',
		todayHighlight: true,
        startDate: '<?php p(_time_add(null, INTERVIEW_DOCTOR_CHANGABLE_TIME_AFTER_NOW, 'Y-m-d'));?>',
        weekStart: 0
	}).on('changeDate', function() { onChangeDate(); });

	function refreshTable() {
		if (!(s_date == null || date < s_date || date > e_date)) {
			refreshRightList();
        	check_valid();
			return;
		}
		App.callAPI("api/dtime/list",
			{
				date: date,
				change_reserve: 1
			}
		)
		.done(function(res) {
			start_date = res.start_date;
			clickable_start_date = res.clickable_start_date;
			s_date = res.start_date.substring(0, 10);
			e_date = res.end_date.substring(0, 10);
			ds = res.week_start.split('-');
			dt = new Date();
			dt.setFullYear(ds[0]);
			dt.setMonth(ds[1] - 1);
			dt.setDate(ds[2]);
			for (w = 0; w < 7; w ++) {
				var d = dt.getFullYear() + "-" + 
					zeroPad(dt.getMonth() + 1) + "-" + 
					zeroPad(dt.getDate());
				$('.week-time-table .time[w='+w+']').attr('d', d);
				dt.setDate(dt.getDate() + 1); // next date
			}
			
			$('.week-time-table .time').attr("state", '').attr('check', 0);

			dtimes = res.dtimes;
			for (d = 0; d < dtimes.length; d ++) {
				dtime = dtimes[d];
				w = dtime.weekday;
				h = dtime.start_hour;
				past = dtime.past;

				var cell = $('.week-time-table .time[w='+ w+'][h='+h+']');
				cell.attr('state', dtime.state).attr('check', 0).attr('past', past);
				if (dtime.o_state)
					cell.attr('o_state', dtime.o_state);
			}

			$('.week-time-table .time')
				.each(function() {
					d = $(this).attr('d');
					h = parseInt($(this).attr('h'));
					$(this).attr('title', d + " " + h + ":00-" + (h+1) + ":00");
					t = d + " " + zeroPad(h) + ":00:00";
					if (t >= clickable_start_date)
						$(this).addClass('clickable');
					else
						$(this).removeClass('clickable');
				})
				.unbind('click')
				.bind('click', function() {
					if (!$(this).hasClass('clickable'))
						return;
					d = $(this).attr('d');
					w = $(this).attr('w');
					h = $(this).attr('h');
					state = parseInt($(this).attr('state'));
					check = parseInt($(this).attr('check'));

					switch(state) {
						case 1: // reservable
							if (first_change || !check_valid(true)) {
								cancel_check();
								$(this).attr('state', 2)
									.attr('o_state', 1)
									.attr('check', 1);
							}
							break;
						case 2: // reserved
							if (check == 1) {
								if (check_valid()) {
									cancel_check();
								}
							}
							break;
						default:
							if (first_change || !check_valid(true)) {
								cancel_check();
								$(this).attr('state', 2)
									.attr('o_state', 0)
									.attr('check', 1);
							}
							break;
					}

					check_valid();
					refreshRightList();
				});

			refreshRightList();
			check_valid();
        })
        .fail(function(res) {
        	errorBox("<?php l('错误发生');?>", res.err_msg);
        });
	}

	function refreshRightList() {
		d = $('#sel_date').datepicker('getDate');
        weekday = d.getDay();
        $('.week-time-table .th-date').html('');
        $('.week-time-table .th-date[w=' + weekday + ']').html(date);

		$('#sel_dtimes ul.dtime-list').html('');
		$('.week-time-table .time[d="' + date + '"]')
			.each(function() {
				state = parseInt($(this).attr('state'));
				check = $(this).attr('check');
				h = parseInt($(this).attr('h'));
				if (state > 0) {
					label = "<span class='state-" + state + "'>";
					switch(state) {
						case 1:
							label += "<?php l("可预约"); ?>";
							break;
						case 2:
							label += "<?php l("已预约"); ?>";
							break;
	                    case 3:
	                        label += "<?php l("不可预约"); ?>";
	                        break;
	                    default:
	                        return;
					}
					label += "</span>";
					$('#sel_dtimes ul.dtime-list')
						.append('<li state="' + state +
	                        '" check="' + check + 
	                        '"><p>' + 
	                        h + ':00-' + (h + 1) + ':00' +
	                        '</p>' + label + '</li>');
				}
			})
	}

    onChangeDate(true);

});
</script>