<?php
/**
 * Bizcn.class.php
 * Author: Jansen
 * QQ: 6206574
 * Date: 2015-11-02
 */

namespace Common\Extend\Domain\Driver;


use Common\Extend\Domain\DomainApiBase;

class Bizcn extends DomainApiBase{
    static $ERROR_CODE = array(
        "200" => "操作成功",
        "202" => "成功下订单",
        "212" => "DNS可以注册",
        "213" => "DNS已经被注册",
        "402" => "帐上金额不足",
        "500" => "非法的命令",
        "504" => "缺少的必要的属性名称",
        "505" => "错误的属性值(如域名错误)",
        "513" => "域名DNS不存在或者无法在CNNIC注册",
        "514" => "创建用户失败，联系人信息不符合注册局要求",
        "521" => "系统忙，请稍后再试",
        "523" => "该域名记录正在转移中",
        "525" => "该域名转移失败",
        "529" => "没权限修改该域名",
        "531" => "域名状态status为禁止修改whois",
        "554" => "注册失败，域名已经被注册",
        "555" => "注册时间或者过期时间不符"
    );
    static $WDSL_URL;
    /**
     * @var string $username 代理商帐号
     */
    private $username = '';
    /**
     * @var string $password 代理商帐号所属密码
     */
    private $password = '';
    /**
     * @var string $ns1 主DNS
     */
    private $ns1 = '';
    /**
     * @var string $ns2 备用DNS
     */
    private $ns2 = '';
    private $ns1ip = '';
    private $ns2ip = '';
    public function __construct(){
        $config = C('DOMAIN_CONFIG.BIZCN');
        $this->username = $config['USERNAME'];
        $this->password = $config['PASSWORD'];
        $this->ns1 = $config['NS1'];
        $this->ns2 = $config['NS2'];
        $this->ns1ip = $config['NS1IP'];
        $this->ns2ip = $config['NS2IP'];
        if (defined('RUN_IN_DEVELOP') && RUN_IN_DEVELOP){
            self::$WDSL_URL = 'https://test.bizcn.com/rrpservices/';
        }else{
            self::$WDSL_URL = 'https://www.bizcn.com/rrpservices/';
        }
    }
    /**
     * 域名查询[completed]
     * @param string $domain
     * @param string $idn
     * @return bool
     */
    public function query($domain, $idn='ENG'){
        $url = self::$WDSL_URL.'checkDomainsService?wsdl';
        $data['check']['name'] = $domain;
        $result = $this->service($url, 'checkDomains', $this->dataMerge($data));
        if ($result === false)  return false;
        if ($result['response']['result']['code'] != 200){
            $this->setError($result);
            return false;
        }
        return ($result['response']['domains']['domain']['avail'] != 1)?false:true;
    }

