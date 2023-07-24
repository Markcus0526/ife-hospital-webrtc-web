<style>
	.dropzone.dz-started .dz-message {
		display: none;
	}
</style>
<form id="fileDropzone" action="common/upload_ajax" class="dropzone">
	<table class="dz-message" data-dz-message style=" width: 100%; height: 350px; "><tr><td>
		<div style="text-align: center"><span><h2><?php l("点击这里上传文件"); ?></h2></span></div>
	</td></tr></table>
</form>
<input type="hidden" name="files" id="files"/>

<div class="text-right" style="margin-top: 10px">
	<button type="submit" class="btn btn-primary btn-ok"><i class="icon-check"></i> <?php l("确定"); ?></button>
	<button type="button" class="btn btn-default btn-cancel"><i class="icon-close"></i> <?php l("取消"); ?></button>
</div>

<?php $this->addjs("js/dropzone/dropzone.min.js"); ?>