<script type="text/javascript">
$(function () {
	$('.btn-ok').click(function() {
		if ($('#files').val() != '') {
			parent.onUploadComplete($('#files').val(), '<?php p($this->upload_id); ?>', '<?php p($this->upload_type); ?>');
		}
		parent.$.fancybox.close();
	});
	
	$('.btn-cancel').click(function() {
		parent.$.fancybox.close();
	});

	
	Dropzone.options.fileDropzone = {
	  init: function() {
		this.on("success", function(file, responseText) {
			eval("var ret = " + responseText);
			files = $('#files').val();
			if (files != "") files += ";";
			$('#files').val(files + ret.path + ":" + ret.filename + ":" + ret.filesize);

			/*
			photo_url = ret.photo_url;
			$("<a href='" + photo_url + "' target='_new'>" + photo_url + "</a>").appendTo($(file.previewTemplate));
			*/
		});
		this.on("error", function(file, responseText) {
			eval("var ret = " + responseText);
			file.previewElement.querySelector("[data-dz-errormessage]").textContent = ret.err_msg;
		});
	  }
	};
});
</script>