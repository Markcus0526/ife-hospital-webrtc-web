<?php 
$my_type = _my_type();
?>
<div class="page-sidebar navbar-collapse collapse">
	<ul class="page-sidebar-menu hidden-sm hidden-xs" data-auto-scroll="true" data-slide-speed="200">
		<li class="profile">
			<img src="<?php p(_avartar_url()); ?>" class="avartar">
			<p><?php p(_my_name()); ?></p>
			<a href="login/logout" class="logout"><?php l("退出"); ?></a>
		</li>
		<?php if ($my_type == UTYPE_PATIENT) { ?>
		<li class="start <?php $this->set_active('chistory'); ?>">
			<a href="javascript:;">
				<img src="ico/chistory.png" class="icon">
				<span class="title"><?php l("病历管理"); ?> </span>
				<span class="arrow open"></span>
				<span class="selected"></span>
			</a>
			<ul class="sub-menu">
				<li class="<?php $this->set_sub_active('chistory_list'); ?>">
					<a href="chistory"><?php l("我的病历"); ?></a>
				</li>
			</ul>
		</li>
		<li class="<?php $this->set_active('interview'); ?>">
			<a href="javascript:;">
				<img src="ico/interview.png" class="icon">
				<span class="title"><?php l("我的会诊"); ?> </span>
				<span class="arrow open"></span>
				<span class="selected"></span>
			</a>
			<ul class="sub-menu">
				<li class="<?php $this->set_sub_active('interview_reserve'); ?>">
					<a href="interview/doctors"><?php l("预约会诊"); ?></a>
				</li>
				<li class="<?php $this->set_sub_active('interview_list'); ?>">
					<a href="interview"><?php l("会诊列表"); ?></a>
				</li>
			</ul>
		</li>
		<?php } else if ($my_type == UTYPE_DOCTOR) { ?>
		<li class="start <?php $this->set_active('dtime'); ?>">
			<a href="javascript:;">
				<img src="ico/dtime.png" class="icon">
				<span class="title"><?php l("时间管理"); ?> </span>
				<span class="arrow open"></span>
				<span class="selected"></span>
			</a>
			<ul class="sub-menu">
				<li class="<?php $this->set_sub_active('dtime_list'); ?>">
					<a href="dtime"><?php l("我的时间"); ?></a>
				</li>
			</ul>
		</li>
		<li class="<?php $this->set_active('interview'); ?>">
			<a href="javascript:;">
				<img src="ico/interview.png" class="icon">
				<span class="title"><?php l("我的会诊"); ?> </span>
				<span class="arrow open"></span>
				<span class="selected"></span>
			</a>
			<ul class="sub-menu">
				<li class="<?php $this->set_sub_active('interview_list'); ?>">
					<a href="interview"><?php l("会诊列表"); ?></a>
				</li>
			</ul>
		</li>
		<?php } else if ($my_type == UTYPE_INTERPRETER) { ?>
		<li class="start <?php $this->set_active('interview'); ?>">
			<a href="javascript:;">
				<img src="ico/interp.png" class="icon">
				<span class="title"><?php l("翻译中心"); ?> </span>
				<span class="arrow open"></span>
				<span class="selected"></span>
			</a>
			<ul class="sub-menu">
				<li class="<?php $this->set_sub_active('interview_interp_list'); ?>">
					<a href="interview/interp_list"><?php l("翻译列表"); ?></a>
				</li>
				<li class="<?php $this->set_sub_active('interview_list'); ?>">
					<a href="interview"><?php l("我的翻译"); ?></a>
				</li>
			</ul>
		</li>
		<?php } else if ($my_type == UTYPE_ADMIN) { ?>
			<?php if (_has_priv(CODE_PRIV_REG_USER, PRIV_REG_USER_D) || _has_priv(CODE_PRIV_REG_USER, PRIV_REG_USER_I)) { ?>
		<li class="<?php $this->set_active('reguser'); ?>">
			<a href="javascript:;">
				<img src="ico/deliberate.png" class="icon">
				<span class="title"><?php l("审核注册用户"); ?> </span>
				<span class="arrow open"></span>
				<span class="selected"></span>
			</a>
			<ul class="sub-menu">
				<?php if (_has_priv(CODE_PRIV_REG_USER, PRIV_REG_USER_D)) { ?>
				<li class="<?php $this->set_sub_active('reguser_doctors'); ?>">
					<a href="reguser/doctors"><?php l("审核注册医生"); ?></a>
				</li>
				<?php } ?>
				<?php if (_has_priv(CODE_PRIV_REG_USER, PRIV_REG_USER_I)) { ?>
				<li class="<?php $this->set_sub_active('reguser_interpreters'); ?>">
					<a href="reguser/interpreters"><?php l("审核注册翻译"); ?></a>
				</li>
				<?php } ?>
			</ul>
		</li>
			<?php } ?>
			<?php if (_has_priv(CODE_PRIV_RESERVE, PRIV_RESERVE) || _has_priv(CODE_PRIV_INTERVIEWS)) { ?>
		<li class="<?php $this->set_active('interview'); ?>">
			<a href="javascript:;">
				<img src="ico/interview.png" class="icon">
				<span class="title"><?php l("会诊管理"); ?> </span>
				<span class="arrow open"></span>
				<span class="selected"></span>
			</a>
			<ul class="sub-menu">
				<?php if (_has_priv(CODE_PRIV_RESERVE, PRIV_RESERVE)) { ?>
				<li class="<?php $this->set_sub_active('interview_reserve'); ?>">
					<a href="interview/doctors"><?php l("预约会诊"); ?></a>
				</li>
				<?php } ?>
				<?php if (_has_priv(CODE_PRIV_INTERVIEWS)) { ?>
				<li class="<?php $this->set_sub_active('interview_list'); ?>">
					<a href="interview"><?php l("会诊列表"); ?></a>
				</li>
				<?php } ?>
			</ul>
		</li>
			<?php } ?>
			<?php if (_has_priv(CODE_PRIV_CHISTORY, PRIV_DETAIL)) { ?>
		<li class="start <?php $this->set_active('chistory'); ?>">
			<a href="javascript:;">
				<img src="ico/chistory.png" class="icon">
				<span class="title"><?php l("病历管理"); ?> </span>
				<span class="arrow open"></span>
				<span class="selected"></span>
			</a>
			<ul class="sub-menu">
				<li class="<?php $this->set_sub_active('chistory_list'); ?>">
					<a href="chistory"><?php l("病历列表"); ?></a>
				</li>
			</ul>
		</li>
			<?php } ?>
			<?php if (_has_priv(CODE_PRIV_PATIENTS, PRIV_DETAIL) || _has_priv(CODE_PRIV_DOCTORS, PRIV_DETAIL) || _has_priv(CODE_PRIV_INTERPRETERS, PRIV_DETAIL)) { ?>
		<li class="<?php $this->set_active('user'); ?>">
			<a href="javascript:;">
				<img src="ico/user.png" class="icon">
				<span class="title"><?php l("用户管理"); ?> </span>
				<span class="arrow open"></span>
				<span class="selected"></span>
			</a>
			<ul class="sub-menu">
				<?php if (_has_priv(CODE_PRIV_PATIENTS, PRIV_DETAIL)) { ?>
				<li class="<?php $this->set_sub_active('user_patients'); ?>">
					<a href="user/patients"><?php l("患者列表"); ?></a>
				</li>
				<?php } ?>
				<?php if (_has_priv(CODE_PRIV_DOCTORS, PRIV_DETAIL)) { ?>
				<li class="<?php $this->set_sub_active('user_doctors'); ?>">
					<a href="user/doctors"><?php l("医生列表"); ?></a>
				</li>
				<?php } ?>
				<?php if (_has_priv(CODE_PRIV_INTERPRETERS, PRIV_DETAIL)) { ?>
				<li class="<?php $this->set_sub_active('user_interpreters'); ?>">
					<a href="user/interpreters"><?php l("翻译列表"); ?></a>
				</li>
				<?php } ?>
			</ul>
		</li>
			<?php } ?>
			<?php if (_has_priv(CODE_PRIV_HOSPITALS)) { ?>
		<li class="start <?php $this->set_active('master'); ?>">
			<a href="javascript:;">
				<img src="ico/master.png" class="icon">
				<span class="title"><?php l("分类管理"); ?> </span>
				<span class="arrow open"></span>
				<span class="selected"></span>
			</a>
			<ul class="sub-menu">
				<li class="<?php $this->set_sub_active('master_hospital'); ?>">
					<a href="hospital"><?php l("医院列表"); ?></a>
				</li>
			</ul>
		</li>
			<?php } ?>
			<?php if (_has_priv(CODE_PRIV_STATS)) { ?>
		<li class="start <?php $this->set_active('stats'); ?>">
			<a href="javascript:;">
				<img src="ico/stats.png" class="icon">
				<span class="title"><?php l("统计分析"); ?> </span>
				<span class="arrow open"></span>
				<span class="selected"></span>
			</a>
			<ul class="sub-menu">
				<li class="<?php $this->set_sub_active('stats_main'); ?>">
					<a href="stats"><?php l("次数与时长"); ?></a>
				</li>
			</ul>
		</li>
			<?php } ?>
			<?php if (_has_priv(CODE_PRIV_FEEDBACK)) { ?>
		<li class="start <?php $this->set_active('feedback'); ?>">
			<a href="javascript:;">
				<img src="ico/feedback.png" class="icon">
				<span class="title"><?php l("用户反馈"); ?> </span>
				<span class="arrow open"></span>
				<span class="selected"></span>
			</a>
			<ul class="sub-menu">
				<li class="<?php $this->set_sub_active('feedback_list'); ?>">
					<a href="feedback"><?php l("反馈列表"); ?></a>
				</li>
			</ul>
		</li>
			<?php } ?>
		<?php } else if ($my_type == UTYPE_SUPER) { ?>
		<li class="<?php $this->set_active('interview'); ?>">
			<a href="javascript:;">
				<img src="ico/interview.png" class="icon">
				<span class="title"><?php l("会诊管理"); ?> </span>
				<span class="arrow open"></span>
				<span class="selected"></span>
			</a>
			<ul class="sub-menu">
				<li class="<?php $this->set_sub_active('interview_list'); ?>">
					<a href="interview"><?php l("会诊列表"); ?></a>
				</li>
			</ul>
		</li>
		<li class="start <?php $this->set_active('chistory'); ?>">
			<a href="javascript:;">
				<img src="ico/chistory.png" class="icon">
				<span class="title"><?php l("病历管理"); ?> </span>
				<span class="arrow open"></span>
				<span class="selected"></span>
			</a>
			<ul class="sub-menu">
				<li class="<?php $this->set_sub_active('chistory_list'); ?>">
					<a href="user/patients/chistory"><?php l("病历列表"); ?></a>
				</li>
			</ul>
		</li>
		<li class="<?php $this->set_active('user'); ?>">
			<a href="javascript:;">
				<img src="ico/user.png" class="icon">
				<span class="title"><?php l("用户管理"); ?> </span>
				<span class="arrow open"></span>
				<span class="selected"></span>
			</a>
			<ul class="sub-menu">
				<li class="<?php $this->set_sub_active('user_patients'); ?>">
					<a href="user/patients"><?php l("患者列表"); ?></a>
				</li>
				<li class="<?php $this->set_sub_active('user_doctors'); ?>">
					<a href="user/doctors"><?php l("医生列表"); ?></a>
				</li>
				<li class="<?php $this->set_sub_active('user_interpreters'); ?>">
					<a href="user/interpreters"><?php l("翻译列表"); ?></a>
				</li>
				<li class="<?php $this->set_sub_active('user_admins'); ?>">
					<a href="user/admins"><?php l("管理员列表"); ?></a>
				</li>
			</ul>
		</li>
		<li class="start <?php $this->set_active('master'); ?>">
			<a href="javascript:;">
				<img src="ico/master.png" class="icon">
				<span class="title"><?php l("分类管理"); ?> </span>
				<span class="arrow open"></span>
				<span class="selected"></span>
			</a>
			<ul class="sub-menu">
				<li class="<?php $this->set_sub_active('master_hospital'); ?>">
					<a href="hospital"><?php l("医院列表"); ?></a>
				</li>
				<li class="<?php $this->set_sub_active('master_depart'); ?>">
					<a href="depart"><?php l("科室列表"); ?></a>
				</li>
				<li class="<?php $this->set_sub_active('master_disease'); ?>">
					<a href="disease"><?php l("病种列表"); ?></a>
				</li>
			</ul>
		</li>
		<li class="start <?php $this->set_active('stats'); ?>">
			<a href="javascript:;">
				<img src="ico/stats.png" class="icon">
				<span class="title"><?php l("统计分析"); ?> </span>
				<span class="arrow open"></span>
				<span class="selected"></span>
			</a>
			<ul class="sub-menu">
				<li class="<?php $this->set_sub_active('stats_main'); ?>">
					<a href="stats"><?php l("次数与时长"); ?></a>
				</li>
			</ul>
		</li>
		<li class="start <?php $this->set_active('feedback'); ?>">
			<a href="javascript:;">
				<img src="ico/feedback.png" class="icon">
				<span class="title"><?php l("用户反馈"); ?> </span>
				<span class="arrow open"></span>
				<span class="selected"></span>
			</a>
			<ul class="sub-menu">
				<li class="<?php $this->set_sub_active('feedback_list'); ?>">
					<a href="feedback"><?php l("反馈列表"); ?></a>
				</li>
			</ul>
		</li>
		<?php }?>
		<li class="<?php $this->set_active('profile'); ?>">
			<a href="javascript:;">
				<img src="ico/profile.png" class="icon">
				<span class="title"><?php l("用户信息"); ?> </span>
				<span class="arrow open"></span>
				<span class="selected"></span>
			</a>
			<ul class="sub-menu">
				<li class="<?php $this->set_sub_active('profile_main'); ?>">
					<a href="profile"><?php l("基本信息"); ?></a>
				</li>
				<?php if($my_type == UTYPE_ADMIN && _has_priv(CODE_PRIV_PROFILE, PRIV_PASSWORD) || $my_type != UTYPE_ADMIN) { ?>
				<li class="<?php $this->set_sub_active('profile_password'); ?>">
					<a href="profile/password"><?php l("更改密码"); ?></a>
				</li>
				<?php } ?>
				<li class="<?php $this->set_sub_active('profile_mobile'); ?>">
					<a href="profile/mobile"><?php l("更改手机"); ?></a>
				</li>
				<li class="<?php $this->set_sub_active('profile_feedback'); ?>">
					<a href="profile/feedback"><?php l("意见反馈"); ?></a>
				</li>
			</ul>
		</li>
	</ul>
	<ul class="page-sidebar-menu visible-sm visible-xs" data-slide-speed="200" data-auto-scroll="true">
	</ul>
</div>