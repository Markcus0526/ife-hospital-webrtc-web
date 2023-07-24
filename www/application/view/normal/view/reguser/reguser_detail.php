<section>
	<div class="page-bar">
		<ul class="page-breadcrumb">
			<li>
				<span><?php l("当前位置"); ?> :</span>
			</li>
			<li>
				<a href="javascript:;"><?php l("审核注册用户");?></a>
				<i class="fa fa-angle-right"></i>
			</li>
			<?php if($this->from == "ud") { ?>
			<li>
				<a href="reguser/doctors"><?php l("审核注册专家");?></a>
				<i class="fa fa-angle-right"></i>
			</li>
			<?php } ?>
			<?php if($this->from == "ui") { ?>
			<li>
				<a href="reguser/interpreters"><?php l("审核注册翻译");?></a>
				<i class="fa fa-angle-right"></i>
			</li>
			<?php } ?>
			<li>
				<?php p($this->title);?>
			</li>
		</ul>
	</div>
	<form id="profile_form" action="api/profile/save" class="form-horizontal" method="post" novalidate="novalidate">
		<div class="form-body">
			<div class="row">
				<div class="col-sm-3 text-center">
					<img src="<?php p(_avartar_url($mReguser->avartar_id())); ?>" class="large-avartar">
				</div>
				<div class="col-sm-9">
					<div class="form-group static">
						<label class="control-label col-md-4">
							<?php 
							if ($mReguser->user_type == UTYPE_DOCTOR) 
								l("专家编号"); 
							else if ($mReguser->user_type == UTYPE_INTERPRETER) 
								l("翻译编号"); 
							?> :
						</label>
						<div class="col-md-8">
							<p class="form-control-static"><?php p($mReguser->reguser_id); ?></p>
						</div>
					</div>
					<div class="form-group static">
						<label class="control-label col-md-4"><?php l("姓名"); ?> :</label>
						<div class="col-md-8">
							<p class="form-control-static"><?php p($mReguser->user_name); ?></p>
						</div>
					</div>
					<?php if ($mReguser->user_type != UTYPE_DOCTOR) { ?>
					<div class="form-group static">
						<label class="control-label col-md-4"><?php l("性别"); ?> :</label>
						<div class="col-md-8">
							<p class="form-control-static"><?php $mReguser->detail_code("sex" , CODE_SEX); ?></p>
						</div>
					</div>
					<?php } ?>
					<div class="form-group static">
						<label class="control-label col-md-4"><?php l("国家"); ?> :</label>
						<div class="col-md-8">
							<p class="form-control-static"><?php $mReguser->detail("country_name"); ?></p>
						</div>
					</div>
					<div class="form-group static">
						<label class="control-label col-md-4"><?php l("注册手机"); ?> :</label>
						<div class="col-md-8">
							<p class="form-control-static"><?php $mReguser->detail("mobile"); ?></p>
						</div>
					</div>
					<div class="form-group static">
						<label class="control-label col-md-4"><?php l("电子邮箱"); ?> :</label>
						<div class="col-md-8">
							<p class="form-control-static"><?php $mReguser->detail("email"); ?></p>
						</div>
					</div>
					<div class="form-group static">
						<label class="control-label col-md-4"><?php l("精通语言"); ?> :</label>
						<div class="col-md-8">
							<p class="form-control-static"><?php $mReguser->detail("languages"); ?></p>
						</div>
					</div>
					<?php if ($mReguser->user_type == UTYPE_DOCTOR) { ?>
					<div class="form-group static">
						<label class="control-label col-md-4"><?php l("疾病专长"); ?> :</label>
						<div class="col-md-8">
							<p class="form-control-static"><?php $mReguser->detail("diseases"); ?></p>
						</div>
					</div>
					<div class="form-group static">
						<label class="control-label col-md-4"><?php l("职称"); ?> :</label>
						<div class="col-md-8">
							<p class="form-control-static"><?php $mReguser->nl2br_l("d_title"); ?></p>
						</div>
					</div>
					<div class="form-group static">
						<label class="control-label col-md-4"><?php l("所属科室"); ?> :</label>
						<div class="col-md-8">
							<p class="form-control-static"><?php $mReguser->detail_l("d_depart"); ?></p>
						</div>
					</div>
					<div class="form-group static">
						<label class="control-label col-md-4"><?php l("所属医院"); ?> :</label>
						<div class="col-md-8">
							<p class="form-control-static"><?php $mReguser->detail("hospitals"); ?></p>
						</div>
					</div>
					<?php } ?>
					<?php if ($mReguser->user_type == UTYPE_INTERPRETER) { ?>
					<div class="form-group static">
						<label class="control-label col-md-4"><?php l("译龄"); ?> :</label>
						<div class="col-md-8">
							<p class="form-control-static"><?php $mReguser->detail("i_age"); ?><?php l("年"); ?></p>
						</div>
					</div>
					<?php } ?>
					<?php if ($mReguser->user_type == UTYPE_INTERPRETER) { ?>
					<div class="form-group static">
						<label class="control-label col-md-4"><?php l("家庭住址"); ?> :</label>
						<div class="col-md-8">
							<p class="form-control-static"><?php $mReguser->detail("home_address"); ?></p>
						</div>
					</div>
					<?php } ?>
					<div class="form-group static">
						<label class="control-label col-md-4"><?php l("简介"); ?> :</label>
						<div class="col-md-8">
							<p class="form-control-static"><?php $mReguser->nl2br_l("introduction"); ?></p>
						</div>
					</div>
					<div class="form-group static">
						<label class="control-label col-md-4"><?php l("资格证书"); ?> :<?php if ($mReguser->user_type == UTYPE_DOCTOR) { ?><br/><?php l("（或简历）"); } ?></label>
						<div class="col-md-8">
							<?php $mReguser->hidden("diplomas"); ?>
							<ul class="attach-list margin-bottom-0" id="ul_diplomas">
                            </ul>
						</div>
					</div>
					<?php if ($mReguser->user_type == UTYPE_INTERPRETER) { ?>
					<div class="form-group static">
						<label class="control-label col-md-4"><?php l("身份证件"); ?> :</label>
						<div class="col-md-8">
							<?php $mReguser->hidden("passports"); ?>
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
					<button type="button" id="btn_accept" class="btn btn-primary"><?php l("审核通过"); ?></button>
					<?php if ($mReguser->status == RSTATUS_NONE) { ?>
					<button type="button" id="btn_reject" class="btn btn-primary"><?php l("审核未通过"); ?></button>
					<?php } ?>
				</div>
			</div>
		</div>
	</form>

	<form id="reject_form" action="api/reguser/reject" class="form-horizontal modal fade" method="post" novalidate="novalidate">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title"><?php l("审核未通过"); ?></h4>
				</div>
				<div class="modal-body">
					<?php $mReguser->hidden("reguser_id"); ?>
					<div class="form-group form-md-line-input">
						<label class="control-label col-md-4"><?php l("原因"); ?> :</label>
						<div class="col-md-8">
							<?php $mReguser->textarea("reject_note" , 6, array("placeholder" => _l("请在此输入..."))); ?>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-primary"><?php l("确定"); ?></button>
					<button type="button" class="btn btn-default" data-dismiss="modal"><?php l("取消"); ?></button>
				</div>
			</div>
		</div>
	</form>
</section>