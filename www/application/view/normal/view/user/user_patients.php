<?php
$my_type = _my_type();
?>
<section>
	<div class="page-bar">
		<ul class="page-breadcrumb">
			<li>
				<span><?php l("当前位置"); ?> :</span>
			</li>
			<?php if ($this->mode == "chistory") { ?>
			<li>
				<a href="user/patients/chistory"><?php l("病历管理");?></a>
				<i class="fa fa-angle-right"></i>
			</li>
			<li>
				<?php l("病历列表");?>
			</li>
			<?php } else { ?>
			<li>
				<a href="javascript:;"><?php l("用户管理");?></a>
				<i class="fa fa-angle-right"></i>
			</li>
			<li>
				<?php l("患者列表");?>
			</li>
			<?php } ?>
		</ul>
	</div>

	<form id="list_form" class="table-container" method="post">
		<?php $this->search->hidden("sort_field"); ?>
		<?php $this->search->hidden("sort_order"); ?>

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
			<div class="col-sm-<?php if ($my_type == UTYPE_SUPER) { ?>7<?php } else { ?>9<?php } ?>">
				<?php $this->search->input("query", array("placeholder" => _l("请输入"))); ?>
			</div>
			<div class="col-sm-1">
				<button type="submit" class="btn btn-primary"><?php l("搜索"); ?></button>
			</div>
			<div class="col-sm-2 text-right">
				<?php if ($this->mode == "") { ?>
					<?php if (_has_priv(CODE_PRIV_PATIENTS, PRIV_ADD)) { ?>
				<a href="user/edit//<?php p(UTYPE_PATIENT); ?>/up" class="action-link"><i class="mdi-content-add-circle"></i> <?php l("新增患者"); ?></a>
					<?php } ?>
				<?php } ?>
			</div>
		</div>
		<table class="table table-striped table-hover table-bordered table-condensed">
			<thead>
				<tr>
					<th class="td-no"><?php l('序号'); ?></th>
					<th><?php $this->search->order_label('user_id', _l('患者编号')); ?></th>
					<th><?php $this->search->order_label('user_name', _l('姓名')); ?></th>
					<th class="td-sex"><?php $this->search->order_label('sex', _l('性别')); ?></th>
					<th><?php $this->search->order_label('mobile', _l('手机号')); ?></th>
					<th><?php $this->search->order_label('email', _l('电子邮箱')); ?></th>
					<?php if ($my_type == UTYPE_SUPER) { ?>
					<th><?php $this->search->order_label('country_id', _l('国家')); ?></th>
					<?php } ?>
					<?php if ($my_type == UTYPE_ADMIN || $my_type == UTYPE_SUPER) { ?>
					<th><?php $this->search->order_label('lock_flag', _l('状态')); ?></th>
					<?php } ?>
					<th class="td-action"><?php l("操作"); ?></th>
				</tr>
			</thead>
			<tbody>
			<?php
				$i = $this->pagebar->start_no();
				foreach ($mUsers as $user) {
			?>
				<tr user_id="<?php p($user->user_id); ?>" class="<?php if($user->lock_flag==LOCK_ON){?>warning<?php } if($user->lock_flag==LOCK_DISABLE){?>danger<?php } ?>">
					<td class="td-no"><?php p($i); ?></td>
					<td class="text-center"><?php $user->detail("user_id"); ?></td>
					<td class="text-center">
						<?php if (_has_priv(CODE_PRIV_PATIENTS, PRIV_DETAIL)) { ?>
						<a href="user/detail/<?php p($user->user_id); ?>/up">
						<?php } ?>
							<img id="img_cavartar" src="<?php p(_avartar_url($user->avartar_id())); ?>" class="small-avartar">
							<?php $user->detail("user_name"); ?>
						<?php if (_has_priv(CODE_PRIV_PATIENTS, PRIV_DETAIL)) { ?>
						</a>
						<?php } ?>
					</td>
					<td class="td-sex"><?php $user->detail_code("sex", CODE_SEX); ?></td>
					<td class="text-center"><?php $user->detail("mobile"); ?></td>
					<td class="text-center"><?php $user->detail("email"); ?></td>
					<?php if ($my_type == UTYPE_SUPER) { ?>
					<td class="td-country text-center" country_id="<?php p($user->country_id); ?>"></td>
					<?php } ?>
					<?php if ($my_type == UTYPE_ADMIN || $my_type == UTYPE_SUPER) { ?>
					<td class="text-center"><?php $user->detail_code("lock_flag", CODE_LOCK); ?></td>
					<?php } ?>
					<td class="td-action">
						<?php if ($this->mode == "chistory") { ?>
						<a href="chistory/index/<?php p($user->user_id); ?>" class="btn btn-xs btn-primary"><?php l("查看病历"); ?></a>
						<?php } else { ?>
						<?php if (_has_priv(CODE_PRIV_PATIENTS, PRIV_DETAIL)) { ?>
						<a href="user/detail/<?php p($user->user_id); ?>/up" class="btn btn-xs btn-primary"><?php l("详情"); ?></a>
						<?php } ?>
						<?php if (_has_priv(CODE_PRIV_PATIENTS, PRIV_EDIT)) { ?>
						<a href="user/edit/<?php p($user->user_id); ?>/up" class="btn btn-xs btn-primary"><?php l("修改"); ?></a>
						<?php } ?>
						<?php if (_has_priv(CODE_PRIV_PATIENTS, PRIV_DELETE)) { ?>
						<button type="button" class="btn btn-xs btn-primary btn-delete"><?php l("删除"); ?></button>
						<?php } ?>
						<?php if (($my_type == UTYPE_ADMIN || $my_type == UTYPE_SUPER) && $user->lock_flag==LOCK_DISABLE) { ?>
						<button type="button" class="btn btn-xs btn-primary btn-unlock"><?php l("恢复"); ?></button>
						<?php } ?>
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
		<?php _nodata_message($mUsers); ?>

		<?php $this->pagebar->display(_url("user/patients/" . $this->mode . "/")); ?>
		
	</form>
</section>