    /**
     * 域名注册[completed]
     * @param string $domain 待注册域名[中文域名需要先转成punycode]
     * @param int $age 注册年限
     * @param array $info 其他注册资料
     * @param string $idn 所属的国际化域名语言
     * @return bool
     */
    public function register($domain, $age, array $info, $idn='ENG'){
        //todo 判断年限
        $domainSuffix = $this->getDomainPrimarySuffix($domain);
        $url = self::$WDSL_URL.'addDomainService?wsdl';
        $data['create']['domainname'] = $domain;
        $data['create']['term'] = $age;
        $data['create']['dns_host1'] = $this->ns1;
        $data['create']['dns_host2'] = $this->ns2;
        $data['create']['dns_ip1'] = $this->ns1ip;
        $data['create']['dns_ip2'] = $this->ns2ip;
        if ($idn == 'CHI') {
            $data['create']['language'] = 'chinese';
        }
        $data['create']['reg_contact_type'] = $info['reg_contact_type'];
        $data['create']['dom_org'] = $info['en_reg_company'];
        $data['create']['dom_fn'] = $info['en_reg_firstname'];
        $data['create']['dom_ln'] = $info['en_reg_lastname'];
        $data['create']['dom_adr1'] = $info['en_reg_address'];
        $data['create']['dom_ct'] = $info['en_reg_city'];
        $data['create']['dom_st'] = $info['en_reg_province'];
        $data['create']['dom_co'] = $info['cn_reg_country'];
        $data['create']['dom_pc'] = $info['cn_reg_postcode'];
        $data['create']['dom_ph'] = $info['cn_reg_telephone'];
        $data['create']['dom_fax'] = $info['cn_reg_fax'];
        $data['create']['dom_em'] = $info['cn_reg_email'];
        $data['create']['custom_reg1'] = $info['cn_reg_idcard'];
        if ($info['reg_contact_type'] == '1'){
            $data['create']['custom_reg2'] = '';//TODO 企业类型
            if ($domainSuffix == '.us'){
                $data['create']['custom_reg3'] = '';//TODO 企业类型
            }
        }elseif($info['reg_contact_type'] == '0'){
            if (in_array($domainSuffix, array('.hk','.my'))){
                $data['create']['custom_reg3'] = $info['cn_reg_birthday'];
            }
        }
        $data['create']['adm_contact_type'] = $info['adm_contact_type'];
        $data['create']['admi_org'] = $info['en_adm_company'];
        $data['create']['admi_fn'] = $info['en_adm_firstname'];
        $data['create']['admi_ln'] = $info['en_adm_lastname'];
        $data['create']['admi_adr1'] = $info['en_adm_address'];
        $data['create']['admi_ct'] = $info['en_adm_city'];
        $data['create']['admi_st'] = $info['en_adm_province'];
        $data['create']['admi_co'] = $info['cn_adm_country'];
        $data['create']['admi_pc'] = $info['cn_adm_postcode'];
        $data['create']['admi_ph'] = $info['cn_adm_telephone'];
        $data['create']['admi_fax'] = $info['cn_adm_fax'];
        $data['create']['admi_em'] = $info['cn_adm_email'];
        $data['create']['custom_adm1'] = $info['cn_adm_idcard'];
        if ($info['adm_contact_type'] == '1'){
            $data['create']['custom_adm2'] = '';//TODO 企业类型
            if ($domainSuffix == '.us'){
                $data['create']['custom_adm3'] = '';//TODO 企业类型
            }
        }elseif($info['adm_contact_type'] == '0'){
            if (in_array($domainSuffix, array('.hk','.my'))){
                $data['create']['custom_adm3'] = $info['cn_adm_birthday'];
            }
        }
        $data['create']['tec_contact_type'] = $info['tec_contact_type'];
        $data['create']['tech_org'] = $info['en_tec_company'];
        $data['create']['tech_fn'] = $info['en_tec_firstname'];
        $data['create']['tech_ln'] = $info['en_tec_lastname'];
        $data['create']['tech_adr1'] = $info['en_tec_address'];
        $data['create']['tech_ct'] = $info['en_tec_city'];
        $data['create']['tech_st'] = $info['en_tec_province'];
        $data['create']['tech_co'] = $info['cn_tec_country'];
        $data['create']['tech_pc'] = $info['cn_tec_postcode'];
        $data['create']['tech_ph'] = $info['cn_tec_telephone'];
        $data['create']['tech_fax'] = $info['cn_tec_fax'];
        $data['create']['tech_em'] = $info['cn_tec_email'];
        $data['create']['custom_tec1'] = $info['cn_tec_idcard'];
        if ($info['tec_contact_type'] == '1'){
            $data['create']['custom_tec2'] = '';//TODO 企业类型
            if ($domainSuffix == '.us'){
                $data['create']['custom_tec3'] = '';//TODO 企业类型
            }
        }elseif($info['tec_contact_type'] == '0'){
            if (in_array($domainSuffix, array('.hk','.my'))){
                $data['create']['custom_tec3'] = $info['cn_tec_birthday'];
            }
        }
        $data['create']['bil_contact_type'] = $info['bil_contact_type'];
        $data['create']['bill_org'] = $info['en_bil_company'];
        $data['create']['bill_fn'] = $info['en_bil_firstname'];
        $data['create']['bill_ln'] = $info['en_bil_lastname'];
        $data['create']['bill_adr1'] = $info['en_bil_address'];
        $data['create']['bill_ct'] = $info['en_bil_city'];
        $data['create']['bill_st'] = $info['en_bil_province'];
        $data['create']['bill_co'] = $info['cn_bil_country'];
        $data['create']['bill_pc'] = $info['cn_bil_postcode'];
        $data['create']['bill_ph'] = $info['cn_bil_telephone'];
        $data['create']['bill_fax'] = $info['cn_bil_fax'];
        $data['create']['bill_em'] = $info['cn_bil_email'];
        $data['create']['custom_bil1'] = $info['cn_bil_idcard'];
        if ($info['bil_contact_type'] == '1'){
            $data['create']['custom_bil2'] = '';//TODO 企业类型
            if ($domainSuffix == '.us'){
                $data['create']['custom_bil3'] = '';//TODO 企业类型
            }
        }elseif($info['bil_contact_type'] == '0'){
            if (in_array($domainSuffix, array('.hk','.my'))){
                $data['create']['custom_bil3'] = $info['cn_bil_birthday'];
            }
        }
        $data['create']['dom_org_m'] = $info['cn_reg_company'];
        $data['create']['dom_fn_m'] = $info['cn_reg_firstname'];
        $data['create']['dom_ln_m'] = $info['cn_reg_lastname'];
        $data['create']['dom_adr_m'] = $info['cn_reg_address'];
        $data['create']['dom_ct_m'] = $info['cn_reg_city_zh'];
        $data['create']['dom_st_m'] = $info['cn_reg_province_zh'];
        $data['create']['admi_org_m'] = $info['cn_adm_company'];
        $data['create']['admi_fn_m'] = $info['cn_adm_firstname'];
        $data['create']['admi_ln_m'] = $info['cn_adm_lastname'];
        $data['create']['admi_adr_m'] = $info['cn_adm_address'];
        $data['create']['admi_ct_m'] = $info['cn_adm_city_zh'];
        $data['create']['admi_st_m'] = $info['cn_adm_province_zh'];
        //.asia后缀专属
        if ($domainSuffix == '.asia'){
            $data['create']['identform'] = 'passport';//固定设置为身份证
            $data['create']['identnumber'] = $info['cn_reg_idcard'];//固定设置为域名所有人的身份证号码
        }
        $result = $this->service($url, 'addDomain', $this->dataMerge($data));
        if ($result === false)  return false;
        if ($result['response']['result']['code'] != 200){
            $this->setError($result);
            return false;
        }
        //TODO 返回orderId是否要存储?
        return $result['response']['orderid']?true:false;
    }

