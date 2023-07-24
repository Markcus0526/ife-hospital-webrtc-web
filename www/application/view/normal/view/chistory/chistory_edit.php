<?php
$my_type = _my_type();
?>
<section>
	<div class="page-bar">
		<ul class="page-breadcrumb">
			<li>
				<span><?php l("当前位置"); ?> :</span>
			</li>
			<?php 
			if ($my_type == UTYPE_PATIENT) {
			?>
			<li>
				<a href="chistory"><?php l("病历管理");?></a>
				<i class="fa fa-angle-right"></i>
			</li>
			<li>
				<a href="chistory"><?php l("我的病历");?></a>
				<i class="fa fa-angle-right"></i>
			</li>
			<?php 
			} else if ($my_type == UTYPE_INTERPRETER) {
			?>
			<li>
				<a href="interview"><?php l("翻译中心");?></a>
				<i class="fa fa-angle-right"></i>
			</li>
			<li>
				<a href="interview"><?php l("我的翻译");?></a>
				<i class="fa fa-angle-right"></i>
			</li>
			<?php 
			} else {
			?>
			<li>
				<a href="user/patients/chistory"><?php l("病历管理");?></a>
				<i class="fa fa-angle-right"></i>
			</li>
			<li>
				<a href="user/patients/chistory"><?php l("病历列表");?></a>
				<i class="fa fa-angle-right"></i>
			</li>
			<li>
				<a href="chistory/index/<?php p($this->patient_id); ?>"><?php l("患者病历");?></a>
				<i class="fa fa-angle-right"></i>
			</li>
			<?php 
			}
			?>
			<li>
				<?php 
					if ($this->chistory_id) {
						if ($mChistory->trans_flag)
							l("修改翻译版病历");
						else
							l("修改病历");
					}
					else {
						if ($mChistory->trans_flag)
							l("新增翻译版病历");
						else
							l("新增病历");
					}
				?>
			</li>
		</ul>
	</div>
	<form id="form" action="api/chistory/save" class="form-horizontal" method="post" novalidate="novalidate">
		<?php $mChistory->hidden("chistory_id"); ?>
		<?php $mChistory->hidden("user_id"); ?>
		<?php $mChistory->hidden("trans_flag"); ?>
		<?php $mChistory->hidden("interview_id"); ?>
		<?php $mChistory->hidden("org_id"); ?>
		<?php $mChistory->hidden("post_flag"); ?>
		<div class="form-body">
			<?php if (!$mChistory->trans_flag) { ?>
			<div class="pull-right">
				<a href="javascript:;" id="guide_link" class="active"><i class="fa fa-info-circle text-danger"></i> <?php l("《病历填写须知》");?></a>
			</div>
			<?php } ?>
			<div class="row chistory-row">
				<div class="col-md-7 col-lg-6">
					<div class="form-group form-md-line-input chistory-row">
						<label class="control-label col-md-4" for="chistory_name"><?php l("病历名称"); ?> <span class="required">*</span> :</label>
						<div class="col-md-8">
							<?php $mChistory->input("chistory_name", array("maxlength" => 100)); ?>
							<div class="form-control-focus">
							</div>
						</div>
					</div>
				</div>
			</div>
			<h4 class="help-heading"><?php l('请填写患者信息');?></h4>
			<div class="row chistory-row" id="patient_profile">
				<div class="col-md-7 col-lg-6">
					<div class="form-group form-md-line-input">
						<label class="control-label col-md-4" for="patient_name"><?php l("姓名"); ?> <span class="required">*</span> :</label>
						<div class="col-md-8">
							<?php $mChistory->input("patient_name", array("maxlength" => 50)); ?>
							<div class="form-control-focus">
							</div>
						</div>
					</div>
					<div class="form-group <?php if ($mChistory->trans_flag) { ?>static<?php } else { ?>form-md-line-input<?php } ?>">
						<label class="control-label col-md-4"><?php l("性别"); ?> <span class="required">*</span> :</label>
						<div class="col-md-8">
						<?php if ($mChistory->trans_flag) { ?>
							<p class="form-control-static"><?php $mChistory->detail_code("patient_sex" , CODE_SEX); ?> </p>
							<?php $mChistory->hidden("patient_sex"); ?>
						<?php } else { ?>
							<?php $mChistory->radio("patient_sex" , CODE_SEX);?>
						<?php } ?>
						</div>
					</div>
					<div class="form-group <?php if ($mChistory->trans_flag) { ?>static<?php } else { ?>form-md-line-input<?php } ?>">
						<label class="control-label col-md-4" for="birthday"><?php l("出生日期"); ?> <span class="required">*</span> :</label>
						<div class="col-md-8">
						<?php if ($mChistory->trans_flag) { ?>
							<p class="form-control-static">
								<?php $mChistory->date("birthday"); ?>
								<?php $mChistory->hidden("birthday"); ?>
							</p>
						<?php } else { ?>
							<?php $mChistory->datebox("birthday"); ?>
							<div class="form-control-focus">
							</div>
						<?php } ?>
						</div>
					</div>
					<?php if (!$mChistory->trans_flag) { ?>
					<div class="form-group form-md-line-input">
						<label class="control-label col-md-4" for="home_address"><?php l("家庭住址"); ?> <span class="required">*</span> :</label>
						<div class="col-md-8">
							<?php $mChistory->input("home_address", array("maxlength" => 255)); ?>
							<div class="form-control-focus">
							</div>
						</div>
					</div>
					<div class="form-group form-md-line-input">
						<label class="control-label col-md-4" for="passports"><?php l("身份证件"); ?> <span class="required">*</span> :</label>
						<div class="col-md-8">
							<ul class="attach-list margin-bottom-0" id="ul_passports">
                            </ul>
							<a href="common/upload/passports" class="btn-upload fancybox file-upload" fancy-width=600 fancy-height=480>
								 <i class="mdi-content-add"></i>
							</a>
							<?php $mChistory->input("passports", array("class" => "input-null")); ?>
						</div>
					</div>
					<?php } ?>
				</div>
				<?php if ($mChistory->trans_flag) { ?>
				<div class="col-md-5 col-lg-6 text-center col-booth">
					<div class="photo-booth">
						<label><?php l('患者实时头像');?></label>
						<img id="img_cavartar" src="<?php p($mChistory->cavartar); ?>" class="large-cavartar">
					</div>
					<?php $mChistory->hidden("cavartar"); ?>
				</div>
				<?php } else { ?>
				<div class="col-md-5 col-lg-6 col-booth">
					<div class="photo-booth">
						<label><?php l('患者实时头像');?></label>
					<?php 
					if ($my_type == UTYPE_SUPER || 
						$my_type == UTYPE_ADMIN) {
					?>
						<img id="img_cavartar" src="<?php p(_avartar_url($mChistory->avartar_id())); ?>" class="large-cavartar">
						<div class="upload-button">
							<a href="common/upload/cavartar" class="btn btn-primary btn-circle fancybox file-upload" fancy-width=600 fancy-height=480>
								<?php l("上传头像");?>
							</a>
						</div>
					<?php
					} else {
					?>
						<img id="img_cavartar" src="<?php p(_avartar_url($mChistory->avartar_id())); ?>" class="large-cavartar">
						<a href="common/booth/cavartar" class="btn-booth fancybox"> 
							<i class="mdi-image-photo-camera"></i>
							<span><?php l("点此拍摄");?></span>
						</a>
					<?php
					}
					?>
					</div>
					<?php $mChistory->hidden("cavartar"); ?>
				</div>
				<?php } ?>
			</div>
			<h4 class="help-heading"><?php l('请仔细填写会诊信息');?></h4>
			<div class="row chistory-row" id="row_select_disease">
				<div class="col-md-7 col-lg-6">
				<?php if ($mChistory->trans_flag) { ?>
					<div class="form-group static chistory-row">
						<label class="control-label col-md-4 text-left" for="disease_id"><?php l("疾病种类"); ?> <span class="required">*</span> :</label>
						<div class="col-md-8">
							<p class="form-control-static"><?php $mChistory->detail("disease_name"); ?>
							</p>
						</div>
					</div>
					<?php $mChistory->hidden("disease_id"); ?>
				<?php } else { ?>
					<div class="form-group form-md-line-select chistory-row">
						<label class="control-label col-md-4 text-left" for="disease_id"><?php l("疾病种类"); ?> <span class="required">*</span> :</label>
						<div class="col-md-8 group-disease">
							<?php $mChistory->select_disease("disease_id", _l("请选择"), false, array("required" => "required", "style" => "opacity:0")); ?>
							<div class="form-control-focus">
							</div>
						</div>
					</div>
				<?php } ?>
				</div>
			</div>
			<div class="form-group form-md-line-input">
				<label class="control-label col-md-12 left" for="want_resolve_problem"><?php l("远程会诊想要解决的具体问题"); ?> <span class="required">*</span> :</label>
				<div class="col-md-12">
					<?php $mChistory->textarea("want_resolve_problem", 3); ?>
					<div class="form-control-focus">
					</div>
				</div>
			</div>
			<h4 class="help-heading"><?php l('请上传相关报告和材料（请上传清晰的电子版）');?></h4>
			<div class="cattaches-container">
			</div>
			<h4 class="help-heading"><?php l('医疗既往史情况');?> :</h4>
			<div class="form-group form-md-line-input">
				<label class="control-label col-md-12 left" for="sensitive_medicine"><?php l("已知的过敏药物"); ?> <span class="required">*</span> :</label>
				<div class="col-md-12">
					<?php $mChistory->textarea("sensitive_medicine", 2); ?>
					<div class="form-control-focus">
					</div>
				</div>
			</div>
			<div class="form-group form-md-line-input">
				<label class="control-label col-md-12 left" for="smoking_drinking"><?php l("吸烟，饮酒史"); ?> <span class="required">*</span> :</label>
				<div class="col-md-12">
					<?php $mChistory->textarea("smoking_drinking", 2); ?>
					<div class="form-control-focus">
					</div>
				</div>
			</div>
			<div class="form-group form-md-line-input">
				<label class="control-label col-md-12 left" for="chronic_disease"><?php l("长期的慢性疾病"); ?> <span class="required">*</span> :</label>
				<div class="col-md-12">
					<?php $mChistory->textarea("chronic_disease", 2); ?>
					<div class="form-control-focus">
					</div>
				</div>
			</div>
			<div class="form-group form-md-line-input">
				<label class="control-label col-md-12 left" for="family_disease"><?php l("相关的家族病史"); ?> <span class="required">*</span> :</label>
				<div class="col-md-12">
					<?php $mChistory->textarea("family_disease", 2); ?>
					<div class="form-control-focus">
					</div>
				</div>
			</div>
			<div class="form-group form-md-line-input">
				<label class="control-label col-md-12 left" for="note"><?php l("其它补充"); ?> :</label>
				<div class="col-md-12">
					<?php $mChistory->textarea("note", 2); ?>
					<div class="form-control-focus">
					</div>
				</div>
			</div>
		</div>

		<div class="form-actions">
			<button type="button" id="btn_save" class="btn btn-primary"><i class="icon-check"></i> <?php l("保存"); ?></button>
			<a href="chistory/index/<?php p($this->patient_id); ?>" class="btn btn-default"><i class="icon-close"></i> <?php l("取消"); ?></a>
		</div>
	</form>

	<div id="guide_view" class="modal fade">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title"><?php l("病历填写须知"); ?></h4>
				</div>
				<div class="modal-body contract-container">
					<?php $mChistory->detail_html("guide"); ?>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default btn-close-cancel-form" data-dismiss="modal"><?php l("确认"); ?></button>
				</div>
			</div>
		</div>
	</div>

	<div id="cattach_template" class="hide">
		<div class="col-md-6">
			<div class="form-group">
				<label class="control-label col-md-3 text-left"></label>
				<div class="col-md-9">
					<div class="down-dtemplate"></div>
					<ul class="attach-list margin-bottom-0">
	                </ul>
					<a href="common/upload/" class="btn-upload fancybox file-upload" fancy-width=600 fancy-height=480>
						 <i class="mdi-content-add"></i>
					</a>
					<input type="text" class="cattach-files input-null">
					<input type="hidden" class="cattach-id">
					<input type="hidden" class="dtemplate-id">
				</div>
			</div>
		</div>

		<p class="alert-sel-disease">
			<?php l("请选择<a href=javascript:; id=sel_disease class=active>疾病种类</a>"); ?>
		</p>
	</div>
</section>