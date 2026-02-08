<?php
	/************************* Copyright Info ***************************
	*   Project Name:       MARKCUS World Tele Clinic System                *
	*   Framework:          MAL MVC Web Framewrok v1.0                  *
	*   Author:             Markcus                                        *
	*   Date:               2017/10/02                                  *
	*                                                                   *
	*   2017 ©      ALL Rights Reserved.                                *
	************************** Copyright Info **************************/

	define('STREAM_VIDEO',				1);
	define('STREAM_AUDIO',				2);
	define('STREAM_VIDEO_AUDIO',		STREAM_VIDEO | STREAM_AUDIO);
	define('RCD_FRAGMENT_INTERVAL',		30); // 30 seconds

	function sort_frag($a, $b)
	{
		if (preg_match_all("/^[pdi_]*([0-9]+)\.(".I_EXT."|".O_EXT.")$/i", $a, $matches)) {
			$tm_a = intval($matches[1][0]);
		}
		if (preg_match_all("/^[pdi_]*([0-9]+)\.(".I_EXT."|".O_EXT.")$/i", $b, $matches)) {
			$tm_b = intval($matches[1][0]);
		}

		return ($tm_a < $tm_b) ? -1 : 1;
	}
	
	class batchModel extends model 
	{
		public $silent_mode;

		public $daily_clear_old_files;

		public $last_date;

		public $ffmpeg_cmd, $ffprobe_cmd;
		public $cwd, $fragment_interval;

		public $tss;

		public function __construct($silent_mode=true){
			$this->silent_mode = $silent_mode;
			$this->daily_clear_old_files = false;
			$this->last_date = null;
			$this->cwd = getcwd();
			$this->ffmpeg_cmd = FFMPEG . " -threads 50 -v error -y ";
			$this->ffprobe_cmd = FFPROBE . " -v quiet -print_format json ";
			$this->fragment_interval = 30; // every 30 seconds
		}

		public function run($run_type=null) {
			$this->clear_old_files();

			$this->convert_record_video();

			$this->last_date = _date();
		}

		private function get_interview_date($interview_id)
		{
			$db = db::get_db();
			$interview_date = $db->scalar("SELECT reserved_starttime FROM t_interview 
				WHERE interview_id=" . _sql($interview_id));

			if ($interview_date) {
				$interview_date = _date($interview_date, "Ymd");
			}

			return $interview_date;
		}

		public function clear_old_files()
		{
			if ($this->last_date != _date() || !$this->daily_clear_old_files)
			{	
				$settings = settingsModel::get_config();			
				$before_date = _date_add(null, -$settings->save_record_limit, "Ymd");
				$this->print_log("clear old files before " . $before_date . "(" . $settings->save_record_limit . " days).");

				if ($settings->save_record_limit <= 0)
					return;

				// clear temorary folder
				$dir = TMP_PATH;

				if ($d = opendir($dir)) {
					while (false !== ($file = readdir($d))) {
						if ($file == "." || $file == "..")
							continue;

						if (preg_match_all("/^([0-9]+)$/i", $file, $matches)) {
							$interview_id = $matches[1][0];

							$create_time = $this->get_interview_date($interview_id);

							if ($create_time == null || $before_date > $create_time) {
								_rmdir($dir . $file);
								$this->print_log("remove folder: " . $dir . $file . " $create_time");
							}
						}
					}
				}

				// clear video folder
				$dir = DATA_PATH . VIDEO_URL;

				if ($dy = opendir($dir)) {
					while (false !== ($year = readdir($dy))) {
						if (is_dir($dir . $year) && 
							preg_match_all("/^([0-9]{4})$/i", $year, $matches)) {
							// year
							$f_cnt = 0;
							if ($dm = opendir($dir . $year)) {
								while (false !== ($month = readdir($dm))) {
									if (is_dir($dir . $year . "/" . $month) && 
										preg_match_all("/^([0-9]{2})$/i", $month, $matches)) {
										$f_cnt ++;
										// month
										$path = $dir . $year . "/" . $month;

										if ($d = opendir($path)) {
											$v_cnt = 0;
											while (false !== ($file = readdir($d))) {
												if ($file == "." || $file == "..")
													continue;

												if (preg_match_all("/^([0-9]+)\.(mp4|webm)$/i", $file, $matches)) {
													$interview_id = $matches[1][0];
													$v_cnt ++;

													$create_time = $this->get_interview_date($interview_id);

													if ($before_date > $create_time) {
														$v_cnt --;
														@unlink($path . "/" . $file);
														$this->print_log("remove old video file: " . $year . "/" . $month . "/" . $file . " $create_time");
													}
												}
											}

											closedir($d);

											if ($v_cnt == 0) {
												$f_cnt --;
												_rmdir($path);
												$this->print_log("remove empty video folder: " . $year . "/" . $month);
											}
										}
									}
								}
								closedir($dm);
							}

							if ($f_cnt == 0) {
								_rmdir($dir . $year);
								$this->print_log("remove empty video folder: " . $year);
							}
						}
					}
				}

				$this->daily_clear_old_files = true;
			}
		}

		public function convert_record_video()
		{
			$path = TMP_PATH;

			if ($d = opendir($path)) {
				while (false !== ($file = readdir($d))) {
					if ($file == "." || $file == "..")
						continue;

					// find all finished interviews.
					//   if 'api/v/e' API method is called, generate [interview_id].fin file
					if (preg_match_all("/^([0-9]+).fin$/i", $file, $matches)) {
						$interview_id = $matches[1][0];

						$ts = _fread($path . $file);
						$expire = time() - $ts;
						if ($expire > 60)
						{
							// start after 1 minutes, why? waiting for all uploaded
							$this->conv_interview($interview_id);
							@unlink($path . $file);
						}
					}
					//   if 'api/v/c' API method is called, generate [interview_id].clear file
					else if (preg_match_all("/^([0-9]+).clear$/i", $file, $matches)) {
						// remove unfinished video
						$interview_id = $matches[1][0];
						$interview_dir = TMP_PATH . $interview_id . "/";
						@_rmdir($interview_dir);
						$this->print_log("remove unfinished video folder: " . $interview_dir);
						if (!is_dir($interview_dir)) {
							@unlink($path . $file);
						}
					}
				}
			}
		}

		// convert a video of a interview
		public function conv_interview($interview_id)
		{
			$user_types = ["p", "d", "i"]; // patient, doctor, interpreter
			// ex: tmp/20171101040027
			$path = TMP_PATH . $interview_id . "/";
			$result_path = DATA_PATH . _video_url($interview_id);
			$result_video = pathinfo($result_path, PATHINFO_BASENAME);

			$first_ts = null;
			$this->tss = array();
			if ($d = opendir($path)) {
				// find all users
				while (false !== ($file = readdir($d))) {
					if ($file == "." || $file == "..")
						continue;

					if ($file == "p" || $file == "d" || $file == "i")
					{
						$ts = $this->conv_user_one_session($interview_id, $file);
						if ($ts == null)
						{
							// invalid holder
							_rmdir($path . $file);
						}
						else {
							$this->tss[$file] = $ts;
							$this->print_log("user video [" . $file . "] first timestamp : " . $ts);
							if ($first_ts == null)
								$first_ts = $ts;
							else if ($first_ts > $ts) 
								$first_ts = $ts;
						}
					}
				}
				closedir($d);
			}
			
			$this->print_log("interview video [" . $interview_id . "] first timestamp : " . $first_ts);

			if ($d = opendir($path)) {
				// find all users
				while (false !== ($file = readdir($d))) {
					if ($file == "." || $file == "..")
						continue;

					if ($file == "p" || $file == "d" || $file == "i")
					{
						$this->conv_user($interview_id, $file, $first_ts);
					}
				}
				closedir($d);
			}

			if ($d = opendir($path)) {
				$timestamps = array();
				$files = array();
				while (false !== ($file = readdir($d))) {
					if ($file == "." || $file == "..")
						continue;

					if (preg_match_all("/^[pdi]_([0-9]+)\.".O_EXT."$/i", $file, $matches)) {
	    				$check = $this->check_file($path, $file);
	    				if ($check == 3) {
							$timestamps[] = $matches[1][0];
							$files[] = $file;
	    				}
					}
				}

				closedir($d);

				$count = count($files);
				if ($count == 2) {
					$delays[] = array(0, 0);

					$ffmpeg = $this->ffmpeg_cmd;
					for ($i = 0; $i < $count; $i ++)
					{
						$r = $this->get_delays($path, $files[$i]);
						$delays[$i] = ($r[1]-$r[0])*1000; // millisecond
						if ($delays[$i] < 0) $delays[$i] = 0;
						$ffmpeg .= " -i " . $files[$i];
					}

					$ffmpeg .= ' -filter_complex "nullsrc=size=1280x480 [background];[0:v] setpts=PTS-STARTPTS, scale=640x480 [left];[1:v] setpts=PTS-STARTPTS, scale=640x480 [right];[background][left] overlay=shortest=1 [background+left];[background+left][right] overlay=repeatlast=1:x=640;[0:a]asetpts=PTS-STARTPTS,aresample=async=1[a1];[1:a]asetpts=PTS-STARTPTS,aresample=async=1[a2];';
					$a1 = "a1"; $a2 = "a2";
					if ($delays[0] > 0) {
						$ffmpeg .= '[a1]adelay='.$delays[0].'[da1];';
						$a1 = "da1";
					}
					if ($delays[1] > 0) {
						$ffmpeg .= '[a2]adelay='.$delays[1].'[da2];';
						$a2 = "da2";
					}
					$ffmpeg .= '['.$a1.']['.$a2.'] amix=inputs=2" ';
					$ffmpeg .= $result_video;

					chdir($path);
					$this->print_log($ffmpeg);
					@system($ffmpeg, $ret);
					chdir($this->cwd);

					$v_path = $path . $result_video;
					
				}
				else if ($count == 3) {
					$delays[] = array(0, 0, 0);

					$ffmpeg = $this->ffmpeg_cmd;
					for ($i = 0; $i < $count; $i ++)
					{
						$r = $this->get_delays($path, $files[$i]);
						$delays[$i] = ($r[1]-$r[0])*1000; // millisecond
						if ($delays[$i] < 0) $delays[$i] = 0;
						$ffmpeg .= " -i " . $files[$i];
					}
					$ffmpeg .= ' -filter_complex "color=c=black:s=1280x960 [background];[0:v] setpts=PTS-STARTPTS, scale=640x480 [left];[1:v] setpts=PTS-STARTPTS, scale=640x480 [right];[2:v] setpts=PTS-STARTPTS, scale=640x480 [bottom];[background][left] overlay=shortest=1 [background+left];[background+left][right] overlay=repeatlast=1:x=640 [background+lr]; [background+lr][bottom] overlay=repeatlast=1:x=320:y=480;[0:a]asetpts=PTS-STARTPTS,aresample=async=1[a1];[1:a]asetpts=PTS-STARTPTS,aresample=async=1[a2];[2:a]asetpts=PTS-STARTPTS,aresample=async=1[a3];';
					$a1 = "a1"; $a2 = "a2"; $a3 = "a3";
					if ($delays[0] > 0) {
						$ffmpeg .= '[a1]adelay='.$delays[0].'[da1];';
						$a1 = "da1";
					}
					if ($delays[1] > 0) {
						$ffmpeg .= '[a2]adelay='.$delays[1].'[da2];';
						$a2 = "da2";
					}
					if ($delays[2] > 0) {
						$ffmpeg .= '[a3]adelay='.$delays[2].'[da3];';
						$a3 = "da3";
					}
					$ffmpeg .= '['.$a1.']['.$a2.']['.$a3.'] amix=inputs=3" ';
					$ffmpeg .= $result_video;

					chdir($path);
					$this->print_log($ffmpeg);
					@system($ffmpeg, $ret);
					chdir($this->cwd);

					$v_path = $path . $result_video;
				}
				else if ($count == 1) {
					// count is one
					$v_path = $path . $files[0];

				}
				else {
					$v_path = null;
				}

				if (filesize($v_path) > 0) {
					$this->print_log("generated interview video: $result_path");
					if (!RECORD_REMAIN) @unlink($result_path);
					@_mkdir(_dirname($result_path));
					@rename($v_path, $result_path);
					if (!RECORD_REMAIN) _rmdir($path);

					return true;
				}
				else {
					$this->print_log("interview video converting failed");
				}
			}

			return false;
		}

		// convert a video of one user
		// return first timestamp
		public function conv_user_one_session($interview_id, $user_type)
		{
			// ex: tmp/20171101040027/p
			$path = TMP_PATH . $interview_id . "/" . $user_type . "/";

			$first_ts = null;

			if ($d = opendir($path)) {
				// find all sessions
				while (false !== ($file = readdir($d))) {
					if ($file == "." || $file == "..")
						continue;

					$ts = null;
					if (preg_match_all("/^[0-9]+$/i", $file, $matches)) {
						$timestamp = $file;

						if ($this->conv_one($interview_id, $user_type, $timestamp))
						{
							$ts = intval($timestamp, 10);
						}
					}
					else if (preg_match_all("/^([0-9]+)\.".O_EXT."$/i", $file, $matches)) {
						$ts = intval($matches[1][0], 10);
					}

					if ($ts) {
						if ($first_ts == null)
							$first_ts = $ts;
						else if ($first_ts > $ts)
							$first_ts = $ts;
					}
				}
				closedir($d);
			}

			return $first_ts;
		}

		// convert a video of one user
		public function conv_user($interview_id, $user_type, $first_ts)
		{
			// ex: tmp/20171101040027/p
			$path = TMP_PATH . $interview_id . "/" . $user_type . "/";

			$delay = $this->tss[$user_type] - $first_ts;
			if ($delay > 0)
			{
				$this->make_empty_video($path, $first_ts . "." . O_EXT, $delay);
			}

			if ($d = opendir($path)) {
				$timestamps = array();
				$files = array();
				// find converted all session videos
				while (false !== ($file = readdir($d))) {
					if ($file == "." || $file == "..")
						continue;

					if (preg_match_all("/^([0-9]+)\.".O_EXT."$/i", $file, $matches)) {
						$files[] = $file;
					}
				}
				usort($files, "sort_frag");
				foreach ($files as $file) {
					if (preg_match_all("/^([0-9]+)\.".O_EXT."$/i", $file, $matches)) {
						$timestamps[] = $matches[1][0];
					}
				}
				closedir($d);

				$count = count($timestamps);
				if ($count == 1)
				{
					// only one session video
			    	@rename($path . $timestamps[0] . ".".O_EXT, TMP_PATH . $interview_id . "/" . $user_type . "_" . $timestamps[0] . ".".O_EXT);
				}
				else if ($count > 1)
				{
					for ($i = 0; $i < $count - 1; $i ++)
					{
						$ts = $timestamps[$i];
						$next_ts = $timestamps[$i + 1];

						// difference time(second) between one and next.
						$len = $next_ts - $ts;

						if ($len > 0 && $len < MAX_GAP_VIDEOS)
						{
							// overlay null image
							$ffmpeg = $this->ffmpeg_cmd . ' -i ' . $timestamps[$i] . '.'.O_EXT.' ';
							$ffmpeg .= ' -filter_complex "nullsrc=size=640x480 [background];[0:v] setpts=PTS-STARTPTS, scale=640x480 [left];[background][left] overlay;[0:a] apad"';
							$ffmpeg .= ' -t ' . $len . ' ' . $timestamps[$i] .'_.'.O_EXT.' ';

					    	chdir($path);
					    	$this->print_log($ffmpeg);
					    	@system($ffmpeg, $ret);
							if (!RECORD_REMAIN) @unlink($timestamps[$i] . '.'.O_EXT);
					    	@rename($timestamps[$i] . '_.'.O_EXT, $timestamps[$i] . '.'.O_EXT);
					    	chdir($this->cwd);
						}
					}

			    	$this->concat_all($path, $files, $user_type . "_" . $timestamps[0] . '.'.O_EXT);
				}

				if (!RECORD_REMAIN) _rmdir($path);
			}
		}

		// convert a video of one user in one session
		//  concat all video segments
		// case of convert success: return true
		// else : return false
		public function conv_one($interview_id, $user_type, $timestamp)
		{
			// ex: tmp/20171101040027/p/1512005448
			$path = TMP_PATH . $interview_id . "/" . $user_type . "/" . $timestamp . "/";

			if ($d = opendir($path)) {
				$files = array();
				// find alll segment video(30 seconds)
				while (false !== ($file = readdir($d))) {
					if ($file == "." || $file == "..")
						continue;

					if (preg_match_all("/^[0-9]+\.".I_EXT."$/i", $file, $matches)) {
						$files[] = $file;
					}
				}
				usort($files, "sort_frag");
				closedir($d);

				$count = count($files);
			    if ($count > 0)
			    {
			    	$last_file = $files[$count - 1];
			    	$last_no = _filename($last_file);
			    	$l = strlen($last_no);
			    	$last_no = intval($last_no);
			    	$files = array();

			    	for ($i = 0; $i <= $last_no; $i ++)
			    	{
			    		array_push($files, str_pad($i, $l, "0", STR_PAD_LEFT).".".I_EXT);
			    	}

			    	if ($this->concat_all($path, $files, $timestamp . ".".O_EXT, true))
			    	{
			    		_rmdir($path);
			    		return true;
			    	}
			    }
			    else 
			    {
			    	_rmdir($path);
			    }
			}

			return false;
		}

		// concat all videos to one video
		// case of convert success: return true
		// else : return false
		public function concat_all($path, $files, $result_file, $one_session=false)
		{
			// ex: tmp/20171101040027/p/1512005448/00001.webm
	    	$this->print_log("concat all path:" . $path);

	    	$ffmpeg = $this->ffmpeg_cmd;
	    	$streams = array();
	    	$i = 0; $l = count($files);
	    	foreach ($files as $file) {
	    		$check = $this->check_file($path, $file);
	    		$filename = _filename($file);
	    		$ext = _ext($file);
	    		if ($check == 0 || $check == STREAM_AUDIO) {
					// if audio only or invalid video, remove file
					@unlink($path . $file);
					$this->print_log("invalid video file. path:" . $path . $file);

					if ($ext == I_EXT && $i < $l) { // webm
						// create empty video
						$file = $filename . "." . O_EXT;
						$this->print_log("make empty video. path:" . $path . $file);
						$this->make_empty_video($path, $file, RCD_FRAGMENT_INTERVAL, true);
					}
					else {
						continue;
					}
	    		}
				else if ($check == STREAM_VIDEO)
				{
					// if video only, add silent audio
					$ffmpeg2 = $this->ffmpeg_cmd;
					$o_file = $filename . "." . O_EXT;
					$acodec = "";
					// if ($ext == I_EXT)
					// 	$acodec = "-acodec libopus";
	    			$ffmpeg2 .= " -f lavfi -i aevalsrc=0 -i " . $file;
	    			if ($ext == I_EXT && $i < $l)  // webm
	    				$ffmpeg2 .= " -t " . RCD_FRAGMENT_INTERVAL;
	    			else
	    				$ffmpeg2 .= " -shortest ";
	    			//$ffmpeg2 .= " -vcodec copy " . $acodec . " -strict -2 _" . $o_file;
	    			$ffmpeg2 .= " -strict -2 _" . $o_file;
			    	chdir($path);
			    	$this->print_log($ffmpeg2);
			    	@system($ffmpeg2, $ret);
					@unlink($path . $file);
			    	if ($ext == I_EXT) {
			    		rename("_$o_file", $o_file);
			    		$file = $o_file;
			    	}
			    	else {
			    		rename("_$o_file", $file);
			    	}
			    	chdir($this->cwd);
				}
				else {
					if ($ext == I_EXT && $i < $l) { // webm
						$o_file = $filename . "." . O_EXT;
						$ffmpeg2 = $this->ffmpeg_cmd . " -i " . $file . " -t " . RCD_FRAGMENT_INTERVAL . " -max_muxing_queue_size 400 " . $o_file;
				    	chdir($path);
				    	$this->print_log($ffmpeg2);
				    	@system($ffmpeg2, $ret);
						@unlink($path . $file);;
				    	chdir($this->cwd);
				    	$file = $o_file;
					}
				}

				// video & audio
    			$ffmpeg .= " -i " . $file;
				$streams[] = "[$i:v] [$i:a:0]";
				$i ++;
	    	}

	    	$result_file = "../" . $result_file;
	    	if ($i == 0)
	    		return false;
	    	else if ($i == 1) {
	    		chdir($path);
				@rename($path . "/" . $files[0], $result_file);
	    		chdir($this->cwd);
	    	}
	    	else {
		    	$ffmpeg .= ' -filter_complex "';
		    	
		    	$ffmpeg .= implode(" ", $streams);
		    	
		    	$ffmpeg .= " concat=n=$i:v=1:a=1 [v] [a]";
		    	$ffmpeg .= '" -s 640x480 -aspect 1.3333 -map [v] -map [a] -strict experimental ';
		    	$ffmpeg .= $result_file;

		    	chdir($path);
		    	$this->print_log($ffmpeg);
		    	@system($ffmpeg, $ret);
		    	chdir($this->cwd);
	    	}

	    	if ($this->check_file($path, $result_file) != STREAM_VIDEO_AUDIO)
	    	{
				$this->print_log("because of invalid result file, remove " . $path . $result_file);
	    		@unlink($path . $result_file);
	    		return false;
	    	}
	    	return true;
		}

		public function check_file($path, $file)
		{
			$r = 0;
			$ffprobe = $this->ffprobe_cmd . " -show_streams " . $file;

	    	chdir($path);
	    	$this->print_log($ffprobe);
			$p = @popen($ffprobe, 'r');
			if ($p) {
				$json = '';
				while (!feof($p)) {
					$json .= fread($p, 8192);
				}
				$ret = json_decode($json, true);

				if (isset($ret["streams"]) && is_array($ret["streams"]))
				{
					$streams = $ret["streams"];
					foreach ($streams as $stream) {
						if ($stream["codec_type"] == "audio")
						{
							$r = $r | STREAM_AUDIO;
						}
						else if ($stream["codec_type"] == "video")
						{
							$r = $r | STREAM_VIDEO;
						}
					}
				}
			}
	    	chdir($this->cwd);
			
			return $r;
		}

		public function make_empty_video($path, $file, $duration, $cache=false)
		{
			if ($cache) {
				$cache_path = TMP_PATH;
				$cache_file = "empty_" . $duration . "." . _ext($file);
				if (!file_exists($cache_path . $cache_file)) {
					$this->make_empty_video($cache_path, $cache_file, $duration);
				}

				@copy($cache_path.$cache_file, $path.$file);
		    	chdir($this->cwd);
			}
			else {
		    	chdir($path);
		    	$ffmpeg = $this->ffmpeg_cmd . " -f lavfi -i aevalsrc=0 -loop 1 -i \"" . SITE_ROOT . "resource/img/empty_video.png\" -t "
		    		. $duration . " -vf scale=640:480 -strict -2 " . $file;
		    	$this->print_log($ffmpeg);
				@system($ffmpeg, $ret);
		    	chdir($this->cwd);
			}
		}

		public function get_delays($path, $file)
		{
			$r = array();
			$ffprobe = $this->ffprobe_cmd . " -show_entries stream=start_time " . $file;

			chdir($path);
			$this->print_log($ffprobe);
			$p = @popen($ffprobe, 'r');
			if ($p) {
				$json = '';
				while (!feof($p)) {
					$json .= fread($p, 8192);
				}
				$ret = json_decode($json, true);

				if (isset($ret["streams"]) && is_array($ret["streams"]))
				{
					$streams = $ret["streams"];
					$i = 0;
					foreach ($streams as $stream) {
						$r[$i] = $stream["start_time"];
						$i ++;
					}

				}
			}
			chdir($this->cwd);

			return $r;
		}

		private function print_log($msg)
		{
			if (!$this->silent_mode)
				print sprintf("%s %s %s\n", 
					_date(), _time(null, "H:i:s"), $msg);
		}

	};
	
	/*************************** The END ********************************
	*   Don't add new code below this line, thanks.                     *
	*	2017 ©      ALL Rights Reserved. 								*
	**************************** The END *******************************/