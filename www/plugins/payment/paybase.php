<?php

class paybase
{
	private $_props;

    function __construct()
    {
    }

	public function __get($prop) {
		if ($prop == "props")
			return $this->_props;
		else
		{
			if ($prop == "pay_url" && IS_CREATEMOCKUP) {
				return "paytest/" . $this->pay_id;
			}
			return $this->_props[$prop];
		}
	}

	public function __set($prop, $val) {
		$this->_props[$prop] = $val;
	}

	static public function get_from_pay_id($pay_id)
	{
		if (DEBUG_PAY == 2) {
			// unuse payment system
			$pay = new static;
			switch($pay_id) {
				case "chinapay":
					$_pay = new chinapay;
					break;
	            case "paypal":
					$_pay = new paypal;
					break;
				default:
					return null;
			}
			
			$pay->_props = $_pay->props;
			return $pay;
		}
		else {
			switch($pay_id) {
				case "chinapay":
	            case "paypal":
					return new $pay_id;
					break;
			}

			return null;	
		}
	}

	/**
     * 生成支付代码 
     * @param   array   $order      订单信息
     */
    function get_button($order_id, $amount, $front_url, $back_url, $pay_time)
    {
        $button = '<a href="'.$front_url.'" class="btn-pay"><img src="'.$this->img_url.'"></a>';

        return $button;
    }

    function respond($order_id)
    {
        return true;
    }

	function get_queryid($order_id)
	{
		$data = array('error_code'=>'0', 'message'=>_l(""), 'query_id'=>$order_id);
		return $data;
	}

    /**
     * 退货交易
     */
    function refund($order_id, $amount, $org_query_id, $back_url, $payment_id)
    {
    	return true;
    }
}

?>