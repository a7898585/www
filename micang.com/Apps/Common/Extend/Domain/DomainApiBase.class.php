<?php

/**
 * 域名注册接口，对接WEBNIC
 * Author: Jansen
 * QQ: 6206574
 * Date: 2015-10-05
 */

namespace Common\Extend\Domain;

use Think\Log;

abstract class DomainApiBase {

    /**
     * @var int $timeout 发送URL请求后的请求超时时间
     */
    protected $timeout = 25;

    /**
     * @var array $error 存储错误信息
     */
    protected $error;
    protected static $MESSAGE_STATUS_MAP = array(
        'DOMAIN_SUCCESS' => array('status' => 200, 'message' => '注册成功'),
        'DOMAIN_NOT_EXIST' => array('status' => 201, 'message' => '域名可注册'),
        'DOMAIN_EXIST' => array('status' => 300, 'message' => '域名已被注册')
    );

    /**
     * 域名查询
     * @param string $domain
     * @param string $idn
     * @return bool
     */
    abstract public function query($domain, $idn = 'ENG');

    /**
     * 域名注册
     * @param $domain 待注册域名
     * @param $age 注册年限
     * @param array $info 其他注册资料
     * @param string $idn 所属的国际化域名语言
     * @return bool
     */
    abstract public function register($domain, $age, array $info, $idn = 'ENG');

    /**
     * 域名续费
     * @param $domain 待续费域名
     * @param $age 续费年限
     * @return bool
     */
    abstract public function renew($domain, $age);

    /**
     * 域名删除
     * @param $domain 待删除域名
     * @return bool
     */
    abstract public function delete($domain);

    /**
     * 域名转移[转入]
     * @param $domain 待转入的域名
     * @param $code 转移密码
     * @param array $info 转移所需的资料
     * @return bool
     */
    abstract public function transferIn($domain, $code, array $info);

    /**
     * 域名转移[转出]
     * @param string $domain
     * @return bool
     */
    abstract public function transferOut($domain);

    /**
     * 域名转移[转入]状态
     * 查询成功时，有8种状态
     * D：转移成功
     * T：转移被拒绝，正在试图重新发送转移请求
     * P：域名管理员已经确认转移，正在等待注册商批准
     * N：72小时内未收到域名管理员的转移授权通知
     * Y：
     * E：验证邮件已发给域名管理员，等待授权通知
     * A：已收到转入申请，正在发送验证邮件通知域名管理员
     * W：已收到转入申请，正在查询WHOIS信息
     * @param $domain
     * @return bool|string
     */
    abstract public function transferInStatus($domain);

    /**
     * 域名锁定[设置域名保护状态]
     * @param $domain 待保护域名
     * @return bool
     */
    abstract public function lock($domain);

    /**
     * 域名解锁[设置域名保护状态]
     * @param $domain
     * @return bool
     */
    abstract public function unlock($domain);

    /**
     * 修改WHOIS信息
     * @param $domain
     * @param array $info
     * @return bool
     */
    abstract public function whoisEdit($domain, array $info);

    /**
     * 开启WHOIS隐私保护
     * @param $domain
     * @return bool
     */
    abstract public function whoisPrivacyLock($domain);

    /**
     * 关闭WHOIS隐私保护
     * @param $domain
     * @return bool
     */
    abstract public function whoisPrivacyUnlock($domain);

    /**
     * WHOIS隐私保护启用状态查询
     * @param $domain
     * @return bool
     */
    abstract public function whoisPrivacyQuery($domain);

    /**
     * 修改域名绑定的DNS
     * @param $domain
     * @param array $dns
     * @return bool
     */
    abstract public function dnsEdit($domain, array $dns);

    /**
     * 修改DNS服务器
     * @param $dns
     * @param array $ip
     * @return bool
     */
    abstract public function dnsServerEdit($dns, array $ip = array());

    /**
     * 注册DNS服务器
     * @param $dns
     * @param array $ip
     * @param array $reg
     * @return bool
     */
    abstract public function dnsServerRegister($dns, array $ip = array(), array $reg = array());

    /**
     * 删除DNS服务器
     * @param $dns
     * @param array $reg
     * @return bool
     */
    abstract public function dnsServerDelete($dns, array $reg = array());

    /**
     * 设置错误信息
     * @param string $error 接口返回的原始错误信息
     */
    abstract protected function setError($error);

    /**
     * 获取错误信息
     * @return array
     */
    final public function getError() {
        return $this->error;
    }

