<?php
$my_type = _my_type();
?>
<section>
	<div class="page-bar">
		<ul class="page-breadcrumb">
			<li>
				<span><?php l("当前位置"); ?> :</span>
			</li>
			<li>
				<a href="javascript:;"><?php l("用户管理");?></a>
				<i class="fa fa-angle-right"></i>
			</li>
			<?php if($this->from == "up") { ?>
			<li>
				<a href="user/patients"><?php l("患者列表");?></a>
				<i class="fa fa-angle-right"></i>
			</li>
			<?php } ?>
			<?php if($this->from == "ud") { ?>
			<li>
				<a href="user/doctors"><?php l("专家列表");?></a>
				<i class="fa fa-angle-right"></i>
			</li>
			<?php } ?>
			<?php if($this->from == "ui") { ?>
			<li>
				<a href="user/interpreters"><?php l("翻译列表");?></a>
				<i class="fa fa-angle-right"></i>
			</li>
			<?php } ?>
			<?php if($this->from == "ua") { ?>
			<li>
				<a href="user/admins"><?php l("管理员列表");?></a>
				<i class="fa fa-angle-right"></i>
			</li>
			<?php } ?>
			<li>
				<?php p($this->title);?>
			</li>
		</ul>
	</div>
	<form id="form" action="api/user/save" class="form-horizontal" method="post" novalidate="novalidate">
		<?php $mUser->hidden("user_id"); ?>
		<?php $mUser->hidden("user_type"); ?>
		<div class="form-body">
			<div class="row">
				<div class="col-sm-2 text-center" style="max-width: 200px;">
					<img id="img_avartar" src="<?php p(_avartar_url($mUser->avartar_id())); ?>" class="large-avartar">
					<?php $mUser->hidden("avartar"); ?>

					<div class="upload-button">
						<a href="common/upload/avartar" class="btn btn-primary btn-circle fancybox file-upload" fancy-width=600 fancy-height=480>
							<?php l("上传头像");?>
						</a>
					</div>
				</div>
				<div class="col-sm-8" style="max-width: 800px;">
					<?php if($mUser->user_id != null && 
						($my_type == UTYPE_ADMIN || $my_type == UTYPE_SUPER)) { ?>
						<?php if($mUser->user_type == UTYPE_PATIENT || $mUser->user_type == UTYPE_DOCTOR || $mUser->user_type == UTYPE_INTERPRETER) { ?>
					<div class="form-group static">
						<label class="control-label col-md-4">
							<?php 
							if ($mUser->user_type == UTYPE_DOCTOR) 
								l("专家编号"); 
							else if ($mUser->user_type == UTYPE_INTERPRETER) 
								l("翻译编号"); 
							else if ($mUser->user_type == UTYPE_PATIENT) 
								l("患者编号"); 
							?> :
						</label>
						<div class="col-md-8">
							<p class="form-control-static"><?php p($mUser->user_id); ?></p>
						</div>
					</div>
						<?php } ?>
					<div class="form-group static">
						<label class="control-label col-md-4">
							<?php l("账号状态"); ?> :
						</label>
						<div class="col-md-8">
							<p class="form-control-static"><?php $mUser->detail_code("lock_flag", CODE_LOCK); ?></p>
						</div>
					</div>
					<?php } ?>
					<div class="form-group form-md-line-input">
						<label class="control-label col-md-4" for="user_name"><?php l("姓名"); ?> <span class="required">*</span> :</label>
						<div class="col-md-8">
							<?php $mUser->input("user_name", array("autocomplete" => "off", "maxlength" => 50)); ?>
							<div class="form-control-focus">
							</div>
						</div>
					</div>
					<?php 
						if ($mUser->user_type != UTYPE_DOCTOR) {
					?>
					<div class="form-group">
						<label class="control-label col-md-4"><?php l("性别"); ?> <span class="required">*</span> :</label>
						<div class="col-md-8">
							<?php $mUser->radio("sex" , CODE_SEX); ?>
						</div>
					</div>
					<?php
						}
					?>
					<div class="form-group form-md-line-input">
						<label class="control-label col-md-4" for="password"><?php l("密码"); ?> <?php if ($mUser->user_id == "") { ?><span class="required">*</span><?php } ?> :</label>
						<div class="col-md-8">
							<?php $mUser->password("password", array("autocomplete" => "off", "maxlength" => 50)); ?>
							<div class="form-control-focus">
							</div>
						</div>
					</div>
					<div class="form-group form-md-line-input">
						<label class="control-label col-md-4" for="confirm_password"><?php l("确认密码 "); ?> <?php if ($mUser->user_id == "") { ?><span class="required">*</span><?php } ?> :</label>
						<div class="col-md-8">
							<?php $mUser->password("confirm_password", array("autocomplete" => "off", "maxlength" => 50)); ?>
							<div class="form-control-focus">
							</div>
						</div>
					</div>
					<div class="form-group form-md-line-input">
						<label class="control-label col-md-4"><?php l("注册手机"); ?> <span class="required">*</span> :</label>
						<div class="col-md-8">
							<?php $mUser->tel("mobile", true, array("maxlength" => 50)); ?>
						</div>
					</div>
					<?php if ($mUser->user_type == UTYPE_PATIENT) { ?>
					<div class="form-group form-md-line-input">
						<label class="control-label col-md-4" for="other_tel"><?php l("紧急联系方式"); ?> :</label>
						<div class="col-md-8">
							<?php $mUser->tel("other_tel", true, array("maxlength" => 25)); ?>
							<div class="form-control-focus">
							</div>
						</div>
					</div>
					<?php } ?>
					<div class="form-group form-md-line-input">
						<label class="control-label col-md-4"><?php l("电子邮箱"); ?> <span class="required">*</span> :</label>
						<div class="col-md-8">
							<?php $mUser->input_email("email", array("maxlength" => 80)); ?>
							<div class="form-control-focus">
							</div>
						</div>
					</div>
					<?php if ($mUser->user_type == UTYPE_DOCTOR) { ?>
					<div class="form-group form-md-line-select">
						<label class="control-label col-md-4"><?php l("精通语言"); ?> <span class="required">*</span> :</label>
						<div class="col-md-8">
							<?php $mUser->select_language("languages", _l("精通语言"), false, array("required" => "required", "style" => "opacity:0")); ?>
						</div>
					</div>
					<?php } else if ($mUser->user_type == UTYPE_INTERPRETER) { ?>
					<div class="form-group form-md-line-select">
						<label class="control-label col-md-4"><?php l("精通语言"); ?> <span class="required">*</span> :</label>
						<div class="col-md-8">
							<?php $mUser->select_language("languages", null, true, array("style" => "opacity:0")); ?>
						</div>
						<div class="control-label-other">
							<?php l("(可多选)"); ?>
						</div>
					</div>
					<?php } ?>
					<?php if ($mUser->user_type == UTYPE_DOCTOR) { ?>
					<div class="form-group form-md-line-select">
						<label class="control-label col-md-4"><?php l("疾病专长"); ?> <span class="required">*</span> :</label>
						<div class="col-md-8">
							<?php $mUser->select_disease("diseases", null, true, array("style" => "opacity:0")); ?>
						</div>
						<div class="control-label-other">
							<?php l("(可多选)"); ?>
						</div>
					</div>
					<div class="form-group form-md-line-input">
						<label class="control-label col-md-4" for="d_title"><?php l("职称"); ?> <span class="required">*</span> :</label>
						<div class="col-md-8">
							<?php $mUser->textarea("d_title", 4, array("required" => "required")); ?>
							<div class="form-control-focus">
							</div>
						</div>
					</div>
					<div class="form-group form-md-line-input">
						<label class="control-label col-md-4" for="d_depart"><?php l("所属科室"); ?> <span class="required">*</span> :</label>
						<div class="col-md-8">
							<?php $mUser->input("d_depart", array("required" => "required", "maxlength" => 255)); ?>
							<div class="form-control-focus">
							</div>
						</div>
					</div>
		
					<div class="form-group form-md-line-input">
						<label class="control-label col-md-4" for="hospitals"><?php l("所属医院"); ?> <span class="required">*</span> :</label>
						<div class="col-md-8">
							<?php $mUser->select_hospital("hospitals", null, true, array("style" => "opacity:0")); ?>
						</div>
						<div class="control-label-other">
							<?php l("(可多选)"); ?>
						</div>
					</div>
					<?php } ?>
					<?php if ($mUser->user_type == UTYPE_INTERPRETER) { ?>
					<div class="form-group form-md-line-input">
						<label class="control-label col-md-4" for="i_age"><?php l("译龄"); ?> <span class="required">*</span> :</label>
						<div class="col-md-8">
							<div class="input-group">
								<?php $mUser->input_number("i_age", array("max" => 90, "min" => 1)); ?>
								<span class="input-group-addon"><?php l("年"); ?></span>
							</div>
							<div class="form-control-focus">
							</div>
						</div>
					</div>
					<?php } ?>
					<?php if ($mUser->user_type == UTYPE_PATIENT || $mUser->user_type == UTYPE_INTERPRETER) { ?>
					<div class="form-group form-md-line-input">
						<label class="control-label col-md-4"><?php l("家庭住址"); ?> <span class="required">*</span> :</label>
						<div class="col-md-8">
							<?php $mUser->input("home_address", array("maxlength" => 255)); ?>
							<div class="form-control-focus">
							</div>
						</div>
					</div>
					<?php } ?>
					<?php if ($mUser->user_type == UTYPE_DOCTOR || $mUser->user_type == UTYPE_INTERPRETER) { ?>
					<div class="form-group form-md-line-input">
						<label class="control-label col-md-4" for="introduction"><?php l("简介"); ?> <span class="required">*</span> :</label>
						<div class="col-md-8">
							<?php $mUser->textarea("introduction", 4, array("placeholder" => _l("请输入您的简介。"))); ?>
							<div class="form-control-focus">
							</div>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-md-4"><?php l("资格证书"); ?> <span class="required">*</span> :<?php if ($mUser->user_type == UTYPE_DOCTOR) { ?><br/><?php l("（或简历）"); } ?></label>
						<div class="col-md-8">
							<ul class="attach-list margin-bottom-0" id="ul_diplomas">
                            </ul>
							<a href="common/upload/diplomas" class="btn-upload fancybox file-upload" fancy-width=600 fancy-height=480>
								 <i class="mdi-content-add"></i>
							</a>
							<?php $mUser->input("diplomas", array("class" => "input-null")); ?>
						</div>
					</div>
					<?php } ?>
					<?php if ($mUser->user_type == UTYPE_PATIENT || $mUser->user_type == UTYPE_INTERPRETER) { ?>
					<div class="form-group">
						<label class="control-label col-md-4"><?php l("身份证件"); ?> <span class="required">*</span> :</label>
						<div class="col-md-8">
							<ul class="attach-list margin-bottom-0" id="ul_passports">
                            </ul>
							<a href="common/upload/passports" class="btn-upload fancybox file-upload" fancy-width=600 fancy-height=480>
								 <i class="mdi-content-add"></i>
							</a>
							<?php $mUser->input("passports", array("class" => "input-null")); ?>
						</div>
					</div>
					<?php } ?>

					<div class="row margin-top-10">
						<div class="col-md-8 col-md-offset-4">
							<button type="submit" class="btn btn-primary"><i class="icon-check"></i> <?php l("确定"); ?></button>
							<a href="<?php p($this->return_url); ?>" class="btn btn-default btn-cancel"><i class="icon-close"></i> <?php l("取消"); ?></a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</form>
</section>