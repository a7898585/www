<?php
/**
 * 域名注册接口，对接WEBNIC
 * Author: Jansen
 * QQ: 6206574
 * Date: 2015-10-05
 */

namespace Common\Extend\Domain\Driver;


use Common\Extend\Domain\DomainApiBase;
use Think\Log;

class Webnic extends DomainApiBase{
    /**
     * @var string $username WEBNIC的代理商帐号
     */
    private $username = '';
    /**
     * @var string $password WEBNIC的代理商帐号所属密码
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
    private $myDomain;
    private $whoisDomain;
    public function __construct(){
        $config = C('DOMAIN_CONFIG.WEBNIC');
        $this->username = $config['USERNAME'];
        $this->password = $config['PASSWORD'];
        $this->ns1 = $config['NS1'];
        $this->ns2 = $config['NS2'];
        $this->ns1ip = $config['NS1IP'];
        $this->ns2ip = $config['NS2IP'];
        if (defined('RUN_IN_DEVELOP') && RUN_IN_DEVELOP){
            $this->myDomain = 'https://ote.webnic.cc';
            $this->whoisDomain = 'http://iwhois.webnic.cc';
        }else{
            $this->myDomain = 'https://my.webnic.cc';
            $this->whoisDomain = 'http://iwhois.webnic.cc';
        }
    }

    /**
     * 域名查询
     * @param string $domain 待注册域名[中文域名需要先转成punycode]
     * @param string $idn
     * @return bool
     */
    public function query($domain, $idn='ENG'){
        $url = $this->myDomain.'/jsp/pn_qry.jsp';
        $data['source'] = $this->username;
        $data['domain'] = $domain;
        $data['lang'] = $idn;
        $result = $this->_post($url, $data, false);
        if ($result === false){
            $this->setError("-1\t接口异常");
            return $result;
        }
        list($status, $message) = explode("\t", $result);
        if ($status !== '0') {
            $this->setError($result);
            return false;
        }
        return true;
    }

