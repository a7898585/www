<?php

/**
 * 微信接口
 * Author: cxl
 * QQ: 273616844
 * Date: 2015-10-21
 */

namespace Common\Extend;

class WeiXin {

    private static $token = '';
    private static $encodingAesKey = '';
    private static $appId = '';
    private static $appSecret = '';
    private static $api_url = 'https://api.weixin.qq.com/cgi-bin';
    private static $mp_url = 'https://mp.weixin.qq.com/cgi-bin';
    private static $access_token = '';

    public function __construct($appId, $appSecret, $token, $encodingAesKey) {
        self::$appId = $appId;
        self::$appSecret = $appSecret;
        self::$token = $token;
        self::$encodingAesKey = $encodingAesKey;
    }

    /**
     * 验证请求有效性
     * @param string $signature
     * @param string $timestamp
     * @param string $nonce
     * @return bool
     */
    public function checkSignature($signature, $timestamp, $nonce) {
        $tmpArr = array(self::$token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);
        return ($tmpStr == $signature) ? true : false;
    }

    /**
     * 获取access token
     * @return bool|mixed
     */
    public function getAccessToken() {
        $accessToken = S('weixin_access_token');
        if (!$accessToken) {
            $url = self::$api_url . '/token';
            $query['grant_type'] = 'client_credential';
            $query['appid'] = self::$appId;
            $query['secret'] = self::$appSecret;
            $result = $this->get($url . '?' . http_build_query($query));
            if (isset($result['errcode']))
                return false;
            S('weixin_access_token', $result['access_token'], 3600);
            $accessToken = $result['access_token'];
        }
        self::$access_token = $accessToken;
        return true;
    }

    /**
     * 发送模板消息
     * @param string $templateId
     * @param string $openId
     * @param string $url
     * @param array $data
     * @return bool|number
     */
    public function sendTemplateMessage($templateId, $openId, $wxUrl, array $data) {
        $url = self::$api_url . '/message/template/send?access_token=' . self::$access_token;
        $query['touser'] = $openId;
        $query['template_id'] = $templateId;
        $query['url'] = $wxUrl;
        $query['data'] = $data;
        $result = $this->post($url, json_encode($query));
        if ($result['errcode'] != 0)
            return false;
        return $result['msgid'];
    }

    /**
     * 获取模板id
     * @return boolean
     */
    public function getTenplateId() {
        if ($this->getAccessToken()) {
            $url = self::$api_url . '/template/api_set_industry?access_token=' . self::$access_token;
            echo $url;
            $query['industry_id1'] = 1;
            $query['industry_id2'] = 41;
            $result = $this->post($url, json_encode($query));
            var_dump($result);
            var_dump($result);
            if ($result['errcode'] != 0)
                return false;
            $url2 = self::$api_url . '/template/api_add_template?access_token=' . self::$access_token;
            $query2['template_id_short'] = 'TM00178';
            $result = $this->post($url2, json_encode($query2));
            var_dump($result);
            if ($result['errcode'] == 0) {
                return $result['template_id'];
            }
        }return false;
    }

    /**
     * 生成二维码
     */
    public function getQcode($mid = '20999') {
        if ($this->getAccessToken()) {
            $qcodeTicket = S('weixin_qcode_ticket_' . $mid);
            if (!$qcodeTicket) {
                $url = self::$api_url . '/qrcode/create?access_token=' . self::$access_token;
                $query['expire_seconds'] = 604800;
                $query['action_name'] = 'QR_SCENE';
                $query['action_info'] = array('scene' => array('scene_id' => $mid));
                $result = $this->post($url, json_encode($query));
                if ($result['errcode'] != 0)
                    return false;
                $qcodeTicket = $result['ticket'];
                S('weixin_qcode_ticket_' . $mid, $qcodeTicket, $result['expire_seconds']);
            }
            $url = self::$mp_url . '/showqrcode?ticket=' . urlencode($qcodeTicket);
            return $url;
        }
        return false;
    }

    /**
     * 获取用户微信信息
     * @param type $mid
     */
    public function getUserInfo($openid) {
        if ($this->getAccessToken()) {
            $userinfo = S('weixin_user_idnfo_' . $openid);
            if (!$userinfo) {
                $url = self::$api_url . '/user/info?access_token=' . self::$access_token . '&openid=' . $openid . '&lang=zh_CN';
                $result = $this->get($url);
                if ($result['errcode'] != 0)
                    return false;
                $userinfo = $result;
                S('weixin_user_idnfo_' . $openid, $userinfo, '3600*24');
            }
            return $userinfo;
        }
        return false;
    }

    static function get($url) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 1);
        curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
        curl_setopt($curl, CURLOPT_HTTPGET, 1);
        curl_setopt($curl, CURLOPT_TIMEOUT, 25);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($curl); // 执行操作
        if (curl_errno($curl)) {
            return false;
        }
        curl_close($curl);
        return json_decode($result, true);
    }

    static function post($url, $data) {
        if (is_array($data)) {
            $data = http_build_query($data);
        }
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 1);
        curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_TIMEOUT, 25);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($curl);
        if (curl_errno($curl)) {
            return false;
        }
        curl_close($curl);
        return json_decode($result, true);
    }

    /**
     * 发送HTTP的GET请求
     * @param string $url
     * @return boolean|mixed
     */
    final protected function _get($url) {
        $urls = parse_url($url);
        $opts = array(
            'http' => array(
                'method' => "GET",
                'header' => "Accept:text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8\r\n" .
                "Accept-Language:zh-CN,zh;q=0.8\r\n" .
                "Cache-Control:no-cache\r\n" .
                "Pragma:no-cache\r\n" .
                "Host:" . $urls['host'] . "\r\n" .
                "User-Agent:Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/30.0.1599.101 Safari/537.36\r\n\r\n",
                'timeout' => 10
            )
        );
        $result = file_get_contents($url, true, stream_context_create($opts));
        if (!$result)
            return false;
        return json_decode($result, true);
    }

    /**
     * 发送HTTP的POST请求
     * @param string $url
     * @param array|string $data
     * @return boolean|mixed
     */
    final protected function _post($url, $data) {
        if (empty($data))
            return false;
        $query = '';
        if (is_string($data)) {
            $query = $data;
        } elseif (is_array($data)) {
            $query = http_build_query($data);
        }
        $opts = array(
            'http' => array(
                'method' => "POST",
                'header' => "Content-type: application/json;charset=utf-8\r\n\r\n",
                'content' => $query
            )
        );
        $result = file_get_contents($url, false, stream_context_create($opts));
        if (!$result)
            return false;
        return json_decode($result, true);
    }

}