    /**
     * 将中文域名转换为punycode域名
     * @param string $domain
     * @return string|bool
     */
    final protected function encode($domain) {
        //检测必要的扩展是否开启
        if (!function_exists('idn_to_ascii'))
            return false;
        return idn_to_ascii($domain);
    }

    /**
     * 将punycode域名转换为中文域名
     * @param string $domain
     * @return string|bool
     */
    final protected function decode($domain) {
        //检测必要的扩展是否开启
        if (!function_exists('idn_to_ascii'))
            return false;
        return idn_to_utf8($domain);
    }

    /**
     * 获取域名的主后缀,例如 .us.com的后缀,主后缀为.com
     * @param string $domain
     * @return string
     */
    final protected function getDomainPrimarySuffix($domain) {
        return substr($domain, strrpos($domain, '.'));
    }

    /**
     * 获取域名的完整后缀
     * @param string $domain
     * @return string
     */
    final protected function getDomainFullSuffix($domain) {
        return strstr($domain, '.');
    }

    /**
     * 域名的名称部分是否为中文
     * @param string $domain
     * @return bool
     */
    final protected function isChineseName($domain) {
        
    }

    /**
     * 域名的后缀是否为中文
     * @param string $domain
     * @return bool
     */
    final protected function isChineseSuffix($domain) {
        $suffix = substr($domain, strrpos($domain, '.') + 1);
        if (!$suffix)
            return false;
        return preg_match('/^[a-z]+$/', $suffix) ? false : true;
    }

    /**
     * 发送HTTP的POST请求
     * @param string $url
     * @param array $data
     * @param bool $log 是否写日志
     * @return boolean|mixed
     */
    final protected function _post($url, array $data, $logged = true) {
        $startTime = date('Y-m-d H:i:s');
        $query = http_build_query($data);
        $handle = curl_init();
        curl_setopt($handle, CURLOPT_POST, true);
        curl_setopt($handle, CURLOPT_POSTFIELDS, $query);
        curl_setopt($handle, CURLOPT_URL, $url);
        if(substr(strtolower($url), 0, 5) == 'https'){
            curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, true);
        }
        curl_setopt($handle, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($handle, CURLOPT_MAXREDIRS, 2);
        curl_setopt($handle, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($handle);
        if (!$response){
            $error = '报错信息：'.curl_error().'('.curl_errno().')';
            $logged && $this->log($url, $query, $error, 'WEBNIC', $startTime);
            return false;
        }
        $header = curl_getinfo($handle);
        if ($header['http_code'] != 200){
            $error = '响应头：'.var_export($header, true)."\r\n响应信息：".$response;
            $logged && $this->log($url, $query, $error, 'WEBNIC', $startTime);
            return false;
        }
        $logged && $this->log($url, $query, $response, 'WEBNIC', $startTime);
        return $response;
    }

    /**
     * 通过WEB service请求服务
     * @param string $url
     * @param string $method
     * @param array $data
     */
    final protected function service($url, $method, array $data, $logged = true) {
        $startTime = date('Y-m-d H:i:s');
        ini_set('soap.wsdl_cache_enabled', '0');
        ini_set('soap.wsdl_cache_ttl', '0');
        try {
            $soapClient = new \SoapClient($url);
            var_dump(/*$soapClient->__getFunctions(), */$soapClient->__getTypes());
            $result = $soapClient->$method($data);
        } catch (\Exception $e) {
            $result = false;
            return $result;
        } finally {
            $logged && $this->log($url . ' 请求方法：' . $method, $data, $result, 'BIZCN', $startTime);
        }
        return is_object($result) ? json_decode(json_encode($result), true) : false;
    }

    /**
     * 记录日志
     * @param string $url 请求URL
     * @param string $result 接口返回信息
     * @param string $target 目前支持WEBNIC和BIZCN
     * @return void
     */
    private function log($url, $data, $result, $target, $startTime) {
        $logFilePath = C('LOG_PATH') . 'domain_' . date('Ymd') . '.log';
        $message = "\n所属接口：" . $target . "\n";
        $message .= "请求地址：" . $url . "\n";
        $message .= "请求参数：" . (is_string($data) ? $data : json_encode($data)) . "\n";
        $message .= "接口返回：" . (is_string($result) ? trim($result) : json_encode($result)) . "\n";
        $message .= "请求时间：" . $startTime . "\n";
        $message .= "返回时间：" . date('Y-m-d H:i:s') . "\n";
        $message .= "===============================================";
        Log::write($message, Log::INFO, '', $logFilePath);
    }

}