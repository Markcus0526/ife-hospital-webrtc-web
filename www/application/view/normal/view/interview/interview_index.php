<?php 
$my_type = _my_type();
if (!$this->is_refresh()) {
?>
 <section>
	<div class="page-bar">
		<ul class="page-breadcrumb">
			<li>
				<span><?php l("当前位置"); ?> :</span>
			</li>
			<li>
				<?php if ($my_type == UTYPE_INTERPRETER) { ?>
				<a href="interview"><?php l("翻译中心");?></a>
				<?php } else if ($my_type == UTYPE_SUPER || $my_type == UTYPE_ADMIN) { ?>
				<a href="interview"><?php l("会诊管理");?></a>
				<?php } else { ?>
				<a href="interview"><?php l("我的会诊");?></a>
				<?php } ?>
				<i class="fa fa-angle-right"></i>
			</li>
			<li>
				<?php 
				if ($my_type == UTYPE_INTERPRETER) { 
					l("我的翻译");
				} else { 
					l("会诊列表");
				} 
				?>
			</li>
		</ul>
	</div>

	<form id="list_form" class="table-container" method="post">
		<?php $this->search->hidden("sort_field"); ?>
		<?php $this->search->hidden("sort_order"); ?>
		<?php $this->search->hidden("from_date"); ?>
		<?php $this->search->hidden("to_date"); ?>
		<?php $this->search->hidden("istatuses"); ?>

		<div class="row margin-bottom-10">
			<div class="col-xs-8">
				<div class="input-group">
					<span class="input-group-addon no-border">
						<?php l("日期"); ?>:
					</span>
					<div id="daterange" class="btn default">
						<i class="fa fa-calendar"></i>
						&nbsp; <span> </span>
						<b class="fa fa-angle-down"></b>
					</div>
				</div>
			</div>
			<?php if ($my_type == UTYPE_DOCTOR || $my_type == UTYPE_INTERPRETER) { ?>
			<div class="col-xs-2 text-center">
				<h5><?php l("会诊时长"); ?></h5>
			</div>
			<div class="col-xs-2 text-center">
				<h5><?php l("会诊次数"); ?></h5>
			</div>
			<?php } ?>
		</div>
		<div class="row margin-bottom-10">
			<div class="<?php if ($my_type == UTYPE_DOCTOR || $my_type == UTYPE_INTERPRETER) { ?>col-xs-8<?php } else { ?>col-xs-12<?php } ?>">
				<div class="input-group">
					<span class="input-group-addon no-border">
						<?php l("状态"); ?>:
					</span>
					<div>
						<?php 
						if ($my_type != UTYPE_DOCTOR && $my_type != UTYPE_INTERPRETER)  {
						?>
						<span class="label-istatus label-istatus-none clickable" status="<?php p(ISTATUS_NONE);?>"><?php l('待付款'); ?></span>
						<?php 
						}
						if ($my_type != UTYPE_DOCTOR)  {
						?>
						<span class="label-istatus label-istatus-waiting clickable" status="<?php p(ISTATUS_WAITING);?>"><?php l('待预约'); ?></span>
						<?php 
						} 
						?>
						<?php 
						if (!($my_type == UTYPE_INTERPRETER))  {
						?>
						<span class="label-istatus label-istatus-payed clickable" status="<?php p(ISTATUS_PAYED);?>"><?php l('已付款'); ?></span>
						<?php 
						} 
						?>
						<span class="label-istatus label-istatus-opened clickable" status="<?php p(ISTATUS_OPENED);?>"><?php l('已生效'); ?></span>
						<span class="label-istatus label-istatus-progressing clickable" status="<?php p(ISTATUS_PROGRESSING);?>"><?php l('进行中'); ?></span>
						<span class="label-istatus label-istatus-unfinished clickable" status="<?php p(ISTATUS_UNFINISHED);?>"><?php l('未完成'); ?></span>
						<span class="label-istatus label-istatus-finished clickable" status="<?php p(ISTATUS_FINISHED);?>"><?php l('已完成'); ?></span>
						<span class="label-istatus label-istatus-canceled clickable" status="<?php p(ISTATUS_CANCELED);?>"><?php l('已失效'); ?></span>
					</div>
				</div>
			</div>
			<?php if ($my_type == UTYPE_DOCTOR || $my_type == UTYPE_INTERPRETER) { ?>
			<div class="col-xs-2 text-center">
				<div class="totals-label"><?php $mTotals->seconds("seconds", null); ?></div>
			</div>
			<div class="col-xs-2 text-center">
				<div class="totals-label"><?php $mTotals->number("counts"); ?></div>
			</div>
			<?php } ?>
		</div>
		<?php 
		if (!($my_type == UTYPE_PATIENT))  {
		?>
		<div class="row margin-bottom-10">
			<div class="col-lg-5 col-sm-7">
				<?php $this->search->input("query", array("placeholder" => _l("请输入关键字"))); ?>
			</div>
			<div class="col-sm-1">
				<button type="submit" class="btn btn-primary"><?php l("搜索"); ?></button>
			</div>
		</div>
		<?php 
		} 
		?>
<?php
}
///////////////////////////////////// refresh start ////////////////////////////////////////
?>	
		<div id="div_refresh">
			<table class="table table-striped table-hover table-bordered table-condensed">
				<thead>
					<tr>
						<th width="100px"><?php $this->search->order_label('reserved_starttime', _l('会诊日期')); ?></th>
						<th width="100px"><?php l('会诊时间'); ?></th>
						<th><?php $this->search->order_label('patient_id', _l('患者')); ?></th>
						<th><?php l('病历'); ?></th>
						<?php if($my_type != UTYPE_DOCTOR) { ?>
						<th><?php $this->search->order_label('doctor_id', _l('专家')); ?></th>
						<?php } ?>
						<?php if($my_type != UTYPE_INTERPRETER) { ?>
						<th><?php $this->search->order_label('interpreter_id', _l('翻译')); ?></th>
						<?php } ?>
						<?php if($my_type != UTYPE_DOCTOR && 
							$my_type != UTYPE_INTERPRETER) { ?>
						<th><?php $this->search->order_label('cost', _l('会诊费')); ?></th>
						<?php } ?>
						<?php if($my_type == UTYPE_INTERPRETER) { ?>
						<th><?php l('翻译类型'); ?></th>
						<?php } ?>
						<th width="100px"><?php l('会诊室'); ?></th>
						<?php if($my_type == UTYPE_DOCTOR || $my_type == UTYPE_INTERPRETER) { ?>
						<th width="100px"><?php l('会诊时长'); ?></th>
						<?php } ?>
						<th width="100px"><?php l('第二诊疗意见'); ?></th>
					</tr>
				</thead>
				<tbody>		
				<?php
					$i = $this->pagebar->start_no();
					foreach ($mInterviews as $interview) {
						$td_class = "";
						if ($interview->status == ISTATUS_CANCELED)
							$td_class = "canceled";
				?>
					<tr class="tr-empty-gap">
						<td colspan="100%"></td>
					</tr>
					<tr class="tr-interview-header" interview_id="<?php p($interview->interview_id); ?>">
						<td class="<?php p($td_class); ?>" colspan="<?php if($my_type == UTYPE_DOCTOR) { ?>7<?php } else {?>8<?php } ?>">
							<?php l("会诊编号"); ?>：<?php $interview->detail("interview_id"); ?>

							<?php l("会诊状态"); ?>：<span class="status" status="<?php p($interview->status); ?>"><?php $interview->detail_code("status", CODE_ISTATUS); ?></span>
							
							<?php if($interview->can_change_time()) { ?>
							&nbsp;<a href="interview/change_time/<?php p($interview->interview_id); ?>" class="active change-time" ><?php l("更改时间"); ?></a>
							<?php } ?>

							<?php if($my_type == UTYPE_INTERPRETER ||
								$my_type == UTYPE_SUPER) { ?>
							<a href="javascript:;" class="btn btn-primary btn-xs btn-user-names" patient_id="<?php $interview->detail("patient_id"); ?>" patient_name="<?php $interview->detail("patient_name"); ?>" patient_name_l="<?php $interview->detail("patient_name_l"); ?>" doctor_id="<?php $interview->detail("doctor_id"); ?>" doctor_name="<?php $interview->detail("doctor_name"); ?>" doctor_name_l="<?php $interview->detail("doctor_name_l"); ?>" interpreter_id="<?php $interview->detail("interpreter_id"); ?>" interpreter_name="<?php $interview->detail("interpreter_name"); ?>" interpreter_name_l="<?php $interview->detail("interpreter_name_l"); ?>" planguage_id="<?php $interview->detail("planguage_id"); ?>" dlanguage_id="<?php $interview->detail("dlanguage_id"); ?>">
							<?php l("翻译用户名"); ?></a>
							</a>
							<?php } ?>
						</td>
						<td class="text-center <?php p($td_class); ?>">
							<?php if(_has_priv(CODE_PRIV_INTERVIEWS, PRIV_IHISTORY) || $my_type == UTYPE_PATIENT) { ?>
							<a href="javascript:;" class="active expand-details" ><?php l("查看详情"); ?></a>
							<a href="javascript:;" class="active hide collapse-details"><?php l("收起"); ?></a>
							<?php } ?>
						</td>
					</tr>
					<tr class="tr-interview-body" interview_id="<?php p($interview->interview_id); ?>">
						<td class="text-center td-interview-date <?php p($td_class); ?>"><?php if ($interview->status != ISTATUS_WAITING) $interview->date("reserved_starttime"); ?></td>
						<td class="text-center td-interview-times <?php p($td_class); ?>"><?php if ($interview->status != ISTATUS_WAITING) { $interview->time("reserved_starttime"); ?>-<?php $interview->time("reserved_endtime"); } ?></td>
						<td class="text-center <?php p($td_class); ?>">
							<?php 
							if ($my_type == UTYPE_INTERPRETER || $my_type == UTYPE_DOCTOR || $my_type == UTYPE_ADMIN || $my_type == UTYPE_SUPER) {
							?>
							<a href="user/detail/<?php p($interview->patient_id); ?>/i<?php p($interview->interview_id); ?>" class="active" <?php if ($my_type == UTYPE_INTERPRETER || $my_type == UTYPE_DOCTOR) { ?>target="_blank"<?php } ?>><?php $interview->detail_l("patient_name"); ?> <?php if ($my_type == UTYPE_INTERPRETER) { ?><i class="fa fa-phone"></i><?php } else if($my_type == UTYPE_DOCTOR) {?><img src="img/contact_mail.png"><?php } ?>
							</a>
							<?php
							}
							else {
								$interview->detail_l("patient_name");
							}
							?>
						</td>
						<td class="text-center <?php p($td_class); ?>">
							<?php if($interview->can_view_chistory()) { ?>
							<a href="chistory/detail/<?php p($interview->patient_id); ?>/<?php p($interview->chistory_id); ?>/i<?php p($interview->interview_id); ?>" class="btn btn-primary btn-xs btn-view-chistory" target="<?php if($my_type == UTYPE_INTERPRETER) p("_blank"); ?>"><?php l("查看原版"); ?></a>

							<?php if($interview->can_view_trans_chistory()) { ?>
							<br/>
							<a href="chistory/detail/<?php p($interview->patient_id); ?>/<?php p($interview->trans_chistory_id); ?>/i<?php p($interview->interview_id); ?>" class="btn btn-primary btn-xs" target="<?php if($my_type == UTYPE_INTERPRETER) p("_blank"); ?>"><?php l("查看翻译版"); ?></a>
							<?php } else if($interview->can_insert_trans_chistory()) {  ?>
							<br/>
							<a href="chistory/edit_trans/<?php p($interview->patient_id); ?>/<?php p($interview->chistory_id); ?>/<?php p($interview->interview_id); ?>" class="btn btn-primary btn-xs" target="<?php if($my_type == UTYPE_INTERPRETER) p("_blank"); ?>"><?php 
								if ($interview->edit_trans_chistory_id == null)
									l("新增翻译版"); 
								else
									l("修改翻译版"); 
								?></a>
							<?php } ?>
							<?php } ?>
						</td>
						<?php if($my_type != UTYPE_DOCTOR) { ?>
						<td class="text-center <?php p($td_class); ?>">
							<?php
							if ($interview->can_rereserve()) {
							?>
							<a href="interview/doctors/<?php p($interview->interview_id);?>" class="btn btn-primary btn-xs"><?php l("重新预约"); ?></a>
							<?php		
							}
							if ($interview->doctor_id) {
								if ($my_type == UTYPE_ADMIN || $my_type == UTYPE_SUPER) {
								?>
								<a href="user/detail/<?php p($interview->doctor_id); ?>/i<?php p($interview->interview_id); ?>"><?php $interview->detail_l("doctor_name"); ?></a>
								<?php		
								}
								else {
									$interview->detail_l("doctor_name");
								}
							}
							?>
						</td>
						<?php } ?>
						<?php if($my_type != UTYPE_INTERPRETER) { ?>
						<td class="text-center <?php p($td_class); ?>">
						<?php if($interview->need_interpreter!=1) { ?>
							--
						<?php } else if($interview->interpreter_id) { 
							if ($my_type == UTYPE_ADMIN || $my_type == UTYPE_SUPER || $my_type == UTYPE_DOCTOR) {?>
							<a href="user/detail/<?php p($interview->interpreter_id); ?>/i<?php p($interview->interview_id); ?>" class="active" <?php if ($my_type == UTYPE_DOCTOR) { ?>target="_blank"<?php } ?>><?php $interview->detail_l("interpreter_name"); ?><?php if($my_type == UTYPE_DOCTOR) {?><img src="img/contact_mail.png"><?php } ?></a>
							<?php 
							} else { 
								$interview->detail_l("interpreter_name");
							}
						} 
							if ($interview->can_invite()){ ?>
							<a href="user/interpreters/select<?php p($interview->interview_id); ?>" class="btn btn-primary btn-xs fancybox btn-invite" fancy-width="max" fancy-height="max"><?php l("指派"); ?></a>
						<?php } ?>
						</td>
						<?php } ?>
						<?php if($my_type != UTYPE_DOCTOR && 
							$my_type != UTYPE_INTERPRETER) { ?>
						<td class="text-right <?php p($td_class); ?>" cost="<?php $interview->detail("cost"); ?>" cunit="<?php $interview->detail("cunit"); ?>">
							<?php
							if ($interview->status != ISTATUS_WAITING) {
								$interview->currency("cost", "cunit");
							
								if ($interview->status == ISTATUS_NONE) {
								?>
								(<?php l("约"); $interview->currency("ex_cost", "ex_cunit"); ?>)
								<?php
									if (_has_priv(CODE_PRIV_INTERVIEWS, PRIV_SET_COST)) {
								?>
									<a href="javascript:;" class="btn-change-cost active" title="<?php l("更改"); ?>"><i class="icon-pencil"></i></a>
								<?php
									}
								?>
									<br/>
									<a href="interview/pay/<?php p($interview->interview_id); ?>" class="btn btn-primary btn-sm btn-go-pay" pay_limit_tm="<?php p($interview->pay_limit_tm); ?>" server_now_tm="<?php p($interview->now_tm); ?>"><?php l('付款'); ?> (<span class="timer"></span>)</a>
								<?php 
								}
							}
							?>
						</td>
						<?php } ?>
						<?php if($my_type == UTYPE_INTERPRETER) { ?>
						<td class="text-center <?php p($td_class); ?>">
							<?php $interview->detail("interp_lang_names"); l("互译");?>
						</td>
						<?php } ?>
						<td class="text-center <?php p($td_class); ?>">
							<?php 
							if ($interview->can_enter_room()) {
								if ($interview->before_starttime) { 
							?>
							<button type="button" class="btn btn-primary btn-xs btn-before-starttime"><?php l("进入"); ?></button>
							<?php 
								} else { 
							?>
							<a href="interview/room/<?php p($interview->interview_id); ?>" class="btn btn-primary btn-xs"><?php l("进入"); ?></a>
							<?php 
								}
							} 
							else if ($interview->can_play_record()) { 
							?>
							<a href="interview/play/<?php p($interview->interview_id); ?>" class="btn btn-primary btn-xs fancybox"><?php l("回放"); ?></a>
							<?php 
							} 
							?>
						</td>
						<?php if($my_type == UTYPE_DOCTOR || $my_type == UTYPE_INTERPRETER) { ?>
						<td class="text-center <?php p($td_class); ?>"><?php $interview->seconds("interview_seconds"); ?></td>
						<?php } ?>
						<td class="text-center <?php p($td_class); ?>">
							<?php if($interview->can_view_prescription()) { ?>
							<a href="interview/prescription/<?php p($interview->interview_id); ?>" class="btn btn-primary btn-xs btn-view-chistory" <?php if ($my_type == UTYPE_INTERPRETER) { ?>target="_blank"<?php } ?>><?php if ($my_type == UTYPE_DOCTOR) l("查看"); else l("查看原版"); ?></a>
							<?php } ?>
							<?php if($interview->can_upload_prescription()) { ?>
							<a href="common/upload/<?php p($interview->interview_id); ?>/prescription" class="btn btn-primary btn-xs fancybox file-upload btn-view-chistory" fancy-width=600 fancy-height=480><?php l("上传"); ?></a>
							<?php } ?>
							<?php if($interview->can_upload_trans_prescription()) { ?>
							<br/>
							<a href="common/upload/<?php p($interview->interview_id); ?>/trans_prescription" class="btn btn-primary btn-xs fancybox file-upload" fancy-width=600 fancy-height=480><?php l("上传翻译版"); ?></a>
							<?php } ?>
							<?php if($interview->can_view_trans_prescription()) { ?>
							<br/>
							<a href="interview/prescription/<?php p($interview->interview_id); ?>/1" target="_blank" class="btn btn-primary btn-xs btn-view-chistory"><?php l("查看翻译版"); ?></a>
							<?php } ?>
						</td>
					</tr>
					<tr class="tr-interview-action" interview_id="<?php p($interview->interview_id); ?>">
						<td class="<?php p($td_class); ?>" colspan="<?php if($my_type == UTYPE_DOCTOR) { ?>6<?php } else {?>7<?php } ?>">
							<?php if($interview->is_d_must_accept_patient()) { ?>
							<div class="text-danger margin-bottom-5">
								<?php l("*请您认真查阅病历，决定是否接受该患者？"); ?>
								&nbsp;
								<button type="button" class="btn btn-primary btn-sm btn-reject-patient"><?php l("否"); ?></button>
								<button type="button" class="btn btn-primary btn-sm btn-accept-patient"><?php l("是"); ?></button>
							</div>
							<?php } ?>
							<div class="details logs hide">
								<h4><?php l("订单跟踪"); ?></h4>
								<ul>
									<?php 
									foreach ($interview->logs as $log) {
									?>
									<li>
										<label><?php $log->detail('time'); ?></label>
										<p>
										<?php 
										$log->nl2br("message");
										if ($log->ihistory_type == IHTYPE_PAYED && _has_priv(CODE_PRIV_INTERVIEWS, PRIV_REFUND)) { 
											$refudable_amount = $interview->cost - $interview->refund_amount;
											if ($refudable_amount > 0) {
										?>
										<button type="button" class="btn btn-xs btn-primary btn-refund" refundable_amount="<?php p($refudable_amount); ?>" cunit="<?php $interview->detail("cunit"); ?>"><?php l("退款"); ?></button>
										<?php 
											}
										} 
										?>
										</p>
									</li>
									<?php
									}
									?>
								</ul>
							</div>
						</td>
						<td colspan="2" class="text-center <?php p($td_class); ?>">
							<div class="details hide">
								<?php 
								if ($interview->can_cancel_by_patient()) {
								?>
								<button type="button" class="btn btn-danger btn-sm btn-cancel" status="<?php p($interview->status); ?>" <?php if($interview->is_disable_cancel_by_patient()) p("disabled"); ?>><?php l("取消订单"); ?></button>
								<?php
								}
								?>
								<?php 
								if (_has_priv(CODE_PRIV_INTERVIEWS, PRIV_CLOSE_INTERVIEW) && $interview->status != ISTATUS_CANCELED) {
								?>
								<button type="button" class="btn btn-danger btn-sm btn-close" status="<?php p($interview->status); ?>"><?php l("关闭订单"); ?></button>
								<?php
								}
								?>
							</div>
						</td>
					</tr>
				<?php
						$i ++;
					}
				?>
				</tbody>
			</table>
			<!--/table -->
			<?php _nodata_message($mInterviews); ?>
		  
			<?php $this->pagebar->display(_url("interview/index/")); ?>
		</div>
