<script type="text/javascript">
$(function () {
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
});


</script>