    /**
     * 域名续费[completed]
     * @param string $domain 待续费域名
     * @param int $age 续费年限
     * @return bool
     * @todo 返回的是订单ID,需要考虑是否保存这个订单ID
     * @todo age只支持1,2,3,4,5,需要注意处理超过年限的情况
     */
    public function renew($domain, $age){
        $url = self::$WDSL_URL.'renewDomainService?wsdl';
        $data['renew']['domain'] = $domain;
        $data['renew']['term'] = $age;
        $result = $this->service($url, 'renewDomain', $this->dataMerge($data));
        if ($result === false)  return false;
        if ($result['response']['result']['code'] != 200){
            $this->setError($result);
            return false;
        }
        return true;
    }

    /**
     * 域名删除[completed]
     * @param string $domain 待删除域名
     * @return bool
     */
    public function delete($domain){
        return false;
    }

    /**
     * 域名转移[转入]
     * @param string $domain 待转入的域名
     * @param string $code 转移密码
     * @param array $info 转移所需的资料
     * @return bool
     */
    public function transferIn($domain, $code, array $info){
        return true;
    }

    /**
     * 域名转移[转出][completed]
     * @param string $domain
     * @return bool
     */
    public function transferOut($domain){
        $url = self::$WDSL_URL.'getEppcodeServiceImpl?wsdl';
        $data['domainname'] = $domain;
        $result = $this->service($url, 'getEppcode', $this->dataMerge($data));
        if ($result === false)  return false;
        if ($result['response']['result']['code'] != 200){
            $this->setError($result);
            return false;
        }
        return true;
    }

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
     * @todo 状态描述翻译需要完善
     */
    public function transferInStatus($domain){
        return true;
    }