    /**
     * 域名注册
     * @param string $domain 待注册域名[中文域名需要先转成punycode]
     * @param int $age 注册年限
     * @param array $info 其他注册资料
     * @param string $idn 所属的国际化域名语言
     * @return bool
     * @todo 目前默认为个人注册,后续开放企业注册时,本方法需要调整
     * @todo .XXX后缀需要完善参数
     */
    public function register($domain, $age, array $info, $idn='ENG'){
        //做一些检测
        $domainSuffix = $this->getDomainFullSuffix($domain);
        if ($domainSuffix == '.tel'){
            //.TEL 只能使用这固定的DNS
            $this->ns1 = 'A0.CTH.DNS.NIC.TEL';
            $this->ns2 = 'D0.CTH.DNS.NIC.TEL';
        }
        if (in_array($domainSuffix, array('.vn', '.in', '.kr', '.th', '.my', '.ph', '.jp', '.eu'))){
            $data['ns1ip'] = $this->ns1ip;
            $data['ns2ip'] = $this->ns2ip;
        }
        $url = $this->myDomain.'/jsp/pn_newreg.jsp';
        $data['source'] = $this->username;
        $data['otime'] = date('Y-m-d H:i:s');
        $data['ochecksum'] = $this->checksum();
        $data['username'] = $this->username();
        $data['newuser'] = 'old';
        $data['proxy'] = '1';
        $data['domainname'] = $domain;
        $data['lang'] = $idn;
        $data['term'] = $age;
        $data['ns1'] = $this->ns1;
        $data['ns2'] = $this->ns2;
        $data['reg_contact_type'] = $info['reg_contact_type'];
        $data['reg_company'] = $info['en_reg_company'];
        $data['reg_fname'] = $info['en_reg_firstname'];
        $data['reg_lname'] = $info['en_reg_lastname'];
        $data['reg_addr1'] = $info['en_reg_address'];
        $data['reg_state'] = $info['en_reg_province'];
        $data['reg_city'] = $info['en_reg_city'];
        $data['reg_postcode'] = $info['cn_reg_postcode'];
        $data['reg_telephone'] = $info['cn_reg_telephone'];
        $data['reg_fax'] = $info['cn_reg_fax'];
        $data['reg_country'] = $info['cn_reg_country'];
        $data['reg_email'] = $info['cn_reg_email'];
        $data['custom_reg1'] = $info['cn_reg_idcard'];
        if ($info['reg_contact_type'] == '1'){
            $data['custom_reg2'] = '';//TODO 企业类型
            if ($domainSuffix == '.us'){
                $data['custom_reg3'] = '';//TODO 企业类型
            }
        }elseif($info['reg_contact_type'] == '0'){
            if (in_array($domainSuffix, array('.hk','.my'))){
                $data['custom_reg3'] = $info['cn_reg_birthday'];
            }
        }
        $data['adm_contact_type'] = $info['adm_contact_type'];
        $data['adm_company'] = $info['en_adm_company'];
        $data['adm_fname'] = $info['en_adm_firstname'];
        $data['adm_lname'] = $info['en_adm_lastname'];
        $data['adm_addr1'] = $info['en_adm_address'];
        $data['adm_state'] = $info['en_adm_province'];
        $data['adm_city'] = $info['en_adm_city'];
        $data['adm_postcode'] = $info['cn_adm_postcode'];
        $data['adm_telephone'] = $info['cn_adm_telephone'];
        $data['adm_fax'] = $info['cn_adm_fax'];
        $data['adm_country'] = $info['cn_adm_country'];
        $data['adm_email'] = $info['cn_adm_email'];
        $data['custom_adm1'] = $info['cn_adm_idcard'];
        if ($info['adm_contact_type'] == '1'){
            $data['custom_adm2'] = '';//TODO 企业类型
            if ($domainSuffix == '.us'){
                $data['custom_adm3'] = '';//TODO 企业类型
            }
        }elseif($info['adm_contact_type'] == '0'){
            if (in_array($domainSuffix, array('.hk','.my'))){
                $data['custom_adm3'] = $info['cn_adm_birthday'];
            }
        }
        $data['tec_contact_type'] = $info['tec_contact_type'];
        $data['tec_company'] = $info['en_tec_company'];
        $data['tec_fname'] = $info['en_tec_firstname'];
        $data['tec_lname'] = $info['en_tec_lastname'];
        $data['tec_addr1'] = $info['en_tec_address'];
        $data['tec_state'] = $info['en_tec_province'];
        $data['tec_city'] = $info['en_tec_city'];
        $data['tec_postcode'] = $info['cn_tec_postcode'];
        $data['tec_telephone'] = $info['cn_tec_telephone'];
        $data['tec_fax'] = $info['cn_tec_fax'];
        $data['tec_country'] = $info['cn_tec_country'];
        $data['tec_email'] = $info['cn_tec_email'];
        $data['custom_tec1'] = $info['cn_tec_idcard'];
        if ($info['tec_contact_type'] == '1'){
            $data['custom_tec2'] = '';//TODO 企业类型
            if ($domainSuffix == '.us'){
                $data['custom_tec3'] = '';//TODO 企业类型
            }
        }elseif($info['tec_contact_type'] == '0'){
            if (in_array($domainSuffix, array('.hk','.my'))){
                $data['custom_tec3'] = $info['cn_tec_birthday'];
            }
        }
        $data['bil_contact_type'] = $info['bil_contact_type'];
        $data['bil_company'] = $info['en_bil_company'];
        $data['bil_fname'] = $info['en_bil_firstname'];
        $data['bil_lname'] = $info['en_bil_lastname'];
        $data['bil_addr1'] = $info['en_bil_address'];
        $data['bil_state'] = $info['en_bil_province'];
        $data['bil_city'] = $info['en_bil_city'];
        $data['bil_postcode'] = $info['cn_bil_postcode'];
        $data['bil_telephone'] = $info['cn_bil_telephone'];
        $data['bil_fax'] = $info['cn_bil_fax'];
        $data['bil_country'] = $info['cn_bil_country'];
        $data['bil_email'] = $info['cn_bil_email'];
        $data['custom_bil1'] = $info['cn_bil_idcard'];
        if ($info['bil_contact_type'] == '1'){
            $data['custom_bil2'] = '';//TODO 企业类型
            if ($domainSuffix == '.us'){
                $data['custom_bil3'] = '';//TODO 企业类型
            }
        }elseif($info['bil_contact_type'] == '0'){
            if (in_array($domainSuffix, array('.hk','.my'))){
                $data['custom_bil3'] = $info['cn_bil_birthday'];
            }
        }
        //.travel后缀专属
        if ($domainSuffix == '.travel'){//TODO 专属后缀必填下面两参数
            $data['uin'] = '';
            $data['refno'] = '';
        }
        $result = $this->_post($url, $data);
        if ($result === false){
            $this->setError("-1\t接口异常");
            return $result;
        }
        list($status, $message) = explode("\t", $result);
        if ($status !== '0'){
            $this->setError($result);
            return false;
        }
        return true;
    }

