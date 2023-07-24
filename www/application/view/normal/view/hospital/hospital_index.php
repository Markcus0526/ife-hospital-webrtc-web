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
				<a href="hospital"><?php l("分类管理");?></a>
				<i class="fa fa-angle-right"></i>
			</li>
			<li>
				<?php l("医院列表");?>
			</li>
		</ul>
	</div>

	<form id="list_form" class="table-container" action="api/hcountry/save" method="post">
		<div class="row">
			<div class="col-xs-6">
				<?php if ($my_type == UTYPE_SUPER) { ?>
				<a href="javascript:;" id="insert_country" class="action-link"><i class="mdi-content-add-circle"></i> <?php l('添加国家'); ?></a>
				<?php } ?>
			</div>
			<div class="col-xs-6 text-right">
				<a href="hospital/index/1" class="<?php if ($this->expand==1) p("active"); ?>"><?php l('展开'); ?></a> |
				<a href="hospital/index/0" class="<?php if ($this->expand==0) p("active"); ?>"><?php l('收起'); ?></a>
			</div>
		</div>

		<?php $this->search->hidden("sort_field"); ?>
		<?php $this->search->hidden("sort_order"); ?>
		<table id="country_list" class="table table-striped table-hover table-bordered table-condensed">
			<thead>
				<tr>
					<th class="td-no"><?php l('序号'); ?></th>
					<th width="30%"><?php l('国家'); ?>、<?php l('医院名称'); ?></th>
					<th width="30%"><?php l('医院地址'); ?></th>
					<th><?php l('移动'); ?></th>
					<th><?php l('默认展开'); ?></th>
					<th class="td-action"><?php l('操作'); ?></th>
				</tr>
			</thead>
			<tbody>
				<tr id="edit_country" class="hide">
					<td class="td-no">+</td>
					<td>
						<?php $mCountry->hidden("o_hcountry_id"); ?>
						<?php $mCountry->input("country_name", array("maxlength" => 50)); ?>
					</td>
					<td>
						<?php $mCountry->select_model("hcountry_id", new countryModel, "country_id", "country_name", _l("请选择国家")); ?>	
					</td>
					<td></td>
					<td></td>
					<td class="td-action">
						<button type="submit" class="btn btn-xs btn-primary"><?php l("保存"); ?></button>
						<button type="button" class="btn btn-xs btn-primary btn-cancel"><?php l("取消"); ?></button>
					</td>
				</tr>
				<tr id="edit_hospital" class="hide">
					<td class="td-no">+</td>
					<td>
						<?php $mHospital->hidden("hospital_id"); ?>
						<?php $mHospital->input("hospital_name", array("maxlength" => 50, "class" => "input-sm")); ?>
					</td>
					<td>
						<?php $mHospital->input("address", array("maxlength" => 255, "class" => "input-sm")); ?>
					</td>
					<td></td>
					<td></td>
					<td class="td-action">
						<button type="submit" class="btn btn-xs btn-primary"><?php l("保存"); ?></button>
						<button type="button" class="btn btn-xs btn-primary btn-cancel"><?php l("取消"); ?></button>
					</td>
				</tr>
			<?php
				$i = $this->pagebar->start_no();
				foreach ($mCountries as $hcountry) {
			?>
				<tr class="hcountry" hcountry_id="<?php p($hcountry->hcountry_id); ?>" expand="<?php p($hcountry->expand); ?>">
					<td class="td-no"><?php p($i); ?></td>
					<td>
						<a href="javascript:;" class="btn-expand"><i class="fa fa-caret-down"></i></a>
						<span class="editable editable-language" country_name="<?php $hcountry->detail("country_name"); ?>" country_name_l="<?php $hcountry->detail("country_name_l"); ?>"><?php $hcountry->detail("country_name"); ?> <?php $hcountry->detail_other_lang("country_name_l"); ?></span>
						<?php if (_has_priv(CODE_PRIV_HOSPITALS, PRIV_ADD)) { ?>
						<a href="javascript:;" class="action-link insert-hospital"><small><i class="mdi-content-add-circle"></i><?php l('添加医院'); ?></small></a>
						<?php } ?>
					</td>
					<td></td>
					<td class="text-center">
						<a href="javascript:;" class="btn-move-top" direct="-2"><i class="glyphicon glyphicon-arrow-up"></i></a>
						<a href="javascript:;" class="btn-move-up" direct="-1"><i class="glyphicon glyphicon-arrow-up"></i></a>
						<a href="javascript:;" class="btn-move-down" direct="1"><i class="glyphicon glyphicon-arrow-down"></i></a>
						<a href="javascript:;" class="btn-move-bottom" direct="2"><i class="glyphicon glyphicon-arrow-down"></i></a>
					</td>
					<td class="td-switch">
						<?php $hcountry->checkbox_switch("expand");?>
					</td>
					<td class="td-action">
						<?php if ($my_type == UTYPE_SUPER) { ?>
						<button type="button" class="btn btn-xs btn-primary btn-edit"><?php l("修改"); ?></button>
						<button type="button" class="btn btn-xs btn-primary btn-delete"><?php l("删除"); ?></button>
						<?php } ?>
					</td>
				</tr>
			<?php 
					foreach($hcountry->hospitals as $hospital) {
			?>
				<tr class="hospital" hcountry_id="<?php p($hcountry->hcountry_id); ?>" hospital_id="<?php p($hospital->hospital_id); ?>">
					<td class="td-no"></td>
					<td>
						<i class="line-top-right"></i>
						<span class="editable editable-language" hospital_name="<?php $hospital->detail("hospital_name"); ?>" hospital_name_l="<?php $hospital->detail("hospital_name_l"); ?>"><?php $hospital->detail("hospital_name"); ?> <?php $hospital->detail_other_lang("hospital_name_l"); ?></span>
					</td>
					<td>
						<span class="editable" for="address"><?php $hospital->detail("address"); ?></span>
					</td>
					<td class="text-center">
						<a href="javascript:;" class="btn-move-up" direct="-1"><i class="glyphicon glyphicon-arrow-up"></i></a>
						<a href="javascript:;" class="btn-move-down" direct="1"><i class="glyphicon glyphicon-arrow-down"></i></a>
					</td>
					<td></td>
					<td class="td-action">
						<?php if (_has_priv(CODE_PRIV_HOSPITALS, PRIV_EDIT)) { ?>
						<button type="button" class="btn btn-xs btn-primary btn-edit"><?php l("修改"); ?></button>
						<?php } ?>
						<?php if (_has_priv(CODE_PRIV_HOSPITALS, PRIV_DELETE)) { ?>
						<button type="button" class="btn btn-xs btn-primary btn-delete"><?php l("删除"); ?></button>
						<?php } ?>
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
		<?php _nodata_message($mCountries); ?>

		<?php $this->pagebar->display(_url("hospital/index/" . $this->expand)); ?>
		
	</form>

	<form id="language_form" action="api/hospital/save_name" method="post" class="form-horizontal modal fade">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title"><?php l("多语言设置"); ?></h4>
				</div>
				<div class="modal-body">
					<input type="hidden" name="id">
					<input type="hidden" name="name_field" value="hospital_name">
					<div class="form-group form-md-line-input">
						<label class="control-label col-md-3"><?php l("名称"); ?> :</label>
						<div class="col-md-9">
							<p class="form-control-static" id="name">
							</p>
						</div>
					</div>
					<?php 
					foreach ($mLanguages as $key => $lang) {
						?>
					<div class="form-group form-md-line-input">
						<label class="control-label col-md-3"><?php l($lang["language_name"]); ?> :</label>
						<div class="col-md-9">
							<input type="text" name="name_l['<?php p($lang["language_code"]); ?>']" class="form-control name_l">
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