    /**
     * 域名锁定[设置域名保护状态][completed]
     * @param string $domain 待保护域名
     * @return bool
     */
    public function lock($domain){
        $url = self::$WDSL_URL.'lockDomainService?wsdl';
        $data['domainname'] = $domain;
        $result = $this->service($url, 'lockDomain', $this->dataMerge($data));
        if ($result === false)  return false;
        if ($result['response']['result']['code'] != 200){
            $this->setError($result);
            return false;
        }
        return true;
    }

    /**
     * 域名解锁[设置域名保护状态][completed]
     * @param string $domain
     * @return bool
     */
    public function unlock($domain){
        $url = self::$WDSL_URL.'unLockDomainService?wsdl';
        $data['domainname'] = $domain;
        $result = $this->service($url, 'unLockDomain', $this->dataMerge($data));
        if ($result === false)  return false;
        if ($result['response']['result']['code'] != 200){
            $this->setError($result);
            return false;
        }
        return true;
    }

    /**
     * 获取域名的锁定状态[completed]
     * @param string $domain
     * @return bool
     */
    public function lockStatus($domain){
        $url = self::$WDSL_URL.'infoDomainLockService?wsdl';
        $data['domainname'] = $domain;
        $result = $this->service($url, 'infoDomainLock', $this->dataMerge($data));
        if ($result === false)  return false;
        if ($result['response']['result']['code'] != 200){
            $this->setError($result);
            return false;
        }
        return $result['response']['lock'];
    }

    /**
     * 修改WHOIS信息
     * @param $domain
     * @param array $info
     * @return bool
     */
    public function whoisEdit($domain, array $info){
        if ($this->isChineseSuffix($this->decode($domain))){
            $result = $this->whoisEditForOwnerCN($domain, $info);
            if ($result === false) return false;
            $result = $this->whoisEditForAdminCN($domain, $info);
            if ($result === false) return false;
            return true;
        }else {
            $result = $this->whoisEditForOwner($domain, $info);
            if ($result === false) return false;
            $result = $this->whoisEditForAdmin($domain, $info);
            if ($result === false) return false;
            $result = $this->whoisEditForTech($domain, $info);
            if ($result === false) return false;
            $result = $this->whoisEditForBiller($domain, $info);
            if ($result === false) return false;
            return true;
        }
    }

    /**
     * 开启WHOIS隐私保护
     * @param $domain
     * @return bool
     */
    public function whoisPrivacyLock($domain){
        return true;
    }

    /**
     * 关闭WHOIS隐私保护
     * @param $domain
     * @return bool
     */
    public function whoisPrivacyUnlock($domain){
        return true;
    }

    /**
     * WHOIS隐私保护启用状态查询
     * @param $domain
     * @return bool
     */
    public function whoisPrivacyQuery($domain){
        return true;
    }

    /**
     * 修改域名绑定的DNS
     * @param $domain
     * @param array $dns
     * @return bool
     */
    public function dnsEdit($domain, array $dns){
        $url = self::$WDSL_URL.'modDomainDnsService?wsdl';
        $data['moddomaindns']['domainname'] = $domain;
        $data['moddomaindns']['dns_host'] = $dns;
        $result = $this->service($url, 'modDomainDns', $this->dataMerge($data));
        if ($result === false)  return false;
        if ($result['response']['result']['code'] != 200){
            $this->setError($result);
            return false;
        }
        return true;
    }

    /**
     * 修改DNS服务器[completed]
     * @param $dns
     * @param array $ip
     * @return bool
     */
    public function dnsServerEdit($dns, array $ip=array()){
        $url = self::$WDSL_URL.'modNameserverService?wsdl';
        $data['modnameserver']['hostname'] = $dns;
        $data['modnameserver']['oldip'] = $ip[0];
        $data['modnameserver']['newip'] = $ip[1];
        $result = $this->service($url, 'modNameserver', $this->dataMerge($data));
        if ($result === false)  return false;
        if ($result['response']['result']['code'] != 200){
            $this->setError($result);
            return false;
        }
        return true;
    }