    /**
     * 域名续费
     * @param string $domain 待续费域名
     * @param int $age 续费年限
     * @return bool
     */
    public function renew($domain, $age){
        $url = $this->myDomain.'/jsp/pn_renew.jsp';
        $data['source'] = $this->username;
        $data['otime'] = date('Y-m-d H:i:s');
        $data['ochecksum'] = $this->checksum();
        $data['proxy'] = '1';
        $data['domainname'] = $domain;
        $data['term'] = $age;
        $result = $this->_post($url, $data);
        if ($result === false){
            $this->setError("-1\t接口异常");
            return $result;
        }
        list($status, $message) = explode("\t", $result);
        if ($status !== '0'){
            $this->setError($result);
            return false;
        }
        return true;
    }

    /**
     * 域名删除
     * @param string $domain 待删除域名
     * @return bool
     */
    public function delete($domain){
        $url = $this->myDomain.'/jsp/pn_del.jsp';
        $data['source'] = $this->username;
        $data['otime'] = date('Y-m-d H:i:s');
        $data['ochecksum'] = $this->checksum();
        $data['domainname'] = $domain;
        $result = $this->_post($url, $data);
        if ($result === false){
            $this->setError("-1\t接口异常");
            return $result;
        }
        list($status, $message) = explode("\t", $result);
        if ($status !== '0'){
            $this->setError($result);
            return false;
        }
        return true;
    }

