<?php

include_once 'chinapay/sdk/acp_service.php';

class chinapay extends paybase 
{
    function __construct()
    {
        $this->pay_name = _l("银联");
        $this->pay_id = "chinapay";
        $this->img_url = "img/yinlian.png";
    }

    /**
     * 生成支付代码
     * @param   array   $order      订单信息
     */
    function get_button($order_id, $amount, $front_url, $back_url, $pay_time)
    {
        date_default_timezone_set('Asia/Shanghai');
        //生成订单号，定长16位，任意数字组合，一天内不允许重复，本例采用当前时间戳，必填
        $order_id = str_pad($order_id, 16, "0", STR_PAD_LEFT);

        $params = array(
            //以下信息非特殊情况不需要改动
            'version' => com\unionpay\acp\sdk\SDKConfig::getSDKConfig()->version,                 //版本号
            'encoding' => 'utf-8',                //编码方式
            'txnType' => '01',                    //交易类型
            'txnSubType' => '01',                 //交易子类
            'bizType' => '000201',                //业务类型
            'frontUrl' =>  $front_url,  //前台通知地址
            'backUrl' => $back_url,     //后台通知地址
            'signMethod' => com\unionpay\acp\sdk\SDKConfig::getSDKConfig()->signMethod,               //签名方法
            'channelType' => '07',                //渠道类型，07-PC，08-手机
            'accessType' => '0',                  //接入类型
            'currencyCode' => '156',              //交易币种，境内商户固定156

            //TODO 以下信息需要填写
            'merId' => CHINAPAY_MERCHANTID,      //商户代码，请改自己的测试商户号，此处默认取demo演示页面传递的参数
            'orderId' => $order_id,    //商户订单号，8-32位数字字母，不能含“-”或“_”，此处默认取demo演示页面传递的参数，可以自行定制规则
            'txnTime' => $pay_time,    //订单发送时间，格式为YYYYMMDDhhmmss，取北京时间，此处默认取demo演示页面传递的参数
            'txnAmt' => $amount * 100,  //交易金额，单位分，此处默认取demo演示页面传递的参数

            // 订单超时时间。
            // 超过此时间后，除网银交易外，其他交易银联系统会拒绝受理，提示超时。 跳转银行网银交易如果超时后交易成功，会自动退款，大约5个工作日金额返还到持卡人账户。
            // 此时间建议取支付时的北京时间加15分钟。
            // 超过超时时间调查询接口应答origRespCode不是A6或者00的就可以判断为失败。
            'payTimeout' => date('YmdHis', strtotime('+30 minutes')),

            // 请求方保留域，
            // 透传字段，查询、通知、对账文件中均会原样出现，如有需要请启用并修改自己希望透传的数据。
            // 出现部分特殊字符时可能影响解析，请按下面建议的方式填写：
            // 1. 如果能确定内容不会出现&={}[]"'等符号时，可以直接填写数据，建议的方法如下。
            //    'reqReserved' =>'透传信息1|透传信息2|透传信息3',
            // 2. 内容可能出现&={}[]"'符号时：
            // 1) 如果需要对账文件里能显示，可将字符替换成全角＆＝｛｝【】“‘字符（自己写代码，此处不演示）；
            // 2) 如果对账文件没有显示要求，可做一下base64（如下）。
            //    注意控制数据长度，实际传输的数据长度不能超过1024位。
            //    查询、通知等接口解析时使用base64_decode解base64后再对数据做后续解析。
            //    'reqReserved' => base64_encode('任意格式的信息都可以'),

            //TODO 其他特殊用法请查看 special_use_purchase.php
        );

        com\unionpay\acp\sdk\AcpService::sign ( $params );
        $uri = com\unionpay\acp\sdk\SDKConfig::getSDKConfig()->frontTransUrl;
        $html_form = com\unionpay\acp\sdk\AcpService::createAutoFormHtml( $params, $uri );

        $button  = '<form style="display:inline-block" action="' . $uri . '" method="post" target="_blank" onsubmit="onChinapaySubmit();">';
        $button  .= '<input type=HIDDEN name="version" id="version" value="' . $params["version"] . '">';
        $button  .= '<input type=HIDDEN name="encoding" id="encoding" value="' . $params["encoding"] . '">';
        $button  .= '<input type=HIDDEN name="txnType" id="txnType" value="' . $params["txnType"] . '">';
        $button  .= '<input type=HIDDEN name="txnSubType" id="txnSubType" value="' . $params["txnSubType"] . '">';
        $button  .= '<input type=HIDDEN name="bizType" id="bizType" value="' . $params["bizType"] . '">';
        $button  .= '<input type=HIDDEN name="frontUrl" id="frontUrl" value="' . $params["frontUrl"] . '">';
        $button  .= '<input type=HIDDEN name="backUrl" id="backUrl" value="' . $params["backUrl"] . '">';
        $button  .= '<input type=HIDDEN name="signMethod" id="signMethod" value="' . $params["signMethod"] . '">';
        $button  .= '<input type=HIDDEN name="channelType" id="channelType" value="' . $params["channelType"] . '">';
        $button  .= '<input type=HIDDEN name="accessType" id="accessType" value="' . $params["accessType"] . '">';
        $button  .= '<input type=HIDDEN name="currencyCode" id="currencyCode" value="' . $params["currencyCode"] . '">';
        $button  .= '<input type=HIDDEN name="merId" id="merId" value="' . $params["merId"] . '">';
        $button  .= '<input type=HIDDEN name="orderId" id="orderId" value="' . $params["orderId"] . '">';
        $button  .= '<input type=HIDDEN name="txnTime" id="txnTime" value="' . $params["txnTime"] . '">';
        $button  .= '<input type=HIDDEN name="txnAmt" id="txnAmt" value="' . $params["txnAmt"] . '">';
        $button  .= '<input type=HIDDEN name="payTimeout" id="payTimeout" value="' . $params["payTimeout"] . '">';
        $button  .= '<input type=HIDDEN name="signature" id="signature" value="' . $params["signature"] . '">';

        if  ($params["signMethod"] == "01")
            $button  .= '<input type=HIDDEN name="certId" id="certId" value="' . $params["certId"] . '">';

        $button  .= '<button type="submit" class="btn-pay"><img src="' . $this->img_url .'"></button>';
        $button  .= '</form>';

        return $button;
    }

