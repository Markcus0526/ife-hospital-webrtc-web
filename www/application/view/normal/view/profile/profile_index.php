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
				<?php l("基本信息");?>
			</li>
		</ul>
	</div>
	<form id="form" action="api/profile/save" class="form-horizontal" method="post" novalidate="novalidate">
		<div class="form-body">
			<div class="row">
				<div class="col-sm-2 col-sm-offset-2 text-center">
					<img id="img_avartar" src="<?php p(_avartar_url()); ?>" class="large-avartar">
					<?php $mUser->hidden("avartar"); ?>

					<?php if($my_type == UTYPE_DOCTOR || $my_type == UTYPE_INTERPRETER || $my_type == UTYPE_ADMIN) { ?>
					<div class="upload-button">
						<a href="common/upload/avartar" class="btn btn-primary btn-circle fancybox file-upload" fancy-width=600 fancy-height=480>
							<?php l("上传头像");?>
						</a>
					</div>
					<?php } ?>
				</div>
				<div class="col-sm-8">
					<?php if ($my_type == UTYPE_PATIENT || $my_type == UTYPE_DOCTOR || $my_type == UTYPE_INTERPRETER) { ?>
					<div class="form-group static">
						<label class="control-label col-md-4">
							<?php 
							if ($my_type == UTYPE_PATIENT)
								l("患者编号");
							else if ($my_type == UTYPE_DOCTOR)
								l("专家编号");
							else if ($my_type == UTYPE_INTERPRETER)
								l("翻译编号");
							?> :
						</label>
						<div class="col-md-8">
							<p class="form-control-static"><?php $mUser->detail("user_id"); ?></p>
						</div>
					</div>
					<?php } ?>
					<div class="form-group static">
						<label class="control-label col-md-4"><?php l("姓名"); ?> :</label>
						<div class="col-md-8">
							<p class="form-control-static"><?php p($mUser->user_name); ?></p>
						</div>
					</div>
					<?php if ($mUser->user_type != UTYPE_DOCTOR) { ?>
					<div class="form-group static">
						<label class="control-label col-md-4"><?php l("性别"); ?> :</label>
						<div class="col-md-8">
							<p class="form-control-static"><?php $mUser->detail_code("sex" , CODE_SEX); ?></p>
						</div>
					</div>
					<?php } ?>
					<div class="form-group static">
						<label class="control-label col-md-4"><?php l("注册手机"); ?> :</label>
						<div class="col-md-8">
							<p class="form-control-static"><?php $mUser->detail("mobile"); ?></p>
						</div>
					</div>
					<?php if ($my_type == UTYPE_ADMIN) { ?>
					<div class="form-group static">
						<label class="control-label col-md-4"><?php l("国家"); ?> :</label>
						<div class="col-md-8">
							<p class="form-control-static"><?php $mUser->detail("country_name"); ?></p>
						</div>
					</div>
					<?php } ?>
					<?php if ($my_type == UTYPE_PATIENT) { ?>
					<div class="form-group static">
						<label class="control-label col-md-4"><?php l("紧急联系方式"); ?> :</label>
						<div class="col-md-8">
							<p class="form-control-static"><?php $mUser->detail("other_tel"); ?></p>
						</div>
					</div>
					<?php } ?>
					<div class="form-group static">
						<label class="control-label col-md-4"><?php l("电子邮箱"); ?> :</label>
						<div class="col-md-8">
							<p class="form-control-static"><?php $mUser->detail("email"); ?></p>
						</div>
					</div>
					<?php if ($my_type == UTYPE_DOCTOR || $my_type == UTYPE_INTERPRETER) { ?>
					<div class="form-group static">
						<label class="control-label col-md-4"><?php l("精通语言"); ?> :</label>
						<div class="col-md-8">
							<p class="form-control-static"><?php $mUser->detail("languages"); ?></p>
						</div>
					</div>
					<?php } ?>
					<?php if ($my_type == UTYPE_DOCTOR) { ?>
					<div class="form-group static">
						<label class="control-label col-md-4"><?php l("疾病专长"); ?> :</label>
						<div class="col-md-8">
							<p class="form-control-static"><?php $mUser->detail("diseases"); ?></p>
						</div>
					</div>
					<div class="form-group static">
						<label class="control-label col-md-4"><?php l("职称"); ?> :</label>
						<div class="col-md-8">
							<p class="form-control-static"><?php $mUser->nl2br_l("d_title"); ?></p>
						</div>
					</div>
					<div class="form-group static">
						<label class="control-label col-md-4"><?php l("所属科室"); ?> :</label>
						<div class="col-md-8">
							<p class="form-control-static"><?php $mUser->detail_l("d_depart"); ?></p>
						</div>
					</div>
					<div class="form-group static">
						<label class="control-label col-md-4"><?php l("所属医院"); ?> :</label>
						<div class="col-md-8">
							<p class="form-control-static"><?php $mUser->detail("hospitals"); ?></p>
						</div>
					</div>
					<?php } ?>
					<?php if ($my_type == UTYPE_INTERPRETER) { ?>
					<div class="form-group static">
						<label class="control-label col-md-4"><?php l("译龄"); ?> :</label>
						<div class="col-md-8">
							<p class="form-control-static"><?php $mUser->detail("i_age"); ?><?php l("年"); ?></p>
						</div>
					</div>
					<?php } ?>
					<?php if ($my_type == UTYPE_PATIENT || $my_type == UTYPE_INTERPRETER) { ?>
					<div class="form-group static">
						<label class="control-label col-md-4"><?php l("家庭住址"); ?> :</label>
						<div class="col-md-8">
							<p class="form-control-static"><?php $mUser->detail("home_address"); ?></p>
						</div>
					</div>
					<?php } ?>
					<?php if ($my_type == UTYPE_DOCTOR || $my_type ==UTYPE_INTERPRETER) { ?>
					<div class="form-group static">
						<label class="control-label col-md-4"><?php l("简介"); ?> :</label>
						<div class="col-md-8">
							<p class="form-control-static"><?php $mUser->nl2br_l("introduction"); ?></p>
						</div>
					</div>
					<div class="form-group static">
						<label class="control-label col-md-4"><?php l("资格证书"); ?> :<?php if ($mUser->user_type == UTYPE_DOCTOR) { ?><br/><?php l("（或简历）"); } ?></label>
						<div class="col-md-8">
							<?php $mUser->hidden("diplomas"); ?>
							<ul class="attach-list margin-bottom-0" id="ul_diplomas">
                            </ul>
						</div>
					</div>
					<?php } ?>
					<?php if ($my_type == UTYPE_PATIENT || $my_type == UTYPE_INTERPRETER) { ?>
					<div class="form-group static">
						<label class="control-label col-md-4"><?php l("身份证件"); ?> :</label>
						<div class="col-md-8">
							<?php $mUser->hidden("passports"); ?>
							<ul class="attach-list margin-bottom-0" id="ul_passports">
                            </ul>
						</div>
					</div>
					<?php } ?>
				</div>
			</div>
		</div>

		<div class="form-actions">
			<div class="row">
				<div class="col-md-6 col-md-offset-6">
				<?php if (!($my_type == UTYPE_DOCTOR || $my_type == UTYPE_INTERPRETER || $my_type == UTYPE_ADMIN)) { ?>
					<a href="profile/edit" class="btn btn-primary"><i class="icon-pencil"></i> <?php l("编辑"); ?></a>
				<?php } ?>
				</div>
			</div>
		</div>
	</form>
</section>