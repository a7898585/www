<?php

namespace Common\Extend;

use Think\Log;

final class BrmApi {
    /**
     * 加密私钥
     * @var string
     */

    const PRIVATE_KEY = '[BRM POWER BY BOOKSIR CO.LTD]';
    /**
     * 注册接口
     * @var string
     */
    const BRM_USER_REGISTER = '/api/gzuser/regiestuser';
    /**
     * 检查用户名
     * @var string
     */
    const BRM_USER_CHECKNAME = '/api/gzuser/checkuserName';
    /**
     * 检查邮箱
     * @var string
     */
    const BRM_USER_CHECKEMAIL = '/api/gzuser/checkemail';
    /**
     * 登录
     * @var string
     */
    const BRM_USER_LOGIN = '/login';
    /**
     * 修改用户密码
     * @var string
     */
    const BRM_USER_CHANGEPASSWORD = '/api/gzuser/modifyPassword';
    /**
     * 用户基本信息
     * @var string
     */
    const BRM_USER_BASEINFO = '/api/gzuser/clientuser';
    /**
     * 充值
     * @var string
     */
    const BRM_FINANCE_RECHARGE = '/recharge';

    /**
     * 获取代理商列表
     * @var string
     */
    const BRM_AGENTS = '/api/business/agentUsers';
    /**
     * 取代理商信息
     * @var string
     */
    const BRM_AGENTINFO = '/api/agent/user';
    /**
     * 获取区域列表
     * @var string
     */
    const BRM_AREAS = '/area/view/getchildren';
    /**
     * 根据区域ID取区域详情
     * @var string
     */
    const BRM_AREA_INFO = '/area/view/getinfo';
    /**
     * 设置代理商
     * @var string
     */
    const BRM_SET_AGENT = '/api/agent/user/agentCustomer/add';
    /**
     * 用户账户登录
     * @var string
     */
    const BRM_GZUSER = '/api/gzuser/tokenlogin';
    /**
     * 验证代理商是否代理指定产品
     * @var string
     */
    const BRM_AGENT_CHECK = '/api/agent/checkAgent';
    /**
     * 代理商账户登录
     * @var string
     */
    const BRM_AGENT_LOGIN = '/api/agent/loginuser';
    /**
     * 修改代理商账户密码
     * @var string
     */
    const BRM_AGENT_PASSWORD = '/api/agent/modifyPassword';
    /**
     * 检测用户身份证号唯一性
     * @var string
     */
    const BRM_GZUSER_IDCARD = '/api/gzuser/checkcard';

    /**
     * 用户信息修改.
     */
    const BRM_USER_EDIT = '/api/gzuser/customercompany/edit';

    /**
     * 产品列表接口.
     */
    const BRM_PROJECT_VERSION = '/api/project/projectVersion';
    /**
     * 产品购买
     */
    const BRM_PROJECT_BUY = '/common/product/buyProductComplete';

    /**
     * 时间戳
     * @var int
     */
    private $timestamp;

    /**
     * 钱多多在BRM中的产品ID
     * @var int
     */
    private $selfProjectId;

    /**
     * 商务卫士在BRM中的产品ID
     * @var unknown
     */
    private $weishiProjectId;
    private $braDomainHost;
    private $brmDomainHost;
    private $brcDomainHost;
    private $pdcDomainHost;
    private $ssoDomainHost;
    private $braOldDomainHost;

    public function __construct() {
        $this->timestamp = microtime(true);
        $this->braDomainHost = C('BRA_DOMAIN_HOST');
        $this->brmDomainHost = C('BRM_DOMAIN_HOST');
        $this->brcDomainHost = C('BRC_DOMAIN_HOST');
        $this->pdcDomainHost = C('PDC_DOMAIN_HOST');
        $this->ssoDomainHost = C('SSO_DOMAIN_HOST');
        $this->selfProjectId = C('PROJECT_ID');
        $this->braOldDomainHost = C('BRA_OLD_DOMAIN_HOST');
    }

    /**
     * 客户端验证登录
     * @param $username
     * @param $password
     * @return bool
     */
    final public function gzuser($username, $password) {
        if (!$username || !$password)
            return false;
        $query = array('userName' => $username, 'password' => md5($password), 'type' => $this->selfProjectId, 'checkKey' => $this->timestamp, 'md5key' => md5(self::PRIVATE_KEY . $this->timestamp));
        $url = $this->braDomainHost . self::BRM_GZUSER . '?' . http_build_query($query);
        $return = $this->get($url);
        return $return;
    }

    /**
     * 代理商账户登录
     * @param string $username
     * @param string $password
     * @return boolean|Ambigous <boolean, mixed>
     */
    final public function agentLogin($username, $password) {
        if (!$username || !$password)
            return false;
        $query = array('userName' => $username, 'password' => md5($password), 'type' => $this->selfProjectId, 'checkKey' => $this->timestamp, 'md5key' => md5(self::PRIVATE_KEY . $this->timestamp));
        $url = $this->braDomainHost . self::BRM_AGENT_LOGIN . '?' . http_build_query($query);
        $return = $this->get($url);
        return $return;
    }

