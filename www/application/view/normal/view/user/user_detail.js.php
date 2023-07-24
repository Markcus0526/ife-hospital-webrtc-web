<script type="text/javascript">
function refreshAttaches(upload_id)
{
	var lks = "";
	var attaches = $('#' + upload_id).val();
	if (attaches != "" && attaches != undefined)
	{
		var as = attaches.split(';');
		for (i = 0; i < as.length; i ++)
		{
			var fs = as[i].split(':');
			lks += "<li>";
			lks += downLink(fs[0], fs[1], fs[2], "<?php l("下载"); ?>");
			lks += " </li>";
		}
	}
	$('#ul_' + upload_id).html(lks);
}

$(function() {
	$('.btn-reserve').click(function(e) {
		e.preventDefault();
		$('#link_reserve').attr('href', $(this).attr('href'));
		$('#alert_reserve').modal('show');
	});

	refreshAttaches('diplomas');

	refreshAttaches('passports');
});
</script>