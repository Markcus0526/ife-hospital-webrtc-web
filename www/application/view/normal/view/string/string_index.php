<section>
	<div class="page-bar">
		<ul class="page-breadcrumb">
			<li>
				<span><?php l("当前位置"); ?> :</span>
			</li>
			<li>
				<a href="string"><?php l("分类管理");?></a>
				<i class="fa fa-angle-right"></i>
			</li>
			<li>
				<?php l("字符串列表");?>
			</li>
		</ul>
	</div>

	<form id="list_form" class="table-container" action="string" method="post">
		<div class="row margin-bottom-10">
			<div class="col-lg-5 col-sm-7">
				<?php $this->search->input("query", array("placeholder" => _l("请输入关键字"))); ?>
			</div>
			<div class="col-lg-1 col-sm-1">
				<button type="submit" class="btn btn-primary"><?php l("搜索"); ?></button>
			</div>
			<div class="col-lg-6 col-xs-4 text-right">
				<a href="string/index?upgrade=1" class="btn btn-primary btn-upgrade"><?php l("刷新"); ?></a>
			</div>
		</div>

		<?php $this->search->hidden("sort_field"); ?>
		<?php $this->search->hidden("sort_order"); ?>
		<table id="string_list" class="table table-striped table-hover table-bordered table-condensed">
			<thead>
				<tr>
					<th class="td-no"><?php l('序号'); ?></th>
					<th width="30%"><?php l('字符串'); ?></th>
					<th><?php l('多语言'); ?></th>
					<th class="td-action"><?php l("操作"); ?></th>
				</tr>
			</thead>
			<tbody>
			<?php
				$i = $this->pagebar->start_no();
				foreach ($mStrings as $string) {
			?>
				<tr class="string <?php if(_is_empty($string->string_l)) p("danger"); ?>" string_id="<?php p($string->string_id); ?>">
					<td class="td-no"><?php p($i); ?></td>
					<td>
						<span class="editable editable-language" string="<?php $string->detail("string"); ?>" string_l="<?php $string->detail("string_l"); ?>"><?php $string->detail("string"); ?> </span>
					</td>
					<td>
						<span class="editable editable-language" string="<?php $string->detail("string"); ?>" string_l="<?php $string->detail("string_l"); ?>"><?php $string->detail_lang("string_l"); ?> </span>
					</td>
					<td class="td-action">
						<button type="button" class="btn btn-xs btn-primary btn-edit-language" string="<?php $string->detail("string"); ?>" string_l="<?php $string->detail("string_l"); ?>"><?php l("修改"); ?></button>
					</td>
				</tr>
			<?php
					$i ++;
				}
			?>
			</tbody>
		</table>
		<!--/table -->
		<?php _nodata_message($mStrings); ?>

		<?php $this->pagebar->display(_url("string/index/")); ?>
		
	</form>

	<form id="language_form" action="api/string/save_name" method="post" class="form-horizontal modal fade">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title"><?php l("多语言设置"); ?></h4>
				</div>
				<div class="modal-body">
					<input type="hidden" name="id">
					<input type="hidden" name="name_field" value="string">
					<div class="form-group form-md-line-input">
						<label class="control-label col-md-3"><?php l("字符串"); ?> :</label>
						<div class="col-md-9">
							<p class="form-control-static" id="string" style="max-height: 65px; overflow-y: auto;">
							</p>
						</div>
					</div>
					<?php 
					foreach ($mLanguages as $key => $lang) {
						?>
					<div class="form-group form-md-line-input">
						<label class="control-label col-md-3"><?php l($lang["language_name"]); ?> :</label>
						<div class="col-md-9">
							<textarea type="text" name="name_l['<?php p($lang["language_code"]); ?>']" rows=2 class="form-control name_l"></textarea>
						</div>
					</div>
						<?php
					}
					?>
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-primary"><?php l("确定"); ?></button>
					<button type="button" class="btn btn-default" data-dismiss="modal"><?php l("取消"); ?></button>
				</div>
			</div>
		</div>
	</form>

</section>