    /**
     * 检查用户名是否存在
     * @param string $username
     * @return boolean
     */
    final public function checkUserName($username) {

        if (!$username)
            return false;
        $url = $this->braDomainHost . self::BRM_USER_CHECKNAME . '?userName=' . $username;
        return $this->get($url);
    }

    /**
     * 检查邮箱是否存在
     * @param string $email
     * @return boolean
     */
    final public function checkEmail($email) {
        if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL))
            return false;
        $url = $this->braDomainHost . self::BRM_USER_CHECKEMAIL . '?email=' . $email;
        return $this->get($url);
    }

    /**
     * 检测代理商是否代理指定产品
     * @param int $agent_id
     * @return boolean
     */
    final public function checkAgent($agent_id) {
        $query = array('projectId' => $this->selfProjectId, 'agentId' => $agent_id, 'checkKey' => $this->timestamp, 'md5key' => md5(self::PRIVATE_KEY . $this->timestamp));

        $url = $this->braDomainHost . self::BRM_AGENT_CHECK . '?' . http_build_query($query);
        $result = $this->get($url);
        if ($result['s'] > 0)
            return true;
        return false;
    }

    /**
     * 登录，成功返回用户ID
     * @param string $username
     * @param string $password 加密后
     * @return boolean|string
     */
    final public function getLoginURL() {
        return $this->ssoDomainHost . self::BRM_USER_LOGIN . '?service=' . urlencode('http://' . $_SERVER['HTTP_HOST']);
    }

    /**
     * 修改密码
     * @param int $mid
     * @param string $old 旧密码，明文
     * @param string $new 新密码，明文
     * @return boolean
     */
    final public function changePassword($mid, $old, $new) {
        $query = array('customerId' => $mid, 'password' => $new, 'oldPassword' => $old, 'md5key' => md5(self::PRIVATE_KEY . $this->timestamp), 'checkKey' => $this->timestamp);
        $url = $this->braDomainHost . self::BRM_USER_CHANGEPASSWORD . '?' . http_build_query($query);
        $result = $this->get($url);
        return $result;
    }

    final public function changeAgentPassword($agent_id, $old, $new) {
        $query = array('agentId' => $agent_id, 'password' => $new, 'oldPassword' => $old, 'md5key' => md5(self::PRIVATE_KEY . $this->timestamp), 'checkKey' => $this->timestamp);
        $url = $this->braDomainHost . self::BRM_AGENT_PASSWORD . '?' . http_build_query($query);
        $result = $this->get($url);
        if ($result['s'] > 0)
            return true;
        return $result['r'];
    }

    /**
     * 充值，只是生成URL
     * @param int $mid
     * @return string
     */
    final public function recharge($mid) {
        $query = array('customerId' => $mid, 'userType' => 1, 'type' => $this->selfProjectId, 'md5key' => md5(self::PRIVATE_KEY . $this->timestamp), 'checkKey' => $this->timestamp);
        return $this->brcDomainHost . self::BRM_FINANCE_RECHARGE . '?' . http_build_query($query);
    }

    /**
     * 取账号信息
     * @param int $mid
     */
    final public function userinfo($mid) {
        $query = array('customerId' => $mid, 'projectId' => $this->selfProjectId, 'md5key' => md5(self::PRIVATE_KEY . $this->timestamp), 'checkKey' => $this->timestamp);
        $url = $this->braDomainHost . self::BRM_USER_BASEINFO . '?' . http_build_query($query);
        $result = $this->get($url);
        if (!is_array($result) || $result['s'] < 0)
            return false;
        return $result['r'];
    }

    /**
     * 身份证号唯一性检查
     * @param int $mid
     * @param string $idCard
     * @return boolean
     */
    final public function checkCard($mid, $idCard) {
        $query = array('customerId' => $mid, 'idCard' => $idCard);
        $url = $this->braDomainHost . self::BRM_GZUSER_IDCARD . '?' . http_build_query($query);
        $result = $this->get($url);
        if (!is_array($result) || $result['s'] < 0)
            return false;
        return true;
    }

    /**
     * 取代理商列表
     * @param string $city
     * @return boolean|array
     */
    final public function agents($city) {
        $query = array('areaid' => $city, 'projectId' => $this->selfProjectId);
        $url = $this->braDomainHost . self::BRM_AGENTS . '?' . http_build_query($query);
        $result = $this->get($url);
        if (!is_array($result))
            return false;
        return $result;
    }

    /**
     * 取代理商信息
     * @param int $agentId
     * @return array|boolean
     */
    final public function agentInfo($agentId) {
        $query = array('agentId' => $agentId);
        $url = $this->braDomainHost . self::BRM_AGENTINFO . '?' . http_build_query($query);
        return $this->get($url);
    }

    /**
     * 绑定代理商
     * @param int $agentId
     * @param int $mid
     * @return boolean
     */
    final public function setAgent($agentId, $mid) {
        $query = array('agentId' => $agentId, 'projectType' => $this->selfProjectId, 'customerId' => $mid, 'md5key' => md5(self::PRIVATE_KEY . $this->timestamp), 'checkKey' => $this->timestamp);
        $url = $this->brmDomainHost . self::BRM_SET_AGENT . '?' . http_build_query($query);
        $result = $this->get($url);

        if (!is_array($result))
            return false;
        return $result;
    }

    /**
     * 取区域列表
     * @param string $areaid
     * @return array
     */
    final public function areas($areaid) {
        $query = array('areaid' => $areaid);
        $url = $this->pdcDomainHost . self::BRM_AREAS . '?' . http_build_query($query, 0);
        return $this->get($url);
    }

    /**
     * 取区域详情
     * @param string $areaid
     * @return array
     */
    final public function getAreaInfo($areaid) {
        $query = array('areaids' => $areaid);
        $url = $this->pdcDomainHost . self::BRM_AREA_INFO . '?' . http_build_query($query);
        return $this->get($url);
    }

    /**
     * 注册
     * @param string $username
     * @param string $password
     * @param string $email
     * @return boolean|array
     */
    final public function register($username, $password, $email, $agent = '', $province = '', $city = '', $company = '') {
        $query = array('userName' => $username, 'password' => $password, 'email' => $email, 'type' => $this->selfProjectId, 'agentId' => $agent, 'companyprovince' => $province, 'companycity' => $city, 'company' => $company, 'checkKey' => $this->timestamp, 'md5key' => md5(self::PRIVATE_KEY . $this->timestamp), 'flagType' => '1');
        $url = $this->braDomainHost . self::BRM_USER_REGISTER . '?' . http_build_query($query);
        return $this->get($url);
    }

    /**
     * 编辑用户资料
     * @param $mid
     * @param $query
     * @return array|mixed
     */
    final public function userEdit($customerId, $city, $province, $company, $tele, $mobile, $email, $address, $sex = '') {
        $query['projectId'] = $this->selfProjectId;
        $query['customerId'] = $customerId;
        $query['city'] = $city;
        $query['province'] = $province;
        $query['company'] = $company;
        $query['tele'] = $tele;
        $query['mobile'] = $mobile;
        $query['sex'] = $sex;
        $query['email'] = $email;
        $query['address'] = $address;

        $result = $this->post($this->braDomainHost . self::BRM_USER_EDIT, $query);
        return $result;
    }

    /**
     * 获取产品列表接口
     * @return boolean
     */
    final public function projectVersion($projectId = '') {
        $query = array('projectId' => $projectId ? $projectId : $this->selfProjectId);
        $url = $this->braDomainHost . self::BRM_PROJECT_VERSION . '?' . http_build_query($query);
        $result = $this->get($url);
        if (!is_array($result) || $result['s'] < 0)
            return false;
        return $result['r'];
    }

    /**
     * 购买产品
     * @param type $mid
     * @param type $version
     * @param type $year
     * @return type
     */
    final public function buyProductComplete($mid, $version, $year) {
        $query = array('type' => 1, 'customerId' => $mid, 'projectId' => $this->selfProjectId, 'version' => $version, 'BuyYear' => $year, 'md5key' => md5(self::PRIVATE_KEY . $this->timestamp), 'checkKey' => $this->timestamp);
        $url = $this->braOldDomainHost . self::BRM_PROJECT_BUY . '?' . http_build_query($query);
        return $this->get($url);
    }

    final public function s_get($url) {
        return $this->get($url);
    }

    final public function s_post($url, $data) {
        return $this->post($url, $data);
    }

    private function post($url, $data) {
        $query = '';
        if (is_string($data)) {
            $query = $data;
        } elseif (is_array($data)) {
            $query = http_build_query($data);
        }
        $url = "{$url}?" . $query;
        $msg = '请求连接为:' . $url;

        $ch = curl_init();
        $timeout = 15;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $file_contents = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $msg.= '返回状态:' . $code . '返回值为:' . $file_contents;
        if ($code == 200) {
            $data = json_decode($file_contents, true);
            if ($data['s'] < 1) {
//                sendEmail("xmlijian@vip.qq.com",'请求失败,状态码为:'.$code,$msg);
            }
            return $data;
        }
//        sendEmail("xmlijian@vip.qq.com",'请求失败,状态码为:'.$code,$msg);
        return array('s' => -1, 'm' => '', 'r' => '请求失败,状态码为:' . $code);
    }

    private function get($url) {
        $ch = curl_init();
        $timeout = 15;
        $msg = '请求连接为:' . $url;
        Log::write($msg);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $file_contents = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($code == 200) {
            return json_decode($file_contents, true);
        }
//        sendEmail("xmlijian@vip.qq.com",'请求失败,状态码为:'.$code,$msg);
        return array('s' => -1, 'm' => '', 'r' => '请求失败,状态码为:' . $code);
    }

    final public function p_get($url) {
        $ch = curl_init();
        $timeout = 15;
        $msg = '请求连接为:' . $url;
        Log::write($msg);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $file_contents = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
//        sendEmail("xmlijian@vip.qq.com",'请求失败,状态码为:'.$code,$msg);
        return array('code' => $code, 'data' => $file_contents, 'r' => '请求失败,状态码为:' . $code);
    }

}

?>