<?php

namespace Common\Extend;

class Bra {

    private $checkKey = "";
    private $md5key = "";
    private $type = 48;
    private $open = 1; //1外网,内网

    public function __construct() {
        $this->checkKey = $this->getcheckKey();
        $this->md5key = $this->getmd5key();
    }

    public function ckToken($token, $timestamp, $customerId) {
        $maketoken = md5("[BRM POWER BY BOOKSIR CO.LTD]" . $timestamp . $customerId);
        if ($token == $maketoken) {
            return $customerId;
        } else {
            return 0;
        }
    }

    private function getcheckKey() {
        return time();
    }

    private function getmd5key() {
        return md5("[BRM POWER BY BOOKSIR CO.LTD]" . $this->checkKey);
    }

    public function getarea($id) {
        return \Org\Net\Curl::get("http://pdc.258.com/area/view/getchildren?areaid={$id}");
    }

    public function getagent($id, $platform) {
        return \Org\Net\Curl::get("http://sapi.258.com/api/agent/agentbyarea?areaId={$id}&projectId={$platform}");
    }

    public function loginjump($username, $password) {
        return \Org\Net\Curl::get("http://brc.258.com/tokenlogin.jsp?userName={$username}&password={$password}&autoLogin=0&type={$this->type}");
    }

    public function login($username, $password) {
        return \Org\Net\Curl::get("http://sapi.258.com/api/gzuser/loginuser?userName={$username}&password={$password}");
    }

    public function getuser($customerId) {
        return \Org\Net\Curl::get("http://sapi.258.com/api/gzuser/user?customerId={$customerId}&checkKey={$this->checkKey}&md5key={$this->md5key}&projectId={$this->type}");
    }

    public function addagent($customerId, $agentId, $type) {
        return \Org\Net\Curl::get("http://brm.258.com/api/agent/user/agentCustomer/add?agentId={$agentId}&projectType={$type}&checkKey={$this->checkKey}&md5key={$this->md5key}&customerId={$customerId}");
    }

    public function ckuser($username) {
        return \Org\Net\Curl::get("http://sapi.258.com/api/gzuser/checkuserName?userName={$username}&checkKey={$this->checkKey}&md5key={$this->md5key}");
    }

    public function ckemail($email) {
        return \Org\Net\Curl::get("http://sapi.258.com/api/gzuser/checkemail?email={$email}&checkKey={$this->checkKey}&md5key={$this->md5key}");
    }

    public function regedit($userName, $password, $email, $mobile, $address, $Contacts) {
        $r = json_decode($this->ckuser($userName));
        if ($r->s < 0) {
            return json_encode($r);
        }
        $r = json_decode($this->ckemail($email));
        if ($r->s < 0) {
            return json_encode($r);
        }
        $url = "&userType=3";
        if ($mobile) {
            $url .= "&mobile=" . $mobile;
        }

        if ($address) {
            $url .= "&address=" . $address;
        }

        if ($Contacts) {
            $url .= "&Contacts=" . $Contacts;
        }
        return \Org\Net\Curl::get("http://sapi.258.com/api/gzuser/regiestuser?type={$this->type}&userName={$userName}&password={$password}&email={$email}{$url}");
    }

    //修改密码
    public function modifyPassword($customerId, $password, $oldpassword) {
        return \Org\Net\Curl::get("http://sapi.258.com/api/gzuser/modifyPassword?password={$password}&oldPassword={$oldpassword}&customerId={$customerId}&checkKey={$this->checkKey}&md5key={$this->md5key}");
    }

//忘记密码
    public function forgetPassword($customerId, $password) {
        return \Org\Net\Curl::get("http://sapi.258.com/api/gzuser/modifyuser?password={$password}&customerId={$customerId}&checkKey={$this->checkKey}&md5key={$this->md5key}");
    }

    //修改资料
    public function editInfo($customerId, $address, $mobile) {

        $url = "";
        if ($address) {
            $url .= "&mobile=" . $mobile;
        }

        if ($mobile) {
            $url .= "&address=" . $address;
        }

        return \Org\Net\Curl::get("http://sapi.258.com/api/gzuser/modifyuser?customerId={$customerId}&checkKey={$this->checkKey}&md5key={$this->md5key}{$url}");
    }

