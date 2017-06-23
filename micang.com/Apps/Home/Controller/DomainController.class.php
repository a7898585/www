<?php

/**
 * DomainController.class.php
 * Author: Jansen
 * QQ: 6206574
 * Date: 2015-10-12
 */

namespace Home\Controller;

use Common\Extend\Domain;
use Common\Extend\DomainApi;

class DomainController extends HomeCommonController {

    public function index() {
        self::register_query();
    }

    /**
     * 域名价格表
     */
    public function price() {
        $Members = D('MemberLevelPriceMap');
        $result = $Members->field('suffix,level,price_register_year1,price_renew_year1,price_transfer,price_redeem')->order('suffix')->select();
        if (is_array($result)) {
            $data = array();
            foreach ($result as $value) {
                $data[$value['suffix']][$value['level']] = $value;
            }
        }
        $this->assign('data', $data);
        $seo = array(
            'title' => '域名价格|' . C('DOMAIN_PURCHASE_TITLE'),
            'key' => '域名价格，' . C('DOMAIN_PURCHASE_KEYWORDS'),
            'des' => C('DOMAIN_PURCHASE_DESC')
        );
        $this->assign('seo', $seo);
        $this->display();
    }

    /**
     * 域名代购
     */
    public function purchase() {
        if (IS_POST) {
            $param = I('post.');
            if (session('MEMBERINFO.id') > 0) {
                $param['mid'] = session('MEMBERINFO.id');
            }
            $param['addtime'] = time();
            $param['uptime'] = time();
            $param['password'] = rand(100000, 999999);
            if (Domain::is($param['domain']) == false) {
                $this->ajaxReturn(array('status' => 500, 'message' => '请输入正确的域名。'));
            }
            if ($param['mid']) {
                $id = M('DomainPurchase')->where(array('domain' => $param['domain'], 'mid' => $param['mid'], 'role' => '0'))->getField('id');
            } else {
                $id = M('DomainPurchase')->where(array('domain' => $param['domain'], 'email' => $param['email'], 'role' => '0'))->getField('id');
            }
            if ($id) {
                $this->ajaxReturn(array('status' => 500, 'message' => '已经添加过代购，请不要重复提交。'));
            }
            try {
                $result = M('DomainPurchase')->add($param);
            } catch (\Exception $e) {
                $this->ajaxReturn(array('status' => 500, 'message' => '代购申请失败，请重试。'));
            }
            if (!$result) {
                $this->ajaxReturn(array('status' => 500, 'message' => '代购申请失败，请重试。'));
            }
            $this->ajaxReturn(array('status' => 200, 'message' => '代购申请成功。', 'id' => $result, 'password' => $param['password'], 'mid' => $param['mid']));
        }
        if (session('MEMBERINFO.id') > 0) {
            $profile = M('MembersProfile')->where(array('mid' => session('MEMBERINFO.id')))->find();
            $this->assign('profile', $profile);
        }
        $data = M('DomainPurchase')->where(array('status' => '3', 'role' => '0'))->order('id desc')->limit(8)->select();
        $this->assign('data', $data);
        $seo = array(
            'title' => C('DOMAIN_PURCHASE_TITLE'),
            'key' => C('DOMAIN_PURCHASE_KEYWORDS'),
            'des' => C('DOMAIN_PURCHASE_DESC')
        );
        $this->assign('seo', $seo);
        $this->display();
    }