    /**
     * 域名转移[转入]
     * @param string $domain 待转入的域名
     * @param string $code 转移密码
     * @param array $info 转移所需的资料
     * @return bool
     */
    public function transferIn($domain, $code, array $info){
        $domainSuffix = $this->getDomainFullSuffix($domain);
        $url = $this->myDomain.'/jsp/pn_newtransfer.jsp';
        $data['source'] = $this->username;
        $data['otime'] = date('Y-m-d H:i:s');
        $data['ochecksum'] = $this->checksum();
        $data['username'] = $this->username();
        $data['userstatus'] = 'old';
        $data['proxy'] = '1';
        $data['domain'] = $domain;
        $data['authinfo'] = $code;
        $data['reg_contact_type'] = $info['reg_contact_type'];
        $data['reg_company'] = $info['en_reg_company'];
        $data['reg_fname'] = $info['en_reg_firstname'];
        $data['reg_lname'] = $info['en_reg_lastname'];
        $data['reg_addr1'] = $info['en_reg_address'];
        $data['reg_state'] = $info['en_reg_province'];
        $data['reg_city'] = $info['en_reg_city'];
        $data['reg_postcode'] = $info['cn_reg_postcode'];
        $data['reg_telephone'] = $info['cn_reg_telephone'];
        $data['reg_fax'] = $info['cn_reg_fax'];
        $data['reg_country'] = $info['cn_reg_country'];
        $data['reg_email'] = $info['cn_reg_email'];
        $data['custom_reg1'] = $info['cn_reg_idcard'];
        if ($info['reg_contact_type'] == '1'){
            $data['custom_reg2'] = '';//TODO 企业类型
            if ($domainSuffix == '.us'){
                $data['custom_reg3'] = '';//TODO 企业类型
            }
        }elseif($info['reg_contact_type'] == '0'){
            if (in_array($domainSuffix, array('.hk','.my'))){
                $data['custom_reg3'] = $info['cn_reg_birthday'];
            }
        }
        $data['adm_contact_type'] = $info['adm_contact_type'];
        $data['adm_company'] = $info['en_adm_company'];
        $data['adm_fname'] = $info['en_adm_firstname'];
        $data['adm_lname'] = $info['en_adm_lastname'];
        $data['adm_addr1'] = $info['en_adm_address'];
        $data['adm_state'] = $info['en_adm_province'];
        $data['adm_city'] = $info['en_adm_city'];
        $data['adm_postcode'] = $info['cn_adm_postcode'];
        $data['adm_telephone'] = $info['cn_adm_telephone'];
        $data['adm_fax'] = $info['cn_adm_fax'];
        $data['adm_country'] = $info['cn_adm_country'];
        $data['adm_email'] = $info['cn_adm_email'];
        $data['custom_adm1'] = $info['cn_adm_idcard'];
        if ($info['adm_contact_type'] == '1'){
            $data['custom_adm2'] = '';//TODO 企业类型
            if ($domainSuffix == '.us'){
                $data['custom_adm3'] = '';//TODO 企业类型
            }
        }elseif($info['adm_contact_type'] == '0'){
            if (in_array($domainSuffix, array('.hk','.my'))){
                $data['custom_adm3'] = $info['cn_adm_birthday'];
            }
        }
        $data['tec_contact_type'] = $info['tec_contact_type'];
        $data['tec_company'] = $info['en_tec_company'];
        $data['tec_fname'] = $info['en_tec_firstname'];
        $data['tec_lname'] = $info['en_tec_lastname'];
        $data['tec_addr1'] = $info['en_tec_address'];
        $data['tec_state'] = $info['en_tec_province'];
        $data['tec_city'] = $info['en_tec_city'];
        $data['tec_postcode'] = $info['cn_tec_postcode'];
        $data['tec_telephone'] = $info['cn_tec_telephone'];
        $data['tec_fax'] = $info['cn_tec_fax'];
        $data['tec_country'] = $info['cn_tec_country'];
        $data['tec_email'] = $info['cn_tec_email'];
        $data['custom_tec1'] = $info['cn_tec_idcard'];
        if ($info['tec_contact_type'] == '1'){
            $data['custom_tec2'] = '';//TODO 企业类型
            if ($domainSuffix == '.us'){
                $data['custom_tec3'] = '';//TODO 企业类型
            }
        }elseif($info['tec_contact_type'] == '0'){
            if (in_array($domainSuffix, array('.hk','.my'))){
                $data['custom_tec3'] = $info['cn_tec_birthday'];
            }
        }
        $data['bil_contact_type'] = $info['bil_contact_type'];
        $data['bil_company'] = $info['en_bil_company'];
        $data['bil_fname'] = $info['en_bil_firstname'];
        $data['bil_lname'] = $info['en_bil_lastname'];
        $data['bil_addr1'] = $info['en_bil_address'];
        $data['bil_state'] = $info['en_bil_province'];
        $data['bil_city'] = $info['en_bil_city'];
        $data['bil_postcode'] = $info['cn_bil_postcode'];
        $data['bil_telephone'] = $info['cn_bil_telephone'];
        $data['bil_fax'] = $info['cn_bil_fax'];
        $data['bil_country'] = $info['cn_bil_country'];
        $data['bil_email'] = $info['cn_bil_email'];
        $data['custom_bil1'] = $info['cn_bil_idcard'];
        if ($info['bil_contact_type'] == '1'){
            $data['custom_bil2'] = '';//TODO 企业类型
            if ($domainSuffix == '.us'){
                $data['custom_bil3'] = '';//TODO 企业类型
            }
        }elseif($info['bil_contact_type'] == '0'){
            if (in_array($domainSuffix, array('.hk','.my'))){
                $data['custom_bil3'] = $info['cn_bil_birthday'];
            }
        }
        $result = $this->_post($url, $data);
        if ($result === false){
            $this->setError("-1\t接口异常");
            return $result;
        }
        list($status, $message) = explode("\t", $result);
        if ($status !== '0'){
            $this->setError($result);
            return false;
        }
        return true;
    }

    /**
     * TODO 域名转移[转出]
     * @param string $domain
     * @return bool
     */
    public function transferOut($domain){
        $url = $this->myDomain.'/jsp/pn_authinfo.jsp';
        $data['source'] = $this->username;
        $data['otime'] = date('Y-m-d H:i:s');
        $data['ochecksum'] = $this->checksum();
        $data['domainname'] = $domain;
        $result = $this->_post($url, $data);
        if ($result === false){
            $this->setError("-1\t接口异常");
            return $result;
        }
        list($status, $message) = explode("\t", $result);
        if ($status !== '0'){
            $this->setError($result);
            return false;
        }
        return true;
    }