    //充值界面 63
    public function recharge($customerId, $type) {
        $type = $type ? $type : $this->type;
        return "http://brc.258.com/recharge/?customerId={$customerId}&userType=1&type={$type}&md5key={$this->md5key}&checkKey={$this->checkKey}";
    }

    //直接付款
    public function toCharge($customerId, $money, $callback, $descn, $flag = '1') {
        return "http://brc.258.com/recharge/nouserpay?projectId={$this->type}&money={$money}&customerId={$customerId}&flag={$flag}&callbackUrl={$callback}&descn={$descn}";
    }

    //用户余额查询
    public function getusermoney($customerId, $type) {
        $type = $type ? $type : $this->type;
        return \Org\Net\Curl::get("http://sapi.258.com/api/order/getusermoney?customerId={$customerId}&type={$type}");
    }

    public function getMoney($customerId, $sort, $type = 48) {
        $type = $type ? $type : $this->type;
        $r = $this->getusermoney($customerId, $type);
        $r = json_decode($r);

        if ($r->s > 0) {
            return $r->r[$sort];
        }
    }

    //直接扣款
    public function koukuan($customerId, $username, $moeny, $descn, $consumeTypeName) {
        return \Org\Net\Curl::get("http://sapi.258.com/api/order/addorder?md5key={$this->md5key}&checkKey={$this->checkKey}&money={$moeny}&status=1&customerId={$customerId}&userName={$username}&projectId={$this->type}&descn={$descn}&consumeTypeName={$descn}&consumeTypeName={$consumeTypeName}&agentId=1");
    }

    //冻结
    public function dongjie($customerId, $username, $moeny, $descn, $consumeTypeName, $type) {
        return \Org\Net\Curl::get("http://sapi.258.com/api/order/addorder?md5key={$this->md5key}&checkKey={$this->checkKey}&money={$moeny}&status=5&customerId={$customerId}&userName={$username}&projectId=$type&descn={$descn}&consumeTypeName={$descn}");
    }

    //冻结扣款
    public function djkoukuan($orderNumber, $money) {
        return \Org\Net\Curl::get("http://sapi.258.com/api/order/updateOrder?orderNumber={$orderNumber}&money={$money}&status=1");
    }

    public function relactionKw($keyword) {
        $keyword = urlencode($keyword);
        return \Org\Net\Curl::get("http://monitor.258.com/keywordAPI/getKeyWords?keyWord={$keyword}");
    }

    public function badword($keyword) {
        $keyword = urlencode($keyword);
        return \Org\Net\Curl::get("http://fk.258.com:84/GetFilterKey?key={$keyword}");
    }

    public function ymrz($type, $data) {
        if (is_array($data)) {
            $urlString = http_build_query($data);
        }
        return \Org\Net\Curl::get("http://sapi.258.com/api/project/renzhenweb/{$type}?md5key={$this->md5key}&checkKey={$this->checkKey}&{$urlString}");
    }

    public function qyrz($type, $data) {
        if (is_array($data)) {
            $urlString = http_build_query($data);
        }
        //$name,$unitNature,$address,$zipCode,$tele,$businessRegNumber,$legal,$companyType,$regMoney,$business,$taxRegNumber,$organizationNumber,$status,$customerId
        return \Org\Net\Curl::get("http://sapi.258.com/api/project/renzhencompany/{$type}?md5key={$this->md5key}&checkKey={$this->checkKey}&{$urlString}");
    }

    public function brmLogin($username, $passwd) {
        return \Org\Net\Curl::get("http://sapi.258.com/api/brmuser/loginuser?name={$username}&passwd={$passwd}&checkKey={$this->checkKey}&md5key={$this->md5key}");
    }

    public function rztongbu($officialId, $name, $sitename, $site, $icp, $stime, $etime) {
        return \Org\Net\Curl::get("http://monitor.258.com/officialAPI/saveOfficial?officialId={$officialId}&name={$name}&sitename={$sitename}&site={$site}&icp={$icp}&stime={$stime}&etime={$etime}");
    }

    //认证推广
    public function payfree($customerId, $agent, $year = 1, $month = 0, $day = 0) {
        return \Org\Net\Curl::get("http://sapi.258.com/api/activity/activity?projectId=25&customerId={$customerId}&activityType=2&year={$year}&month={$month}&day={$day}&option=tg&agentId={$agent}");
    }

}