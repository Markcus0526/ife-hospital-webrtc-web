<?php 
$my_type = _my_type();
?>
<script type="text/javascript">
var pay_timer = null;
function onUploadComplete(files, upload_id, upload_type)
{
    if (upload_type == 'prescription') {
        confirmBox('<?php l('提示');?>', '<?php l('确定上传该文件？');?>', function() {
            App.callAPI("api/interview/upload_prescription",
                {
                    interview_id: upload_id,
                    prescription: files
                }
            )
            .done(function(res) {
                alertBox("<?php l('提示');?>", "<?php l('成功上传第二诊疗意见');?>", function() {
                    App.reload();
                });
            })
            .fail(function(res) {
                errorBox("<?php l('错误发生');?>", res.err_msg);
            });
        });
    }
    else if (upload_type == 'trans_prescription') {
        confirmBox('<?php l('提示');?>', '<?php l('确定上传该文件？上传后无法再更改。');?>', function() {
            App.callAPI("api/interview/upload_trans_prescription",
                {
                    interview_id: upload_id,
                    trans_prescription: files
                }
            )
            .done(function(res) {
                alertBox("<?php l('提示');?>", "<?php l('成功上传第二诊疗意见');?>", function() {
                    App.reload();
                });
            })
            .fail(function(res) {
                errorBox("<?php l('错误发生');?>", res.err_msg);
            });
        });
    }
}

<?php 
if ($my_type == UTYPE_ADMIN || $my_type == UTYPE_SUPER) {
?>
// 指派
var selected_interview_id = null;
function onSelectInterpreter(user_id)
{
    App.callAPI("api/interview/invite",
        {
            interview_id: selected_interview_id,
            interpreter_id: user_id
        }
    )
    .done(function(res) {
        alertBox("<?php l('提示');?>", "<?php l('成功指派');?>", function() {
            App.reload();
        });
    })
    .fail(function(res) {
        errorBox("<?php l('错误发生');?>", res.err_msg);
    });
}
<?php
}
?>