    /**
     * 域名代售
     */
    public function purchase_sell() {
        if (IS_POST) {
            $param = I('post.');
            if (session('MEMBERINFO.id') > 0) {
                $param['mid'] = session('MEMBERINFO.id');
            }
            $param['addtime'] = time();
            $param['uptime'] = time();
            $param['password'] = rand(100000, 999999);
            if (Domain::is($param['domain']) == false) {
                $this->ajaxReturn(array('status' => 500, 'message' => '请输入正确的域名。'));
            }
            if ($param['mid']) {
                $id = M('DomainPurchase')->where(array('domain' => $param['domain'], 'mid' => $param['mid'], 'role' => '1'))->getField('id');
            } else {
                $id = M('DomainPurchase')->where(array('domain' => $param['domain'], 'email' => $param['email'], 'role' => '1'))->getField('id');
            }
            if ($id) {
                $this->ajaxReturn(array('status' => 500, 'message' => '已经添加过代售，请不要重复提交。'));
            }
            try {
                $result = M('DomainPurchase')->add($param);
            } catch (\Exception $e) {
                $this->ajaxReturn(array('status' => 500, 'message' => '代售申请失败，请重试。'));
            }
            if (!$result) {
                $this->ajaxReturn(array('status' => 500, 'message' => '代售申请失败，请重试。'));
            }
            $this->ajaxReturn(array('status' => 200, 'message' => '代售申请成功。', 'id' => $result, 'password' => $param['password'], 'mid' => $param['mid']));
        }
        if (session('MEMBERINFO.id') > 0) {
            $profile = M('MembersProfile')->where(array('mid' => session('MEMBERINFO.id')))->find();
            $this->assign('profile', $profile);
        }
        $data = M('DomainPurchase')->where(array('status' => '3', 'role' => '1'))->order('id desc')->limit(8)->select();
        $this->assign('data', $data);
        $seo = array(
            'title' => C('DOMAIN_PURCHASE_TITLE'),
            'key' => C('DOMAIN_PURCHASE_KEYWORDS'),
            'des' => C('DOMAIN_PURCHASE_DESC')
        );
        $this->assign('seo', $seo);
        $this->display();
    }

    /*
     * 域名邮箱认证
     */

    public function email_verify() {
        $safeKey = C('SAFE_KEY');
        $key = I('get.key');
        $verify_id = intval(I('get.verify_id'));
        $verifyInfo = M("DomainEmailVerify")->where(array('id' => $verify_id))->find();
        if (!is_array($verifyInfo) || empty($verifyInfo))
            exit('非法操作');
        if ($verifyInfo['status'] == '1') {
            $this->success("认证成功", '/');
        }
        $checkKey = md5($verify_id . $safeKey . $verifyInfo['domain']);
        if ($checkKey != $key)
            exit('验证失败');
        $where['mid'] = $verifyInfo['mid'];
        $where['email'] = $verifyInfo['email'];
        $where['status'] = '0';
        $domains = M("DomainEmailVerify")->where($where)->select();
        if ($domains) {
            M()->startTrans();
            foreach ($domains as $list) {
                unset($result, $where, $data);
                $where['id'] = $list['id'];
                $data['status'] = '1';
                $result = M("DomainEmailVerify")->where($where)->data($data)->save();
                if (!$result) {
                    M()->rollback();
                    $this->error("认证失败，请重试", '/');
                }
                unset($result, $where, $data);
                $where['mid'] = $list['mid'];
                $where['domain'] = $list['domain'];
                $where['is_transfer'] = '0';
                $data['is_transfer'] = '3';
                $result = M("MembersDomains")->where($where)->data($data)->save();
                if (!$result) {
                    M()->rollback();
                    $this->error("认证失败，请重试", '/');
                }
            }
            M()->commit();
        }
        $this->success("认证成功", '/');
    }

    /**
     * 域名注册
     */
    public function register($op = null) {
        switch ($op) {
            case 'query':
                self::register_query();
                break;
            case 'query_result':
                self::register_query_result();
                break;
            case 'add_cart':
                self::register_add_cart();
                break;
            default:
                self::register_query();
        }
    }

    public function batch($op = null) {
        $this->assign('navtap', 'domain_register_query');
        if ($op == 'add_cart') {
            //验证登录
            if (session('MEMBERINFO.id') <= 0) {
                $this->error('您尚未登录，请先登录。', '/public/login');
            }
            self::batch_add_cart();
        } else {
            $this->display();
        }
    }

