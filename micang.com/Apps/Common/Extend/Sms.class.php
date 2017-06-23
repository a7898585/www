<?php

namespace Common\Extend;

final class Sms {

    //主帐号
    private $accountSid = '8a48b551493219e001493809c3a404c2';
    //主帐号Token
    private $accountToken = '642674e6989b427783908c71cce13c5d';
    //应用Id
    private $appId='8a48b551510f653b0151199c58112690';
//    private $appId = '8a48b551493219e00149380a17e604c5';//测试用
    //请求地址，格式如下，不需要写https://
    private $serverIP = 'app.cloopen.com';
//    private $serverIP = 'sandboxapp.cloopen.com';//测试用
    //请求端口
    private $serverPort = '8883';
    //REST版本号
    private $softVersion = '2013-12-26';

    /**
     * 
     * @param string $to 手机号码集合,用英文逗号分开
     * @param string $code
     * @param number $tplId 模板ID
     * @return boolean|mixed
     */
    final public function sendCaptcha($to, $code, $tplId = '8559') {
        if (is_array($this->checkParam()))
            return false;
        $smsBody = array('to' => $to, 'templateId' => $tplId, 'appId' => $this->appId, 'datas' => array(strval($code), '10'));
        $batch = date("YmdHis");
        // 大写的sig参数
        $sign = strtoupper(md5($this->accountSid . $this->accountToken . $batch));
        // 生成请求URL
        $url = 'https://' . $this->serverIP . ':' . $this->serverPort . '/' . $this->softVersion . '/Accounts/' . $this->accountSid . '/SMS/TemplateSMS?sig=' . $sign;
        // 生成授权：主帐户Id + 英文冒号 + 时间戳。
        $authen = base64_encode($this->accountSid . ':' . $batch);
        // 生成包头
        $header = array('Accept:application/json', 'Content-Type:application/json;charset=utf-8', 'Authorization:' . $authen);
        // 发送请求
        $response = $this->curl_post($url, json_encode($smsBody), $header);
        $result = json_decode($response, true);
        if ($result['statusCode'] > 0)
            return false;
        return $result['templateSMS'];
    }

    private function checkParam() {
        if ($this->serverIP == "") {
            return array('statusCode' => 172004, 'statusMsg' => 'IP为空');
        }
        if ($this->serverPort <= 0) {
            return array('statusCode' => 172005, 'statusMsg' => '端口错误（小于等于0）');
        }
        if ($this->softVersion == "") {
            return array('statusCode' => 172013, 'statusMsg' => '版本号为空');
        }
        if ($this->accountSid == "") {
            return array('statusCode' => 172006, 'statusMsg' => '主帐号为空');
        }
        if ($this->accountToken == "") {
            return array('statusCode' => 172007, 'statusMsg' => '主帐号令牌为空');
        }
        if ($this->appId == "") {
            return array('statusCode' => 172012, 'statusMsg' => '应用ID为空');
        }
        return true;
    }

    /**
     * 发起HTTPS请求
     */
    private function curl_post($url, $data, $header, $post = 1) {
        //初始化curl
        $ch = curl_init();
        //参数设置
        $res = curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POST, $post);
        if ($post) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        $result = curl_exec($ch);
        curl_close($ch);
        //连接失败
        if ($result == false) {
            return json_encode(array('statusCode' => 172001, 'statusMsg' => '网络错误'));
        }
        return $result;
    }

}

?>