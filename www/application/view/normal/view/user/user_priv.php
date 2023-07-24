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
				<a href="user/admins"><?php l("管理员列表");?></a>
				<i class="fa fa-angle-right"></i>
			</li>
			<li>
				<?php l("权限设置");?>
			</li>
		</ul>
	</div>

	<form id="form" action="api/user/priv" class="form-horizontal table-container" method="post" novalidate="novalidate">
		<?php $mUser->hidden("user_id"); ?>
		<div class="form-body">
			<div class="form-group static">
				<label class="control-label col-md-2"><?php l("姓名"); ?> :</label>
				<div class="col-md-10">
					<p class="form-control-static"><?php p($mUser->user_name); ?></p>
				</div>
			</div>

			<table class="table table-striped table-hover table-bordered table-condensed">
				<thead>
					<tr>
						<th width="30%"><?php l('权限列表'); ?></th>
						<th><?php l('权限范围'); ?></th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td class="text-center text-middle"><?php l('审核注册用户'); ?></td>
						<td><?php $mUser->priv->checkbox("priv_reg_user", CODE_PRIV_REG_USER, null, true); ?></td>
					</tr>
					<tr>
						<td class="text-center text-middle"><?php l('用户管理'); ?></td>
						<td>
							<?php $mUser->priv->checkbox("priv_patients", CODE_PRIV_PATIENTS, null, false, true, _l("患者管理") . ":"); ?>
							<?php $mUser->priv->checkbox("priv_doctors", CODE_PRIV_DOCTORS, null, false, true, _l("专家管理") . ":"); ?>
							<?php $mUser->priv->checkbox("priv_interpreters", CODE_PRIV_INTERPRETERS, null, false, true, _l("翻译管理") . ":"); ?>
						</td>
					</tr>
					<tr>
						<td class="text-center text-middle"><?php l('分类管理'); ?></td>
						<td><?php $mUser->priv->checkbox("priv_hospitals", CODE_PRIV_HOSPITALS, null, false, true, _l("医院管理") . ":"); ?>
					</tr>
					<tr>
						<td class="text-center text-middle"><?php l('病历管理'); ?></td>
						<td><?php $mUser->priv->checkbox("priv_chistory", CODE_PRIV_CHISTORY, null, false, true, _l("病历管理") . ":"); ?>
					</tr>
					<tr>
						<td class="text-center text-middle"><?php l('会诊管理'); ?></td>
						<td>
							<?php $mUser->priv->checkbox("priv_reserve", CODE_PRIV_RESERVE); ?>
							<?php $mUser->priv->checkbox("priv_interviews", CODE_PRIV_INTERVIEWS, null, false, true, _l("会诊列表") . ":"); ?>
						</td>
					</tr>
					<tr>
						<td class="text-center text-middle"><?php l('用户信息'); ?></td>
						<td><?php $mUser->priv->checkbox("priv_profile", CODE_PRIV_PROFILE, null, true); ?></td>
					</tr>
					<tr>
						<td class="text-center text-middle"><?php l('统计分析'); ?></td>
						<td><?php $mUser->priv->checkbox("priv_stats", CODE_PRIV_STATS, null, false, true, _l("次数与时长") . ":"); ?>
					</tr>
					<tr>
						<td class="text-center text-middle"><?php l('用户反馈'); ?></td>
						<td><?php $mUser->priv->checkbox("priv_feedback", CODE_PRIV_FEEDBACK); ?></td>
					</tr>
					<tr>
						<td class="text-center text-middle"><?php l('系统检测'); ?></td>
						<td><?php $mUser->priv->checkbox("priv_syscheck", CODE_PRIV_SYSCHECK); ?></td>
					</tr>
				</tbody>
			</table>
		</div>

		<div class="form-actions">
			<button type="submit" class="btn btn-primary"><i class="icon-check"></i> <?php l("确定"); ?></button>
			<a href="user/admins" class="btn btn-default btn-cancel"><i class="icon-close"></i> <?php l("取消"); ?></a>
		</div>

	</form>
</section>