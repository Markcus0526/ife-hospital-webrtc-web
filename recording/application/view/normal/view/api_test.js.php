<script type="text/javascript">
$(function () {
	$('#save_form').ajaxForm({
		success: function(ret, statusText, xhr, form) {
			try {
				$('#api_result').text(ret);
			}
			finally {
			}
		}
	});

});
</script>