    /**
     * 注册DNS服务器[completed]
     * @param $dns
     * @param array $ip
     * @param array $reg
     * @return bool
     */
    public function dnsServerRegister($dns, array $ip=array(), array $reg=array()){
        $url = self::$WDSL_URL.'addNameserverService?wsdl';
        $data['addnameserver']['hostname'] = $dns;
        $data['addnameserver']['ip'] = $ip[0];
        $result = $this->service($url, 'addNameserver', $this->dataMerge($data));
        if ($result === false)  return false;
        if ($result['response']['result']['code'] != 200){
            $this->setError($result);
            return false;
        }
        return true;
    }

    /**
     * 删除DNS服务器[completed]
     * @param $dns
     * @param array $reg
     * @return bool
     */
    public function dnsServerDelete($dns, array $reg=array()){
        $url = self::$WDSL_URL.'delNameserverService?wsdl';
        $data['hostname'] = $dns;
        $result = $this->service($url, 'delNameserver', $this->dataMerge($data));
        if ($result === false)  return false;
        if ($result['response']['result']['code'] != 200){
            $this->setError($result);
            return false;
        }
        return true;
    }

    /**
     * 设置错误信息
     * @param string $error 接口返回的原始错误信息
     */
    protected function setError($error){
        $this->error = array(
            'status'=>$error['response']['result']['code'],
            'message'=>self::$ERROR_CODE[$error['response']['result']['code']]
        );
    }

    /**
     * 修改WHOIS信息的域名所有人资料[completed]
     * @param string $domain
     * @param array $info
     * @return bool
     */
    private function whoisEditForOwner($domain, array $info){
        $url = self::$WDSL_URL.'modDomainOwnerService?wsdl';
        $data['modowner']['domainname'] = $domain;
        $data['modowner']['dom_fn'] = $info['en_reg_firstname'];
        $data['modowner']['dom_ln'] = $info['en_reg_lastname'];
        $data['modowner']['dom_adr1'] = $info['en_reg_address'];
        $data['modowner']['dom_st'] = $info['en_reg_province'];
        $data['modowner']['dom_ct'] = $info['en_reg_city'];
        $data['modowner']['dom_pc'] = $info['cn_reg_postcode'];
        $data['modowner']['dom_ph'] = $info['cn_reg_telephone'];
        $data['modowner']['dom_fax'] = $info['cn_reg_fax'];
        $data['modowner']['dom_co'] = $info['cn_reg_country'];
        $data['modowner']['dom_em'] = $info['cn_reg_email'];
        $data['modowner']['dom_org'] = $info['en_reg_company'];
        $data['modowner']['dom_org_m'] = $info['cn_reg_company'];
        $data['modowner']['dom_fn_m'] = $info['cn_reg_firstname'];
        $data['modowner']['dom_ln_m'] = $info['cn_reg_lastname'];
        $data['modowner']['dom_adr_m'] = $info['cn_reg_address'];
        $data['modowner']['dom_st_m'] = $info['cn_reg_province_zh'];
        $data['modowner']['dom_ct_m'] = $info['cn_reg_city_zh'];
        $result = $this->service($url, 'modOwner', $this->dataMerge($data));
        if ($result === false)  return false;
        if ($result['response']['result']['code'] != 200){
            $this->setError($result);
            return false;
        }
        return true;
    }

    /**
     * 修改WHOIS信息的域名管理员资料[completed]
     * @param string $domain
     * @param array $info
     * @return bool
     */
    private function whoisEditForAdmin($domain, array $info){
        $url = self::$WDSL_URL.'modDomainAdminService?wsdl';
        $data['modadmin']['domainname'] = $domain;
        $data['modadmin']['admi_fn'] = $info['en_adm_firstname'];
        $data['modadmin']['admi_ln'] = $info['en_adm_lastname'];
        $data['modadmin']['admi_adr1'] = $info['en_adm_address'];
        $data['modadmin']['admi_st'] = $info['en_adm_province'];
        $data['modadmin']['admi_ct'] = $info['en_adm_city'];
        $data['modadmin']['admi_pc'] = $info['cn_adm_postcode'];
        $data['modadmin']['admi_ph'] = $info['cn_adm_telephone'];
        $data['modadmin']['admi_fax'] = $info['cn_adm_fax'];
        $data['modadmin']['admi_co'] = $info['cn_adm_country'];
        $data['modadmin']['admi_em'] = $info['cn_adm_email'];
        $data['modadmin']['admi_org'] = $info['en_adm_company'];
        $data['modadmin']['admi_org_m'] = $info['cn_adm_company'];
        $data['modadmin']['admi_fn_m'] = $info['cn_adm_firstname'];
        $data['modadmin']['admi_ln_m'] = $info['cn_adm_lastname'];
        $data['modadmin']['admi_adr_m'] = $info['cn_adm_address'];
        $data['modadmin']['admi_st_m'] = $info['cn_adm_province_zh'];
        $data['modadmin']['admi_ct_m'] = $info['cn_adm_city_zh'];
        $result = $this->service($url, 'modAdmin', $this->dataMerge($data));
        if ($result === false)  return false;
        if ($result['response']['result']['code'] != 200){
            $this->setError($result);
            return false;
        }
        return true;
    }

