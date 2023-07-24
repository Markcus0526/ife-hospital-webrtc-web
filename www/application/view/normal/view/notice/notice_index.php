<section>
	<div class="page-bar">
		<ul class="page-breadcrumb">
			<li>
				<span><?php l("当前位置"); ?> :</span>
			</li>
			<li>
				<a href="notice"><?php l("系统管理");?></a>
				<i class="fa fa-angle-right"></i>
			</li>
			<li>
				<?php l("广播信息设置");?>
			</li>
		</ul>
	</div>

	<form id="list_form" class="table-container" action="api/notice/save" method="post">
		<div class="row">
			<div class="col-xs-6">
				<a href="javascript:;" id="insert_notice" class="action-link"><i class="mdi-content-add-circle"></i> <?php l("添加广播"); ?></a>
			</div>
		</div>

		<?php $this->search->hidden("sort_field"); ?>
		<?php $this->search->hidden("sort_order"); ?>
		<table id="notice_list" class="table table-striped table-hover table-bordered table-condensed">
			<thead>
				<tr>
					<th class="td-no"><?php l('序号'); ?></th>
					<th width="30%"><?php l('内容'); ?></th>
					<th><?php l('多语言'); ?></th>
					<th class="td-action"><?php l("操作"); ?></th>
				</tr>
			</thead>
			<tbody>
				<tr id="edit_notice" class="hide">
					<td class="td-no">+</td>
					<td>
						<?php $mNotice->hidden("notice_id"); ?>
						<?php $mNotice->input("content", array("maxlength" => 255)); ?>
					</td>
					<td></td>
					<td class="td-action">
						<button type="submit" class="btn btn-xs btn-primary"><?php l("保存"); ?></button>
						<button type="button" id="btn_cancel_notice" class="btn btn-xs btn-primary"><?php l("取消"); ?></button>
					</td>
				</tr>
			<?php
				$i = $this->pagebar->start_no();
				foreach ($mNotices as $notice) {
			?>
				<tr class="notice <?php if(_is_empty($notice->content_l)) p("danger"); ?>" notice_id="<?php p($notice->notice_id); ?>">
					<td class="td-no"><?php p($i); ?></td>
					<td>
						<span class="editable editable-language" content="<?php $notice->detail("content"); ?>" content_l="<?php $notice->detail("content_l"); ?>"><?php $notice->detail("content"); ?> </span>
					</td>
					<td>
						<span class="editable editable-language" content="<?php $notice->detail("content"); ?>" content_l="<?php $notice->detail("content_l"); ?>"><?php $notice->detail_lang("content_l"); ?> </span>
					</td>
					<td class="td-action">
						<button type="button" class="btn btn-xs btn-primary btn-edit" ><?php l("修改"); ?></button>
						<button type="button" class="btn btn-xs btn-primary btn-delete"><?php l("删除"); ?></button>
					</td>
				</tr>
			<?php
					$i ++;
				}
			?>
			</tbody>
		</table>
		<!--/table -->
		<?php _nodata_message($mNotices); ?>

		<?php $this->pagebar->display(_url("notice/index/" . $this->expand)); ?>
		
	</form>

	<form id="language_form" action="api/notice/save_name" method="post" class="form-horizontal modal fade">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title"><?php l("多语言设置"); ?></h4>
				</div>
				<div class="modal-body">
					<input type="hidden" name="id">
					<input type="hidden" name="name_field" value="notice">
					<?php 
					foreach ($mLanguages as $key => $lang) {
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
							<input type="text" name="name_l['<?php p($lang["language_code"]); ?>']" maxlength=255 class="form-control name_l">
							<?php
							}
							?>
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