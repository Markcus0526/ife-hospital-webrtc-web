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
				<?php l("更改密码");?>
			</li>
		</ul>
	</div>
	<form id="form" action="api/profile/password" class="form-horizontal" method="post" novalidate="novalidate">
		<div class="form-body">
			<div class="form-group form-md-line-input">
				<label class="control-label col-md-4" for="old_password"><?php l("旧密码"); ?> <span class="required">*</span> :</label>
				<div class="col-md-5">
					<?php $mUser->password("old_password"); ?>
					<div class="form-control-focus">
					</div>
				</div>
			</div>
			<div class="form-group form-md-line-input">
				<label class="control-label col-md-4" for="new_password"><?php l("新密码"); ?> <span class="required">*</span> :</label>
				<div class="col-md-5">
					<?php $mUser->password("new_password"); ?>
					<div class="form-control-focus">
					</div>
				</div>
			</div>
			<div class="form-group form-md-line-input">
				<label class="control-label col-md-4" for="confirm_new_password"><?php l("确认新密码"); ?> <span class="required">*</span> :</label>
				<div class="col-md-5">
					<?php $mUser->password("confirm_new_password"); ?>
					<div class="form-control-focus">
					</div>
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