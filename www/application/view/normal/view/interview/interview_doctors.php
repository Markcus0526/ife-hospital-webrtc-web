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
				<a href="interview"><?php if ($my_type == UTYPE_ADMIN || $my_type == UTYPE_SUPER) l("会诊管理"); else l("我的会诊");?></a>
				<i class="fa fa-angle-right"></i>
			</li>
			<li>
				<a href="interview/doctors"><?php l("预约会诊");?></a>
				<i class="fa fa-angle-right"></i>
			</li>
			<li>
				<?php l("专家库");?>
			</li>
		</ul>
	</div>

	<div class="doctor-filtering">
		<ul class="nav nav-pills max-two-rows">
			<li><span all="hcountry"><?php l("医院");?>：</span></li>
			<?php 
			foreach ($mHcountries as $hcountry) {
			?>
			<li class="<?php if ($hcountry->expanded) {?>active<?php } ?>">
				<a href="#country_<?php p($hcountry->hcountry_id);?>" data-toggle="tab">
					<?php $hcountry->detail_l("country_name");?> 
					<i class="fa fa-angle-down <?php if (!$hcountry->expanded) p("hide")?>"></i><i class="fa fa-angle-up <?php if ($hcountry->expanded) p("hide")?>"></i>
				</a>
			</li>
			<?php
			}
			?>
		</ul>
		<div class="tab-content max-two-rows">
			<?php 
			foreach ($mHcountries as $hcountry) {
			?>
			<div class="tab-pane fade <?php if ($hcountry->expanded) {?>active<?php } ?> in" id="country_<?php p($hcountry->hcountry_id);?>">
				<a href="javascript:;" class="option <?php if ($hcountry->hcountry_id == $this->search->hcountry_id && $this->search->hospital_id=="") {?>active<?php } ?>" hcountry_id="<?php p($hcountry->hcountry_id);?>" hospital_id=""><?php l("全部"); ?></a>
				<?php 
				foreach ($hcountry->hospitals as $hospital) {
					?>
					<a href="javascript:;" class="option <?php if ( $this->search->hospital_id==$hospital->hospital_id) {?>active<?php } ?>"hcountry_id="<?php p($hcountry->hcountry_id);?>" hospital_id="<?php p($hospital->hospital_id);?>"><?php $hospital->detail_l("hospital_name"); ?></a>
					<?php
				}
				?>
			</div>
			<?php
			}
			?>
		</div>
		<ul class="nav nav-pills">
			<li><span all="disease"><?php l("病种");?>：</span></li>
			<?php 
			foreach ($mDiseases as $disease) {
			?>
			<li class="<?php if ($disease->disease_id==$this->search->disease_id) {?>active<?php } ?>">
				<a href="#disease_<?php p($disease->disease_id);?>" disease_id="<?php p($disease->disease_id);?>" data-toggle="tab">
					<?php $disease->detail_l("disease_name");?> 
				</a>
			</li>
			<?php
			}
			?>
		</ul>
	</div>

	<form id="list_form" method="post">
		<?php $this->search->hidden("sort_field"); ?>
		<?php $this->search->hidden("sort_order"); ?>
		<?php $this->search->hidden("hcountry_id"); ?>
		<?php $this->search->hidden("hospital_id"); ?>
		<?php $this->search->hidden("depart_id"); ?>
		<?php $this->search->hidden("disease_id"); ?>

		<p class="counts">
			<?php l("共有"); ?> <em><?php p(number_format($this->counts)); ?></em> <?php l("位国际专家"); ?>
		</p>

		<ul class="row doctor-list">
			<?php
			foreach ($mDoctors as $doctor) {
			?>
			<li class="col-sm-6 col-xs-12">
				<div>
					<div class="avartar">
						<img src="<?php p(_avartar_url($doctor->avartar_id())); ?>" class="large-avartar">
					</div>
					<div class="detail">
						<h2><?php $doctor->detail("user_name"); ?></h2>
						<dl>
							<dt class="label-disease"><?php l("疾病专长"); ?> :</dt>
							<dd><?php $doctor->detail("diseases"); ?></dd>
							<dt class="label-d-title"><?php l("职称"); ?> :</dt>
							<dd><?php $doctor->nl2br_l("d_title"); ?></dd>
							<dt class="label-d-fee"><?php l("会诊费"); ?> :</dt>
							<dd>
								<?php if($doctor->show_d_fee) { ?>
								<?php $doctor->currency("d_fee", "d_cunit"); ?>
								(<?php l("约"); $doctor->currency("ex_d_fee", "ex_d_cunit"); ?>)
								<?php } else { ?>
								**** <img src="img/shut_eye.png">
								<?php } ?>
							</dd>
						</dl>

						<div class="action">
							<a  href="user/detail/<?php p($doctor->user_id); ?>/d" class="btn btn-default"><?php l("查看"); ?></a>
							<?php if (!_is_empty($this->re_interview_id))  { ?>
							<a href="interview/re_reserve/<?php p($this->re_interview_id); ?>/<?php p($doctor->user_id); ?>" class="btn btn-primary btn-reserve"><?php l("预约"); ?></a>
							<?php } else { ?>
							<a href="interview/reserve/<?php p($doctor->user_id); ?>" class="btn btn-primary btn-reserve"><?php l("预约"); ?></a>
							<?php } ?>
						</div>
					</div>
				</div>
			</li>
			<?php
			}
			?>
		</ul>
		<?php _nodata_message($mDoctors); ?>

		<div class="text-center">
			<?php $this->pagebar->display(_url("interview/doctors/" . $this->re_interview_id . "/"), "", false, false); ?>
		</div>
	</form>

	<div id="alert_reserve" class="modal fade">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title"><?php l("提示"); ?></h4>
				</div>
				<div class="modal-body">
					<h3>
						<?php l("在预约前，请确认您的病历已完善且符合要求！"); ?>
					</h3>
				</div>
				<div class="modal-footer">
					<a href="" id="link_reserve" class="btn btn-primary"><?php l("确定"); ?></a>
				</div>
			</div>
		</div>
	</div>

</section>