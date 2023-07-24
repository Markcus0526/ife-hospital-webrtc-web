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
				<a href="user/interpreters"><?php l("翻译列表");?></a>
				<i class="fa fa-angle-right"></i>
			</li>
			<li>
				<?php l("评价统计");?>
			</li>
		</ul>
	</div>

	<div class="row">
		<div class="col-md-6 col-md-offset-3">
			<table class="table-irating table table-striped table-hover table-bordered table-condensed">
				<thead>
					<tr>
						<th width="33%"><?php l("不满意"); ?></th>
						<th width="34%"><?php l("满意"); ?></th>
						<th width="33%"><?php l("非常满意"); ?></th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td class="text-center">
							<?php p($mIratings[IRATE_UNSATISFY]); ?>
						</td>
						<td class="text-center">
							<?php p($mIratings[IRATE_SATISFY]); ?>
						</td>
						<td class="text-center">
							<?php p($mIratings[IRATE_VERYSATISFY]); ?>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</section>