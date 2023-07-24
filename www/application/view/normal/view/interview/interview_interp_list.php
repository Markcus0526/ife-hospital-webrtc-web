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
				<a href="dtime"><?php l("翻译中心");?></a>
				<i class="fa fa-angle-right"></i>
			</li>
			<li>
				<?php l("翻译列表");?>
			</li>
		</ul>
	</div>

	<form id="list_form" class="table-container form-inline" method="post">
		<?php $this->search->hidden("sort_field"); ?>
		<?php $this->search->hidden("sort_order"); ?>
		<?php $this->search->hidden("from_date"); ?>
		<?php $this->search->hidden("to_date"); ?>
		<?php $this->search->hidden("from_time"); ?>
		<?php $this->search->hidden("to_time"); ?>
		<?php $this->search->hidden("istatuses"); ?>

		<div class="row margin-bottom-10">
			<div class="col-xs-12">
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
				
				<div class="input-group">
					<span class="input-group-addon no-border">
						<?php l("时间"); ?>:
					</span>

					<button type="button" id="btn_select_times" class="btn btn-default">
						<?php $this->search->detail("from_time"); ?>
						~
						<?php $this->search->detail("to_time"); ?>
						<b class="fa fa-angle-down"></b>
					</button>
				</div>
			</div>
		</div>
<?php
}
///////////////////////////////////// refresh start ////////////////////////////////////////
?>	
		<div id="div_refresh">
			<table class="table table-striped table-hover table-bordered table-condensed">
				<thead>
					<tr>
						<th><?php $this->search->order_label('reserved_starttime', _l('会诊日期')); ?></th>
						<th><?php $this->search->order_label('bumon_name', _l('会诊时间')); ?></th>
						<th><?php $this->search->order_label('bbunrui_name', _l('患者')); ?></th>
						<th><?php $this->search->order_label('offer_amount', _l('专家')); ?></th>
						<th><?php l('翻译类型'); ?></th>
						<th><?php l('操作'); ?></th>
					</tr>
				</thead>
				<tbody>		
				<?php
					$i = $this->pagebar->start_no();
					foreach ($mInterviews as $interview) {
				?>
					<tr class="tr-interview-header" interview_id="<?php p($interview->interview_id); ?>">
						<td colspan="100%">
							<?php l("会诊编号"); ?>：<?php $interview->detail("interview_id"); ?>

							<?php l("会诊状态"); ?>：<span class="status" status="<?php p($interview->status); ?>"><?php $interview->detail_code("status", CODE_ISTATUS); ?></span>
						</td>
					</tr>
					<tr class="tr-interview-body" interview_id="<?php p($interview->interview_id); ?>" reserved_starttime="<?php p($interview->reserved_starttime); ?>">
						<td class="text-center td-interview-date"><?php $interview->date("reserved_starttime"); ?></td>
						<td class="text-center td-interview-times"><?php $interview->time("reserved_starttime"); ?>-<?php $interview->time("reserved_endtime"); ?></td>
						<td class="text-center">
							<?php $interview->detail("patient_name"); ?>
						</td>
						<td class="text-center">
							<?php $interview->detail("doctor_name"); ?>
						</td>
						<td class="text-center">
							<?php $interview->detail("interp_lang_names"); l("互译");?>
						</td>
						<td class="text-center">
							<button type="button" class="btn btn-primary btn-xs btn-bid"><?php l("接单"); ?></button>
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
		  
			<?php $this->pagebar->display(_url("interview/interp_list/")); ?>
		</div>
<?php 
///////////////////////////////////// refresh end ////////////////////////////////////////
if (!$this->is_refresh()) {
?>		
	</form>

	<div id="select_times" class="modal fade">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title"><?php l("选择时间"); ?></h4>
				</div>
				<div class="modal-body">
					<form class="form-inline text-center">
						<div class="input-group">
							<?php 
								$this->search->id_prefix = "modal_"; 
							?>
							<?php $this->search->input("from_time", array("class" => "timepicker timepicker-24 input-small text-center")); ?>
							<span class="input-group-addon no-border">
								~
							</span>
							<?php $this->search->input("to_time", array("class" => "timepicker timepicker-24 input-small text-center")); ?>
						</div>
					</form>
				</div>
				<div class="modal-footer">
					<button id="btn_select_times_ok" type="submit" class="btn btn-primary"><?php l("确定"); ?></button>
					<button type="button" class="btn btn-default" data-dismiss="modal"><?php l("取消"); ?></button>
				</div>
			</div>
		</div>
	</div>
</section>
<?php
}
?>