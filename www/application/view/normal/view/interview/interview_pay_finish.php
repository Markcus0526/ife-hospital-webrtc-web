<section class="time-out-section">
	<div class="page-bar">
		<ul class="page-breadcrumb">
			<li>
				<span><?php l("当前位置"); ?> :</span>
			</li>
			<li>
				<?php l("支付页面");?>
			</li>
		</ul>
	</div>
	<form id="form" class="form-horizontal" method="post" novalidate="novalidate">
		<div class="form-body">
			<?php if ($mInterview->pay_id) { ?>
			<div class="text-center">
				<div class="text-left" style="display:inline-block;">
					<div style="font-size: 1.5em; color: black;">
						<?php l("正在处理支付数据，请稍候..."); ?>
					</div>
					<div>
						<span id="seconds" style="font-size: 1.5em; color: lightseagreen;">5</span><span style="font-size: 1em;"><?php l("秒之后此页面将自动关闭！")?></span>
					</div>
				</div>
			</div>
			<?php } else { ?>
			<div class="form-group static">
				<div class="col-md-8 col-md-offset-2 text-center">
					<div class="well well-danger">
						<?php l("对不起，支付失败!"); ?>
					</div>
				</div>
			</div>
			<div class="form-group static">
				<div class="col-md-8 col-md-offset-4">
					<a href="interview" class="btn btn-primary"><?php l("返回"); ?></a>
				</div>
			</div>
			<?php } ?>
		</div>
	</form>
</section>