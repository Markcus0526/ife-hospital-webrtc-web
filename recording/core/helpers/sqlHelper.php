<?php
	/************************* Copyright Info ***************************
	*	Project Name:		3QC World Tele Clinic System				*
	*	Framework:			MAL MVC Web Framewrok v1.0					*
	*	Author:				Quan										*
	*	Date:				2017/10/02									*
	*																	*
	*	2017 ©      ALL Rights Reserved. 								*
	************************** Copyright Info **************************/

	class sqlHelper {
		static public function join_and($sqls) {
			return join(" AND ", $sqls);
		}

		static public function join_or($sqls) {
			return join(" OR ", $sqls);
		}

		static public function join_sql($sqls, $op = "AND") {
			if ($sqls == null || count($sqls) == 0)
				return "";
			$sql = $sqls[0];
			for ($i = 1; $i < count($sqls); $i ++) {
				$sql .= " " . $op . " " . $sqls[$i];
			}
			return $sql;
		}

		static public function in_vals($vals, $ignore=-1) {
			if (is_array($vals)) {
				$_vals = array();
				foreach ($vals as $val) {
					if ($val != -1)
						$_vals[] = _sql($val);
				}

				if (count($_vals))
					return "(" . implode(',', $_vals) . ")";	
			}

			return null;
		}
	}
	
	/*************************** The END ********************************
	*   Don't add new code below this line, thanks.						*
	*	2017 ©      ALL Rights Reserved. 								*
	**************************** The END *******************************/