    /**
     * 响应操作
     */
    function respond($order_id)
    {
        global $_POST;

        //生成订单号，定长16位，任意数字组合，一天内不允许重复，本例采用当前时间戳，必填
        $order_id = str_pad($order_id, 16, "0", STR_PAD_LEFT);


        if (!isset($_POST['signature']))
            return false;

        $signPubKeyCert = $_POST['signPubKeyCert'];
        $signPubKeyCert = str_replace("-----BEGIN CERTIFICATE-----", "", $signPubKeyCert);
        $signPubKeyCert = str_replace("-----END CERTIFICATE-----", "", $signPubKeyCert);
        $signPubKeyCert = "-----BEGIN CERTIFICATE-----\n".trim($signPubKeyCert)."\n-----END CERTIFICATE-----";
        $_POST['signPubKeyCert'] = $signPubKeyCert;

        //var_dump($signPubKeyCert);

        //echo 'before validation';
        $valid = com\unionpay\acp\sdk\AcpService::validate ($_POST);
        if (!$valid)
            return false;
        //echo 'after validation';
        $respCode = $_POST["respCode"];
        //die();
        if ($respCode == '00'){
            return $order_id == $_POST['orderId'];
        } else {
            //echo "<h3>交易失败！</h3>";
            return false;
        }
    }


    /**
     * 查询交易
     */
    function get_queryid($order_id, $pay_time)
    {
        $order_id = str_pad($order_id, 16, "0", STR_PAD_LEFT);

        $params = array(
            //以下信息非特殊情况不需要改动
            'version' => com\unionpay\acp\sdk\SDKConfig::getSDKConfig()->version,		  //版本号
            'encoding' => 'utf-8',		  //编码方式
            'signMethod' => com\unionpay\acp\sdk\SDKConfig::getSDKConfig()->signMethod,		  //签名方法
            'txnType' => '00',		      //交易类型
            'txnSubType' => '00',		  //交易子类
            'bizType' => '000000',		  //业务类型
            'accessType' => '0',		  //接入类型
            'channelType' => '07',		  //渠道类型

            //TODO 以下信息需要填写
            'orderId' => $order_id,	//请修改被查询的交易的订单号，8-32位数字字母，不能含“-”或“_”，此处默认取demo演示页面传递的参数
            'merId' => CHINAPAY_MERCHANTID,	    //商户代码，请改自己的测试商户号，此处默认取demo演示页面传递的参数
            'txnTime' => $pay_time, 	//请修改被查询的交易的订单发送时间，格式为YYYYMMDDhhmmss，此处默认取demo演示页面传递的参数
        );

        com\unionpay\acp\sdk\AcpService::sign ( $params ); // 签名
        $url = com\unionpay\acp\sdk\SDKConfig::getSDKConfig()->singleQueryUrl;

        $result_arr = com\unionpay\acp\sdk\AcpService::post ( $params, $url);
        if(count($result_arr)<=0) { //没收到200应答的情况
            $data = array('error_code'=>'6', 'message'=>_l("失败。"));
            return $data;
        }

        if (!com\unionpay\acp\sdk\AcpService::validate ($result_arr) ){
            $data = array('error_code'=>'1', 'message'=>_l("应答报文验签失败。"));
            return $data;
        }

        echo "应答报文验签成功<br>\n";
        if ($result_arr["respCode"] == "00"){
            if ($result_arr["origRespCode"] == "00"){
                //交易成功
                //TODO
                //echo "交易成功。<br>\n";
                $data = array('error_code'=>'0', 'message'=>_l("交易成功。"), 'query_id'=>$result_arr["queryId"]);
                return $data;
            } else if ($result_arr["origRespCode"] == "03"
                || $result_arr["origRespCode"] == "04"
                || $result_arr["origRespCode"] == "05"){
                //后续需发起交易状态查询交易确定交易状态
                //TODO
                $data = array('error_code'=>'2', 'message'=>_l("处理超时，请稍后查询。"));
                return $data;
            } else {
                //其他应答码做以失败处理
                //TODO
                $data = array('error_code'=>'3', 'message'=>_l("交易失败。"));
                return $data;
            }
        } else if ($result_arr["respCode"] == "03"
            || $result_arr["respCode"] == "04"
            || $result_arr["respCode"] == "05" ){
            //后续需发起交易状态查询交易确定交易状态
            //TODO
            $data = array('error_code'=>'4', 'message'=>_l("处理超时，请稍后查询。"));
            return $data;
        } else {
            //其他应答码做以失败处理
            //TODO
            $data = array('error_code'=>'5', 'message'=>_l("失败：").$result_arr["respCode"].",".$result_arr["respMsg"]);
            return $data;
        }
    }

