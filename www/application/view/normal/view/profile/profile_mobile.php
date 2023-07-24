<section>
	<div class="page-bar">
		<ul class="page-breadcrumb">
			<li>
				<span><?php l("当前位置"); ?> :</span>
			</li>
			<li>
				<a href="profile"><?php l("用户信息");?></a>
				<i class="fa fa-angle-right"></i>
			</li>
			<li>
				<?php l("更改手机");?>
			</li>
		</ul>
	</div>
	<form id="form" action="api/profile/mobile" class="form-horizontal" method="post" novalidate="novalidate">
		<div class="form-body">
			<div class="form-group">
				<label class="control-label col-md-4"><?php l("已绑定手机"); ?> :</label>
				<div class="col-md-8">
					<p class="form-control-static"><?php p($mUser->mobile); ?></p>
					<?php $mUser->hidden('mobile'); ?>
				</div>
			</div>
			<div class="form-group form-md-line-input">
				<label class="control-label col-md-4" for="mobile_passkey"><?php l("短信验证码"); ?> <span class="required">*</span> :</label>
				<div class="col-md-5">
					<div class="input-group">
						<div class="input-group-control">
							<?php $mUser->input("mobile_passkey", array("mobile" => "mobile")); ?>
							<div class="form-control-focus">
							</div>
						</div>
						<span class="input-group-btn btn-right">
							<button mobile="mobile" class="btn btn-primary send-passkey" type="button"><?php l("点击获取"); ?><span></span></button>
						</span>
					</div>
				</div>
			</div>
			<div class="form-group form-md-line-input">
				<label class="control-label col-md-4" for="new_mobile"><?php l("新绑定手机"); ?> <span class="required">*</span> :</label>
				<div class="col-md-5">
					<?php $mUser->tel("new_mobile"); ?>
					<div class="form-control-focus">
					</div>
					<span class="help-block help-block-default"><?php l("请输入新绑定手机号码。"); ?></span>
				</div>
			</div>
			<div class="form-group form-md-line-input">
				<label class="control-label col-md-4" for="new_mobile_passkey"><?php l("短信验证码"); ?> <span class="required">*</span> :</label>
				<div class="col-md-5">
					<div class="input-group">
						<div class="input-group-control">
							<?php $mUser->input("new_mobile_passkey", array("mobile" => "new_mobile")); ?>
							<div class="form-control-focus">
							</div>
						</div>
						<span class="input-group-btn btn-right">
							<button mobile="new_mobile" class="btn btn-primary send-passkey" type="button"><?php l("点击获取"); ?><span></span></button>
						</span>
					</div>
					<span class="help-block help-block-info hide"><?php l("已成功发送，请在倒计时结束前输入收到的短信验证码。");?></span>
				</div>
			</div>
		</div>

		<div class="form-actions">
			<div class="row">
				<div class="col-md-8 col-md-offset-4">
					<button type="submit" class="btn btn-primary"><i class="icon-check"></i> <?php l("确定"); ?></button>
				</div>
			</div>
		</div>
	</form>
</section>