<?php
	/************************* Copyright Info ***************************
	*	Project Name:		3QC World Tele Clinic System				*
	*	Framework:			MAL MVC Web Framewrok v1.0					*
	*	Author:				Quan										*
	*	Date:				2017/10/02									*
	*																	*
	*	2017 ©      ALL Rights Reserved. 								*
	************************** Copyright Info **************************/

	class exrateController extends controller {
		public function __construct(){
			parent::__construct();	
		}

		public function check_priv($action, $utype)
		{
		}

		public function sample() {
			$json = '{
    "reason": "查询成功",
    "result": {
        "update": "2016-07-22 10:32:31",
        "list": [
            [
                "美元", /*货币名称*/
                "100", /*交易单位*/
                "665.63", /*现汇买入价*/
                "660.3", /*现钞买入价*/
                "668.3", /*现钞卖出价*/
                "666.69" /*中行折算价*/
            ],
            [
                "港币",
                "100",
                "85.83",
                "85.14",
                "86.15",
                "85.96"
            ],
            [
                "日元",
                "100",
                "6.2771",
                "6.0834",
                "6.3211",
                "6.3014"
            ],
            [
                "欧元",
                "100",
                "732.74",
                "710.13",
                "737.88",
                "735.79"
            ],
            [
                "英镑",
                "100",
                "879.28",
                "852.15",
                "885.46",
                "879.01"
            ]
        ]
    },
    "error_code": 0
}
';
			print $json;
			exit;
		}

		public function rate_ajax() {
			$param_names = array("force", "exrate_time");
			$this->set_api_params($param_names);
			$params = $this->api_params;

            $exrate_time = _date($params->exrate_time);

			$exrate = exrateModel::get_last($params->force, $exrate_time);

			$this->finish(array(
				"exrate_time" => _date($exrate_time),
				"rmb_to_usd" => $exrate->rmb_to_usd,
				"usd_to_rmb" => $exrate->usd_to_rmb), ERR_OK);
		}
	}
	
	/*************************** The END ********************************
	*   Don't add new code below this line, thanks.						*
	*	2017 ©      ALL Rights Reserved. 								*
	**************************** The END *******************************/