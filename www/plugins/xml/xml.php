<?php
	/************************* Copyright Info ***************************
	*	Project Name:		3QC World Tele Clinic System				*
	*	Framework:			MAL MVC Web Framewrok v1.0					*
	*	Author:				Quan										*
	*	Date:				2017/10/02									*
	*																	*
	*	2017 ©      ALL Rights Reserved. 								*
	************************** Copyright Info **************************/

	class xml {
		private $xml;

		public function __construct($datafile){
			$path = SITE_ROOT . "/" . $datafile;
			$fp = @fopen($path, "r");
			if ($fp != null) {
				$xmlstr = @fread($fp, filesize($path));
				@fclose($fp);

				$this->xml = new SimpleXMLElement($xmlstr);
			}
		}

		public function __get($prop) {
			return $this->xml->$prop;
		}

	};

	/*************************** The END ********************************
	*   Don't add new code below this line, thanks.						*
	*	2017 ©      ALL Rights Reserved. 								*
	**************************** The END *******************************/