    /**
     * 域名转移[转入]状态
     * @param string $domain 待转入域名
     * @return bool|string
     */
    public function transferInStatus($domain){
        $url = $this->myDomain.'/jsp/pn_trfstatus.jsp';
        $data['source'] = $this->username;
        $data['otime'] = date('Y-m-d H:i:s');
        $data['ochecksum'] = $this->checksum();
        $data['domainname'] = $domain;
        $result = $this->_post($url, $data, false);
        if ($result === false){
            $this->setError("-1\t接口异常");
            return $result;
        }
        list($status, $message) = explode("\t", $result);
        if ($status !== '0'){
            $this->setError($result);
            return false;
        }
        return substr($message, strpos($message, '('), 1);
    }

    /**
     * 域名锁定[设置域名保护状态]
     * @param string $domain 待保护域名
     * @return bool
     */
    public function lock($domain){
        $url = $this->myDomain.'/jsp/pn_protect.jsp';
        $data['source'] = $this->username;
        $data['otime'] = date('Y-m-d H:i:s');
        $data['ochecksum'] = $this->checksum();
        $data['status'] = 'N';//先解除转移锁定
        $data['domainname'] = $domain;
        $result = $this->_post($url, $data);
        if ($result === false)  return false;
        list($status, $message) = explode("\t", $result);
        if ($status !== '0'){
            $this->setError($result);
            return false;
        }
        unset($result, $status, $message);
        $data['status'] = 'L';//解除删除锁定
        $result = $this->_post($url, $data);
        if ($result === false)  return false;
        list($status, $message) = explode("\t", $result);
        if ($status !== '0'){
            $this->setError($result);
            return false;
        }
        return true;
    }

    /**
     * 域名解锁[设置域名保护状态]
     * @param string $domain 待解锁域名
     * @return bool
     */
    public function unlock($domain){
        $url = $this->myDomain.'/jsp/pn_protect.jsp';
        $data['source'] = $this->username;
        $data['otime'] = date('Y-m-d H:i:s');
        $data['ochecksum'] = $this->checksum();
        $data['status'] = 'A';
        $data['domainname'] = $domain;
        $result = $this->_post($url, $data);
        if ($result === false){
            $this->setError("-1\t接口异常");
            return $result;
        }
        list($status, $message) = explode("\t", $result);
        if ($status !== '0'){
            $this->setError($result);
            return false;
        }
        return true;
    }

