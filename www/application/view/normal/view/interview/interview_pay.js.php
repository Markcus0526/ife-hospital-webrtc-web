<script type="text/javascript">
var g_check_payed_timer;
	
$(function() {
	$('#agree').change(function() {
		if ($(this).prop('checked')) {
			$('#div_pay_buttons').removeClass('hide');
		}
		else {
			$('#div_pay_buttons').addClass('hide');
		}
	});
	$('#error_payment').on('click', function(event) {
		document.location.href = '<?php echo $this->pay_finish_url;?>';
	});
	$('#complete_payment').on('click', function(event) {
		document.location.href = '<?php echo $this->pay_finish_url;?>';
	});
});
function onChinapaySubmit() {
	$('#waiting_payment_dialog').modal('show');

	g_check_payed_timer = setInterval(function() {
		checkPayedStatus();
	}, 1000);
}
function onPaypalSubmit(url) {
	$('#waiting_payment_dialog').modal('show');

	g_check_payed_timer = setInterval(function() {
		checkPayedStatus();
	}, 1000);

	window.open(url);
}
function checkPayedStatus() {
	var url = '<?php echo $this->pay_check_url;?>';
	$.ajax({
		url: url,
		success: function(resp) {
			var data = JSON.parse(resp);
			console.log(data);
			if (data.payed) {
				clearInterval(g_check_payed_timer);
				$('#error_payment').attr('disabled', true);
			}
		}
	})
}

$('#contract_link').click(function() {
	$('#contract_view').modal('show');
});
</script>