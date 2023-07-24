<script type="text/javascript">
	
$(function() {
	$('.nav-pills li a').click(function() {
		nav = $(this).parents('.nav-pills');
		nav.find('li a .fa-angle-down').addClass('hide');
		nav.find('li a .fa-angle-up').removeClass('hide');
		$(this).find('.fa-angle-down').removeClass('hide');
		$(this).find('.fa-angle-up').addClass('hide');
	});

	// filtering
	$('.tab-pane a[hcountry_id][hospital_id]').click(function() {
		$('#hcountry_id').val($(this).attr("hcountry_id"));
		$('#hospital_id').val($(this).attr("hospital_id"));
		$('#list_form').submit();
	});

	$('.tab-pane a[depart_id]').click(function() {
		$('#depart_id').val($(this).attr("depart_id"));
		$('#list_form').submit();
	});

	$('.nav-pills a[disease_id]').click(function() {
		$('#disease_id').val($(this).attr("disease_id"));
		$('#list_form').submit();
	});

	$('.nav-pills li span[all]').click(function() {
		all = $(this).attr('all');
		switch(all)
		{
			case 'hcountry':
				$('#hcountry_id').val("");
				$('#hospital_id').val("");
				break;
			case 'deaprt':
				$('#depart_id').val("");
				break;
			case 'disease':
				$('#disease_id').val("");
				break;
		}

		$('#list_form').submit();
	});

	<?php if (_my_type() == UTYPE_PATIENT) {?>
	$('.btn-reserve').click(function(e) {
		e.preventDefault();
		$('#link_reserve').attr('href', $(this).attr('href'));
		$('#alert_reserve').modal('show');
	});
	<?php } ?>

	var dw = $('.detail dl').width();
	var lw = $('.label-disease').width();
	$('.label-disease+dd').css('max-width', dw-lw-10);
	lw = $('.label-d-title').width();
	$('.label-d-title+dd').css('max-width', dw-lw-10);
	lw = $('.label-d-fee').width();
	$('.label-d-fee+dd').css('max-width', dw-lw-10);
});

</script>