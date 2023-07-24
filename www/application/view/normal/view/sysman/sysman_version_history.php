<section>
	<div class="page-bar">
		<ul class="page-breadcrumb">
			<li>
				<span><?php l("当前位置"); ?> :</span>
			</li>
			<li>
				<?php l("系统管理");?>
				<i class="fa fa-angle-right"></i>
			</li>
			<li>
				<?php l("更新历史");?>
			</li>
		</ul>
	</div>

	<div class="row">
		<div class="col-md-12">
			<table class="table table-striped table-bordered">
				<tr>
					<th width="90px"><?php l("版本"); ?></th>
					<th><?php l("更新详情"); ?></th>
					<th width="120px"><?php l("更新日期"); ?></th>
				</tr>
			<?php 
				foreach($mPatched as $p) {
			?>
				<tr>
					<td><?php $p->detail("version"); ?></td>
					<td><?php $p->nl2br("description"); ?></td>
					<td><?php $p->date("create_time"); ?></td>
				</tr>
			<?php 
				}
			?>
			</table>
		</div>
	</div>
</section>