<script type="text/javascript">
$(function() {
	// filtering
    $('#daterange').daterangepicker({
            opens: 'right',
            startDate: $('#from_date').val(),
            endDate: $('#to_date').val(),
            showDropdowns: true,
            showWeekNumbers: true,
            timePicker: false,
            timePickerIncrement: 1,
            timePicker12Hour: true,
            buttonClasses: ['btn'],
            applyClass: 'green',
            cancelClass: 'default',
            format: 'YYYY-MM-DD',
            separator: ' to ',
            locale: $.fn.daterangepicker.custome_locale
        },
        function (start, end) {
            $('#daterange span').html(start.format('YYYY-MM-DD') + ' - ' + end.format('YYYY-MM-DD'));

            $('#from_date').val(start.format('YYYY-MM-DD'));
            $('#to_date').val(end.format('YYYY-MM-DD'));

            $('#list_form').submit();
        }
    );
    $('#daterange span').html($('#from_date').val() + ' - ' + $('#to_date').val());

	// init country name
	$('.td-country[country_id]').each(function() {
		country_id = $(this).attr('country_id');
		country_name = $('#country_id option[value="' + country_id + '"]').text();
		$(this).text(country_name);
	});

	$('#country_id').change(function() {
		$('#list_form').submit();
	});

	$('#user_type').change(function() {
		$('#list_form').submit();
	});

    <?php if (_has_priv(CODE_PRIV_STATS, PRIV_EXRATE)) { ?>
    $('#btn_exrate').click(function() {
        App.callAPI("api/exrate/rate", { force: true })
        .done(function(res) {
            $('#rate_usd_to_rmb').text(res.usd_to_rmb);
            $('#rate_rmb_to_usd').text(res.rmb_to_usd);
            $('#exrate_form').modal('show');
        })
        .fail(function(res) {
            errorBox("<?php l('错误发生');?>", res.err_msg);
        });
    });
    <?php } ?>
});
</script>