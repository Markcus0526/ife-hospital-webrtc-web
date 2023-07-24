<?php
$my_type = _my_type();
?>
<section>
	<?php if (!$this->select_mode) { ?>
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
				<?php l("翻译列表");?>
			</li>
		</ul>
	</div>

	<?php if ($my_type == UTYPE_SUPER) { ?>
	<form id="fee_form" action="api/user/save_ifee" method="post">
		<div class="row margin-bottom-10">
			<div class="col-sm-6">
				<div class="input-group">
					<span class="input-group-addon no-border">
						<?php l("翻译费用"); ?>:
					</span>
					<span class="form-control-static">
						<?php l("占会诊费的"); ?>
						<?php $mConfig->detail("interpreter_fee"); ?>
						%
						<a href="javascript:;" class="btn-change-fee active" title="<?php l("更改"); ?>"><i class="icon-pencil"></i></a>	
					</span>
				</div>
			</div>
		</div>
	</form>
	<?php } ?>
	<?php } ?>

	<form id="list_form" class="table-container" method="post">
		<?php $this->search->hidden("sort_field"); ?>
		<?php $this->search->hidden("sort_order"); ?>

		<div class="row margin-bottom-10">
			<?php if ($my_type == UTYPE_SUPER) { ?>
			<div class="col-sm-3">
				<div class="input-group">
					<span class="input-group-addon no-border">
						<?php l("国家"); ?>:
					</span>
					<?php $this->search->select_model("country_id", new countryModel, "country_id", "country_name", _l("全部")); ?>				
				</div>
			</div>
			<?php } ?>
			<div class="col-sm-<?php if ($my_type == UTYPE_SUPER) { ?>6<?php } else { ?>9<?php } ?>">
				<?php $this->search->input("query", array("placeholder" => _l("请输入"))); ?>
			</div>
			<div class="col-sm-1">
				<button type="submit" class="btn btn-primary"><?php l("搜索"); ?></button>
			</div>
			<?php if (!$this->select_mode) { ?>
			<div class="col-sm-2 text-right">
				<?php if (_has_priv(CODE_PRIV_INTERPRETERS, PRIV_ADD)) { ?>
				<a href="user/edit//<?php p(UTYPE_INTERPRETER); ?>/ui" class="action-link"><i class="mdi-content-add-circle"></i> <?php l("新增翻译"); ?></a>
				<?php } ?>
			</div>
			<?php } ?>
		</div>
		<table class="table table-striped table-hover table-bordered table-condensed">
			<thead>
				<tr>
					<th class="td-no"><?php l('序号'); ?></th>
					<th><?php $this->search->order_label('user_id', _l('翻译编号')); ?></th>
					<th><?php $this->search->order_label('user_name', _l('姓名')); ?></th>
					<?php if (!$this->select_mode) { ?>
					<th class="td-sex"><?php $this->search->order_label('sex', _l('性别')); ?></th>
					<?php } ?>
					<th><?php $this->search->order_label('i_age', _l('译龄')); ?></th>
					<th><?php $this->search->order_label('mobile', _l('手机号')); ?></th>
					<th><?php $this->search->order_label('email', _l('电子邮箱')); ?></th>
					<?php if ($my_type == UTYPE_SUPER) { ?>
					<th><?php $this->search->order_label('country_id', _l('国家')); ?></th>
					<?php } ?>
					<th><?php l('翻译类型'); ?></th>
					<?php if ($my_type == UTYPE_ADMIN || $my_type == UTYPE_SUPER) { ?>
					<th><?php $this->search->order_label('lock_flag', _l('状态')); ?></th>
					<?php } ?>
					<?php if ($this->select_mode) { ?>
					<th><?php l("操作"); ?></th>
					<?php } else { ?>
					<th class="td-action-4"><?php l("操作"); ?></th>
					<?php } ?>
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
						<?php if (!$this->select_mode && _has_priv(CODE_PRIV_INTERPRETERS, PRIV_DETAIL)) { ?>
						<a href="user/detail/<?php p($user->user_id); ?>/ui">
						<?php } ?>
							<img id="img_cavartar" src="<?php p(_avartar_url($user->avartar_id())); ?>" class="small-avartar">
							<?php $user->detail("user_name"); ?>
						<?php if (!$this->select_mode && _has_priv(CODE_PRIV_INTERPRETERS, PRIV_DETAIL)) { ?>
						</a>
						<?php } ?>
					</td>
					<?php if (!$this->select_mode) { ?>
					<td class="td-sex"><?php $user->detail_code("sex", CODE_SEX); ?></td>
					<?php } ?>
					<td class="text-center"><?php $user->detail("i_age"); l("年"); ?></td>
					<td class="text-center"><?php $user->detail("mobile"); ?></td>
					<td class="text-center"><?php $user->detail("email"); ?></td>
					<?php if ($my_type == UTYPE_SUPER) { ?>
					<td class="td-country text-center" country_id="<?php p($user->country_id); ?>"></td>
					<?php } ?>
					<td class="text-center"><?php $user->detail("languages"); ?></td>
					<?php if ($my_type == UTYPE_ADMIN || $my_type == UTYPE_SUPER) { ?>
					<td class="text-center"><?php $user->detail_code("lock_flag", CODE_LOCK); ?></td>
					<?php } ?>
					<?php if ($this->select_mode) { ?>
					<td class="text-center">
						<button type="button" class="btn btn-xs btn-primary btn-select"><?php l("选择"); ?></button>
					</td>
					<?php } else { ?>
					<td class="td-action">
						<?php if (_has_priv(CODE_PRIV_INTERPRETERS, PRIV_DETAIL)) { ?>
						<a href="user/detail/<?php p($user->user_id); ?>/ui" class="btn btn-xs btn-primary"><?php l("详情"); ?></a>
						<?php } ?>
						<?php if (_has_priv(CODE_PRIV_INTERPRETERS, PRIV_EDIT)) { ?>
						<a href="user/edit/<?php p($user->user_id); ?>//ui" class="btn btn-xs btn-primary"><?php l("修改"); ?></a>
						<?php } ?>
						<?php if (_has_priv(CODE_PRIV_INTERPRETERS, PRIV_DELETE)) { ?>
						<button type="button" class="btn btn-xs btn-primary btn-delete"><?php l("删除"); ?></button>
						<?php } ?>
						<?php if (($my_type == UTYPE_ADMIN || $my_type == UTYPE_SUPER) && $user->lock_flag==LOCK_DISABLE) { ?>
						<button type="button" class="btn btn-xs btn-primary btn-unlock"><?php l("恢复"); ?></button>
						<?php } ?>
					</td>
					<?php } ?>
				</tr>
			<?php
					$i ++;
				}
			?>
			</tbody>
		</table>
		<!--/table -->
		<?php _nodata_message($mUsers); ?>

		<?php $this->pagebar->display(_url("user/interpreters/" . $this->mode . "/")); ?>
		
	</form>

	<?php 
	if ($my_type == UTYPE_SUPER) { ?>

	<form id="cost_form" action="api/user/save_ifee" class="form-horizontal modal fade" method="post" novalidate="novalidate">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title"><?php l("更改翻译费用"); ?></h4>
				</div>
				<div class="modal-body">
					<div class="form-group form-md-line-input">
						<label class="control-label col-md-5"><?php l("翻译费用"); ?> <span class="required">*</span> :</label>
						<div class="col-md-7">
							<div class="input-group">
								<span class="input-group-addon">
									<?php l("占会诊费的"); ?>
								</span>
								<?php $mConfig->input("interpreter_fee", 
									array("class" => "text-right", "org_fee" => $mConfig->interpreter_fee)); ?>
								<span class="input-group-addon">
									%
								</span>
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
</section>