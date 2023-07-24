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
				<a href="interview"><?php l("会诊列表");?></a>
				<i class="fa fa-angle-right"></i>
			</li>
			<li>
				<?php l("更改时间");?>
			</li>
		</ul>
	</div>
	<form id="form" action="api/interview/change_time" class="form-horizontal" method="post">
		<?php $mInterview->hidden("interview_id"); ?>
		<?php $mInterview->hidden("reserved_starttime"); ?>
		<?php $mInterview->hidden("reserved_endtime"); ?>
		<div class="row">
			<div class="col-md-12">
				<div id="sel_date" class="sel-datepicker-inline" data-date="<?php p(_date($mInterview->reserved_date)); ?>">
				</div>
				<span id="sel_dtimes" class="sel-dtimes-inline">
					<ul class="dtime-list">
					</ul>
				</span>
			</div>
			<div class="col-md-12">
				<div class="week-time-table">
					<table>
						<thead>
							<td colspan="100%" class="text-right">
								<span class="state" state=2></span><?php l("已预约"); ?>
								<span class="state" state=1></span><?php l("可预约"); ?>
								<span class="state"></span><?php l("不可预约"); ?>
							</td>
						</thead>
						<tbody>
						<?php 
						$weekdays = _weekdays();
						for ($w = 0; $w < 7; $w ++) {
							?>
							<tr>
								<th class="th-date" w="<?php p($w); ?>">
									<?php 
									if ($w == $this->weekday) {
										p($this->date);
									}?>
								</th>
								<th class="th-weekday"><?php p($weekdays[$w]); ?></th>
								<?php
								for ($h = 0; $h < 24; $h ++) {
									?>
									<td class="time" w="<?php p($w); ?>" h="<?php p($h); ?>"></td>
									<?php
								}
								?>
							</tr>
							<?php
						}
						?>
						</tbody>
						<tfoot>
							<tr>
								<td></td>
								<?php
								for ($h = 0; $h <= 24; $h ++) {
									?>
									<td><span><?php p($h); ?></span></td>
									<?php
								}
								?>
							</tr>
						</tfoot>
					</table>
				</div>
			</div>
		</div>

		<div class="form-actions text-center sign-submit">
			<div class="input-group-sign">
				<div class="input-group">
					<span class="input-group-addon no-border">
						<?php l("请确认签字"); ?>:
					</span>
					<?php $mInterview->input('doctor_sign'); ?>
				</div>
			</div>
			<button type="submit" class="btn btn-primary button-submit">
				<?php l("提交"); ?>
			</button>
		</div>
	</form>
</section>
