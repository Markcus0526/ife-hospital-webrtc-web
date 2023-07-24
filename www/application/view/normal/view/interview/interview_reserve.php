<section>
	<div class="page-bar">
		<ul class="page-breadcrumb">
			<li>
				<span><?php l("当前位置"); ?> :</span>
			</li>
			<li>
				<a href="interview"><?php l("我的会诊");?></a>
				<i class="fa fa-angle-right"></i>
			</li>
			<li>
				<?php l("预约会诊");?>
			</li>
		</ul>
	</div>
	<form id="form" action="api/interview/save" class="form-horizontal" method="post">
		<?php $mInterview->hidden("doctor_id"); ?>
		<?php $mInterview->hidden("reserved_starttime"); ?>
		<?php $mInterview->hidden("reserved_endtime"); ?>
		<?php $mInterview->hidden("cunit"); ?>
		<?php $mInterview->hidden("d_cost"); ?>
		<?php $mInterview->hidden("i_cost"); ?>
		<?php $mInterview->hidden("ex_cunit"); ?>
		<?php $mInterview->hidden("ex_rate"); ?>
		<div class="form-wizard">
			<div class="form-body">
				<ul class="steps">
					<li>
						<a href="#step1" data-toggle="tab" class="step active">
							<span class="number">1 </span>
							<span class="desc"> <?php l("选择会诊时间"); ?> </span>
						</a>
					</li>
					<li>
						<a href="#step2" data-toggle="tab" class="step">
							<span class="number">2 </span>
							<span class="desc"> <?php l("填写患者信息"); ?> </span>
						</a>
					</li>
					<li>
						<a href="#step3" data-toggle="tab" class="step">
							<span class="number">3 </span>
							<span class="desc"> <?php l("是否需要翻译"); ?> </span>
						</a>
					</li>
					<li>
						<a href="#step4" data-toggle="tab" class="step">
							<span class="number">4 </span>
							<span class="desc"> <?php l("确认预约信息"); ?> </span>
						</a>
					</li>
				</ul>
				<div class="tab-content">
					<div class="tab-pane active" id="step1">
						<div class="row margin-top-20">
							<div class="col-md-12 text-center">
								<div id="sel_date" class="sel-datepicker-inline" data-date="<?php p(_date($mInterview->date)); ?>">
								</div>			
								<span id="sel_dtimes" class="sel-dtimes-inline text-left">
									<h2><?php l("专家"); ?> : <?php $mDoctor->detail("user_name"); ?></h2>
									<h2><?php l("时间选择"); ?> : </h2>
									<ul class="dtime-list">
									</ul>
								</span>
							</div>
						</div>
					</div>
					<div class="tab-pane" id="step2">
						<?php if(_my_type() != UTYPE_PATIENT) {?>
						<div class="form-group form-md-line-input">
							<label class="control-label col-md-4" for="patient_id"><?php l("患者"); ?> <span class="required">*</span> :</label>
							<div class="col-md-8">
								<?php $mInterview->select2("patient_id", "patient_name"); ?>
								<div class="form-control-focus">
								</div>
							</div>
						</div>
						<?php } ?>
						<div id="chistory_lang" class="<?php if(_my_type() != UTYPE_PATIENT) {?>hide<?php } ?>">
							<div class="form-group form-md-line-input">
								<label class="control-label col-md-4" for="chistory_id"><?php l("病历档案"); ?> <span class="required">*</span> :</label>
								<div class="col-md-8">
									<?php $mInterview->select2("chistory_id", "chistory_name"); ?>
									<div class="form-control-focus">
									</div>
								</div>
							</div>
							<div class="form-group form-md-line-input">
								<label class="control-label col-md-4" for="patient_name"><?php l("姓名"); ?> :</label>
								<div class="col-md-8">
									<p class="form-control-static" id="patient_name"><?php $mInterview->detail("patient_name"); ?></p>
								</div>
							</div>
							<div class="form-group form-md-line-input">
								<label class="control-label col-md-4"><?php l("性别"); ?> :</label>
								<div class="col-md-8">
									<p class="form-control-static" id="patient_sex_name"><?php $mInterview->detail_code("patient_sex" , CODE_SEX); ?></p>
								</div>
							</div>
							<div class="form-group form-md-line-input">
								<label class="control-label col-md-4"><?php l("出生日期"); ?> :</label>
								<div class="col-md-8">
									<p class="form-control-static" id="patient_birthday"><?php $mInterview->detail("patient_birthday"); ?>
									</p>
								</div>
							</div>
							<div class="form-group form-md-line-input">
								<label class="control-label col-md-4"><?php l("疾病种类"); ?> :</label>
								<div class="col-md-8">
									<p class="form-control-static" id="disease_name"><?php $mInterview->detail("disease_name"); ?>
									</p>
								</div>
							</div>
							<div class="form-group form-md-line-select">
								<label class="control-label col-md-4" for="planguage_id"><?php l("精通语言"); ?> <span class="required">*</span> :</label>
								<div class="col-md-3">
									<?php $mInterview->select_model("planguage_id", new languageModel, "language_id", "language_name", _l("请选择"), null, array("required" => "required", "style" => "opacity:0"), true); ?>
								</div>
								<p class="form-control-static text-danger col-md-5">
									* <?php l("请您认真选择您的精通语言，当您需要翻译服务时，它会作为翻译接单时的重要依据。"); ?>
								</p>
							</div>
						</div>
					</div>
					<div class="tab-pane" id="step3">
						<div class="form-group form-md-line-input">
							<label class="control-label col-md-4"><?php l("患者精通语言"); ?> :</label>
							<div class="col-md-8">
								<p class="form-control-static" id="planguage_name"><?php $mInterview->detail("planguage_name"); ?></p>
							</div>
						</div>
						<div class="form-group form-md-line-input">
							<label class="control-label col-md-4"><?php l("专家精通语言"); ?> :</label>
							<div class="col-md-8">
								<p class="form-control-static" id="doctor_languages"><?php $mInterview->detail("doctor_languages"); ?></p>
							</div>
						</div>
						<div class="form-group form-md-line-input">
							<label class="control-label col-md-4"><?php l("是否需要翻译"); ?> :</label>
							<div class="col-md-3">
								<?php $mInterview->radio("need_interpreter", CODE_YESNO); ?>
							</div>
							<p class="form-control-static text-danger col-md-5">
								* <?php l("翻译职能：翻译病历、会诊翻译、翻译第二诊疗意见。"); ?>
							</p>
						</div>
					</div>
					<div class="tab-pane" id="step4">
						<div class="form-group form-md-line-input">
							<label class="control-label col-md-4"><?php l("会诊时间"); ?> :</label>
							<div class="col-md-8">
								<p class="form-control-static" id="interview_datetimes"><?php $mInterview->detail("interview_datetimes"); ?></p>
							</div>
						</div>
						<div class="form-group form-md-line-input">
							<label class="control-label col-md-4"><?php l("患者"); ?> :</label>
							<div class="col-md-8">
								<p class="form-control-static" id="patient_name2"><?php $mInterview->detail("patient_name"); ?></p>
							</div>
						</div>
						<div class="form-group form-md-line-input">
							<label class="control-label col-md-4"><?php l("专家"); ?> :</label>
							<div class="col-md-8">
								<p class="form-control-static" id="doctor_name"><?php $mDoctor->detail("user_name"); ?></p>
							</div>
						</div>
						<div class="form-group form-md-line-input">
							<label class="control-label col-md-4"><?php l("翻译"); ?> :</label>
							<div class="col-md-8">
								<p class="form-control-static" id="need_interpreter_label"><?php $mInterview->detail_code("need_interpreter_label", CODE_NEED); ?></p>
							</div>
						</div>
						<div class="form-group form-md-line-input">
							<label class="control-label col-md-4"><?php l("会诊费"); ?> :</label>
							<div class="col-md-8">
								<p class="form-control-static">
									<?php p(_cunit($mInterview->cunit)); ?> <span id="cost"></span>
									&nbsp;&nbsp;
									(<?php l("约"); p(_cunit($mInterview->ex_cunit)); ?> <span id="ex_cost"></span>)
									&nbsp;&nbsp;&nbsp;
									<span class="text-danger">* <?php l("今日汇率："); ?> <?php p(_cunit($mInterview->cunit)); ?>1=<?php p(_cunit($mInterview->ex_cunit)); $mInterview->detail("ex_rate"); ?></span>
								</p>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="form-actions">
			<div class="row">
				<div class="col-md-12 text-center">
					<button type="button" class="btn btn-primary button-previous">
						<?php l("上一步"); ?> 
					</button>
					<button type="button" class="btn btn-primary button-next">
						<?php l("下一步"); ?>
					</button>
					<button type="button" class="btn btn-primary button-submit">
						<?php l("提交"); ?>
					</buttn>
				</div>
			</div>
		</div>
	</form>
</section>
