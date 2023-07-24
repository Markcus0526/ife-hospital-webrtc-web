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
				<a href="javascript:;"><?php l("用户信息");?></a>
				<i class="fa fa-angle-right"></i>
			</li>
			<li>
				<a href="profile"><?php l("基本信息");?></a>
				<i class="fa fa-angle-right"></i>
			</li>
			<li>
				<?php l("编辑信息");?>
			</li>
		</ul>
	</div>
	<form id="form" action="api/profile/save" class="form-horizontal" method="post" novalidate="novalidate">
		<div class="form-body">
			<div class="row">
				<div class="col-sm-2 col-sm-offset-2 text-center">
					<img id="img_avartar" src="<?php p(_avartar_url()); ?>" class="large-avartar">
					<?php $mUser->hidden("avartar"); ?>

					<div class="upload-button">
						<a href="common/upload/avartar" class="btn btn-primary btn-circle fancybox file-upload" fancy-width=600 fancy-height=480>
							<?php l("上传头像");?>
						</a>
					</div>
				</div>
				<div class="col-sm-8">
					<div class="form-group form-md-line-input">
						<label class="control-label col-md-3" for="user_name"><?php l("姓名"); ?> <span class="required">*</span> :</label>
						<div class="col-md-9">
							<?php $mUser->input("user_name"); ?>
							<div class="form-control-focus">
							</div>
							<span class="help-block"><?php l('请输入姓名。');?></span>
						</div>
					</div>
					<?php if ($my_type != UTYPE_DOCTOR) { ?>
					<div class="form-group">
						<label class="control-label col-md-3"><?php l("性别"); ?> :</label>
						<div class="col-md-9">
							<?php $mUser->radio("sex" , CODE_SEX); ?>
						</div>
					</div>
					<?php } ?>
					<div class="form-group">
						<label class="control-label col-md-3"><?php l("注册手机"); ?> :</label>
						<div class="col-md-9">
							<p class="form-control-static"><?php $mUser->detail("mobile"); ?></p>
						</div>
					</div>
					<?php if ($my_type == UTYPE_PATIENT) { ?>
					<div class="form-group form-md-line-input">
						<label class="control-label col-md-3" for="other_tel"><?php l("紧急联系方式"); ?> :</label>
						<div class="col-md-9">
							<?php $mUser->tel("other_tel"); ?>
							<div class="form-control-focus">
							</div>
							<span class="help-block"><?php l('请输入紧急联系方式。');?></span>
						</div>
					</div>
					<?php } ?>
					<?php if ($my_type == UTYPE_DOCTOR || $my_type == UTYPE_INTERPRETER) { ?>
					<div class="form-group">
						<label class="control-label col-md-3"><?php l("精通语言"); ?> :</label>
						<div class="col-md-9">
							<?php $mUser->checkbox_language("languages"); ?>
						</div>
					</div>
					<?php } ?>
					<div class="form-group form-md-line-input">
						<label class="control-label col-md-3"><?php l("电子邮箱"); ?> <span class="required">*</span> :</label>
						<div class="col-md-9">
							<?php $mUser->input_email("email"); ?>
						</div>
					</div>
					<?php if ($my_type == UTYPE_DOCTOR) { ?>
					<div class="form-group">
						<label class="control-label col-md-3"><?php l("疾病专长"); ?> :</label>
						<div class="col-md-9">
							<?php $mUser->checkbox_disease("diseases"); ?>
						</div>
					</div>
					<div class="form-group form-md-line-input">
						<label class="control-label col-md-3" for="d_title"><?php l("职称"); ?> <span class="required">*</span> :</label>
						<div class="col-md-9">
							<?php $mUser->textarea("d_title", 2, array("placeholder" => _l("职称"), "required" => "required", "maxlength" => 255)); ?>
							<div class="form-control-focus">
							</div>
							<span class="help-block"><?php l('请输入职称。');?></span>
						</div>
					</div>
					<div class="form-group form-md-line-input">
						<label class="control-label col-md-3" for="d_depart"><?php l("所属科室"); ?> <span class="required">*</span> :</label>
						<div class="col-md-9">
							<?php $mUser->input("d_depart", array("maxlength" => 255)); ?>
							<div class="form-control-focus">
							</div>
						</div>
					</div>
					<div class="form-group form-md-line-input">
						<label class="control-label col-md-3" for="hospitals"><?php l("所属医院"); ?> <span class="required">*</span> :</label>
						<div class="col-md-9">
							<?php $mUser->select_hospital("hospitals", null, true, array("style" => "opacity:0")); ?>
						</div>
					</div>
					<?php } ?>
					<?php if ($my_type == UTYPE_INTERPRETER) { ?>
					<div class="form-group form-md-line-input">
						<label class="control-label col-md-3" for="i_age"><?php l("译龄"); ?> <span class="required">*</span> :</label>
						<div class="col-md-9">
							<div class="input-group">
								<?php $mUser->input_number("i_age"); ?>
								<span class="input-group-addon">年</span>
							</div>
							<div class="form-control-focus">
							</div>
						</div>
					</div>
					<?php } ?>
					<?php if ($my_type == UTYPE_PATIENT || $my_type == UTYPE_INTERPRETER) { ?>
					<div class="form-group form-md-line-input">
						<label class="control-label col-md-3"><?php l("家庭住址"); ?> <span class="required">*</span> :</label>
						<div class="col-md-9">
							<?php $mUser->input("home_address"); ?>
						</div>
					</div>
					<?php } ?>
					<?php if ($my_type == UTYPE_DOCTOR || $my_type == UTYPE_INTERPRETER) { ?>
					<div class="form-group form-md-line-input">
						<label class="control-label col-md-3" for="introduction"><?php l("简介"); ?> <span class="required">*</span> :</label>
						<div class="col-md-9">
							<?php $mUser->textarea("introduction", 5, array("placeholder" => _l("请输入您的简介。"))); ?>
							<div class="form-control-focus">
							</div>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-md-3"><?php l("资格证书"); ?> :<?php if ($my_type == UTYPE_DOCTOR) { ?><br/><?php l("（或简历）"); } ?></label>
						<div class="col-md-9">
							<?php $mUser->hidden("diplomas"); ?>
							<ul class="attach-list margin-bottom-0" id="ul_diplomas">
                            </ul>
							<a href="common/upload/diplomas" class="btn-upload fancybox file-upload" fancy-width=600 fancy-height=480>
								 <i class="mdi-content-add"></i>
							</a>
						</div>
					</div>
					<?php } ?>
					<?php if ($my_type == UTYPE_PATIENT || $my_type == UTYPE_INTERPRETER) { ?>
					<div class="form-group form-md-line-input">
					<div class="form-group">
						<label class="control-label col-md-3"><?php l("身份证件"); ?> <span class="required">*</span> :</label>
						<div class="col-md-9">
							<ul class="attach-list margin-bottom-0" id="ul_passports">
                            </ul>
							<a href="common/upload/passports" class="btn-upload fancybox file-upload" fancy-width=600 fancy-height=480>
								 <i class="mdi-content-add"></i>
							</a>
							<?php $mUser->input("passports", array("class" => "input-null")); ?>
						</div>
					</div>
					<?php } ?>
				</div>
			</div>
		</div>

		<div class="form-actions">
			<div class="row">
				<div class="col-md-6 col-md-offset-6">
					<button type="submit" class="btn btn-primary"><i class="icon-check"></i> <?php l("确定"); ?></button>
					<a href="profile" class="btn btn-default"><i class="icon-close"></i> <?php l("取消"); ?></a>
				</div>
			</div>
		</div>
	</form>
</section>