var refresh_url = null;
$(function () {
    refresh_url = "interview/index_refresh/" + $('#page_no').val() + "/" + $('#page_size').val();
    page_refresh(refresh_url, '#div_refresh', 'on_refresh()', <?php p(REFRESH_INTERVAL); ?>);

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

    istatuses = $('#istatuses').val().split(',');
    $(istatuses).each(function(key, status) {
        $(".label-istatus[status=" + status + "]").attr('check', 1);
    });

    $('.label-istatus').click(function() {
        checked = $(this).attr('check');
        $(this).attr('check', checked == 1 ? 0: 1);

        istatuses = [];
        $('.label-istatus[check=1]').each(function() {
            istatuses.push($(this).attr('status'));
        });
        $('#istatuses').val(istatuses.join(","));

        $('#list_form').submit();
    });

    // cancel form
    var cancel_form = $('#cancel_form').validate($.extend({
        rules : {
            cancel_cause_id: {
                required: true
            },
            cancel_cause_note: {
                required: function(ele) {
                    var cancel_cause_id = $('#cancel_cause_id').val();
                    return (cancel_cause_id == <?php p(CCAUSE_OTHER); ?>);
                }
            }
        },

        // Messages for form validation
        messages : {
            cancel_cause_id: {
                required: '<?php l('请选择取消原因。');?>'
            },
            cancel_cause_note: {
                required: '<?php l('请输入取消原因。');?>'
            }
        }
    }, getValidationRules()));

    $('#cancel_form').on('submit', function() {
        if ($(this).valid())
            $(this).modal('hide');
    });
    $('#cancel_form').ajaxForm({
        dataType : 'json',
        success: function(res, statusText, xhr, form) {
            try {
                if (res.err_code == 0)
                {
                    alertBox("<?php l('提示');?>", "<?php l('成功取消会诊。');?>", function() {
                        App.reload();
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
    
    <?php 
    if (_has_priv(CODE_PRIV_INTERVIEWS, PRIV_CLOSE_INTERVIEW)) {
    ?>
    // close form
    var close_form = $('#close_form').validate($.extend({
        rules : {
            cancel_cause_note: {
                required: true
            }
        },

        // Messages for form validation
        messages : {
            cancel_cause_note: {
                required: '<?php l('请输入关闭理由。');?>'
            }
        }
    }, getValidationRules()));

    $('#close_form').on('submit', function() {
        if ($(this).valid())
            $(this).modal('hide');
    });
    $('#close_form').ajaxForm({
        dataType : 'json',
        success: function(res, statusText, xhr, form) {
            try {
                if (res.err_code == 0)
                {
                    alertBox("<?php l('提示');?>", "<?php l('成功关闭会诊。');?>", function() {
                        App.reload();
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
    <?php
    }
    ?>
    <?php 
    if (_has_priv(CODE_PRIV_INTERVIEWS, PRIV_REFUND)) {
    ?>
    // close form
    var close_form = $('#refund_form').validate($.extend({
        rules : {
            refund_amount: {
                required: true,
                min: 0
            },
            refund_note: {
                required: true
            }
        },

        // Messages for form validation
        messages : {
            refund_amount: {
                required: '<?php l('请输入退款金额。');?>',
                min: '<?php l('请输入0以上的数值。');?>',
                max: function (v, a) {
                    cunit = $('#refund_form #cost_cunit').text()
                    if (cunit == '￥')
                        valid = "<?php l('请输入￥{0}以下的金额。');?>";
                    else if (cunit == '$')
                        valid = "<?php l('请输入$ {0}以下的金额。');?>";

                    return $.validator.format(valid, v);

                }
            },
            refund_note: {
                required: '<?php l('请输入退款原因。');?>'
            }
        }
    }, getValidationRules()));

    $('#refund_form').on('submit', function() {
        if ($(this).valid())
            $(this).modal('hide');
    });
    $('#refund_form').ajaxForm({
        dataType : 'json',
        success: function(res, statusText, xhr, form) {
            try {
                if (res.err_code == 0)
                {
                    alertBox("<?php l('提示');?>", "<?php l('成功退款。');?>", function() {
                        App.reload();
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
    <?php
    }
    ?>

    $('.btn-before-starttime').click(function() {
        errorBox("<?php l('提示');?>", "<?php p(_err_msg(ERR_BEFORE_STARTTIME)); ?>");
    });

    <?php
    if (_has_priv(CODE_PRIV_INTERVIEWS, PRIV_SET_COST)) {
    ?>    
    var cost_form = $('#cost_form').validate($.extend({
        rules : {
            cost: {
                required: true,
                number: true,
                min: 0
            }
        },

        // Messages for form validation
        messages : {
            cost: {
                required: '<?php l('请输入会诊费。');?>',
                number: '<?php l('请输入有效数字。');?>',
                min: '<?php l('请输入0以上的数值。');?>',
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
                    alertBox("<?php l('提示');?>", "<?php l('成功更改会诊费。');?>");

                    $('#cost_form').modal('hide');
                    page_refresh(refresh_url, '#div_refresh', 'on_refresh()', 0);
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
    <?php
    }
    ?>

    <?php 
    if ($my_type == UTYPE_DOCTOR) {
    ?>
    var close_form = $('#reject_patient_form').validate($.extend({
        rules : {
            reject_cause_id: {
                required: true
            },
            reject_cause_note: {
                required: function(ele) {
                    var reject_cause_id = $('#reject_cause_id').val();
                    return (reject_cause_id == <?php p(RCAUSE_OTHER); ?>);
                }
            }
        },

        // Messages for form validation
        messages : {
            reject_cause_id: {
                required: '<?php l('请选择拒绝理由。');?>'
            },
            reject_cause_note: {
                required: '<?php l('请输入拒绝理由。');?>'
            }
        }
    }, getValidationRules()));

    $('#reject_patient_form').on('submit', function() {
        if ($(this).valid())
            $(this).modal('hide');
    });
    $('#reject_patient_form').ajaxForm({
        dataType : 'json',
        success: function(res, statusText, xhr, form) {
            try {
                if (res.err_code == 0)
                {
                    alertBox("<?php l('提示');?>", "<?php l('成功拒绝患者。');?>", function() {
                        App.reload();
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

    $('#accept_patient_form').on('submit', function() {
        if ($(this).valid())
            $(this).modal('hide');
    });
    $('#accept_patient_form').ajaxForm({
        dataType : 'json',
        success: function(res, statusText, xhr, form) {
            try {
                if (res.err_code == 0)
                {
                    alertBox("<?php l('提示');?>", "<?php l('成功接受患者。');?>", function() {
                        App.reload();
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
    <?php
    }
    ?>

});

$('#user_names_form').ajaxForm({
    dataType : 'json',
    success: function(res, statusText, xhr, form) {
        try {
            if (res.err_code == 0)
            {
                alertBox("<?php l('提示');?>", "<?php l('成功设置。');?>", function() {
                    App.reload();
                });

                $('#user_names_form').modal('hide');
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
function showUserNamesForm(patient_id, patient_name, patient_name_l,
        doctor_id, doctor_name, doctor_name_l,
        interpreter_id, interpreter_name, interpreter_name_l,
        planguage_id, dlanguage_id
    ) {
    $('#user_names_form [name="patient_id"]').val(patient_id);
    $('#user_names_form .patient_name strong').text(patient_name);
    $('#user_names_form [name="doctor_id"]').val(doctor_id);
    $('#user_names_form .doctor_name strong').text(doctor_name);
    $('#user_names_form [name="interpreter_id"]').val(interpreter_id);
    $('#user_names_form .interpreter_name strong').text(interpreter_name);
    $('#user_names_form .name_l').val('');

    $('#user_names_form .lang-row').hide();
    $('#user_names_form .lang-row[language_id="' + planguage_id + '"]').show();
    $('#user_names_form .lang-row[language_id="' + dlanguage_id + '"]').show();

    var set_name_l = function(name_l, field) {
        if (name_l != '') {
            try {
                name_l = JSON.parse(name_l);

                $.each(name_l, function(key, val) {
                    $('#user_names_form [name="' + field + '[\'' + key + '\']"').val(val);
                });
            }
            catch(e) {

            }
        }
    }

    set_name_l(patient_name_l, 'patient_name_l');
    set_name_l(doctor_name_l, 'doctor_name_l');
    set_name_l(interpreter_name_l, 'interpreter_name_l');

    $('#user_names_form').modal('show');
}

var details_expands = {};
function on_refresh() {
    if (!g_first_refresh)
    {
        init_data_sort();
    }

    $.each(details_expands, function(interview_id, flag) {
        if (flag == 1) {
            $('[interview_id=' + interview_id + '] .expand-details').addClass('hide');
            $('[interview_id=' + interview_id + '] .collapse-details').removeClass('hide');
            $('[interview_id=' + interview_id + '] .details').removeClass('hide');    
        }
    });

    $('.expand-details').click(function() {
        $(this).addClass('hide');
        $(this).parent().find('.collapse-details').removeClass('hide');
        var interview_id = $(this).parents('tr').attr('interview_id');
        $('[interview_id=' + interview_id + '] .details').removeClass('hide');
        details_expands[interview_id] = 1;
    });

    $('.collapse-details').click(function() {
        $(this).addClass('hide');
        $(this).parent().find('.expand-details').removeClass('hide');
        var interview_id = $(this).parents('tr').attr('interview_id');
        $('[interview_id=' + interview_id + '] .details').addClass('hide');
        details_expands[interview_id] = 0;
    });

    $('.btn-cancel').click(function() {
        $('#cancel_form').validate().resetForm();
        var interview_id = $(this).parents('tr').attr('interview_id');
        var status = $(this).attr('status');
        $('#cancel_form [name="interview_id"]').val(interview_id);
        if (status == <?php p(ISTATUS_NONE); ?>)
            $('#alert_couldnot_refund').hide();
        else
            $('#alert_couldnot_refund').show();
        $('#cancel_form').modal('show');
    });

    <?php 
    if (_has_priv(CODE_PRIV_INTERVIEWS, PRIV_CLOSE_INTERVIEW) && $interview->status != ISTATUS_CANCELED) {
    ?>
    $('.btn-close').click(function() {
        $('#close_form').validate().resetForm();
        var interview_id = $(this).parents('tr').attr('interview_id');
        var status = $(this).attr('status');
        $('#close_form [name="interview_id"]').val(interview_id);
        $('#close_form').modal('show');
    });
    <?php
    }
    ?>

    <?php 
    if (_has_priv(CODE_PRIV_INTERVIEWS, PRIV_REFUND)) {
    ?>
    $('.btn-refund').click(function() {
        $('#refund_form').validate().resetForm();
        var interview_id = $(this).parents('tr').attr('interview_id');
        var refundable_amount = $(this).attr('refundable_amount');
        $('#refund_form [name="interview_id"]').val(interview_id);
        $('#refund_form [name="refund_amount"]').attr('max', refundable_amount);
        cunit = $(this).attr('cunit');
        if (cunit == "rmb")
            $('#refund_form #cost_cunit').text("￥");
        else if (cunit == "usd")
            $('#refund_form #cost_cunit').text("$");
        $('#refund_form').modal('show');
    });
    <?php
    }
    ?>

    <?php 
    if ($my_type == UTYPE_ADMIN || $my_type == UTYPE_SUPER) {
    ?>
    // 指派
    $('.btn-invite').click(function() {
        selected_interview_id = $(this).parents('tr[interview_id]').attr('interview_id');
    });
    <?php 
    }
    if (_has_priv(CODE_PRIV_INTERVIEWS, PRIV_SET_COST)) {
    ?>
    $('.btn-change-cost').click(function() {
        $('#cost_form').validate().resetForm();
        $('#cost_form [name="interview_id"]').val($(this).parents('tr[interview_id]').attr('interview_id'));
        $('#cost_form [name="cost"]').val(toFloat($(this).parents('td[cost]').attr('cost')));
        cunit = $(this).parents('td[cost]').attr('cunit');
        if (cunit == "rmb")
            $('#cost_form #cost_cunit').text("￥");
        else if (cunit == "usd")
            $('#cost_form #cost_cunit').text("$");

        $('#cost_form').modal('show');
    });
    <?php 
    }
    ?>

    <?php 
    if ($my_type == UTYPE_DOCTOR) {
    ?>
    $('.btn-reject-patient').click(function() {
        $('#reject_patient_form').validate().resetForm();
        var interview_id = $(this).parents('tr').attr('interview_id');
        $('#reject_patient_form [name="interview_id"]').val(interview_id);
        $('#reject_patient_form').modal('show');
    });
    $('.btn-accept-patient').click(function() {
        $('#accept_patient_form').validate().resetForm();
        var interview_id = $(this).parents('tr').attr('interview_id');
        $('#accept_patient_form [name="interview_id"]').val(interview_id);
        $('#accept_patient_form').modal('show');
    });
    <?php
    }
    ?>

    $('.btn-user-names').click(function() {
        showUserNamesForm(
            $(this).attr('patient_id'),
            $(this).attr('patient_name'),
            $(this).attr('patient_name_l'),
            $(this).attr('doctor_id'),
            $(this).attr('doctor_name'),
            $(this).attr('doctor_name_l'),
            $(this).attr('interpreter_id'),
            $(this).attr('interpreter_name'),
            $(this).attr('interpreter_name_l'),
            $(this).attr('planguage_id'),
            $(this).attr('dlanguage_id')
        );
    });

    // pay count down
    var exist_pay = false;
    $('.btn-go-pay').each(function() {
        exist_pay = true;
        var now_tm = Math.floor((new Date()).getTime() / 1000);
        var pay_limit_tm = parseInt($(this).attr("pay_limit_tm"));
        var server_now_tm = parseInt($(this).attr("server_now_tm"));

        pay_limit_tm -= (server_now_tm - now_tm);

        $(this).attr("pay_limit_tm", pay_limit_tm);
    });

    var refreshPayTimer = function() {
        $('.btn-go-pay').each(function() {
            var now_tm = Math.floor((new Date()).getTime() / 1000);
            var pay_limit_tm = parseInt($(this).attr("pay_limit_tm"));

            var limit = pay_limit_tm - now_tm;
            var hour = Math.floor(limit / 3600);
            var min = Math.floor(limit / 60) % 60;
            var sec = limit % 60;

            if (limit >= 0)
                $(this).find(".timer").text(zeroPad(hour) + ":" + zeroPad(min) + ":" + zeroPad(sec));
            else {
                $(this).hide();
                page_refresh(refresh_url, '#div_refresh', 'on_refresh()', -1);
            }
        });
    }
    refreshPayTimer();

    if (pay_timer)
        clearInterval(pay_timer);
    if (exist_pay)
    {
        pay_timer = setInterval(refreshPayTimer, 1000);
    }

    App.initFancybox("#div_refresh");
}
</script>