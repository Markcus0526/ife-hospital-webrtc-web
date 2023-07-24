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
			<li>
				<a href="javascript:;"><?php l("审核注册专家");?></a>
				<i class="fa fa-angle-right"></i>
			</li>
			<li>
				<?php if ($this->status == RSTATUS_NONE) { ?>
					<?php l("待审核");?>
				<?php } else { ?>
					<?php l("审核未通过");?>
				<?php } ?>
			</li>
		</ul>
	</div>

	<form id="list_form" class="table-container" method="post">
		<?php $this->search->hidden("sort_field"); ?>
		<?php $this->search->hidden("sort_order"); ?>

		<div class="row margin-bottom-10">
			<div class="col-sm-8">
			</div>
			<div class="col-sm-4 text-right">
				<a href="reguser/doctors/<?php p(RSTATUS_NONE); ?>" class="<?php if($this->status == RSTATUS_NONE) { ?>action-link<?php } ?>"><?php l("待审核"); ?></a>
				|
				<a href="reguser/doctors/<?php p(RSTATUS_REJECT); ?>" class="<?php if($this->status == RSTATUS_REJECT) { ?>action-link<?php } ?>"><?php l("审核未通过"); ?></a>
			</div>
		</div>
		<table class="table table-striped table-hover table-bordered table-condensed">
			<thead>
				<tr>
					<th class="td-no"><?php l('序号'); ?></th>
					<th><?php $this->search->order_label('create_time', _l('注册时间')); ?></th>
					<th><?php $this->search->order_label('user_name', _l('姓名')); ?></th>
					<th><?php $this->search->order_label('mobile', _l('手机号')); ?></th>
					<th><?php $this->search->order_label('email', _l('电子邮箱')); ?></th>
					<th><?php l('疾病种类'); ?></th>
					<th><?php l('就职医院'); ?></th>
					<?php if ($this->status == RSTATUS_NONE) { ?>
					<th class="td-action"><?php l("操作"); ?></th>
					<?php } else { ?>
					<th><?php l("原因"); ?></th>
					<?php } ?>
				</tr>
			</thead>
			<tbody>
			<?php
				$i = $this->pagebar->start_no();
				foreach ($mReguser as $reguser) {
			?>
				<tr reguser_id="<?php p($reguser->reguser_id); ?>">
					<td class="td-no"><?php p($i); ?></td>
					<td class="text-center"><?php $reguser->datetime("create_time"); ?></td>
					<td class="text-center">
						<a href="reguser/detail/<?php p($reguser->reguser_id); ?>/ud">
							<img id="img_cavartar" src="<?php p(_avartar_url($reguser->avartar_id())); ?>" class="small-avartar">
							<?php $reguser->detail("user_name"); ?>
						</a>
					</td>
					<td class="text-center"><?php $reguser->detail("mobile"); ?></td>
					<td class="text-center"><?php $reguser->detail("email"); ?></td>
					<td class="text-center"><?php $reguser->detail("diseases"); ?></td>
					<td class="text-center"><?php $reguser->detail("hospitals"); ?></td>
					<?php if ($this->status == RSTATUS_NONE) { ?>
					<td class="td-action">
						<a href="reguser/detail/<?php p($reguser->reguser_id); ?>/ud" class="btn btn-xs btn-primary"><?php l("审核"); ?></a>
					</td>
					<?php } else { ?>
					<td><?php $reguser->nl2br("reject_note"); ?></td>
					<?php } ?>
				</tr>
			<?php
					$i ++;
				}
			?>
			</tbody>
		</table>
		<!--/table -->
		<?php _nodata_message($mReguser); ?>

		<?php $this->pagebar->display(_url("reguser/doctors/" . $this->status)); ?>
		
	</form>
</section>