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
		"countryModel",				// model name
		"m_country",
		"country_id",
		array(
			"country_name",
			"country_code",
			"dial_code",
			"dial_code_order",
			"sub_dial_codes"),
		array("auto_inc" => true, 
			"del_flag" => false));

	class countryModel extends model // 国家信息模型
	{
		public static function get_country_name($country_id)
		{
			$country = countryModel::get_model($country_id);
			if ($country) 
				return $country->country_name;
			return "";
		}

		public static function from_dial_code($dial_code, $sub_dial_code=null)
		{
			if (strlen($sub_dial_code)) 
				$sub_dial_code = "," . $sub_dial_code . ",";
			else 
				$sub_dial_code = null;

			$countries = array();

			$country = new static;
			$err = $country->select("dial_code=" . _sql($dial_code), 
				array("order" => "dial_code_order ASC"));
			while ($err == ERR_OK)
			{
				if ($sub_dial_code) 
				{
					if (strstr($country->sub_dial_codes, $sub_dial_code))
						return array($country);
				}
				$countries[] = clone $country;
				$err = $country->fetch();
			}

			if (count($countries))
				return $countries;

			return null;
		}

		public static function tel_num_to_country_id($tel)
		{
			if (substr($tel, 0, 1) == "+") 
			{
				// 4 digits dial code
				$dial4 = substr($tel, 1, 4);
				$countries = static::from_dial_code($dial4);
				if ($countries)
					return $countries[0]->country_id;

				// 3 digits dial code
				$dial3 = substr($tel, 1, 3);
				$countries = static::from_dial_code($dial3);
				if ($countries)
					return $countries[0]->country_id;

				// 2 digits dial code
				$dial2 = substr($tel, 1, 2);
				$countries = static::from_dial_code($dial2);
				if ($countries)
					return $countries[0]->country_id;

				// 1 digits dial code
				$dial1 = substr($tel, 1, 1);
				$sub_dial = substr($tel, 2, 3);
				$countries = static::from_dial_code($dial1, $sub_dial);
				if ($countries)
					return $countries[0]->country_id;

				return null;
			}
			else {
				// 没有国际区号
				return null;
			}
		}

	};
	
	/*************************** The END ********************************
	*   Don't add new code below this line, thanks.						*
	*	2017 ©      ALL Rights Reserved. 								*
	**************************** The END *******************************/