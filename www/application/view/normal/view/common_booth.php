<h2><?php 
if ($this->mode == "avartar")
	l("拍摄头像"); 
else
	l("拍摄照片");
?>
</h2>
<form class="form-horizontal" method="post">
	<div class="row">
		<div class="col-sm-6 text-center">
			<h3><?php l("摄像头"); ?></h3>
			<video id="video"></video>
			<div id="media_error" class="text-danger"></div>
		</div>
		<div class="col-sm-6 text-center">
			<h3><?php 
			if ($this->mode == "avartar")
				l("头像"); 
			else
				l("照片");
			?>
			</h3>
			<img src="" id="photo" class="<?php if ($this->mode == "avartar") p("large-avartar"); ?>">
			<canvas id="canvas" style="display: none;"></canvas>
		</div>
	</div>

	<div class="form-actions margin-top-10">
		<div class="row">
			<div class="col-sm-6 text-center">
				<button type="button" id="btn_capture" class="btn btn-default"><?php l("拍摄"); ?></button>
			</div>
			<div class="col-sm-6 text-center">
				<button type="button" id="btn_ok" class="btn btn-primary"><?php l("确定"); ?></button>
				<button type="button" id="btn_close" class="btn btn-default"><?php l("关闭"); ?></button>
			</div>
		</div>
	</div>
</form>


