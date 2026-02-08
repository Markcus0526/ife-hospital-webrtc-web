<?php
	/************************* Copyright Info ***************************
	*	Project Name:		MARKCUS World Tele Clinic System				*
	*	Framework:			MAL MVC Web Framewrok v1.0					*
	*	Author:				Markcus										*
	*	Date:				2017/10/02									*
	*																	*
	*	2017 ©      ALL Rights Reserved. 								*
	************************** Copyright Info **************************/

	class payController extends controller {
		public function __construct() {
			parent::__construct();
		}

		public function check_priv($action, $utype)
		{
			parent::check_priv($action, UTYPE_NONE);
		}

		public function respond($interview_id, $pay_time, $pay_id, $cost) {
			$payment = paybase::get_from_pay_id($pay_id);

			$payed = false;
			if ($payment != null) {
				$payed = $payment->respond($interview_id);
			}
			
			if ($payed) {
				$paymentId = $_GET['paymentId'];
				if (!isset($paymentId)) {
					$paymentId = $_POST['paymentId'];
				}
				$interview = interviewModel::get_model($interview_id);
				$err = $interview->pay($pay_id, $pay_time, $cost, $paymentId);
			}
			// do nothing
			exit;
		}

		public function cancel($interview_id, $pay_mode) {
			exit;	
		}
	}
	
	/*************************** The END ********************************
	*   Don't add new code below this line, thanks.						*
	*	2017 ©      ALL Rights Reserved. 								*
	**************************** The END *******************************/