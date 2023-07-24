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
				<?php l("次数与时长");?>
			</li>
		</ul>
	</div>

	<form id="list_form" class="table-container" method="post">
		<?php $this->search->hidden("sort_field"); ?>
		<?php $this->search->hidden("sort_order"); ?>
		<?php $this->search->hidden("from_date"); ?>
		<?php $this->search->hidden("to_date"); ?>

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
			<div class="col-xs-4 text-right">
				<?php if (_has_priv(CODE_PRIV_STATS, PRIV_EXRATE)) { ?>
				<button id="btn_exrate" type="button" class="btn btn-primary"><?php l("查询汇率"); ?></button>
				<?php } ?>
			</div>
		</div>
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
			<div class="col-sm-2">
				<div class="input-group">
					<span class="input-group-addon no-border">
						<?php l("角色"); ?>:
					</span>
					<?php $this->search->select_utype("user_type", _l("全部"), null, $this->search->utypes); ?>				
				</div>
			</div>
			<div class="col-sm-5">
				<?php $this->search->input("query", array("placeholder" => _l("请输入"))); ?>
			</div>
			<div class="col-sm-1">
				<button type="submit" class="btn btn-primary"><?php l("搜索"); ?></button>
			</div>
			<div class="col-sm-1 text-right">
				<?php if (_has_priv(CODE_PRIV_STATS, PRIV_EXPORT)) { ?>
				<a href="stats/excel" class="btn btn-primary"><?php l("导出"); ?></a>
				<?php } ?>
			</div>
		</div>
		<table class="table table-striped table-hover table-bordered table-condensed">
			<thead>
				<tr>
					<th class="td-no"><?php l('序号'); ?></th>
					<?php if ($my_type == UTYPE_SUPER) { ?>
					<th><?php $this->search->order_label('user_id', _l('国家')); ?></th>
					<?php } ?>
					<th><?php $this->search->order_label('user_type', _l('角色')); ?></th>
					<th><?php $this->search->order_label('user_name', _l('姓名')); ?></th>
					<th><?php $this->search->order_label('mobile', _l('手机号')); ?></th>
					<th><?php $this->search->order_label('mobile', _l('电子邮箱')); ?></th>
					<th><?php $this->search->order_label('counts', _l('会诊次数')); ?></th>
					<th><?php $this->search->order_label('seconds', _l('会诊时长')); ?></th>
				</tr>
			</thead>
			<tbody>
			<?php
				$i = $this->pagebar->start_no();
				foreach ($mUsers as $user) {
			?>
				<tr user_id="<?php p($user->user_id); ?>">
					<td class="td-no"><?php p($i); ?></td>
					<?php if ($my_type == UTYPE_SUPER) { ?>
					<td class="td-country" country_id="<?php p($user->country_id); ?>"></td>
					<?php } ?>
					<td class="text-center">
						<a href="stats/detail/<?php p($user->user_id); ?>">
							<?php $user->detail_code("user_type", CODE_UTYPE); ?>
						</a>
					</td>
					<td class="text-center">
						<a href="stats/detail/<?php p($user->user_id); ?>">
							<?php $user->detail("user_name"); ?>
						</a>
					</td>
					<td class="text-center">
						<a href="stats/detail/<?php p($user->user_id); ?>">
							<?php $user->detail("mobile"); ?>
						</a>
					</td>
					<td class="text-center">
						<a href="stats/detail/<?php p($user->user_id); ?>">
							<?php $user->detail("email"); ?>
						</a>
					</td>
					<td class="text-center">
						<a href="stats/detail/<?php p($user->user_id); ?>">
							<?php $user->number("counts"); ?>
						</a>
					</td>
					<td class="text-center">
						<a href="stats/detail/<?php p($user->user_id); ?>">
							<?php $user->seconds("seconds", null); ?>
						</a>
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

		<?php $this->pagebar->display(_url("stats/index/")); ?>
		
	</form>

	<form id="exrate_form" action="api/exrate/search" class="form-horizontal modal fade" method="post" novalidate="novalidate">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title"><?php l("查询汇率"); ?></h4>
				</div>
				<div class="modal-body">
					<input type="hidden" name="exrate_time">
					<div class="form-group form-md-line-input">
						<label class="control-label col-md-6"><?php l("日期"); ?> :</label>
						<div class="col-md-6">
							<p class="form-control-static">
								<?php p(_date()); ?>
							</p>
						</div>
					</div>
					<div class="form-group form-md-line-input">
						<label class="control-label col-md-6"><?php l("该日汇率"); ?> :</label>
						<div class="col-md-6">
							<p class="form-control-static">
								<i class="fa fa-dollar"></i>1=<i class="fa fa-yen"></i><span id="rate_usd_to_rmb"></span><br/>
								<i class="fa fa-yen"></i>1=<i class="fa fa-dollar"></i><span id="rate_rmb_to_usd"></span><br/>
							</p>
						</div>
					</div>
				</div>
				<div class="modal-footer">
				</div>
			</div>
		</div>
	</form>
</section>