    /**
     * 修改WHOIS信息的域名技术员资料[completed]
     * @param string $domain
     * @param array $info
     * @return bool
     */
    private function whoisEditForTech($domain, array $info){
        $url = self::$WDSL_URL.'modDomainTechService?wsdl';
        $data['modtech']['domainname'] = $domain;
        $data['modtech']['tech_fn'] = $info['en_tec_firstname'];
        $data['modtech']['tech_ln'] = $info['en_tec_lastname'];
        $data['modtech']['tech_adr1'] = $info['en_tec_address'];
        $data['modtech']['tech_st'] = $info['en_tec_province'];
        $data['modtech']['tech_ct'] = $info['en_tec_city'];
        $data['modtech']['tech_pc'] = $info['cn_tec_postcode'];
        $data['modtech']['tech_ph'] = $info['cn_tec_telephone'];
        $data['modtech']['tech_fax'] = $info['cn_tec_fax'];
        $data['modtech']['tech_co'] = $info['cn_tec_country'];
        $data['modtech']['tech_em'] = $info['cn_tec_email'];
        $data['modtech']['tech_org'] = $info['en_tec_company'];
        $data['modtech']['tech_org_m'] = $info['cn_tec_company'];
        $data['modtech']['tech_fn_m'] = $info['cn_tec_firstname'];
        $data['modtech']['tech_ln_m'] = $info['cn_tec_lastname'];
        $data['modtech']['tech_adr_m'] = $info['cn_tec_address'];
        $data['modtech']['tech_st_m'] = $info['cn_tec_province_zh'];
        $data['modtech']['tech_ct_m'] = $info['cn_tec_city_zh'];
        $result = $this->service($url, 'modTech', $this->dataMerge($data));
        if ($result === false)  return false;
        if ($result['response']['result']['code'] != 200){
            $this->setError($result);
            return false;
        }
        return true;
    }

    /**
     * 修改WHOIS信息的域名缴费员资料[completed]
     * @param string $domain
     * @param array $info
     * @return bool
     */
    private function whoisEditForBiller($domain, array $info){
        $url = self::$WDSL_URL.'modDomainBillingService?wsdl';
        $data['modbilling']['domainname'] = $domain;
        $data['modbilling']['bill_fn'] = $info['en_bil_firstname'];
        $data['modbilling']['bill_ln'] = $info['en_bil_lastname'];
        $data['modbilling']['bill_adr1'] = $info['en_bil_address'];
        $data['modbilling']['bill_st'] = $info['en_bil_province'];
        $data['modbilling']['bill_ct'] = $info['en_bil_city'];
        $data['modbilling']['bill_pc'] = $info['cn_bil_postcode'];
        $data['modbilling']['bill_ph'] = $info['cn_bil_telephone'];
        $data['modbilling']['bill_fax'] = $info['cn_bil_fax'];
        $data['modbilling']['bill_co'] = $info['cn_bil_country'];
        $data['modbilling']['bill_em'] = $info['cn_bil_email'];
        $data['modbilling']['bill_org'] = $info['en_bil_company'];
        $data['modbilling']['bill_org_m'] = $info['cn_bil_company'];
        $data['modbilling']['bill_fn_m'] = $info['cn_bil_firstname'];
        $data['modbilling']['bill_ln_m'] = $info['cn_bil_lastname'];
        $data['modbilling']['bill_adr_m'] = $info['cn_bil_address'];
        $data['modbilling']['bill_st_m'] = $info['cn_bil_province_zh'];
        $data['modbilling']['bill_ct_m'] = $info['cn_bil_city_zh'];
        $result = $this->service($url, 'modBilling', $this->dataMerge($data));
        if ($result === false)  return false;
        if ($result['response']['result']['code'] != 200){
            $this->setError($result);
            return false;
        }
        return true;
    }