    /**
     * 修改WHOIS信息
     * @param string $domain 待修改域名
     * @param array $info whois资料
     * @return bool
     */
    public function whoisEdit($domain, array $info){
        $domainSuffix = $this->getDomainFullSuffix($domain);
        $url = $this->myDomain.'/jsp/pn_newmod.jsp';
        $data['source'] = $this->username;
        $data['otime'] = date('Y-m-d H:i:s');
        $data['ochecksum'] = $this->checksum();
        $data['domainname'] = $domain;
        $data['reg_company'] = $info['en_reg_company'];
        $data['reg_fname'] = $info['en_reg_firstname'];
        $data['reg_lname'] = $info['en_reg_lastname'];
        $data['reg_addr1'] = $info['en_reg_address'];
        $data['reg_state'] = $info['en_reg_province'];
        $data['reg_city'] = $info['en_reg_city'];
        $data['reg_postcode'] = $info['cn_reg_postcode'];
        $data['reg_telephone'] = $info['cn_reg_telephone'];
        $data['reg_fax'] = $info['cn_reg_fax'];
        $data['reg_country'] = $info['cn_reg_country'];
        $data['reg_email'] = $info['cn_reg_email'];
        $data['custom_reg1'] = $info['cn_reg_idcard'];
        if ($info['reg_contact_type'] == '1'){
            $data['custom_reg2'] = '';//TODO 企业类型
            if ($domainSuffix == '.us'){
                $data['custom_reg3'] = '';//TODO 企业类型
            }
        }elseif($info['reg_contact_type'] == '0'){
            if (in_array($domainSuffix, array('.hk','.my'))){
                $data['custom_reg3'] = $info['cn_reg_birthday'];
            }
        }
        $data['adm_company'] = $info['en_adm_company'];
        $data['adm_fname'] = $info['en_adm_firstname'];
        $data['adm_lname'] = $info['en_adm_lastname'];
        $data['adm_addr1'] = $info['en_adm_address'];
        $data['adm_state'] = $info['en_adm_province'];
        $data['adm_city'] = $info['en_adm_city'];
        $data['adm_postcode'] = $info['cn_adm_postcode'];
        $data['adm_telephone'] = $info['cn_adm_telephone'];
        $data['adm_fax'] = $info['cn_adm_fax'];
        $data['adm_country'] = $info['cn_adm_country'];
        $data['adm_email'] = $info['cn_adm_email'];
        $data['custom_adm1'] = $info['cn_adm_idcard'];
        if ($info['adm_contact_type'] == '1'){
            $data['custom_adm2'] = '';//TODO 企业类型
            if ($domainSuffix == '.us'){
                $data['custom_adm3'] = '';//TODO 企业类型
            }
        }elseif($info['adm_contact_type'] == '0'){
            if (in_array($domainSuffix, array('.hk','.my'))){
                $data['custom_adm3'] = $info['cn_adm_birthday'];
            }
        }
        $data['tec_company'] = $info['en_tec_company'];
        $data['tec_fname'] = $info['en_tec_firstname'];
        $data['tec_lname'] = $info['en_tec_lastname'];
        $data['tec_addr1'] = $info['en_tec_address'];
        $data['tec_state'] = $info['en_tec_province'];
        $data['tec_city'] = $info['en_tec_city'];
        $data['tec_postcode'] = $info['cn_tec_postcode'];
        $data['tec_telephone'] = $info['cn_tec_telephone'];
        $data['tec_fax'] = $info['cn_tec_fax'];
        $data['tec_country'] = $info['cn_tec_country'];
        $data['tec_email'] = $info['cn_tec_email'];
        $data['custom_tec1'] = $info['cn_tec_idcard'];
        if ($info['tec_contact_type'] == '1'){
            $data['custom_tec2'] = '';//TODO 企业类型
            if ($domainSuffix == '.us'){
                $data['custom_tec3'] = '';//TODO 企业类型
            }
        }elseif($info['tec_contact_type'] == '0'){
            if (in_array($domainSuffix, array('.hk','.my'))){
                $data['custom_tec3'] = $info['cn_tec_birthday'];
            }
        }
        $data['bil_company'] = $info['en_bil_company'];
        $data['bil_fname'] = $info['en_bil_firstname'];
        $data['bil_lname'] = $info['en_bil_lastname'];
        $data['bil_addr1'] = $info['en_bil_address'];
        $data['bil_state'] = $info['en_bil_province'];
        $data['bil_city'] = $info['en_bil_city'];
        $data['bil_postcode'] = $info['cn_bil_postcode'];
        $data['bil_telephone'] = $info['cn_bil_telephone'];
        $data['bil_fax'] = $info['cn_bil_fax'];
        $data['bil_country'] = $info['cn_bil_country'];
        $data['bil_email'] = $info['cn_bil_email'];
        $data['custom_bil1'] = $info['cn_bil_idcard'];
        if ($info['bil_contact_type'] == '1'){
            $data['custom_bil2'] = '';//TODO 企业类型
            if ($domainSuffix == '.us'){
                $data['custom_bil3'] = '';//TODO 企业类型
            }
        }elseif($info['bil_contact_type'] == '0'){
            if (in_array($domainSuffix, array('.hk','.my'))){
                $data['custom_bil3'] = $info['cn_bil_birthday'];
            }
        }
        $result = $this->_post($url, $data);
        if ($result === false){
            $this->setError("-1\t接口异常");
            return $result;
        }
        list($status, $message) = explode("\t", $result);
        if ($status !== '0'){
            $this->setError($result);
            return false;
        }
        return true;
    }

