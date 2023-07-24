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
				<a href="javascript:;"><?php l("用户管理");?></a>
				<i class="fa fa-angle-right"></i>
			</li>
			<li>
				<?php l("专家列表");?>
			</li>
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
			<?php if (_has_priv(CODE_PRIV_DOCTORS, PRIV_ADD)) { ?>
				<a href="user/edit//<?php p(UTYPE_DOCTOR); ?>/ud" class="action-link"><i class="mdi-content-add-circle"></i> <?php l("新增专家"); ?></a>
			<?php } ?>
			</div>
		</div>
		<table class="table table-striped table-hover table-bordered table-condensed">
			<thead>
				<tr>
					<th class="td-no"><?php l('序号'); ?></th>
					<th><?php $this->search->order_label('user_id', _l('专家编号')); ?></th>
					<th><?php $this->search->order_label('user_name', _l('姓名')); ?></th>
					<th><?php $this->search->order_label('mobile', _l('手机号')); ?></th>
					<th><?php $this->search->order_label('email', _l('电子邮箱')); ?></th>
					<?php if ($my_type == UTYPE_SUPER) { ?>
					<th><?php $this->search->order_label('country_id', _l('国家')); ?></th>
					<?php } ?>
					<th><?php l('疾病种类'); ?></th>
					<th><?php l('就职医院'); ?></th>
					<?php if (_has_priv(CODE_PRIV_DOCTORS, PRIV_SET_FEE)) { ?>
					<th><?php $this->search->order_label('d_fee', _l('会诊费')); ?></th>
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
				<tr user_id="<?php p($user->user_id); ?>" d_fee="<?php p($user->d_fee); ?>" d_cunit="<?php p($user->d_cunit); ?>" class="<?php if($user->lock_flag==LOCK_ON){?>warning<?php } if($user->lock_flag==LOCK_DISABLE){?>danger<?php } ?>">
					<td class="td-no"><?php p($i); ?></td>
					<td class="text-center"><?php $user->detail("user_id"); ?></td>
					<td class="text-center">
						<?php if (_has_priv(CODE_PRIV_DOCTORS, PRIV_DETAIL)) { ?>
						<a href="user/detail/<?php p($user->user_id); ?>/ud">
						<?php } ?>
							<img id="img_cavartar" src="<?php p(_avartar_url($user->avartar_id())); ?>" class="small-avartar">
							<?php $user->detail("user_name"); ?>
						<?php if (_has_priv(CODE_PRIV_DOCTORS, PRIV_DETAIL)) { ?>
						</a>
						<?php } ?>
					</td>
					<td class="text-center"><?php $user->detail("mobile"); ?></td>
					<td class="text-center"><?php $user->detail("email"); ?></td>
					<?php if ($my_type == UTYPE_SUPER) { ?>
					<td class="td-country text-center" country_id="<?php p($user->country_id); ?>"></td>
					<?php } ?>
					<td class="text-center"><?php $user->detail("diseases"); ?></td>
					<td class="text-center"><?php $user->detail("hospitals"); ?></td>
					<?php if (_has_priv(CODE_PRIV_DOCTORS, PRIV_SET_FEE)) { ?>
					<td class="text-right">
						<?php $user->currency("d_fee", "d_cunit", "&nbsp;"); ?>
						<a href="javascript:;" class="btn-change-fee active" title="<?php l("更改"); ?>"><i class="icon-pencil"></i></a>
					</td>
					<?php } ?>
					<?php if ($my_type == UTYPE_ADMIN || $my_type == UTYPE_SUPER) { ?>
					<td class="text-center"><?php $user->detail_code("lock_flag", CODE_LOCK); ?></td>
					<?php } ?>
					<td class="td-action">
						<?php if (_has_priv(CODE_PRIV_DOCTORS, PRIV_DETAIL)) { ?>
						<a href="user/detail/<?php p($user->user_id); ?>/ud" class="btn btn-xs btn-primary"><?php l("详情"); ?></a>
						<?php } ?>
						<?php if (_has_priv(CODE_PRIV_DOCTORS, PRIV_EDIT)) { ?>
						<a href="user/edit/<?php p($user->user_id); ?>//ud" class="btn btn-xs btn-primary"><?php l("修改"); ?></a>
						<?php } ?>
						<?php if (_has_priv(CODE_PRIV_DOCTORS, PRIV_DELETE)) { ?>
						<button type="button" class="btn btn-xs btn-primary btn-delete"><?php l("删除"); ?></button>
						<?php } ?>
						<?php if (($my_type == UTYPE_ADMIN || $my_type == UTYPE_SUPER) && $user->lock_flag==LOCK_DISABLE) { ?>
						<button type="button" class="btn btn-xs btn-primary btn-unlock"><?php l("恢复"); ?></button>
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

		<?php $this->pagebar->display(_url("user/doctors/")); ?>
		
	</form>

	<?php 
	if (_has_priv(CODE_PRIV_DOCTORS, PRIV_SET_FEE)) {
	?>
	<form id="cost_form" action="api/user/save" class="form-horizontal modal fade" method="post" novalidate="novalidate">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title"><?php l("更改会诊费"); ?></h4>
				</div>
				<div class="modal-body">
					<input type="hidden" name="user_id">
					<div class="form-group form-md-line-input">
						<label class="control-label col-md-4"><?php l("会诊费"); ?> <span class="required">*</span> :</label>
						<div class="col-md-8 form-inline">
							<label class="ui-radio" for="d_cunit_usd">
								<input type="radio" class="radio" id="d_cunit_usd" name="d_cunit" value="usd"><span>
								<i class="fa fa-dollar"></i></span>
							</label>

							<input type="text" name="d_fee_usd" class="form-control">
						</div>
					</div>
					<div class="form-group form-md-line-input">
						<div class="col-md-8 col-md-offset-4 form-inline">
							<label class="ui-radio" for="d_cunit_rmb">
								<input type="radio" class="radio" id="d_cunit_rmb" name="d_cunit" value="rmb"><span>
								<i class="fa fa-yen"></i></span>
							</label>

							<input type="text" name="d_fee_rmb" class="form-control">
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
</section>