<?php 
///////////////////////////////////// refresh end ////////////////////////////////////////
if (!$this->is_refresh()) {
?>		
	</form>

	<form id="cancel_form" action="api/interview/cancel" class="form-horizontal modal fade" method="post" novalidate="novalidate">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title"><?php l("提示"); ?></h4>
				</div>
				<div class="modal-body">
					<input type="hidden" name="interview_id">
					<div class="form-group form-md-line-input">
						<label class="control-label col-md-4"><?php l("取消原因"); ?> :</label>
						<div class="col-md-8">
							<?php $mCInterview->select_code("cancel_cause_id" , CODE_CCAUSE); ?>
						</div>
					</div>
					<div class="form-group form-md-line-input">
						<label class="control-label col-md-4"></label>
						<div class="col-md-8">
							<?php $mCInterview->textarea("cancel_cause_note" , 6, array("placeholder" => _l("请在此输入..."))); ?>
						</div>
					</div>

					<div class="alert alert-warning margin-bottom-0">
						<?php l("温馨提示"); ?>:<br/>
						<p><?php l("*订单成功取消后无法恢复"); ?></p>
						<?php if ($my_type == UTYPE_PATIENT) { ?>
						<p id="alert_couldnot_refund"><?php l("*订单取消后，您所支付的会诊费将不予退还，请您慎重操作！"); ?></p>
						<?php } ?>
					</div>
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-primary"><?php l("确定取消"); ?></button>
					<button type="button" class="btn btn-default btn-close-cancel-form" data-dismiss="modal"><?php l("我再想想"); ?></button>
				</div>
			</div>
		</div>
	</form>

	<?php 
	if (_has_priv(CODE_PRIV_INTERVIEWS, PRIV_CLOSE_INTERVIEW)) {
	?>
	<form id="close_form" action="api/interview/close" class="form-horizontal modal fade" method="post" novalidate="novalidate">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title"><?php l("提示"); ?></h4>
				</div>
				<div class="modal-body">
					<input type="hidden" name="interview_id">
					<div class="form-group form-md-line-input">
						<label class="control-label col-md-4"><?php l("关闭理由"); ?> :</label>
						<div class="col-md-8">
							<?php $mCInterview->textarea("cancel_cause_note" , 6, array("placeholder" => _l("请在此输入..."))); ?>
						</div>
					</div>

					<div class="alert alert-warning margin-bottom-0">
						<?php l("温馨提示"); ?>:<br/>
						<p><?php l("*订单成功关闭后无法恢复"); ?></p>
					</div>
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-primary"><?php l("确定"); ?></button>
					<button type="button" class="btn btn-default btn-close-form" data-dismiss="modal"><?php l("取消"); ?></button>
				</div>
			</div>
		</div>
	</form>
	<?php
	}
	?>

	<?php 
	if (_has_priv(CODE_PRIV_INTERVIEWS, PRIV_REFUND)) {
	?>
	<form id="refund_form" action="api/interview/refund" class="form-horizontal modal fade" method="post" novalidate="novalidate">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title"><?php l("退款"); ?></h4>
				</div>
				<div class="modal-body">
					<input type="hidden" name="interview_id">
					<div class="form-group form-md-line-input">
						<label class="control-label col-md-4"><?php l("退款金额"); ?> :</label>
						<div class="col-md-8">
							<div class="input-group">
								<span id="cost_cunit" class="input-group-addon">
								</span>
								<?php $mCInterview->input_number("refund_amount"); ?>
							</div>
						</div>
					</div>
					<div class="form-group form-md-line-input">
						<label class="control-label col-md-4"><?php l("退款原因"); ?> :</label>
						<div class="col-md-8">
							<?php $mCInterview->textarea("refund_note" , 6, array("placeholder" => _l("请在此输入..."))); ?>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-primary"><?php l("确定"); ?></button>
					<button type="button" class="btn btn-default btn-close-refund-form" data-dismiss="modal"><?php l("取消"); ?></button>
				</div>
			</div>
		</div>
	</form>
	<?php
	}
	?>

	<?php 
	if (_has_priv(CODE_PRIV_INTERVIEWS, PRIV_SET_COST)) {
	?>
	<form id="cost_form" action="api/interview/change_cost" class="form-horizontal modal fade" method="post" novalidate="novalidate">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title"><?php l("更改会诊费"); ?></h4>
				</div>
				<div class="modal-body">
					<input type="hidden" name="interview_id">
					<div class="form-group form-md-line-input">
						<label class="control-label col-md-4"><?php l("会诊费"); ?> <span class="required">*</span> :</label>
						<div class="col-md-4">
							<div class="input-group">
								<span id="cost_cunit" class="input-group-addon">
								</span>
								<input type="text" name="cost" class="form-control">
							</div>
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
	<?php
	}
	?>

	<?php if($my_type == UTYPE_DOCTOR) { ?>
	<form id="reject_patient_form" action="api/interview/accept_patient" class="form-horizontal modal fade" method="post" novalidate="novalidate">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title"><?php l("提示"); ?></h4>
				</div>
				<div class="modal-body">
					<input type="hidden" name="interview_id">
					<input type="hidden" name="accept" value="0">
					<div class="form-group form-md-line-input">
						<label class="control-label col-md-3"><?php l("拒绝理由"); ?> :</label>
						<div class="col-md-9">
							<?php $mAInterview->select_code("reject_cause_id" , CODE_RCAUSE); ?>
						</div>
					</div>
					<div class="form-group form-md-line-input">
						<label class="control-label col-md-3"></label>
						<div class="col-md-9">
							<?php $mAInterview->textarea("reject_cause_note" , 6, array("placeholder" => _l("请在此输入..."))); ?>
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

	<form id="accept_patient_form" action="api/interview/accept_patient" class="form-horizontal modal fade" method="post" novalidate="novalidate">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title"><?php l("提示"); ?></h4>
				</div>
				<div class="modal-body">
					<input type="hidden" name="interview_id">
					<input type="hidden" name="accept" value="1">
					<p class="alert alert-main text-center margin-bottom-0">
						<?php l("您确定接受该患者？"); ?>
					</p>

					<div class="alert alert-warning margin-bottom-0">
						<?php l("温馨提示"); ?>:<br/>
						<p><?php l("*确定后将无法取消会诊"); ?></p>
					</div>
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-primary"><?php l("确定"); ?></button>
					<button type="button" class="btn btn-default" data-dismiss="modal"><?php l("取消"); ?></button>
				</div>
			</div>
		</div>
	</form>
	<?php } ?>

	<form id="user_names_form" action="api/interview/save_user_name" method="post" class="form-horizontal modal fade">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title"><?php l("用户名多语言设置"); ?></h4>
				</div>
				<div class="modal-body">
					<input type="hidden" name="patient_id">
					<input type="hidden" name="doctor_id">
					<input type="hidden" name="interpreter_id">
					<div class="form-group form-md-line-input">
						<label class="control-label col-md-3"><?php l("语言"); ?></label>
						<label class="col-md-3 text-center patient_name"><strong></strong><br/>(<?php l("患者"); ?>)</label>
						<label class="col-md-3 text-center doctor_name"><strong></strong><br/>(<?php l("专家"); ?>)</label>
						<label class="col-md-3 text-center interpreter_name"><strong></strong><br/>(<?php l("翻译"); ?>)</label>
					</div>
					<?php 
					$mLanguages = languageModel::get_all_code();
					foreach ($mLanguages as $key => $lang) {
						?>
					<div class="form-group form-md-line-input lang-row" language_id="<?php l($lang["language_id"]); ?>">
						<label class="control-label col-md-3"><?php l($lang["language_name"]); ?> :</label>
						<div class="col-md-3">
							<input type="text" name="patient_name_l['<?php p($lang["language_code"]); ?>']" class="form-control name_l">
						</div>
						<div class="col-md-3">
							<input type="text" name="doctor_name_l['<?php p($lang["language_code"]); ?>']" class="form-control name_l">
						</div>
						<div class="col-md-3">
							<input type="text" name="interpreter_name_l['<?php p($lang["language_code"]); ?>']" class="form-control name_l">
						</div>
					</div>
						<?php
					}
					?>
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-primary"><?php l("确定"); ?></button>
					<button type="button" class="btn btn-default" data-dismiss="modal"><?php l("取消"); ?></button>
				</div>
			</div>
		</div>
	</form>

</section>
<?php
}
?>