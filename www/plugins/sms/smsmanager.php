<?php

define('RETCODE_SUCCESS', "100");
class smsManager {
    // return RETCODE_SUCCESS if success
    public static function sendSMS($phoneNum, $content) {
        $phoneNum = str_replace("-", "", $phoneNum);

        $result = self::commonSendSMS($phoneNum, $content);

        if ($result != null) {
            if ($result['code'] == 0) {
                return true;
            }
        }
        return false;
    }

    public static function commonSendSMS($phoneNum, $content) {
        if (($phoneNum == null) || (mb_strlen($phoneNum, 'utf-8') == 0))
            return null;

        set_time_limit(0);

        $ch = curl_init();

        /* Set authentication method */
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept:text/plain;charset=utf-8',
            'Content-Type:application/x-www-form-urlencoded', 'charset=utf-8'));

        /* Set response result as stream */
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        /* Set timeout value */
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        /* Set communication methon */
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        // Sending single SMS
        $data = array('text'=>$content,'apikey'=>SMS_APIKEY,'mobile'=>$phoneNum);
        $json_data = self::send($ch,$data);
        $array = json_decode($json_data, true);

        set_time_limit(SMS_LIMITTIME);
        return $array;
    }

    //Query account
    public static function get_user($ch,$apikey){
        curl_setopt ($ch, CURLOPT_URL, 'https://sms.yunpian.com/v2/user/get.json');
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array('apikey' => $apikey)));
        $result = curl_exec($ch);
        $error = curl_error($ch);
        self::checkErr($result,$error);
        return $result;
    }
    public static function send($ch,$data){
        curl_setopt ($ch, CURLOPT_URL, 'https://sms.yunpian.com/v2/sms/single_send.json');
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        $result = curl_exec($ch);
        $error = curl_error($ch);
        //var_dump($result);
        self::checkErr($result,$error);
        return $result;
    }
    public static function tpl_send($ch,$data){
        curl_setopt ($ch, CURLOPT_URL,
            'https://sms.yunpian.com/v2/sms/tpl_single_send.json');
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        $result = curl_exec($ch);
        $error = curl_error($ch);
        //var_dump($result);
        self::checkErr($result,$error);
        return $result;
    }
    public static function voice_send($ch,$data){
        curl_setopt ($ch, CURLOPT_URL, 'http://voice.yunpian.com/v2/voice/send.json');
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        $result = curl_exec($ch);
        $error = curl_error($ch);
        self::checkErr($result,$error);
        return $result;
    }
    public static function notify_send($ch,$data){
        curl_setopt ($ch, CURLOPT_URL, 'https://voice.yunpian.com/v2/voice/tpl_notify.json');
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        $result = curl_exec($ch);
        $error = curl_error($ch);
        self::checkErr($result,$error);
        return $result;
    }

    public static function checkErr($result,$error) {
        if($result === false)
        {
            echo 'Curl error: ' . $error;
        }
        else
        {
            //echo 'No error';
        }
    }
}