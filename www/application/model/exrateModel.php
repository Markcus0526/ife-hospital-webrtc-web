<?php
	/************************* Copyright Info ***************************
	*	Project Name:		MARKCUS World Tele Clinic System				*
	*	Framework:			MAL MVC Web Framewrok v1.0					*
	*	Author:				Markcus										*
	*	Date:				2017/10/02									*
	*																	*
	*	2017 ©      ALL Rights Reserved. 								*
	************************** Copyright Info **************************/
	
	_model(
		"exrateModel",			// model name
		"m_exrate",
		"exrate_id",
		array(
			"exrate_time", 	// 更新时刻
			"rmb_to_usd", 	// 从人民币至美元
			"usd_to_rmb" 	// 从美元至人民币
			),
		array("auto_inc" => true, 
			"operator_info" => false,
			"del_flag" => false));

	class exrateModel extends model // 汇率信息模型
	{
		public static function get_rate($cunit)
		{
			$exrate = static::get_last();
			if ($exrate) {
				if ($cunit == "usd") {
					$ex_cunit = "rmb";
					$ex_rate = $exrate->usd_to_rmb;
				}
				else if ($cunit == "rmb") {
					$ex_cunit = "usd";
					$ex_rate = $exrate->rmb_to_usd;
				}


				return array($ex_cunit, $ex_rate);
			}

			return null;
		}

		public static function get_last($force=false, $exrate_time=null) {
			if ($force) {
				// call api
				$api_url = EXRATE_API_URL . EXRATE_API_KEY;

				$fp = fopen($api_url, "r");
				if ($fp != null) {
					$json = '';
					while (!feof($fp)) {
					  $json .= fread($fp, 8192);
					}
					@fclose($fp);

					if ($json != "")
					{
						$json = preg_replace("/(\/\*[^\/\*]+\*\/)/e", "", $json);
						$json = json_decode($json, true);
						
						if ($json["error_code"] == 0)
						{
							$exrate = new exrateModel;
							$exrate_time = $json["result"]["update"];
							$err = $exrate->select("exrate_time=" . _sql($exrate_time));
							foreach ($json["result"]["list"] as $curr) {
								if ($curr[0] == "美元") {
									$exrate->usd_to_rmb = $curr[2] / $curr[1];
									$exrate->rmb_to_usd = _round(1 / $exrate->usd_to_rmb, 4);
									break;
								}
							}
							$exrate->exrate_time = $exrate_time;
							$exrate->update_time = "##NOW()";
							$exrate->save();

							return $exrate;
						}
					}
				}
			}
			else {
				$exrate = new static;
				$where = "";
				if ($exrate_time != null) {
					$where = "exrate_time<" . _sql($exrate_time);
				}

				$err = $exrate->select($where, array("order" => "exrate_time DESC"));
				if ($err == ERR_NODATA ||
					($exrate_time == null && _date() > _date($exrate->update_time))) {
					return static::get_last(true);
				}
			}
			return $exrate;
		}
	};
	
	/*************************** The END ********************************
	*   Don't add new code below this line, thanks.						*
	*	2017 ©      ALL Rights Reserved. 								*
	**************************** The END *******************************/