<?php
	/************************* Copyright Info ***************************
	*	Project Name:		3QC World Tele Clinic System				*
	*	Framework:			MAL MVC Web Framewrok v1.0					*
	*	Author:				Quan										*
	*	Date:				2017/10/02									*
	*																	*
	*	2017 ©      ALL Rights Reserved. 								*
	************************** Copyright Info **************************/

	$bc_stack = null;

	class breadcrumbHelper {
		private $stack;

		public function __construct($home_url="home")
		{
			$this->stack = array(array("title" => "ホーム", "url" => $home_url));
		}

		public function push($title, $url)
		{
			array_push($this->stack, array("title" => $title, "url" => $url));
		}

		public function render() {
			p("<ul class='breadcrumb'>");

			for ($i = 0; $i < count($this->stack); $i ++) {
				$item = $this->stack[$i];
				$active = "";
				if ($i == count($this->stack) - 1) {
					$active = "active";
				}
				p("<li class='" . $active . "'>");
				if (isset($item['url'])) {
					p("<a href='" . $item['url'] . "'>" . $item["title"] . "</a>");
				}
				else 
					p($item["title"]);
				p("</li>");
			}

			p("</ul>");
		}
	};
	
	/*************************** The END ********************************
	*   Don't add new code below this line, thanks.						*
	*	2017 ©      ALL Rights Reserved. 								*
	**************************** The END *******************************/