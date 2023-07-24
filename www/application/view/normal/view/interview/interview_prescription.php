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
				<?php if ($my_type == UTYPE_INTERPRETER) { ?>
				<a href="interview"><?php l("翻译中心");?></a>
				<?php } else if ($my_type == UTYPE_SUPER || $my_type == UTYPE_ADMIN) { ?>
				<a href="interview"><?php l("会诊管理");?></a>
				<?php } else { ?>
				<a href="interview"><?php l("我的会诊");?></a>
				<?php } ?>
				<i class="fa fa-angle-right"></i>
			</li>
			<li>
				<a href="interview"><?php l("会诊列表");?></a>
				<i class="fa fa-angle-right"></i>
			</li>
			<li>
			<?php if ($this->trans_flag == 1) { ?>
				<?php l("查看翻译版第二诊疗意见");?>
			<?php } else { ?> 
				<?php l("查看第二诊疗意见");?>
			<?php } ?>
			</li>
		</ul>
	</div>
	<form id="form" action="api/interview/upload_prescription" class="form-horizontal" method="post" novalidate="novalidate">
		<?php $mInterview->hidden("interview_id"); ?>
		<div class="form-body">
			<div class="form-group form-md-line-input static">
				<label class="control-label col-md-3 col-lg-1" for="patient_name"><?php l("患者"); ?> :</label>
				<div class="col-md-9 col-lg-11">
					<p class="form-control-static"><?php $mInterview->detail("patient_name"); ?>
					</p>
				</div>
			</div>
			<div class="form-group form-md-line-input static">
				<label class="control-label col-md-3 col-lg-1" for="doctor_name"><?php l("专家"); ?> :</label>
				<div class="col-md-9 col-lg-11">
					<p class="form-control-static"><?php $mInterview->detail_l("doctor_name"); ?>
					</p>
				</div>
			</div>
			<?php if ($this->trans_flag == 1) { ?>
			<div class="form-group">
				<label class="control-label col-md-3 col-lg-1" for="ul_trans_prescription"><?php l("第二诊疗意见"); ?> :</label>
				<div class="col-md-9 col-lg-11">
					<?php $mInterview->hidden("trans_prescription"); ?>
					<ul class="attach-list margin-bottom-0" id="ul_trans_prescription">
                    </ul>
				</div>
			</div>
			<?php } else { ?> 
			<div class="form-group">
				<label class="control-label col-md-3 col-lg-1" for="ul_prescription"><?php l("第二诊疗意见"); ?> :</label>
				<div class="col-md-9 col-lg-11">
					<?php $mInterview->hidden("prescription"); ?>
					<ul class="attach-list margin-bottom-0" id="ul_prescription">
                    </ul>
				</div>
			</div>
			<?php } ?>
		</div>
	</form>

</section>