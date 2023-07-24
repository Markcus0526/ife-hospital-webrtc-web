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
	// init attach
	function refreshCattaches(cattaches)
	{
		var hrow = null;
		$('.cattaches-container').html('');
		if (cattaches.length > 0) {
			for (var i = 0; i < cattaches.length; i ++)
			{
				cattach = cattaches[i];
				files_id = "cattach_files_" + i;
				c = $('#cattach_template > div').clone();
				c.find('.control-label').html(cattach.dtemplate_name + ' :');
				c.find('input.cattach-files')
					.attr("id", files_id)
					.attr("name", files_id)
					.val(cattach.files);
				c.find('.attach-list').attr('id', 'ul_' + files_id);

				if (i % 2 == 0)
					hrow = $('<div class="row chistory-attach-row"></div>');

				hrow.append(c);

				if (i % 2 == 1) {
					$('.cattaches-container').append(hrow);
					hrow = null;
				}
			}
			
			if (hrow) {
				$('.cattaches-container').append(hrow);
			}
			
			for (i = 0; i < cattaches.length; i ++)
			{
				files_id = "cattach_files_" + i;
				refreshAttaches(files_id);
			}
		}
		else {
			msg = $('#cattach_template .alert-sel-disease').clone();
			$('.cattaches-container').append(msg);
		}
	}

	refreshAttaches("passports");
    refreshCattaches(<?php p(_json_encode($mChistory->cattaches)); ?>);

    function resizeLayout()
    {
    	profile = $('#patient_profile');
    	h = profile.height();
    	profile.find('.photo-booth').height(h);
    }
    resizeLayout();

   	$('#guide_link').click(function() {
		$('#guide_view').modal('show');
	});
});

</script>