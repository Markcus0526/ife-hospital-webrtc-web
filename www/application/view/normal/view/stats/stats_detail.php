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
				<a href="stats"><?php l("统计分析");?></a>
				<i class="fa fa-angle-right"></i>
			</li>
			<li>
				<a href="stats"><?php l("次数与时长");?></a>
				<i class="fa fa-angle-right"></i>
			</li>
			<li>
				<?php l("会诊记录详情");?>
			</li>
		</ul>
	</div>

	<form id="list_form" class="table-container" method="post">
		<?php $this->search->hidden("sort_field"); ?>
		<?php $this->search->hidden("sort_order"); ?>
		<div id="div_refresh">
			<table class="table table-striped table-hover table-bordered table-condensed">
				<thead>
					<tr>
						<th><?php $this->search->order_label('reserved_starttime', _l('会诊日期')); ?></th>
						<th><?php l('会诊时间'); ?></th>
						<th><?php $this->search->order_label('patient_id', _l('患者')); ?></th>
						<th><?php $this->search->order_label('doctor_id', _l('专家')); ?></th>
						<th><?php $this->search->order_label('interpreter_id', _l('翻译')); ?></th>
						<th><?php $this->search->order_label('cost', _l('会诊费')); ?></th>
						<th><?php l('会诊时长'); ?></th>
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
					<tr class="tr-interview-header" interview_id="<?php p($interview->interview_id); ?>">
						<td class="<?php p($td_class); ?>" colspan="6">
							<?php l("会诊编号"); ?>：<?php $interview->detail("interview_id"); ?>

							<?php l("会诊状态"); ?>：<span class="status" status="<?php p($interview->status); ?>"><?php $interview->detail_code("status", CODE_ISTATUS); ?></span>
						</td>
						<td class="text-center <?php p($td_class); ?>">
							<a href="javascript:;" class="active expand-details" ><?php l("查看详情"); ?></a>
							<a href="javascript:;" class="active hide collapse-details"><?php l("收起"); ?></a>
						</td>
					</tr>
					<tr class="tr-interview-body" interview_id="<?php p($interview->interview_id); ?>">
						<td class="text-center td-interview-date <?php p($td_class); ?>"><?php $interview->date("reserved_starttime"); ?></td>
						<td class="text-center td-interview-times <?php p($td_class); ?>"><?php $interview->time("reserved_starttime"); ?>-<?php $interview->time("reserved_endtime"); ?></td>
						<td class="text-center <?php p($td_class); ?>">
							<a href="user/detail/<?php p($interview->patient_id); ?>/i<?php p($interview->interview_id); ?>"><?php $interview->detail("patient_name"); ?></a>
						</td>
						<td class="text-center <?php p($td_class); ?>">
							<a href="user/detail/<?php p($interview->doctor_id); ?>/i<?php p($interview->interview_id); ?>"><?php $interview->detail("doctor_name"); ?></a>
						</td>
						<td class="text-center <?php p($td_class); ?>">
						<?php if($interview->need_interpreter!=1) { ?>
							--
						<?php } else if($interview->interpreter_id) { ?>
							<a href="user/detail/<?php p($interview->interpreter_id); ?>/i<?php p($interview->interview_id); ?>"><?php $interview->detail("interpreter_name"); ?></a>
						<?php } ?>
						</td>
						<td class="text-right <?php p($td_class); ?>">
							<?php $interview->currency("cost", "cunit"); ?>
						</td>
						<td class="text-center <?php p($td_class); ?>"><?php $interview->seconds("interview_seconds", null); ?></td>
					</tr>
					<tr class="tr-interview-action" interview_id="<?php p($interview->interview_id); ?>">
						<td class="<?php p($td_class); ?>" colspan="5">
							<div class="details logs hide">
								<h4><?php l("订单跟踪"); ?></h4>
								<ul>
									<?php 
									foreach ($interview->logs as $log) {
									?>
									<li>
										<label><?php $log->detail('time'); ?></label>
										<p><?php $log->nl2br("message"); ?></p>
									</li>
									<?php
									}
									?>
								</ul>
							</div>
						</td>
						<td colspan="2" class="text-center <?php p($td_class); ?>">
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
		  
			<?php $this->pagebar->display(_url("stats/detail/" . $this->user_id . "/")); ?>
		</div>
	</form>
</section>