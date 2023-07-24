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
			<li>
				<?php l("病历详情");?>
			</li>
			<?php 
			} else if ($my_type == UTYPE_DOCTOR) {
			?>
			<li>
				<a href="interview"><?php l("我的会诊");?></a>
				<i class="fa fa-angle-right"></i>
			</li>
			<li>
				<a href="interview"><?php l("会诊列表");?></a>
				<i class="fa fa-angle-right"></i>
			</li>
			<li>
				<?php l("查看病历");?>
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
			<li>
				<?php l("查看病历");?>
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
			<li>
				<?php l("病历详情");?>
			</li>
			<?php 
			}
			?>
		</ul>
	</div>
	<form id="form" action="api/chistory/save" class="form-horizontal" method="post" novalidate="novalidate">
		<?php $mChistory->hidden("chistory_id"); ?>
		<div class="form-body">
			<div class="form-group static chistory-row">
				<label class="control-label col-md-3 col-lg-2" for="chistory_name"><?php l("病历名称"); ?> :</label>
				<div class="col-md-9 col-lg-10">
					<p class="form-control-static"><?php $mChistory->detail("chistory_name"); ?></p>
				</div>
			</div>
			<h4 class="help-heading"><?php l('患者信息');?></h4>
			<div class="row chistory-row" id="patient_profile">
				<div class="col-md-7 col-lg-6">
					<div class="form-group static">
						<label class="control-label col-md-4" for="patient_name"><?php l("姓名"); ?> :</label>
						<div class="col-md-8">
							<p class="form-control-static"><?php $mChistory->detail("patient_name"); ?></p>
						</div>
					</div>
					<div class="form-group static">
						<label class="control-label col-md-4"><?php l("性别"); ?> :</label>
						<div class="col-md-8">
							<p class="form-control-static"><?php $mChistory->detail_code("patient_sex" , CODE_SEX); ?></p>
						</div>
					</div>
					<div class="form-group static">
						<label class="control-label col-md-4"><?php l("出生日期"); ?> :</label>
						<div class="col-md-8">
							<p class="form-control-static"><?php $mChistory->date("birthday"); ?>
							</p>
						</div>
					</div>
					<?php 
					if ($mChistory->can_view_address_passport()) {
					?>
					<div class="form-group static">
						<label class="control-label col-md-4"><?php l("家庭住址"); ?> :</label>
						<div class="col-md-8">
							<p class="form-control-static"><?php $mChistory->detail("home_address"); ?>
							</p>
						</div>
					</div>
					<div class="form-group static">
						<label class="control-label col-md-4"><?php l("身份证件"); ?> :</label>
						<div class="col-md-8">
							<?php $mChistory->hidden("passports"); ?>
							<ul class="attach-list margin-bottom-0" id="ul_passports">
                            </ul>
						</div>
					</div>
					<?php 
					}
					?>
				</div>
				<div class="col-md-5 col-lg-6 col-booth">
					<div class="photo-booth">
						<label><?php l('患者实时头像');?></label>
						<img id="img_cavartar" src="<?php p(_avartar_url($mChistory->avartar_id())); ?>" class="large-cavartar">
					</div>
				</div>
			</div>
			<h4 class="help-heading"><?php l('会诊信息');?></h4>
			<div class="form-group static chistory-row">
				<label class="control-label col-md-3 col-lg-2 text-left" for="disease_id"><?php l("疾病种类"); ?> :</label>
				<div class="col-md-9 col-lg-10">
					<p class="form-control-static"><?php $mChistory->detail("disease_name"); ?>
					</p>
				</div>
			</div>
			<div class="form-group static">
				<label class="control-label col-md-12 left" for="want_resolve_problem"><?php l("远程会诊想要解决的具体问题"); ?> :</label>
				<div class="col-md-12">
					<p class="form-control-static"><?php $mChistory->nl2br("want_resolve_problem"); ?>
					</p>
				</div>
			</div>
			<h4 class="help-heading"><?php l('相关报告和材料');?></h4>
			<div class="cattaches-container">
			</div>
			<h4 class="help-heading"><?php l('医疗既往史情况');?> :</h4>
			<div class="form-group static">
				<label class="control-label col-md-12 left" for="sensitive_medicine"><?php l("已知的过敏药物"); ?> :</label>
				<div class="col-md-12">
					<p class="form-control-static"><?php $mChistory->nl2br("sensitive_medicine"); ?>
					</p>
				</div>
			</div>
			<div class="form-group static">
				<label class="control-label col-md-12 left" for="smoking_drinking"><?php l("吸烟，饮酒史"); ?> :</label>
				<div class="col-md-12">
					<p class="form-control-static"><?php $mChistory->nl2br("smoking_drinking"); ?>
					</p>
				</div>
			</div>
			<div class="form-group static">
				<label class="control-label col-md-12 left" for="chronic_disease"><?php l("长期的慢性疾病"); ?> :</label>
				<div class="col-md-12">
					<p class="form-control-static"><?php $mChistory->nl2br("chronic_disease"); ?>
					</p>
				</div>
			</div>
			<div class="form-group static">
				<label class="control-label col-md-12 left" for="family_disease"><?php l("相关的家族病史"); ?> :</label>
				<div class="col-md-12">
					<p class="form-control-static"><?php $mChistory->nl2br("family_disease"); ?>
					</p>
				</div>
			</div>
			<div class="form-group static">
				<label class="control-label col-md-12 left" for="note"><?php l("其它补充"); ?> :</label>
				<div class="col-md-12">
					<p class="form-control-static"><?php $mChistory->nl2br("note"); ?>
					</p>
				</div>
			</div>
		</div>

		<div class="form-actions">
		<?php 
			if ($my_type == UTYPE_SUPER || $my_type == UTYPE_ADMIN || $my_type == UTYPE_PATIENT && $mChistory->can_edit()) { ?>
			<a href="chistory/edit/<?php p($mChistory->user_id); ?>/<?php p($mChistory->chistory_id); ?>" class="btn btn-primary"><i class="icon-pencil"></i> <?php l("编辑"); ?></a>
		<?php 
			} 
			else if ($my_type == UTYPE_INTERPRETER && $mChistory->can_edit()) { ?>
			<a href="chistory/edit_trans/<?php p($mChistory->user_id); ?>/<?php p($mChistory->org_id); ?>/<?php p($mChistory->interview_id); ?>" class="btn btn-primary"><?php l("编辑"); ?></a>
		<?php 
			}
			if ($this->from_id != "") {
		?>
			<a href="<?php p($this->from_url($this->from_id)); ?>" class="btn btn-primary"><?php l("返回"); ?></a>
		<?php
			}
		?>
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
					<ul class="attach-list margin-bottom-0">
	                </ul>
					<input type="text" class="cattach-files input-null">
				</div>
			</div>
		</div>

		<p class="alert-sel-disease">
			
		</p>
	</div>
</section>