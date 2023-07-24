<section>
	<div class="page-bar">
		<ul class="page-breadcrumb">
			<li>
				<span><?php l("当前位置"); ?> :</span>
			</li>
			<li>
				<a href="javascript:;"><?php l("系统检测");?></a>
				<i class="fa fa-angle-right"></i>
			</li>
			<li>
				<?php l("检测设备");?>
			</li>
		</ul>
	</div>
	<form id="form" action="" class="form-horizontal" method="post" novalidate="novalidate">
		<div class="form-body">
			<video id="player_video" class="video-check" muted></video>
			<p>
				<?php l("请点击“检验摄像头”按钮，查看摄像头是否安装，画质是否清晰。");?>
				<br/>
				<button type="button" id="check_camera" class="btn btn-default">
					<?php l("检验摄像头");?>
				</button>
			</p>
			<p>
				<?php l("请点击“检验麦克风”按钮，对着麦克风说话，是否有绿色进度条。");?>
				<br/>
				<button type="button" id="check_mic" class="btn btn-default">
					<?php l("检验麦克风");?>
				</button>

				<canvas id="mic_equalizer" width="100" height="30" style="vertical-align: middle;"></canvas>
			</p>
			<audio id="player_speaker" src="sound/sample.mp3"></audio>
			<p>
				<?php l("请点击“播放测试声音”按钮，试试能否听到声音。");?>
				<br/>
				<button type="button" id="check_speaker" class="btn btn-default">
					<?php l("播放测试声音");?>
				</button>

				<canvas id="speaker_equalizer" width="300" height="30" style="vertical-align: middle;"></canvas>
			</p>
		</div>
	</form>
</section>