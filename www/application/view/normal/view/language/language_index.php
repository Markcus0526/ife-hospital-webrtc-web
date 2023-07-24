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
				<?php l("语言列表");?>
			</li>
		</ul>
	</div>

	<form id="list_form" class="table-container" action="api/language/save" method="post">
		<div class="row">
			<div class="col-xs-6">
				<a href="javascript:;" id="insert_language" class="action-link"><i class="mdi-content-add-circle"></i> <?php l("添加语言"); ?></a>
			</div>
			<div class="col-xs-6 text-right">
			</div>
		</div>
		<?php $this->search->hidden("sort_field"); ?>
		<?php $this->search->hidden("sort_order"); ?>
		<table id="language_list" class="table table-striped table-hover table-bordered table-condensed">
			<thead>
				<tr>
					<th class="td-no"><?php l('序号'); ?></th>
					<th width="30%"><?php l('语言名称'); ?></th>
					<th><?php l('语言码'); ?></th>
					<th class="td-action"><?php l('移动'); ?></th>
					<th class="td-action"><?php l("操作"); ?></th>
				</tr>
			</thead>
			<tbody>
				<tr id="edit_language" class="hide">
					<td class="td-no">+</td>
					<td>
						<?php $mLanguage->hidden("language_id"); ?>
						<?php $mLanguage->input("language_name", array("maxlength" => 50)); ?>
					</td>
					<td>
						<?php $mLanguage->input("language_code", array("maxlength" => 10)); ?>
					</td>
					<td></td>
					<td class="td-action-4">
						<button type="submit" class="btn btn-xs btn-primary"><?php l("保存"); ?></button>
						<button type="button" id="btn_cancel_language" class="btn btn-xs btn-primary"><?php l("取消"); ?></button>
					</td>
				</tr>
			<?php
				$i = $this->pagebar->start_no();
				foreach ($mLanguages as $language) {
			?>
				<tr class="language" language_id="<?php p($language->language_id); ?>">
					<td class="td-no"><?php p($i); ?></td>
					<td>
						<span class="editable editable-language" language_name="<?php $language->detail("language_name"); ?>" language_name_l="<?php $language->detail("language_name_l"); ?>"><?php $language->detail("language_name"); ?> <?php $language->detail_other_lang("language_name_l"); ?></span>
					</td>
					<td><span class="editable"><?php $language->detail("language_code"); ?></span></td>
					<td class="text-center">
						<a href="javascript:;" class="btn-move-top" direct="-2"><i class="glyphicon glyphicon-arrow-up"></i></a>
						<a href="javascript:;" class="btn-move-up" direct="-1"><i class="glyphicon glyphicon-arrow-up"></i></a>
						<a href="javascript:;" class="btn-move-down" direct="1"><i class="glyphicon glyphicon-arrow-down"></i></a>
						<a href="javascript:;" class="btn-move-bottom" direct="2"><i class="glyphicon glyphicon-arrow-down"></i></a>
					</td>
					<td class="td-action-4">
						<?php
						if (!_is_empty($language->language_code) && $language->language_code!=DEFAULT_LANGUAGE) {
						?>
						<a href="language/resource/<?php p($language->language_code); ?>" class="btn btn-xs btn-primary"><?php l("翻译资源"); ?></a>
						<?php 
						}
						?>
						<button type="button" class="btn btn-xs btn-primary btn-edit"><?php l("修改"); ?></button>
						<?php
						if ($language->language_code!=DEFAULT_LANGUAGE) {
						?>
						<button type="button" class="btn btn-xs btn-primary btn-delete"><?php l("删除"); ?></button>
						<?php 
						}
						?>
					</td>
				</tr>
			<?php
					$i ++;
				}
			?>
			</tbody>
		</table>
		<!--/table -->
		<?php _nodata_message($mLanguages); ?>

		<?php $this->pagebar->display(_url("language/index")); ?>
		
	</form>

	<form id="language_form" action="api/language/save_name" method="post" class="form-horizontal modal fade">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title"><?php l("多语言设置"); ?></h4>
				</div>
				<div class="modal-body">
					<input type="hidden" name="id">
					<input type="hidden" name="name_field" value="language_name">
					<?php 
					foreach ($mPageLanguages as $key => $lang) {
						if ($lang["language_code"] != "") {
						?>
					<div class="form-group form-md-line-input">
						<label class="control-label col-md-3"><?php l($lang["language_name"]); ?> :</label>
						<div class="col-md-9">
							<?php
							if ($lang["language_code"]==DEFAULT_LANGUAGE) {
							?>
							<p class="form-control-static" id="name">
							</p>
							<?php
							}
							else {
							?>
							<input type="text" name="name_l['<?php p($lang["language_code"]); ?>']" class="form-control name_l">
							<?php
							}
							?>
						</div>
					</div>
						<?php
						}
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