<script type="text/javascript">
$(function () {
    page_refresh("interview/interp_list_refresh/" + $('#page_no').val() + "/" + $('#page_size').val(), '#div_refresh', 'on_refresh()', <?php p(REFRESH_INTERVAL); ?>);

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

    $('#btn_select_times').click(function() {
        $('#select_times').modal('show');
    });

    $('#btn_select_times_ok').click(function() {
        $('#from_time').val($('#modal_from_time').val());
        $('#to_time').val($('#modal_to_time').val());

        $('#list_form').submit();
    });
});

function on_refresh() {
    if (!g_first_refresh)
    {
        init_data_sort();
    }

    $('.btn-bid').click(function() {
        var interview_id = $(this).parents('tr').attr('interview_id');
        var reserved_starttime = $(this).parents('tr').attr('reserved_starttime');
        confirmBox('<?php l('提示');?>', '<?php l('是否确定担任本次会诊翻译？');?>', function() {
            App.callAPI("api/interview/bid",
                {
                    interview_id: interview_id,
                    reserved_starttime: reserved_starttime
                }
            )
            .done(function(res) {
                alertBox("<?php l('成功接单');?>", "<?php l('您已成功接单，请在会诊开始前做好准备。');?>", function() {
                    goto_url("interview");
                });
            })
            .fail(function(res) {
                errorBox("<?php l('错误发生');?>", res.err_msg);
            });
        });
    });
}
</script>