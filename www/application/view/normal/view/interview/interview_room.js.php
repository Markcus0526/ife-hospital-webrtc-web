<script type="text/javascript">

$(function() {
	<?php 
	$my_type = _my_type(); 
	switch ($my_type) {
		case UTYPE_PATIENT:
			$my_type = "p";
			break;
		case UTYPE_DOCTOR:
			$my_type = "d";
			break;
		case UTYPE_INTERPRETER:
			$my_type = "i";
			break;
		default:
			$my_type = "a";
			break;
	}
	?>
	user_type = "<?php p($my_type); ?>";

	interviewRoom.init({
		scs_url: "<?php p(SCS_URL); ?>",
		rcd_url: "<?php p(RECORDING_API); ?>",
		ice_servers: <?php p(ICE_SERVERS); ?>,
		interview_id: "<?php p($mInterview->interview_id); ?>",
		user_id: "<?php p(_my_id()); ?>",
		user_type: user_type,
		token: "<?php p(_token()); ?>",
		need_interpreter: "<?php p($mInterview->need_interpreter); ?>",
		messages: <?php p(_json_encode($mImessages)); ?>,
		err: {
			ERR_CONNECT_FAIL_API: [1000, "<?php l("对不起，不能连接服务器。");?>"],
			ERR_INVALID_PARAMETER: [1001, "<?php l("对不起，您没有进入该会诊室的权限。");?>"],
			ERR_ALREADY_LOGIN: [1002, "<?php l("对不起，您在别的地方中已经进入该会诊室。");?>"],
			ERR_UNKNOWN_COMMAND: [1003, "<?php l("对不起，服务器无法处理您的需求。");?>"],
			ERR_INTERVIEW_NOT_PROGRESS: [1004, "<?php l("因为该会诊还没开始，您无法进入。");?>"],
			ERR_NO_INTERVIEW: [1005, ""],
			ERR_NOPRIV: [10, ""]
		},
		msg: {
			PATIENT: "<?php l("患者");?>",
			DOCTOR: "<?php l("专家");?>",
			INTERPRETER: "<?php l("翻译");?>",
			ONLINE: "<?php l("在线");?>",
			OFFLINE: "<?php l("掉线");?>",
			ALERT: "<?php l("提示");?>",
			PATIENT_ONLINE: "<?php l("患者已在线");?>",
			DOCTOR_ONLINE: "<?php l("专家已在线");?>",
			INTERPRETER_ONLINE: "<?php l("翻译已在线");?>",
			PATIENT_OFFLINE: "<?php l("患者已掉线");?>",
			DOCTOR_OFFLINE: "<?php l("专家已掉线");?>",
			INTERPRETER_OFFLINE: "<?php l("翻译已掉线");?>",
			ALERT_ERROR: "<?php l("错误发生");?>",
			INSTALL_CAMERA: "<?php l("请安装摄像头和麦克风。");?>",
			PERMIT_CAMERA: "<?php l("请允许摄像头和麦克风的使用。");?>",
			COULDNOT_ACCESS_CAMERA: "<?php l("不能连接摄像头和麦克风。");?>",
			CONNECTING: "<?php l("服务器连接中，请耐心等候。");?>",
			CONNECT_FAIL: "<?php l("对不起，暂不能连接会诊服务器，请稍后再连接。");?>",
			UNSUPPORT_WEBRTC: "<?php l("对不起， 您使用的阅览器不支持影像对话，请使用Chrome等阅览器。");?>",
			CONFIRM_FINISH_TITLE: "<?php l("结束");?>",
			CONFIRM_FINISH: "<?php l("确定结束此次会诊？");?>",
			FINISHING: "<?php l("会诊结束中。");?>",
			AUTOFINISHING: "<?php l("专家还没上线，将会诊自动结束。");?>",
			FINISHED: "<?php l("会诊已结束。");?>",
            WAITING: "<?php l("请稍等..."); ?>"
		},
		quality: '<?php p($mInterview->quality); ?>'
	});
});
</script>