<section>
	<div class="page-bar">
		<ul class="page-breadcrumb">
			<li>
				<span><?php l("当前位置"); ?> :</span>
			</li>
			<li>
				<?php l("意见反馈");?>
			</li>
		</ul>
	</div>
	<div class="row">
		<div class="col-md-10 col-md-offset-1">
			<form id="form" class="feedback-form" action="api/profile/feedback" method="post" novalidate="novalidate">
				<div class="form-body">
					<div class="form-group form-md-line-input">
						<?php $mFeedback->textarea("comment", 10, array("placeholder" => _l("请输入您的宝贵意见。"))); ?>
						<label for="comment"><?php l("反馈内容"); ?> <span class="required">*</span> :</label>
					</div>
				</div>

				<div class="form-actions">
					<button type="submit" class="btn btn-primary"><i class="icon-check"></i> <?php l("提交"); ?></button>
				</div>
			</form>
		</div>
	</div>
</section>