    /**
     * 修改中文域名的WHOIS信息的域名所有者资料
     * @param string $domain
     * @param array $info
     * @return bool
     */
    private function whoisEditForOwnerCN($domain, array $info){
        $url = self::$WDSL_URL.'modChineseDomainOwnerService?wsdl';
        $data['modowner']['domainname'] = $domain;
        $data['modowner']['dom_adr1'] = $info['en_reg_address'];
        $data['modowner']['dom_adr_m'] = $info['cn_reg_address'];
        $data['modowner']['dom_co'] = $info['cn_reg_country'];
        $data['modowner']['dom_ct'] = $info['en_reg_city'];
        $data['modowner']['dom_ct_m'] = $info['cn_reg_city_zh'];
        $data['modowner']['dom_em'] = $info['cn_reg_email'];
        $data['modowner']['dom_fax'] = $info['cn_reg_fax'];
        $data['modowner']['dom_pc'] = $info['cn_reg_postcode'];
        $data['modowner']['dom_ph'] = $info['cn_reg_telephone'];
        $data['modowner']['dom_st'] = $info['en_reg_province'];
        $data['modowner']['dom_st_m'] = $info['cn_reg_province_zh'];
        $data['modowner']['type'] = 'owner';
        $result = $this->service($url, 'modOwner', $this->dataMerge($data));
        if ($result === false)  return false;
        if ($result['response']['result']['code'] != 200){
            $this->setError($result);
            return false;
        }
        return true;
    }

    /**
     * 修改中文域名的WHOIS信息的域名管理员资料
     * @param string $domain
     * @param array $info
     * @return bool
     */
    private function whoisEditForAdminCN($domain, array $info){
        $url = self::$WDSL_URL.'modChineseDomainAdminService?wsdl';
        $data['modadmin']['domainname'] = $domain;
        $data['modadmin']['admi_adr1'] = $info['en_adm_address'];
        $data['modadmin']['admi_adr_m'] = $info['cn_adm_address'];
        $data['modadmin']['admi_co'] = $info['cn_adm_country'];
        $data['modadmin']['admi_ct'] = $info['en_adm_city'];
        $data['modadmin']['admi_ct_m'] = $info['cn_adm_city_zh'];
        $data['modadmin']['admi_em'] = $info['cn_adm_email'];
        $data['modadmin']['admi_fax'] = $info['cn_adm_fax'];
        $data['modadmin']['admi_fn'] = $info['en_adm_firstname'];
        $data['modadmin']['admi_fn_m'] = $info['cn_adm_firstname'];
        $data['modadmin']['admi_ln'] = $info['en_adm_lastname'];
        $data['modadmin']['admi_ln_m'] = $info['cn_adm_lastname'];
        $data['modadmin']['admi_org'] = $info['en_adm_company'];
        $data['modadmin']['admi_org_m'] = $info['cn_adm_company'];
        $data['modadmin']['admi_ph'] = $info['cn_adm_telephone'];
        $data['modadmin']['admi_st'] = $info['en_adm_province'];
        $data['modadmin']['admi_st_m'] = $info['cn_adm_province_zh'];
        $data['modowner']['dom_adr_m'] = $info['cn_reg_address'];
        $data['modowner']['dom_ct_m'] = $info['cn_reg_city_zh'];
        $data['modadmin']['type'] = 'admin';
        $result = $this->service($url, 'modAdmin', $this->dataMerge($data));
        if ($result === false)  return false;
        if ($result['response']['result']['code'] != 200){
            $this->setError($result);
            return false;
        }
        return true;
    }

    /**
     * 合并数据
     * @param array $data
     * @return array
     */
    private function dataMerge(array $data){
        $data1['user']['name'] = $this->username;
        $data1['user']['password'] = $this->password;
        return array_merge($data1, $data);
    }
}