    private function batch_add_cart() {
        if (IS_POST) {
            $domains = explode("\r\n", I('post.domains'));
            if (!is_array($domains) && count($domains) == 0) {
                $this->error('请输入域名。');
            }
            //检测后缀是否在开放后缀范围中
            $result = M('DomainSuffix')->where(array('status' => '1'))->getField('name, status');
            if (!is_array($result) || empty($result)) {
                $this->error('目前没有开放注册的域名后缀。');
            }
            $suffixs = array_keys($result);
            $domainLength = count($domains);
            for ($i = 0; $i < $domainLength; $i++) {
                if (!Domain::is($domains[$i])) {
                    unset($domains[$i]);
                } elseif (!in_array(Domain::getFullSuffix($domains[$i]), $suffixs)) {
                    unset($domains[$i]);
                }
            }
            if (count($domains) == 0) {
                $this->error('请输入域名。');
            }
            //构造数据
            foreach ($domains as $domain) {
                //检测域名是否已在用户购物车内
                $result = M('MembersDomainCart')->where(array('domain' => $domain, 'mid' => session('MEMBERINFO.id')))->find();
                if (!is_array($result)) {
                    $data['domain'] = $domain;
                    $data['suffix'] = Domain::getSuffix($domain);
                    $data['idn'] = Domain::isChinese($domain) ? 'idn' : '';
                    $data['mid'] = session('MEMBERINFO.id');
                    M('MembersDomainCart')->data($data)->add();
                }
            }
            header('Location:/trolley');
            exit();
        }
        header('Location:/domain/batch');
        exit();
    }

    //查询
    private function register_query() {
        $suffixs_usual = M('DomainSuffix')->where(array('usual' => '1', 'status' => '1'))->field('name,usual')->select();
        $usualSuffixLength = count($suffixs_usual);
        for ($i = 0; $i < $usualSuffixLength; $i++) {
            if (strpos($suffixs_usual[$i]['name'], 'idn.') !== false)
                unset($suffixs_usual[$i]);
        }
        $suffixs_unusual = M('DomainSuffix')->where(array('usual' => '0', 'status' => '1'))->field('name,usual')->select();
        $unusualSuffixLength = count($suffixs_unusual);
        for ($i = 0; $i < $unusualSuffixLength; $i++) {
            if (strpos($suffixs_unusual[$i]['name'], 'idn.') !== false)
                unset($suffixs_unusual[$i]);
        }
        $this->assign('suffixs_usual', $suffixs_usual);
        $this->assign('suffixs_unusual', $suffixs_unusual);
        $seo = array(
            'title' => C('DOMAIN_QUERY_TITLE'),
            'key' => C('DOMAIN_QUERY_KEYWORDS'),
            'des' => C('DOMAIN_QUERY_DESC')
        );
        $this->assign('navtap', 'domain_register_query');
        $this->assign('seo', $seo);
        $this->display('register_query');
    }

    //查询结果
    private function register_query_result() {
        if (IS_POST) {
            $arrDomains = explode("\r\n", I('post.domain'));
            if (empty($arrDomains)) {
                header('Location:/domain/register');
                exit();
            }
            $suffixs = I('post.suffix');
            //目前开放注册的所有域名后缀
            $openSuffixs = M('DomainSuffix')->where(array('status' => '1'))->getField('name', true);
            if (empty($openSuffixs) || count($openSuffixs) == 0) {
                $this->error('目前暂无开放注册的域名，无法查询。');
            }
            $domains = array();
            $domainLength = count($arrDomains);
            for ($i = 0; $i < $domainLength; $i++) {
                $arrDomains[$i] = Domain::toLower(trim($arrDomains[$i]));
                if (empty($arrDomains[$i]))
                    continue;
                if (strpos($arrDomains[$i], '.') === false) {
                    //没有填写后缀的情况,检测是否勾选了后缀
                    if (!is_array($suffixs) || empty($suffixs))
                        continue;
                    foreach ($suffixs as $suffix) {
                        $domains[] = $arrDomains[$i] . $suffix;
                    }
                } else {
                    //有填写后缀的情况,检测后缀是否在开放后缀里
                    if (!in_array(Domain::getFullSuffix($arrDomains[$i]), $openSuffixs))
                        continue;
                    $domains[] = $arrDomains[$i];
                }
            }
            if (empty($domains)) {
                header('Location:/domain/register');
                exit();
            }
            //只取前20个
            $domains = array_slice($domains, 0, 20);
            $this->assign('domains', array_unique($domains));
            $seo = array(
                'title' => C('DOMAIN_QUERY_TITLE'),
                'key' => C('DOMAIN_QUERY_KEYWORDS'),
                'des' => C('DOMAIN_QUERY_DESC')
            );
            $this->assign('navtap', 'domain_register_query');
            $this->assign('seo', $seo);
            $this->display('register_query_result');
        }
        header('Location:/domain/register');
        exit();
    }

