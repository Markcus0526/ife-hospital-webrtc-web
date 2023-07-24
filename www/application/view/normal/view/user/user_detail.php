<?php
$my_type = _my_type();
?>
<section>
	<div class="page-bar">
		<ul class="page-breadcrumb">
			<li>
				<span><?php l("当前位置"); ?> :</span>
			</li>
			<?php if(substr($this->from, 0, 1) == "u") { ?>
			<li>
				<a href="javascript:;"><?php l("用户管理");?></a>
				<i class="fa fa-angle-right"></i>
			</li>
			<?php } ?>
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
			<?php if($this->from == "d") { ?>
			<li>
				<a href="interview/doctors"><?php l("专家库");?></a>
				<i class="fa fa-angle-right"></i>
			</li>
			<?php } ?>
			<?php if(substr($this->from, 0, 1) == "i") { ?>
				<?php if($my_type == UTYPE_ADMIN || $my_type == UTYPE_SUPER) { ?>
			<li>
				<a href="interview"><?php l("会诊管理");?></a>
				<i class="fa fa-angle-right"></i>
			</li>
			<li>
				<a href="interview"><?php l("会诊列表");?></a>
				<i class="fa fa-angle-right"></i>
			</li>
				<?php } ?>
				<?php if($my_type == UTYPE_INTERPRETER) { ?>
			<li>
				<a href="interview"><?php l("翻译中心");?></a>
				<i class="fa fa-angle-right"></i>
			</li>
			<li>
				<a href="interview"><?php l("我的翻译");?></a>
				<i class="fa fa-angle-right"></i>
			</li>
				<?php } ?>
				<?php if($my_type == UTYPE_DOCTOR) { ?>
			<li>
				<a href="interview"><?php l("我的会诊");?></a>
				<i class="fa fa-angle-right"></i>
			</li>
			<li>
				<a href="interview"><?php l("会诊列表");?></a>
				<i class="fa fa-angle-right"></i>
			</li>
				<?php } ?>
			<?php } ?>
			<li>
				<?php p($this->title);?>
			</li>
		</ul>
	</div>
	<form id="profile_form" action="api/profile/save" class="form-horizontal" method="post" novalidate="novalidate">
		<div class="form-body">
			<div class="row">
				<div class="col-sm-2 text-center" style="max-width: 200px;">
					<img id="img_avartar" src="<?php p(_avartar_url($mUser->avartar_id())); ?>" class="large-avartar">
				</div>
				<div class="col-sm-8" style="max-width: 700px;">
					<?php if(($my_type == UTYPE_ADMIN || $my_type == UTYPE_SUPER) && 
						$this->from != "d") { ?>
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
					<div class="form-group static">
						<label class="control-label col-md-4"><?php l("姓名"); ?> :</label>
						<div class="col-md-8">
							<p class="form-control-static"><?php $mUser->detail_l("user_name"); ?></p>
						</div>
					</div>
					<?php if($this->from != "d" &&
						($my_type == UTYPE_ADMIN || $my_type == UTYPE_SUPER || 
							 $my_type == UTYPE_INTERPRETER && $mUser->user_type == UTYPE_PATIENT || 
							 $my_type == UTYPE_DOCTOR && ($mUser->user_type == UTYPE_PATIENT || $mUser->user_type == UTYPE_INTERPRETER ))) { ?>
						<?php if ($mUser->user_type != UTYPE_DOCTOR) { ?>
					<div class="form-group static">
						<label class="control-label col-md-4"><?php l("性别"); ?> :</label>
						<div class="col-md-8">
							<p class="form-control-static"><?php $mUser->detail_code("sex" , CODE_SEX); ?></p>
						</div>
					</div>
						<?php } ?>
						<?php if ($my_type == UTYPE_SUPER) { ?>
					<div class="form-group static">
						<label class="control-label col-md-4"><?php l("国家"); ?> :</label>
						<div class="col-md-8">
							<p class="form-control-static"><?php $mUser->detail("country_name"); ?></p>
						</div>
					</div>
						<?php } ?>
						<?php if ($my_type == UTYPE_ADMIN || $my_type == UTYPE_SUPER || ($my_type == UTYPE_INTERPRETER && $mUser->user_type == UTYPE_PATIENT)) { ?>
					<div class="form-group static">
						<label class="control-label col-md-4"><?php if ($my_type == UTYPE_INTERPRETER && $mUser->user_type == UTYPE_PATIENT)
							l("联系方式"); else l("注册手机"); ?> :</label>
						<div class="col-md-8">
							<p class="form-control-static"><?php $mUser->detail("mobile"); ?></p>
						</div>
					</div>
							<?php if ($mUser->user_type == UTYPE_PATIENT) { ?>
					<div class="form-group static">
						<label class="control-label col-md-4"><?php l("紧急联系方式"); ?> :</label>
						<div class="col-md-8">
							<p class="form-control-static"><?php $mUser->detail("other_tel"); ?></p>
						</div>
					</div>
							<?php } ?>
						<?php } ?>
					<div class="form-group static">
						<label class="control-label col-md-4"><?php l("电子邮箱"); ?> :</label>
						<div class="col-md-8">
							<p class="form-control-static"><?php $mUser->detail("email"); ?></p>
						</div>
					</div>
						<?php if (($my_type == UTYPE_ADMIN || $my_type == UTYPE_SUPER) &&
							($mUser->user_type == UTYPE_DOCTOR || $mUser->user_type == UTYPE_INTERPRETER)) { ?>
					<div class="form-group static">
						<label class="control-label col-md-4"><?php l("精通语言"); ?> :</label>
						<div class="col-md-8">
							<p class="form-control-static"><?php $mUser->detail("languages"); ?></p>
						</div>
					</div>
						<?php } ?>
					<?php } ?>
					<?php if ($mUser->user_type == UTYPE_DOCTOR) { ?>
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
						<?php if ($this->from == "d") { ?>
					<div class="form-group static">
						<label class="control-label col-md-4"><?php l("会诊费"); ?> :</label>
						<div class="col-md-8">
							<p class="form-control-static">
							<?php if($mUser->show_d_fee) { ?>
							<?php $mUser->currency("d_fee", "d_cunit"); ?>
							(<?php l("约"); $mUser->currency("ex_d_fee", "ex_d_cunit"); ?>)
							<span class="text-danger">
								<?php l("* 含翻译费用"); ?>
							</span>
							<?php } else { ?>
							**** <img src="img/shut_eye.png">
							<?php } ?>
							</p>
						</div>
					</div>
						<?php } ?>
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
					<?php if ($my_type == UTYPE_ADMIN || $my_type == UTYPE_SUPER) { ?>
						<?php if ($mUser->user_type == UTYPE_INTERPRETER) { ?>
					<div class="form-group static">
						<label class="control-label col-md-4"><?php l("译龄"); ?> :</label>
						<div class="col-md-8">
							<p class="form-control-static"><?php $mUser->detail("i_age"); ?><?php l("年"); ?></p>
						</div>
					</div>
						<?php } ?>
						<?php if ($mUser->user_type == UTYPE_PATIENT || $mUser->user_type == UTYPE_INTERPRETER) { ?>
					<div class="form-group static">
						<label class="control-label col-md-4"><?php l("家庭住址"); ?> :</label>
						<div class="col-md-8">
							<p class="form-control-static"><?php $mUser->detail("home_address"); ?></p>
						</div>
					</div>
						<?php } ?>
					<?php } ?>
					<?php if ($mUser->user_type == UTYPE_DOCTOR || $mUser->user_type == UTYPE_INTERPRETER) { ?>
						<?php if (!($my_type == UTYPE_DOCTOR && $mUser->user_type == UTYPE_INTERPRETER)) { ?>
					<div class="form-group static">
						<label class="control-label col-md-4"><?php l("简介"); ?> :</label>
						<div class="col-md-8">
							<p class="form-control-static"><?php $mUser->nl2br_l("introduction"); ?></p>
						</div>
					</div>
						<?php } ?>
						<?php if ($my_type == UTYPE_ADMIN || $my_type == UTYPE_SUPER) { ?>
							<?php if ($this->from != "d") { ?>
					<div class="form-group static">
						<label class="control-label col-md-4"><?php l("资格证书"); ?> :<?php if ($mUser->user_type == UTYPE_DOCTOR) { ?><br/><?php l("（或简历）"); } ?></label>
						<div class="col-md-8">
							<?php $mUser->hidden("diplomas"); ?>
							<ul class="attach-list margin-bottom-0" id="ul_diplomas">
                            </ul>
						</div>
					</div>
							<?php } ?>
						<?php } ?>
					<?php } ?>
					<?php if ($mUser->user_type == UTYPE_PATIENT || $mUser->user_type == UTYPE_INTERPRETER) { ?>
						<?php if (!($my_type == UTYPE_DOCTOR || $my_type == UTYPE_INTERPRETER || $my_type == UTYPE_PATIENT)) { ?>
					<div class="form-group static">
						<label class="control-label col-md-4"><?php l("身份证件"); ?> :</label>
						<div class="col-md-8">
							<?php $mUser->hidden("passports"); ?>
							<ul class="attach-list margin-bottom-0" id="ul_passports">
                            </ul>
						</div>
					</div>
						<?php } ?>
					<?php } ?>
					<?php if ($my_type == UTYPE_DOCTOR &&
						($mUser->user_type == UTYPE_PATIENT || $mUser->user_type == UTYPE_INTERPRETER)) { ?>
					<div class="form-group static">
						<label class="control-label col-md-4 text-danger"><?php l("注意"); ?> :</label>
						<div class="col-md-8 text-danger">
							<p class="form-control-static">
								<?php l("为了确保邮件沟通的高效性，请您将邮件同时抄送给客服人员"); ?><br/>
								<?php l("（邮箱地址：{0}）。", implode(",", userModel::get_nation_admin_emails())); ?>
							</p>
						</div>
					</div>
					<?php } ?>

					<div class="row margin-top-10">
						<div class="col-md-8 col-md-offset-4">
						<?php if($this->from == 'd') { ?>
							<a href="interview/reserve/<?php p($mUser->user_id); ?>" class="btn btn-primary btn-reserve"><?php l("预约"); ?></a>
							<a href="javascript:history.go(-1);" class="btn btn-primary"><?php l("返回"); ?></a>
						<?php } else if(_my_type() == UTYPE_ADMIN || _my_type() == UTYPE_SUPER) { ?>
							<a href="user/edit/<?php p($mUser->user_id); ?>" class="btn btn-primary"><i class="icon-pencil"></i> <?php l("编辑"); ?></a>
						<?php } ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</form>
	
	<div id="alert_reserve" class="modal fade">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title"><?php l("提示"); ?></h4>
				</div>
				<div class="modal-body">
					<h3>
						<?php l("在预约前，请确认您的病历已完善且符合要求！"); ?>
					</h3>
				</div>
				<div class="modal-footer">
					<a href="" id="link_reserve" class="btn btn-primary"><?php l("确定"); ?></a>
				</div>
			</div>
		</div>
	</div>
</section>