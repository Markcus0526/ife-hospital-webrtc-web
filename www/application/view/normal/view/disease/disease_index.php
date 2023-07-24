<section>
	<div class="page-bar">
		<ul class="page-breadcrumb">
			<li>
				<span><?php l("当前位置"); ?> :</span>
			</li>
			<li>
				<a href="disease"><?php l("分类管理");?></a>
				<i class="fa fa-angle-right"></i>
			</li>
			<li>
				<?php l("病种列表");?>
			</li>
		</ul>
	</div>

	<form id="list_form" class="table-container" action="api/disease/save" method="post">
		<div class="row">
			<div class="col-xs-6">
				<a href="javascript:;" id="insert_disease" class="action-link"><i class="mdi-content-add-circle"></i> <?php l("添加病种"); ?></a>
			</div>
			<div class="col-xs-6 text-right">
				<a href="disease/index/1" class="<?php if ($this->expand==1) p("active"); ?>"><?php l('展开'); ?></a> |
				<a href="disease/index/0" class="<?php if ($this->expand==0) p("active"); ?>"><?php l('收起'); ?></a>
			</div>
		</div>

		<?php $this->search->hidden("sort_field"); ?>
		<?php $this->search->hidden("sort_order"); ?>
		<table id="disease_list" class="table table-striped table-hover table-bordered table-condensed">
			<thead>
				<tr>
					<th class="td-no"><?php l('序号'); ?></th>
					<th width="30%"><?php l('病种名称'); ?></th>
					<th><?php l('病种简介'); ?></th>
					<th class="td-action"><?php l('移动'); ?></th>
					<th><?php l('默认展开'); ?></th>
					<th class="td-action"><?php l("操作"); ?></th>
				</tr>
			</thead>
			<tbody>
				<tr id="edit_disease" class="hide">
					<td class="td-no">+</td>
					<td>
						<?php $mDisease->hidden("disease_id"); ?>
						<?php $mDisease->input("disease_name", array("maxlength" => 100)); ?>
					</td>
					<td>
						<?php $mDisease->textarea("description", 3); ?>
					</td>
					<td></td>
					<td></td>
					<td class="td-action">
						<button type="submit" class="btn btn-xs btn-primary"><?php l("保存"); ?></button>
						<button type="button" id="btn_cancel_disease" class="btn btn-xs btn-primary"><?php l("取消"); ?></button>
					</td>
				</tr>
				<tr id="edit_dtemplate" class="hide">
					<td class="td-no">+</td>
					<td>
						<?php $mDisease->hidden("dtemplate_id"); ?>
						<?php $mDisease->input("dtemplate_name", array("maxlength" => 100)); ?>
					</td>
					<td></td>
					<td></td>
					<td></td>
					<td class="td-action">
						<button type="submit" class="btn btn-xs btn-primary"><?php l("保存"); ?></button>
						<button type="button" id="btn_cancel_dtemplate" class="btn btn-xs btn-primary"><?php l("取消"); ?></button>
					</td>
				</tr>
			<?php
				$i = $this->pagebar->start_no();
				foreach ($mDiseases as $disease) {
			?>
				<tr class="disease" disease_id="<?php p($disease->disease_id); ?>">
					<td class="td-no"><?php p($i); ?></td>
					<td>
						<a href="javascript:;" class="btn-expand"><i class="fa fa-caret-down"></i></a>
						<span class="editable editable-language" disease_name="<?php $disease->detail("disease_name"); ?>" disease_name_l="<?php $disease->detail("disease_name_l"); ?>"><?php $disease->detail("disease_name"); ?> <?php $disease->detail_other_lang("disease_name_l"); ?></span>

						<a href="javascript:;" class="action-link insert-dtemplate"><small><i class="mdi-content-add-circle"></i><?php l('添加病历模板'); ?></small></a>
					</td>
					<td><span class="editable"><?php $disease->nl2br("description"); ?></span></td>
					<td class="text-center">
						<a href="javascript:;" class="btn-move-top" direct="-2"><i class="glyphicon glyphicon-arrow-up"></i></a>
						<a href="javascript:;" class="btn-move-up" direct="-1"><i class="glyphicon glyphicon-arrow-up"></i></a>
						<a href="javascript:;" class="btn-move-down" direct="1"><i class="glyphicon glyphicon-arrow-down"></i></a>
						<a href="javascript:;" class="btn-move-bottom" direct="2"><i class="glyphicon glyphicon-arrow-down"></i></a>
					</td>
					<td class="td-switch">
						<?php $disease->checkbox_switch("expand");?>
					</td>
					<td class="td-action">
						<button type="button" class="btn btn-xs btn-primary btn-edit"><?php l("修改"); ?></button>
						<button type="button" class="btn btn-xs btn-primary btn-delete"><?php l("删除"); ?></button>
					</td>
				</tr>
			<?php 
					foreach($disease->dtemplates as $dtemplate) {
			?>
				<tr class="dtemplate" disease_id="<?php p($disease->disease_id); ?>" dtemplate_id="<?php p($dtemplate->dtemplate_id); ?>">
					<td class="td-no"></td>
					<td>
						<i class="line-top-right"></i>
						<span class="editable editable-language" dtemplate_name="<?php $dtemplate->detail("dtemplate_name"); ?>" dtemplate_name_l="<?php $dtemplate->detail("dtemplate_name_l"); ?>"><?php $dtemplate->detail("dtemplate_name"); ?> <?php $dtemplate->detail_other_lang("dtemplate_name_l"); ?></span>
					</td>
					<td>
						<?php $dtemplate->attaches_down('template_file'); ?>
						<a href="javascript:;" class="btn-upload-template" title="<?php l("上传模板文件"); ?>" template_file='<?php p($dtemplate->template_file); ?>'>
							<img src="img/upload.png">
						</a>
					</td>
					<td class="text-center">
						<a href="javascript:;" class="btn-move-up" direct="-1"><i class="glyphicon glyphicon-arrow-up"></i></a>
						<a href="javascript:;" class="btn-move-down" direct="1"><i class="glyphicon glyphicon-arrow-down"></i></a>
					</td>
					<td></td>
					<td class="td-action">
						<button type="button" class="btn btn-xs btn-primary btn-edit"><?php l("修改"); ?></button>
						<button type="button" class="btn btn-xs btn-primary btn-delete"><?php l("删除"); ?></button>
					</td>
				</tr>
			<?php
					}
			?>
			<?php
					$i ++;
				}
			?>
			</tbody>
		</table>
		<!--/table -->
		<?php _nodata_message($mDiseases); ?>

		<?php $this->pagebar->display(_url("disease/index/" . $this->expand)); ?>
		
	</form>

	<form id="language_form" action="api/disease/save_name" method="post" class="form-horizontal modal fade">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title"><?php l("多语言设置"); ?></h4>
				</div>
				<div class="modal-body">
					<input type="hidden" name="id">
					<input type="hidden" name="name_field" value="disease_name">
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
							<input type="text" name="name_l['<?php p($lang["language_code"]); ?>']" maxlength=100 class="form-control name_l">
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

	<form id="dtemplate_form" action="api/disease/save_dtemplate" method="post" class="form-horizontal modal fade">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title"><?php l("添加病历模板"); ?></h4>
				</div>
				<div class="modal-body">
					<input type="hidden" name="disease_id">
					<div class="form-group form-md-line-input">
						<label class="control-label col-md-3"><?php l("模板名称"); ?> :</label>
						<div class="col-md-9">
							<input type="text" name="dtemplate_name" class="form-control" maxlength=100>
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

	<form id="upload_form" action="api/disease/upload_template" method="post" class="form-horizontal modal fade">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title"><?php l("上传模板"); ?></h4>
				</div>
				<div class="modal-body">
					<input type="hidden" name="dtemplate_id">
					<?php 
					foreach ($mLanguages as $key => $lang) {
						?>
					<div class="form-group form-md-line-input">
						<label class="control-label col-md-3"><?php l($lang["language_name"]); ?> :</label>
						<div class="col-md-9">
							<ul class="attach-list margin-bottom-0" id="ul_template_file_<?php p($lang["language_code"]); ?>">
		                    </ul>
							<a href="common/upload/template_file_<?php p($lang["language_code"]); ?>" class="btn-upload fancybox file-upload" fancy-width=600 fancy-height=480>
								 <i class="mdi-content-add"></i>
							</a>

							<input type="text" id="template_file_<?php p($lang["language_code"]); ?>" name="template_file_l['<?php p($lang["language_code"]); ?>']" class="input-null">
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