    /**
     * 加入购物车确认
     */
    private function register_add_cart() {
        if (IS_POST) {
            //验证登录
            if (session('MEMBERINFO.id') <= 0) {
                $this->error('您尚未登录，请先登录。', '/public/login');
            }
            if (!is_array(I('post.domains')) && count(I('post.domains')) == 0) {
                $this->error('没有选择域名。');
            }
            //构造数据
            foreach (I('post.domains') as $domain) {
                $domain = Domain::toLower($domain);
                //检测域名是否已在用户购物车内
                $result = M('MembersDomainCart')->where(array('domain' => $domain, 'mid' => session('MEMBERINFO.id')))->find();
                if (!is_array($result)) {
                    $data['domain'] = $domain;
                    $data['suffix'] = Domain::getSuffix($domain);
                    $data['idn'] = Domain::isChinese($domain) ? 'idn' : '';
                    $data['mid'] = session('MEMBERINFO.id');
                    M('MembersDomainCart')->data($data)->add();
                }
            }
            header('Location:/trolley');
            exit();
        }
        header('Location:/domain/register');
        exit();
    }

    //Ajax查询域名是否注册
    public function ajax_query_do($domain) {
        $domain = Domain::toLower($domain);
        //查是否在本站竞拍
        $info = M('MembersDomainSales')->field('id,mid,type')->where(array('domain' => $domain))->find();
        if ($info) {
            if ($info['type'] == '1') {
                $this->ajaxReturn(array('status' => 301, 'message' => $domain, 'id' => $info['id'], 'price' => $info['seller_price']));
            } elseif ($info['type'] == '2') {
                $this->ajaxReturn(array('status' => 302, 'message' => $domain, 'id' => $info['id']));
            } elseif ($info['type'] == '3') {
                $this->ajaxReturn(array('status' => 303, 'message' => $domain, 'id' => $info['id']));
            }
        }
        $suffix = Domain::getFullSuffix($domain);
        $domainSuffixInfo = M('DomainSuffix')->where(array('name' => $suffix))->find();
        if (!is_array($domainSuffixInfo)) {
            $this->ajaxReturn(array('status' => 400, 'error' => '不支持的域名后缀', 'message' => $domain));
        }
        if ($domainSuffixInfo['status'] == '0') {
            $this->ajaxReturn(array('status' => 400, 'error' => '不支持的域名后缀', 'message' => $domain));
        }
        $registrar = $domainSuffixInfo['registrar']; //使用哪个平台注册
        //todo 根据后缀确定调用的接口
        $domainApi = new DomainApi($registrar);
        //TODO 检测域名的语种，当前默认为英语
        $result = $domainApi->query($domain);
        if (!$result) {
            $error = $domainApi->getError();
            if ($error['status'] === '1') {
                $this->ajaxReturn(array('status' => 404, 'message' => $domain));
            } else {
                $this->ajaxReturn(array('status' => 500, 'message' => $domain, 'error' => $error['message']));
            }
        }
        $this->ajaxReturn(array('status' => 200, 'message' => $domain));
    }

}