    /**
     * 订阅WHOIS隐私保护
     * @param $domain
     * @return bool
     */
    private function whoisPrivacySubscribe($domain){
        $url = $this->myDomain.'/jsp/pn_whoisprivacy.jsp';
        $data['source'] = $this->username;
        $data['otime'] = date('Y-m-d H:i:s');
        $data['ochecksum'] = $this->checksum();
        $data['domainname'] = $domain;
        $result = $this->_post($url, $data);
        if ($result === false){
            $this->setError("-1\t接口异常");
            return $result;
        }
        list($status, $message) = explode("\t", $result);
        if ($status === '0')    return true;
        //如果状态为4且为已订阅状态，则直接返回true
        if($status==='4' && $message==='System message (Domain not eligible for whois privacy because already subscribed.)')  return true;
        $this->setError($result);
        return false;
    }
    /**
     * 开启WHOIS隐私保护
     * @param string $domain
     * @return bool
     */
    public function whoisPrivacyLock($domain){
        if (!in_array($this->getDomainFullSuffix($domain), array('.com','.net')))   return false;
        $result = $this->whoisPrivacyQuery($domain);
        if ($result)    return true;
        $error = $this->getError();
        if ($error['status']==='4' && $error['message']==='System message (Domain Not Found)'){
            $result = $this->whoisPrivacySubscribe($domain);
            if ($result === false)  return false;
        }elseif($result===false && $error['status']!=='1'){
            return false;
        }
        //订阅成功，执行启用
        $url = $this->myDomain.'/jsp/pn_whoisprivacyacti.jsp';
        $data['source'] = $this->username;
        $data['otime'] = date('Y-m-d H:i:s');
        $data['ochecksum'] = $this->checksum();
        $data['domain'] = $domain;
        $data['action'] = 'act';
        $result = $this->_post($url, $data);
        if ($result === false){
            $this->setError("-1\t接口异常");
            return $result;
        }
        list($status, $message) = explode("\t", $result);
        if ($status !== '0'){
            $this->setError($result);
            return false;
        }
        return true;
    }

    /**
     * 关闭WHOIS隐私保护
     * @param string $domain
     * @return bool
     */
    public function whoisPrivacyUnlock($domain){
        $url = $this->myDomain.'/jsp/pn_whoisprivacyacti.jsp';
        $data['source'] = $this->username;
        $data['otime'] = date('Y-m-d H:i:s');
        $data['ochecksum'] = $this->checksum();
        $data['domain'] = $domain;
        $data['action'] = 'deact';
        $result = $this->_post($url, $data);
        if ($result === false){
            $this->setError("-1\t接口异常");
            return $result;
        }
        list($status, $message) = explode("\t", $result);
        if ($status !== '0'){
            $this->setError($result);
            return false;
        }
        return true;
    }

    /**
     * WHOIS隐私保护启用状态查询
     * @param string $domain
     * @return bool true为激活 false为未激活 false时需要配合getError方法
     */
    public function whoisPrivacyQuery($domain){
        $url = $this->myDomain.'/jsp/pn_whoisprivacyquery.jsp';
        $data['source'] = $this->username;
        $data['otime'] = date('Y-m-d H:i:s');
        $data['ochecksum'] = $this->checksum();
        $data['domain'] = $domain;
        $result = $this->_post($url, $data, false);
        if ($result === false){
            $this->setError("-1\t接口异常");
            return $result;
        }
        list($status, $message) = explode("\t", $result);
        if ($status === '0')    return true;
        if ($status === '1')    return false;
        $this->setError($result);
        return false;
    }

    /**
     * 修改域名绑定的DNS
     * @param string $domain 域名
     * @param array $dns 新的dns
     * @return bool
     */
    public function dnsEdit($domain, array $dns){
        $url = $this->myDomain.'/jsp/pn_dns.jsp';
        $data['source'] = $this->username;
        $data['otime'] = date('Y-m-d H:i:s');
        $data['ochecksum'] = $this->checksum();
        $data['domain'] = $domain;
        if (isset($dns[0])) {
            $data['ns1'] = $dns[0]['ns'];
            if (isset($dns[0]['ip']))   $data['nsip1'] = $dns[0]['ip'];
        }
        if (isset($dns[1])) {
            $data['ns2'] = $dns[1]['ns'];
            if (isset($dns[1]['ip']))   $data['nsip2'] = $dns[1]['ip'];
        }
        if (isset($dns[2])) {
            $data['ns3'] = $dns[2]['ns'];
            if (isset($dns[2]['ip']))   $data['nsip3'] = $dns[2]['ip'];
        }
        if (isset($dns[3])) {
            $data['ns4'] = $dns[3]['ns'];
            if (isset($dns[3]['ip']))   $data['nsip4'] = $dns[3]['ip'];
        }
        if (isset($dns[4])) {
            $data['ns5'] = $dns[4]['ns'];
            if (isset($dns[4]['ip']))   $data['nsip5'] = $dns[4]['ip'];
        }
        if (isset($dns[5])) {
            $data['ns6'] = $dns[5]['ns'];
            if (isset($dns[5]['ip']))   $data['nsip6'] = $dns[5]['ip'];
        }
        $result = $this->_post($url, $data);
        if ($result === false){
            $this->setError("-1\t接口异常");
            return $result;
        }
        list($status, $message) = explode("\t", $result);
        if ($status !== '0'){
            $this->setError($result);
            return false;
        }
        return true;
    }

