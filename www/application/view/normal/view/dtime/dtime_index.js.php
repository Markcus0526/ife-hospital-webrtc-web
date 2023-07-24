<script type="text/javascript">
	
$(function() {
	var date = '<?php p($this->date); ?>', start_date = null;

    function onChangeDate() {
        var new_date = $('#sel_date').datepicker('getFormattedDate');
        if (date != new_date) {
        	date = new_date;
        	refreshTable();	
        }
    }

	$('#sel_date').datepicker({
		format: 'yyyy-mm-dd',
        language: '<?php p(_lang()); ?>',
		todayHighlight: true,
        startDate: '<?php p(_time_add(null, INTERVIEW_DOCTOR_SETTABLE_TIME_AFTER_NOW, 'Y-m-d'));?>',
        weekStart: 0
	}).on('changeDate', onChangeDate);

	function refreshTable() {
		App.callAPI("api/dtime/list?brtt=" + (new Date),
			{
				date: date
			}
		)
		.done(function(res) {
			start_date = res.start_date;
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

			$('.week-time-table .time').attr("state", '');

			dtimes = res.dtimes;
			for (d = 0; d < dtimes.length; d ++) {
				dtime = dtimes[d];
				w = dtime.weekday;
				h = dtime.start_hour;
				$('.week-time-table .time[w='+ w+'][h='+h+']').attr('state', dtime.state);
			}

			$('.week-time-table .time')
				.each(function() {
					d = $(this).attr('d');
					h = parseInt($(this).attr('h'));
					state = parseInt($(this).attr('state'));
					$(this).attr('title', d + " " + h + ":00-" + (h+1) + ":00");
					t = d + " " + zeroPad(h) + ":00:00";
					if (t >= start_date)
						$(this).addClass('clickable');
					else
						$(this).removeClass('clickable');

					if (state == 2)
						$(this).removeClass('clickable');
				})
				.unbind('click')
				.bind('click', function() {
					if (!$(this).hasClass('clickable'))
						return;
					var cell = $(this);
					d = cell.attr('d');
					w = cell.attr('w');
					h = cell.attr('h');
					state = parseInt($(this).attr('state'));

					switch(state) {
						case 1:
							state = 0;
							break;
						case 2: // reserved
							$('#sel_date').datepicker('setDate', d);
							return;
							break;
						default:
							state = 1;
							break;
					}

					cell.attr('state', state);
					App.callAPI("api/dtime/state?brtt=" + (new Date),
						{
							date: d,
							start_time: h * 60,
							state: state
						}
					)
					.done(function() {
						if (date == d)
							refreshTable();
						else {
							$('#sel_date').datepicker('setDate', d);
						}
					})
			        .fail(function(res) {
			        	errorBox("<?php l('错误发生');?>", res.err_msg);
			        	refreshTable();	
			        	
			        });
				});

			refreshRightList()
			
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
	                    default:
	                        return;
					}
					label += "</span>";
					$('#sel_dtimes ul.dtime-list')
						.append('<li state="' + state +
	                        '"><p>' + 
	                        h + ':00-' + (h + 1) + ':00' +
	                        '</p>' + label + '</li>');
				}
			})
	}

	refreshTable();
});
</script>