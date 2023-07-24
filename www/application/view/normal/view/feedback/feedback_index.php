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
				<a href="feedback"><?php l("用户反馈");?></a>
				<i class="fa fa-angle-right"></i>
			</li>
			<li>
				<?php l("反馈列表");?>
			</li>
		</ul>
	</div>

	<form id="list_form" class="table-container" method="post">
		<?php $this->search->hidden("sort_field"); ?>
		<?php $this->search->hidden("sort_order"); ?>
		<?php $this->search->hidden("from_date"); ?>
		<?php $this->search->hidden("to_date"); ?>

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
			</div>
		</div>
		<div class="row margin-bottom-10">
			<?php if ($my_type == UTYPE_SUPER) { ?>
			<div class="col-sm-2">
				<div class="input-group">
					<span class="input-group-addon no-border">
						<?php l("国家"); ?>:
					</span>
					<?php $this->search->select_model("country_id", new countryModel, "country_id", "country_name", _l("全部")); ?>				
				</div>
			</div>
			<?php } ?>
			<div class="col-lg-2 col-sm-3 col-xs-5">
				<div class="input-group">
					<span class="input-group-addon no-border">
						<?php l("角色"); ?>:
					</span>
					<?php $this->search->select_utype("user_type", _l("全部"), null, array(UTYPE_PATIENT, UTYPE_DOCTOR, UTYPE_INTERPRETER)); ?>
				</div>
			</div>
			<div class="col-lg-2 col-sm-3 col-xs-5">
				<div class="input-group">
					<span class="input-group-addon no-border">
						<?php l("状态"); ?>:
					</span>
					<?php $this->search->select_code("status", CODE_FSTATUS, _l("全部")); ?>
				</div>
			</div>
		</div>
		<table class="table table-striped table-hover table-bordered table-condensed table-feedback">
			<thead>
				<tr>
					<th class="td-no"><?php l('序号'); ?></th>
					<th><?php $this->search->order_label('create_time', _l('反馈日期')); ?></th>
					<th><?php l('反馈时间'); ?></th>
					<?php if ($my_type == UTYPE_SUPER) { ?>
					<th><?php $this->search->order_label('country_id', _l('国家')); ?></th>
					<?php } ?>
					<th><?php $this->search->order_label('user_type', _l('角色')); ?></th>
					<th><?php l('姓名'); ?></th>
					<th><?php $this->search->order_label('mobile', _l('手机号')); ?></th>
					<th><?php $this->search->order_label('email', _l('电子邮箱')); ?></th>
					<th class="td-action"><?php l("状态"); ?></th>
				</tr>
			</thead>
			<tbody>
			<?php
				$i = $this->pagebar->start_no();
				foreach ($mFeedbacks as $feedback) {
			?>
				<tr feedback_id="<?php p($feedback->feedback_id); ?>" status="<?php p($feedback->status); ?>">
					<td class="td-no"><?php p($i); ?></td>
					<td class="text-center"><?php $feedback->date("create_time"); ?></td>
					<td class="text-center"><?php $feedback->time("create_time"); ?></td>
					<?php if ($my_type == UTYPE_SUPER) { ?>
					<td class="td-country" country_id="<?php p($feedback->country_id); ?>"></td>
					<?php } ?>
					<td class="text-center"><?php $feedback->detail_code("user_type", CODE_UTYPE); ?></td>
					<td class="text-center"><?php $feedback->detail("user_name"); ?></td>
					<td class="text-center"><?php $feedback->detail("mobile"); ?></td>
					<td class="text-center"><?php $feedback->detail("email"); ?></td>
					<td class="text-center">
						<span class="label <?php if($feedback->status == FSTATUS_UNREAD) {?>label-primary clickable<?php } else { ?>label-gray<?php } ?>"><?php $feedback->detail_code("status", CODE_FSTATUS); ?></span>
					</td>
				</tr>
				<tr feedback_id="<?php p($feedback->feedback_id); ?>" status="<?php p($feedback->status); ?>">
					<td colspan="100%">
						<?php l("反馈内容"); ?>: <?php $feedback->nl2br("comment"); ?>
					</td>
				</tr>
			<?php
					$i ++;
				}
			?>
			</tbody>
		</table>
		<!--/table -->
		<?php _nodata_message($mFeedbacks); ?>

		<?php $this->pagebar->display(_url("feedback/index/")); ?>
		
	</form>
</section>