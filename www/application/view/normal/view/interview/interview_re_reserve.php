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
				<a href="interview"><?php if ($my_type == UTYPE_SUPER || $my_type == UTYPE_ADMIN) l("会诊管理"); else l("我的会诊");?></a>
				<i class="fa fa-angle-right"></i>
			</li>
			<li>
				<a href="interview"><?php l("会诊列表");?></a>
				<i class="fa fa-angle-right"></i>
			</li>
			<li>
				<?php l("重新预约");?>
			</li>
		</ul>
	</div>
	<form id="form" action="api/interview/save" class="form-horizontal" method="post">
		<?php $mInterview->hidden("interview_id"); ?>
		<?php $mInterview->hidden("patient_id"); ?>
		<?php $mInterview->hidden("doctor_id"); ?>
		<?php $mInterview->hidden("chistory_id"); ?>
		<?php $mInterview->hidden("reserved_starttime"); ?>
		<?php $mInterview->hidden("reserved_endtime"); ?>
		<?php $mInterview->hidden("cunit"); ?>
		<?php $mInterview->hidden("d_cost"); ?>
		<?php $mInterview->hidden("i_cost"); ?>
		<?php $mInterview->hidden("ex_cunit"); ?>
		<?php $mInterview->hidden("ex_rate"); ?>
		<?php $mInterview->hidden("need_interpreter"); ?>
		<?php $mInterview->hidden("planguage_id"); ?>
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
								<p class="form-control-static" id="need_interpreter_label"><?php $mInterview->detail_code("need_interpreter", CODE_NEED); ?></p>
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
