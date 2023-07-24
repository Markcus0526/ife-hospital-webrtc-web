<section>
	<div class="page-bar">
		<ul class="page-breadcrumb">
			<li>
				<span><?php l("当前位置"); ?> :</span>
			</li>
			<li>
				<a href="language"><?php l("分类管理");?></a>
				<i class="fa fa-angle-right"></i>
			</li>
			<li>
				<a href="language"><?php l("语言列表");?></a>
				<i class="fa fa-angle-right"></i>
			</li>
			<li>
				<?php l("翻译资源");?>
			</li>
		</ul>
	</div>

	<form id="resource_form" class="table-container" action="api/language/save_resource" method="post">

		<div class="row margin-bottom-10">
			<div class="col-xs-6">
				<!--<a href="language/resource/<?php p($this->language_code); ?>?upgrade=1" class="btn btn-primary btn-upgrade"><?php l("刷新"); ?></a>-->
			</div>
			<div class="col-xs-6 text-right">
				<button type="submit" class="btn btn-primary"><?php l("保存"); ?></button>
			</div>
		</div>

		<input type="hidden" name="language_code" value="<?php p($this->language_code); ?>">

		<table id="language_list" class="table table-striped table-hover table-bordered table-condensed">
			<thead>
				<tr>
					<th class="td-no"><?php l('序号'); ?></th>
					<th width="50%"><?php l('源字符串'); ?></th>
					<th><?php l('翻译字符串'); ?></th>
				</tr>
			</thead>
			<tbody>
			<?php
				$i = $this->pagebar->start_no(); $n = 0;
				foreach ($mStrings as $org_string => $interp_string) {
			?>
				<tr class="language" language_id="<?php p($language->language_id); ?>">
					<td class="td-no"><?php p($i); ?></td>
					<td>
						<?php p(_str2html($org_string)); ?>
						<input type="hidden" name="org_string[<?php p($n); ?>]" value="<?php p(htmlspecialchars($org_string)); ?>">
					</td>
					<td>
						<textarea class="form-control" name="interp_string[<?php p($n); ?>]" rows="2"><?php p(htmlspecialchars($interp_string)); ?></textarea>
					</td>
				</tr>
			<?php
					$i ++; $n ++;
				}
			?>
			</tbody>
		</table>
		<!--/table -->

		<?php _nodata_message($mStrings); ?>

		<?php $this->pagebar->display(_url("language/resource/" . $this->language_code . "/")); ?>
	</form>

</section>