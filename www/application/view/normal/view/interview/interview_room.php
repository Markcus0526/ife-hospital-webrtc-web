<?php 
$my_type = _my_type();

function show_video_bar($utype)
{
	if ($utype == _my_type()) {
?>
	<i class="ln-icon-mic btn-mic"></i>
	<div class="btn-group btn-volume dropup">
		<i class="ln-icon-volume-high dropdown-toggle" data-toggle="dropdown"></i>
		<div class="dropdown-menu open">
			<div id="slider-vertical" class="slider" style="height: 100px;">
			</div>
		</div>
	</div>
<?php
	}
}
?>
<section class="interview-room transparent">
	<header>
		<?php
		if ($my_type == UTYPE_DOCTOR || $my_type == UTYPE_PATIENT || $my_type == UTYPE_INTERPRETER) { ?>
		<div class="sel-quality">
			<?php $mInterview->select_quality("quality"); ?>
		</div>
		<?php 
		}
		?>
		<button type="button" class="btn btn-primary btn-start"><?php l("刷新"); ?></button>
		<span class="start-warning">
		<?php l("如果对方显示已在线，但没有视频画面，请您点击“刷新”按钮重新连接。"); ?>
		</span>
		
		<div class="header-right">
			<strong><?php l("系统时间"); ?> <span id="clock"></span></strong>
			
			<?php if ($my_type == UTYPE_DOCTOR) { ?>
			<button type="button" class="btn btn-danger btn-finish"><?php l("结束会诊"); ?></button>
			<?php } else { ?>
			<button type="button" class="btn btn-primary btn-exit"><?php l("退出会诊室"); ?></button>
			<?php } ?>
		</div>
	</header>
	<article>
		<div class="alert-waiting-doctor hide">
			<?php l("温馨提示：如果专家没在%s分钟内上线，会诊将自动结束，请您耐心等候专家更改会诊时间通知！", INTERVIEW_OFFLINE_LIMIT);?>	
		</div>
		<div class="room-main">
		<?php if ($mInterview->need_interpreter) {?>
			<?php if ($my_type == UTYPE_PATIENT) { ?>
			<div class="row row-big">
				<div class="col-md-6 text-center">
					<h4 id="state_msg_i"><?php l("翻译已掉线"); ?></h4>
					<video id="video_i" class="video-big video-interpreter" autoplay></video>
					<div class="video-bar">
					</div>
					<h3><?php l("翻译"); ?> : <?php $mInterview->detail_l("interpreter_name"); ?></h3>
					<div id="media_error_i" class="media-error"></div>
				</div>
				<div class="col-md-6 text-center">
					<h4 id="state_msg_d"><?php l("专家已掉线"); ?></h4>
					<video id="video_d" class="video-big video-doctor" autoplay></video>
					<div class="video-bar">
					</div>
					<h3><?php l("专家"); ?> : <?php $mInterview->detail_l("doctor_name"); ?></h3>
					<div id="media_error_d" class="media-error"></div>
				</div>
			</div>
			<div class="row row-small">
				<div class="col-md-12 text-center">
					<h4 id="state_msg_p"><?php l("患者已掉线"); ?></h4>
					<video id="video_p" class="video-small video-patient" autoplay></video>
					<div id="video_bar_p" class="video-bar">
						<?php show_video_bar(UTYPE_PATIENT); ?>
					</div>
					<h3><?php l("患者"); ?> : <?php $mInterview->detail("patient_name"); ?></h3>
					<div id="media_error_p" class="media-error"></div>
				</div>
			</div>
			<?php } else if ($my_type == UTYPE_DOCTOR) { ?>
			<div class="row row-big">
				<div class="col-md-6 text-center">
					<h4 id="state_msg_i"><?php l("翻译已掉线"); ?></h4>
					<video id="video_i" class="video-big video-interpreter" autoplay></video>
					<div class="video-bar">
					</div>
					<h3><?php l("翻译"); ?> : <?php $mInterview->detail_l("interpreter_name"); ?></h3>
					<div id="media_error_i" class="media-error"></div>
				</div>
				<div class="col-md-6 text-center">
					<h4 id="state_msg_p"><?php l("患者已掉线"); ?></h4>
					<video id="video_p" class="video-big video-patient" autoplay></video>
					<div class="video-bar">
					</div>
					<h3><?php l("患者"); ?> : <?php $mInterview->detail("patient_name"); ?></h3>
					<div id="media_error_p" class="media-error"></div>
				</div>
			</div>
			<div class="row row-small">
				<div class="col-md-12 text-center">
					<h4 id="state_msg_d"><?php l("专家已掉线"); ?></h4>
					<video id="video_d" class="video-small video-doctor" autoplay></video>
					<div id="video_bar_d" class="video-bar">
						<?php show_video_bar(UTYPE_DOCTOR); ?>
					</div>
					<h3><?php l("专家"); ?> : <?php $mInterview->detail_l("doctor_name"); ?></h3>
					<div id="media_error_d" class="media-error"></div>
				</div>
			</div>
			<?php } else {  // interpreter, admin, super?>
			<div class="row row-big">
				<div class="col-md-6 text-center">
					<h4 id="state_msg_p"><?php l("患者已掉线"); ?></h4>
					<video id="video_p" class="video-big video-patient" autoplay></video>
					<div class="video-bar">
					</div>
					<h3><?php l("患者"); ?> : <?php $mInterview->detail("patient_name"); ?></h3>
					<div id="media_error_p" class="media-error"></div>
				</div>
				<div class="col-md-6 text-center">
					<h4 id="state_msg_d"><?php l("专家已掉线"); ?></h4>
					<video id="video_d" class="video-big video-doctor" autoplay></video>
					<div class="video-bar">
					</div>
					<h3><?php l("专家"); ?> : <?php $mInterview->detail_l("doctor_name"); ?></h3>
					<div id="media_error_d" class="media-error"></div>
				</div>
			</div>
			<div class="row row-small">
				<div class="col-md-12 text-center">
					<h4 id="state_msg_i"><?php l("翻译已掉线"); ?></h4>
					<video id="video_i" class="video-small video-interpreter" autoplay></video>
					<div id="video_bar_i" class="video-bar">
						<?php show_video_bar(UTYPE_INTERPRETER); ?>
					</div>
					<h3><?php l("翻译"); ?> : <?php $mInterview->detail_l("interpreter_name"); ?></h3>
					<div id="media_error_i" class="media-error"></div>
				</div>
			</div>
			<?php } ?>
		<?php } else { ?>
			<div class="row row-big">
				<div class="col-md-6 text-center">
					<h4 id="state_msg_p"><?php l("患者已掉线"); ?></h4>
					<video id="video_p" class="video-big video-patient" autoplay></video>
					<div id="video_bar_p" class="video-bar">
						<?php show_video_bar(UTYPE_PATIENT); ?>
					</div>
					<h3><?php l("患者"); ?> : <?php $mInterview->detail("patient_name"); ?></h3>
					<div id="media_error_p" class="media-error"></div>
				</div>
				<div class="col-md-6 text-center">
					<h4 id="state_msg_d"><?php l("专家已掉线"); ?></h4>
					<video id="video_d" class="video-big video-doctor" autoplay></video>
					<div id="video_bar_d" class="video-bar">
						<?php show_video_bar(UTYPE_DOCTOR); ?>
					</div>
					<h3><?php l("专家"); ?> : <?php $mInterview->detail_l("doctor_name"); ?></h3>
					<div id="media_error_d" class="media-error"></div>
				</div>
			</div>
		<?php } ?>
		</div>
	</article>
	<aside>
		<h2><?php l("人员列表"); ?><span id="stats"></span></h2>
		<table>
			<thead>
				<tr>
					<th><?php l("姓名"); ?></th>
					<th><?php l("角色"); ?></th>
					<th><?php l("状态"); ?></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><?php $mInterview->detail("patient_name"); ?></td>
					<td><img src="ico/patient.png"> <?php l("患者"); ?></td>
					<td id="state_p" class="state"></td>
				</tr>
				<tr>
					<td><?php $mInterview->detail_l("doctor_name"); ?></td>
					<td><img src="ico/doctor.png"> <?php l("专家"); ?></td>
					<td id="state_d" class="state"></td>
				</tr>
				<?php if ($mInterview->need_interpreter) {?>
				<tr>
					<td><?php $mInterview->detail_l("interpreter_name"); ?></td>
					<td><img src="ico/interpreter.png"> <?php l("翻译"); ?></td>
					<td id="state_i" class="state"></td>
				</tr>
				<?php } ?>
			</tbody>
		</table>

		<h2><?php l("讨论区"); ?></h2>
		<div class="chat-panel">
			<div id="chat_view" class="chat-container">
	            <div id="messages"></div>
	            <div id="scroll_bottom"></div>
	    	</div>
	    	<form id="input_bar" action="api/interview/send_message" class="input-bar" method="post">
	    		<?php $mInterview->hidden('interview_id'); ?>
	    		<input type="hidden" name="no" id="message_no">
	            <label class="input-wrapper">
	                <textarea value="" placeholder="<?php l("请输入文本。(按ENTER送信内容)"); ?>" required minlength="1" maxlength="21000" id="chat_input" name="content" rows='1'></textarea>
	            </label>
	            <div class="button-wrapper">
	                <button id="btn_send_message" class="btn btn-default" type="submit">
	                    <i class="ln-icon-paper-plane"></i>
	                </button>
	            </div>
	        </form>
		</div>
	</aside>
</section>