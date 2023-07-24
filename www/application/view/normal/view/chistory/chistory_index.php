<?php
$my_type = _my_type();
?>
<section>
	<div class="page-bar">
		<ul class="page-breadcrumb">
			<li>
				<span><?php l("当前位置"); ?> :</span>
			</li>
			<?php 
			if ($my_type == UTYPE_PATIENT) {
			?>
			<li>
				<a href="chistory"><?php l("病历管理");?></a>
				<i class="fa fa-angle-right"></i>
			</li>
			<li>
				<?php l("我的病历");?>
			</li>
			<?php 
			} else {
			?>
			<li>
				<a href="user/patients/chistory"><?php l("病历管理");?></a>
				<i class="fa fa-angle-right"></i>
			</li>
			<li>
				<a href="user/patients/chistory"><?php l("病历列表");?></a>
				<i class="fa fa-angle-right"></i>
			</li>
			<li>
				<?php l("患者病历");?>
			</li>
			<?php 
			}
			?>
		</ul>
	</div>

	<?php if (_has_priv(CODE_PRIV_CHISTORY, PRIV_ADD)) { ?>
	<div class="text-right">
		<a href="chistory/edit/<?php p($this->patient_id); ?>" class="action-link"><i class="mdi-content-add-circle"></i> <?php l("新增病历"); ?></a>
	</div>
	<?php } ?>

	<form id="list_form" class="table-container" method="post">
		<?php $this->search->hidden("sort_field"); ?>
		<?php $this->search->hidden("sort_order"); ?>
		<table class="table table-striped table-hover table-bordered table-condensed">
			<thead>
				<tr>
					<th class="td-no"><?php l('序号'); ?></th>
					<?php if (!($my_type == UTYPE_PATIENT)) { ?>
					<th><?php $this->search->order_label('user_id', _l('患者编号')); ?></th>
					<?php } ?>
					<th><?php $this->search->order_label('chistory_name', _l('病历名称')); ?></th>
					<th><?php $this->search->order_label('patient_name', _l('姓名')); ?></th>
					<th><?php $this->search->order_label('patient_sex', _l('性别')); ?></th>
					<th><?php $this->search->order_label('birthday', _l('出生日期')); ?></th>
					<th><?php $this->search->order_label('disease_id', _l('疾病种类')); ?></th>
					<th class="td-action"><?php l("操作"); ?></th>
				</tr>
			</thead>
			<tbody>
			<?php
				$i = $this->pagebar->start_no();
				foreach ($mChistorys as $chistory) {
			?>
				<tr chistory_id="<?php p($chistory->chistory_id); ?>">
					<td class="td-no"><?php p($i); ?></td>
					<?php if (!($my_type == UTYPE_PATIENT)) { ?>
					<td class="text-center"><?php $chistory->detail('user_id'); ?></td>
					<?php } ?>
					<td class="text-center">
						<?php if (_has_priv(CODE_PRIV_CHISTORY, PRIV_DETAIL)) { ?>
						<a href="chistory/detail/<?php p($chistory->user_id); ?>/<?php p($chistory->chistory_id); ?>">
						<?php } ?>
							<?php $chistory->detail("chistory_name"); ?>
						<?php if (_has_priv(CODE_PRIV_CHISTORY, PRIV_DETAIL)) { ?>
						</a>
						<?php } ?>
					</td>
					<td class="text-center">
						<a href="chistory/detail/<?php p($chistory->user_id); ?>/<?php p($chistory->chistory_id); ?>"><img id="img_cavartar" src="<?php p(_avartar_url($chistory->avartar_id())); ?>" class="small-cavartar">
						<?php $chistory->detail("patient_name"); ?></a>
					</td>
					<td class="text-center"><?php $chistory->detail_code("patient_sex", CODE_SEX); ?></td>
					<td class="text-center"><?php $chistory->date('birthday'); ?></td>
					<td class="text-center"><?php $chistory->detail("disease_name"); ?></td>
					<td class="td-action-4">
						<?php if (_has_priv(CODE_PRIV_CHISTORY, PRIV_DETAIL)) { ?>
						<a href="chistory/detail/<?php p($chistory->user_id); ?>/<?php p($chistory->chistory_id); ?>" class="btn btn-xs btn-primary"><?php l("详情"); ?></a>
						<?php } ?>
						<?php if (_has_priv(CODE_PRIV_CHISTORY, PRIV_EDIT) && $chistory->can_edit_delete()) { ?>
						<a href="chistory/edit/<?php p($chistory->user_id); ?>/<?php p($chistory->chistory_id); ?>" class="btn btn-xs btn-primary"><?php l("修改"); ?></a>
						<?php } ?>
						<?php if (_has_priv(CODE_PRIV_CHISTORY, PRIV_DELETE) && $chistory->can_edit_delete()) { ?>
						<button type="button" class="btn btn-xs btn-primary btn-delete"><?php l("删除"); ?></button>
						<?php } ?>
					</td>
				</tr>
			<?php
					$i ++;
				}
			?>
			</tbody>
		</table>
		<!--/table -->
		<?php _nodata_message($mChistorys); ?>

		<?php $this->pagebar->display(_url("chistory/index/" . $this->patient_id . "/")); ?>
		
	</form>
</section>