    /**
     * 修改DNS服务器
     * @param string $dns 域名
     * @param array $ip
     * @return bool
     */
    public function dnsServerEdit($dns, array $ip=array()){
        $url = $this->myDomain.'/jsp/pn_dnsmod.jsp';
        $data['source'] = $this->username;
        $data['otime'] = date('Y-m-d H:i:s');
        $data['ochecksum'] = $this->checksum();
        $data['dns'] = $dns;
        if (isset($ip[0]))  $data['ip1'] = $ip[0];
        if (isset($ip[1]))  $data['ip2'] = $ip[1];
        if (isset($ip[2]))  $data['ip3'] = $ip[2];
        if (isset($ip[3]))  $data['ip4'] = $ip[3];
        if (isset($ip[4]))  $data['ip5'] = $ip[4];
        if (isset($ip[5]))  $data['ip6'] = $ip[5];
        $result = $this->_post($url, $data);
        if ($result === false){
            $this->setError("-1\t接口异常");
            return $result;
        }
        list($status, $message) = explode("\t", $result);
        if ($status !== '0'){
            $this->setError($result);
            return false;
        }
        return true;
    }

    /**
     * 注册DNS服务器
     * @param string $dns
     * @param array $ip
     * @param array $reg
     * @return bool
     */
    public function dnsServerRegister($dns, array $ip=array(), array $reg=array()){
        $url = $this->myDomain.'/jsp/pn_dnsreg.jsp';
        $data['source'] = $this->username;
        $data['otime'] = date('Y-m-d H:i:s');
        $data['ochecksum'] = $this->checksum();
        $data['dns1'] = $dns;
        if (isset($ip[0]))  $data['ip1'] = $ip[0];
        if (isset($ip[1]))  $data['ip2'] = $ip[1];
        if (isset($ip[2]))  $data['ip3'] = $ip[2];
        if (isset($ip[3]))  $data['ip4'] = $ip[3];
        if (isset($ip[4]))  $data['ip5'] = $ip[4];
        if (isset($ip[5]))  $data['ip6'] = $ip[5];
        if (!empty($reg))  $data['reg'] = $reg;
        $result = $this->_post($url, $data);
        if ($result === false){
            $this->setError("-1\t接口异常");
            return $result;
        }
        list($status, $message) = explode("\t", $result);
        if ($status !== '0'){
            $this->setError($result);
            return false;
        }
        return true;
    }

    /**
     * 删除DNS服务器
     * @param string $dns
     * @param array $reg
     * @return bool
     */
    public function dnsServerDelete($dns, array $reg=array()){
        $url = $this->myDomain.'/jsp/pn_dnsdel.jsp';
        $data['source'] = $this->username;
        $data['otime'] = date('Y-m-d H:i:s');
        $data['ochecksum'] = $this->checksum();
        $data['dns1'] = $dns;
        if (!empty($reg))  $data['reg'] = $reg;
        $result = $this->_post($url, $data);
        if ($result === false){
            $this->setError("-1\t接口异常");
            return $result;
        }
        list($status, $message) = explode("\t", $result);
        if ($status !== '0'){
            $this->setError($result);
            return false;
        }
        return true;
    }

    /**
     * 设置错误信息
     * @param string $error 接口返回的原始错误信息
     */
    public function setError($error){
        list($status, $message) = explode("\t", $error);
        $this->error = array('status'=>$status, 'message'=>trim($message));
    }

    /**
     * 生成去掉前缀的用户名
     * @return mixed
     */
    private function username(){
        return str_replace(array('webcc-','webnic-'), '', $this->username);
    }

    /**
     * 生成校验码
     * @return string
     */
    private function checksum(){
        return md5($this->username.date('Y-m-d H:i:s').md5($this->password));
    }
}