<script type="text/javascript">
$(function() {
    // init country name
    $('.td-country[country_id]').each(function() {
        country_id = $(this).attr('country_id');
        country_name = $('#country_id option[value="' + country_id + '"]').text();
        $(this).text(country_name);
    });
    
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

    $('#country_id, #user_type, #status').change(function() {
        $("#list_form").submit();
    });

    $('[feedback_id][status="0"] .label').click(function() {
        feedback_id = $(this).closest('tr').attr('feedback_id');
        App.callAPI("api/feedback/read",
            {
                feedback_ids: [feedback_id]
            }
        )
        .done(function(res) {
            if (res.err_code == 0) {
                $('[feedback_id="' + feedback_id + '"]').attr('status', 1);
                $('[feedback_id="' + feedback_id + '"] .label').removeClass('label-primary').addClass('label-gray').text("<?php p(_code_label(CODE_FSTATUS, 1)); ?>");
            }
        });
    });
})
</script>