    /**
     * 退货交易
     */
    function refund($order_id, $query_id, $amount, $back_url, $payment_id)
    {
        //生成订单号，定长16位，任意数字组合，一天内不允许重复，本例采用当前时间戳，必填
        $order_id = str_pad($order_id, 16, "1", STR_PAD_LEFT);

        $params = array(
            //以下信息非特殊情况不需要改动
            'version' => com\unionpay\acp\sdk\SDKConfig::getSDKConfig()->version,  //版本号
            'encoding' => 'utf-8',            //编码方式
            'signMethod' => com\unionpay\acp\sdk\SDKConfig::getSDKConfig()->signMethod, //签名方法
            'txnType' => '04',                //交易类型
            'txnSubType' => '00',             //交易子类
            'bizType' => '000201',            //业务类型
            'accessType' => '0',              //接入类型
            'channelType' => '07',            //渠道类型
            'backUrl' => $back_url, //后台通知地址
            
            //TODO 以下信息需要填写
            'orderId' => $order_id,     //商户订单号，8-32位数字字母，不能含“-”或“_”，可以自行定制规则，重新产生，不同于原消费，此处默认取demo演示页面传递的参数
            'merId' => CHINAPAY_MERCHANTID,         //商户代码，请改成自己的测试商户号，此处默认取demo演示页面传递的参数
            'origQryId' => $query_id, //原消费的queryId，可以从查询接口或者通知接口中获取，此处默认取demo演示页面传递的参数
            'txnTime' => date('YmdHis'),     //订单发送时间，格式为YYYYMMDDhhmmss，重新产生，不同于原消费，此处默认取demo演示页面传递的参数
            'txnAmt' => $amount * 100,       //交易金额，退货总金额需要小于等于原消费

            // 请求方保留域，
            // 透传字段，查询、通知、对账文件中均会原样出现，如有需要请启用并修改自己希望透传的数据。
            // 出现部分特殊字符时可能影响解析，请按下面建议的方式填写：
            // 1. 如果能确定内容不会出现&={}[]"'等符号时，可以直接填写数据，建议的方法如下。
            //    'reqReserved' =>'透传信息1|透传信息2|透传信息3',
            // 2. 内容可能出现&={}[]"'符号时：
            // 1) 如果需要对账文件里能显示，可将字符替换成全角＆＝｛｝【】“‘字符（自己写代码，此处不演示）；
            // 2) 如果对账文件没有显示要求，可做一下base64（如下）。
            //    注意控制数据长度，实际传输的数据长度不能超过1024位。
            //    查询、通知等接口解析时使用base64_decode解base64后再对数据做后续解析。
            //    'reqReserved' => base64_encode('任意格式的信息都可以'),
        );

        com\unionpay\acp\sdk\AcpService::sign ( $params ); // 签名
        $url = com\unionpay\acp\sdk\SDKConfig::getSDKConfig()->backTransUrl;

        $result_arr = com\unionpay\acp\sdk\AcpService::post ( $params, $url);
        if(count($result_arr)<=0) { //没收到200应答的情况
            return false;
        }

        if (!com\unionpay\acp\sdk\AcpService::validate ($result_arr) ){
            // 应答报文验签失败
            return false;
        }

        // 应答报文验签成功
        if ($result_arr["respCode"] == "00"){
            //交易已受理，等待接收后台通知更新订单状态，如果通知长时间未收到也可发起交易状态查询
            return true;
        } else if ($result_arr["respCode"] == "03"
                || $result_arr["respCode"] == "04"
                || $result_arr["respCode"] == "05" ){
            //后续需发起交易状态查询交易确定交易状态
            // echo "处理超时，请稍微查询。"
            return false;
        } else {
            //其他应答码做以失败处理
            //echo "失败：" . $result_arr["respMsg"] . "。<br>\n";
            return false;
        }
    }
}
