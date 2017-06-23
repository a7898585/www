<?php

/**
 * TrolleyController.class.php
 * Author: Jansen
 * QQ: 6206574
 * Date: 2015-11-17
 */

namespace Home\Controller;

use Common\Extend\Domain;

class TrolleyController extends HomeCommonController {

    public function _initialize() {
        parent::_initialize();
        if (session('MEMBERINFO.id') <= 0) {
            send_http_status(404);
            exit();
        }
    }

    public function index() {
        //验证登录
        if (session('MEMBERINFO.id') <= 0) {
            $this->error('您尚未登录，请先登录。', '/public/login?refer_url=/trolley');
        }
        //获取模板
        $templates = M('MembersDomainTemplate')->where(array('mid' => session('MEMBERINFO.id'), 'status' => '1'))->getField('id,title');
        if (!is_array($templates)) {
            $this->error('您还没有创建域名模板，请先创建。', getDoMain('member') . 'template/international');
        }
        //取用户最新资料
        $memberInfo = M('Members')->where(array('id' => session('MEMBERINFO.id')))->find();
        //取购物车内的待注册域名
        $domains = M('MembersDomainCart')->where(array('mid' => session('MEMBERINFO.id')))->select();
        if (empty($domains)) {
            $this->error('您购物车为空，请先添加。', getDoMain() . 'domain/');
        }
        //获取各域名后缀的注册价格
        foreach ($domains as $item) {
            $suffix[] = $item['idn'] . $item['suffix'];
        }
        $totalMoney = 0;
        unset($where);
        $where['suffix'] = array('IN', $suffix);
        $where['level'] = $memberInfo['level'];
        $domainPrices_arr = M('MemberLevelPriceMap')->where($where)->select();
        foreach ($domainPrices_arr as $item) {
            $domainPrices[$item['suffix']] = $item;
        }
        //获取各域名后缀的可注册和续费年限
        $where['name'] = array('IN', $suffix);
        $domainAges = M('DomainSuffix')->where($where)->getField('name,allow_register_age,allow_renew_age');
        foreach ($domains as $k => $item) {
            $domains[$k]['domain'] = Domain::toLower($item['domain']);
            $thisSuffix = $item['idn'] . $item['suffix'];
            $domains[$k]['full_suffix'] = $thisSuffix;
            $domains[$k]['total_money'] = $domainPrices[$thisSuffix]['price_'.$item['type'].'_year' . $item['age']] / 100;
            $domains[$k]['allow_register_age'] = explode(',', $domainAges[$thisSuffix]['allow_register_age']);
            $domains[$k]['allow_renew_age'] = explode(',', $domainAges[$thisSuffix]['allow_renew_age']);
            //统计待注册的所有域名总金额
            $totalMoney += $domains[$k]['total_money'];
        }
        $this->assign('totalMoney', $totalMoney);
        $this->assign('domains', $domains);
        $this->assign('domainPrices', $domainPrices);
        $this->assign('templates', $templates);
        $seo = array(
            'title' => C('DOMAIN_QUERY_CART_TITLE'),
            'key' => C('DOMAIN_QUERY_CART_KEYWORDS'),
            'des' => C('DOMAIN_QUERY_CART_DESC')
        );
        $this->assign('navtap', 'domain_register_query');
        $this->assign('seo', $seo);
        $this->display();
    }

    // 购物车确认
    public function confirm() {
        //验证登录
        if (session('MEMBERINFO.id') <= 0) {
            $this->error('您尚未登录，请先登录。', '/public/login?refer_url=/trolley');
        }
        if (IS_POST) {
            //存储购物车数据
            $domains = I('post.domains');
            if (empty($domains)) {
                header('Location: /trolley');
                exit();
            }
            //取各后缀的价格
            $trolleyIds = array_keys($domains);
            $trolleyDomains = M('MembersDomainCart')->where(array('id' => array('IN', $trolleyIds)))->getField('id,idn,suffix,type');
            //获取各域名后缀的注册价格
            foreach ($trolleyDomains as $k => $item) {
                $suffixs[$k] = $item['idn'] . $item['suffix'];
            }
            //取用户最新资料
            $memberInfo = M('Members')->where(array('id' => session('MEMBERINFO.id')))->find();
            //取用户对应的价格
            $where['suffix'] = array('IN', $suffixs);
            $where['level'] = $memberInfo['level'];
            $memberLevelPriceMap = M('MemberLevelPriceMap')->where($where)->select();
            foreach ($memberLevelPriceMap as $item) {
                $domainPrices[$item['suffix']] = $item;
            }
            //获取各域名后缀的可注册和续费年限
            $where['name'] = array('IN', $suffixs);
            $domainAges = M('DomainSuffix')->where($where)->getField('name,allow_register_age,allow_renew_age');
            foreach ($domainAges as $key => $item){
                $domainAges[$key]['allow_register_age'] = explode(',', $item['allow_register_age']);
                $domainAges[$key]['allow_renew_age'] = explode(',', $item['allow_renew_age']);
            }
            M('MembersDomainCart')->startTrans();
            //更新待注册域名的使用的模板和注册年限
            foreach ($domains as $trolleyId => $domain) {
                if (intval($domain['age']) <= 0) {
                    M('MembersDomainCart')->rollback();
                    $this->error('数据校验失败，请重新提交。');
                }
                //当前域名的后缀
                $thisSuffix = $trolleyDomains[$trolleyId]['idn'].$trolleyDomains[$trolleyId]['suffix'];
                //当前域名的操作类型：注册或续费
                $thisOpType = $trolleyDomains[$trolleyId]['type'];
                if (!in_array(intval($domain['age']), $domainAges[$thisSuffix]['allow_'.$thisOpType.'_age'])){
                    M('MembersDomainCart')->rollback();
                    $this->error('数据校验失败，请重新提交。');
                }
                unset($where, $data);
                $where['id'] = $trolleyId;
                $where['mid'] = session('MEMBERINFO.id');
                $data['template'] = intval($domain['tmpid']);
                $data['age'] = intval($domain['age']);
                $data['price'] = $domainPrices[$thisSuffix]['price_'.$thisOpType.'_year' . intval($domain['age'])];
                try {
                    M('MembersDomainCart')->where($where)->data($data)->save();
                } catch (\Exception $e) {
                    M('MembersDomainCart')->rollback();
                    $this->error('存储待注册域名数据时发生异常，请重新提交。');
                }
            }
            M('MembersDomainCart')->commit();
            //获取购物车里的待注册域名列表
            unset($domains);
            $domains = M('MembersDomainCart')->where(array('mid' => session('MEMBERINFO.id')))->select();
            if (empty($domains)) {
                header('Location: /trolley');
                exit();
            }
            $this->assign('domains', $domains);
            //获取模板
            $templates = M('MembersDomainTemplate')->where(array('mid' => session('MEMBERINFO.id'), 'status' => '1'))->getField('id,title');
            if (!is_array($templates)) {
                $this->error('您还没有创建域名模板，请先创建。', getDoMain('member') . 'template/international');
            }
            //可用余额
            $memberMoney = M('MembersMoney')->where(array('mid' => session('MEMBERINFO.id')))->getField('total_money');
            $this->assign('money', $memberMoney);
            $seo = array(
                'title' => C('DOMAIN_REGISTER_DONE_TITLE'),
                'key' => C('DOMAIN_QUERY_CART_KEYWORDS'),
                'des' => C('DOMAIN_QUERY_CART_DESC')
            );
            $this->assign('navtap', 'domain_register_query');
            $this->assign('seo', $seo);
            $this->assign('templates', $templates);
            $this->display();
            exit();
        }
        header('Location: /trolley');
        exit();
    }

    // 从购物车中取域名，开始执行注册或续费操作
    public function settle() {
        if (session('MEMBERINFO.id') <= 0) {
            $this->error('您尚未登录，请先登录。', '/public/login?refer_url=/trolley');
        }
        //取购物车内的待注册域名
        $domains = M('MembersDomainCart')->where(array('mid' => session('MEMBERINFO.id')))->select();
        if (empty($domains)) {
            $this->error('您购物车为空，请先添加。', '/domain');
        }
        $this->assign('domains', $domains);
        //获取模板
        $templates = M('MembersDomainTemplate')->where(array('mid' => session('MEMBERINFO.id'), 'status' => '1'))->getField('id,title');
        $this->assign('templates', $templates);
        $seo = array(
            'title' => C('DOMAIN_REGISTER_SUCCESS_TITLE'),
            'key' => C('DOMAIN_QUERY_CART_KEYWORDS'),
            'des' => C('DOMAIN_QUERY_CART_DESC')
        );
        $this->assign('navtap', 'domain_register_query');
        $this->assign('seo', $seo);
        $this->display();
    }

    //添加购物车
    public function ajax_add_cart() {
        if (IS_POST) {
            $domains = I('post.domain');
            if (!$domains) {
                $this->ajaxReturn(array('status' => 300, 'message' => '没有选择域名。'));
            }
            //验证登录
            if (session('MEMBERINFO.id') <= 0) {
                $this->ajaxReturn(array('status' => 400, 'message' => '您尚未登录，请先登录。', 'url' => '/public/login'));
            }
            $domain_arr = explode(',', $domains);
            //构造数据
            $new_num = 0;
            foreach ($domain_arr as $domain) {
                $domain = Domain::toLower($domain);
                //检测域名是否已在用户购物车内
                $result = M('MembersDomainCart')->where(array('domain' => $domain, 'mid' => session('MEMBERINFO.id')))->find();
                if (!is_array($result)) {
                    $data['domain'] = $domain;
                    $data['suffix'] = Domain::getSuffix($domain);
                    $data['idn'] = Domain::isChinese($domain) ? 'idn' : '';
                    $data['mid'] = session('MEMBERINFO.id');
                    $data['type'] = 'register';
                    M('MembersDomainCart')->data($data)->add();
                    ++$new_num;
                }
            }
            $this->ajaxReturn(array('status' => 200, 'new_num' => $new_num, 'message' => '已加入购物车。'));
        }
        $this->ajaxReturn(array('status' => 300, 'message' => '参数出错'));
    }

    //删除购物车
    public function ajax_del_cart($id) {
        //验证登录
        if (session('MEMBERINFO.id') <= 0) {
            $this->ajaxReturn(array('status' => 400, 'message' => '您尚未登录，请先登录。', 'url' => '/public/login'));
        }
        $result = M('MembersDomainCart')->where(array('id' => $id, 'mid' => session('MEMBERINFO.id')))->delete();
        if (!$result) {
            $this->ajaxReturn(array('status' => 500, 'message' => '从购物车删除出错，请重试。'));
        }
        $this->ajaxReturn(array('status' => 200, 'message' => '已移出购物车。'));
    }

    //删除购物车
    public function ajax_delete_by_domain($domain) {
        $where['domain'] = Domain::toLower($domain);
        $where['mid'] = session('MEMBERINFO.id');
        $result = M('MembersDomainCart')->where($where)->delete();
        if (!$result) {
            $this->ajaxReturn(array('status' => 500, 'message' => '删除失败', 'data' => $domain));
        }
        $this->ajaxReturn(array('status' => 200, 'message' => '删除成功', 'data' => $domain));
    }

    //清空购物车
    public function ajax_clear_cart() {
        //验证登录
        if (session('MEMBERINFO.id') <= 0) {
            $this->ajaxReturn(array('status' => 400, 'message' => '您尚未登录，请先登录。', 'url' => '/public/login'));
        }
        M('MembersDomainCart')->where(array('mid' => session('MEMBERINFO.id')))->delete();
        $this->ajaxReturn(array('status' => 200, 'message' => '已清空购物车。'));
    }

    //取会员账户余额
    public function ajax_get_total_money() {
        $result = M('MembersMoney')->where(array('mid' => session('MEMBERINFO.id')))->find();
        $this->ajaxReturn(array('status' => 200, 'message' => array('fen' => $result['total_money'], 'yuan' => number_format($result['total_money'] / 100, 2))));
    }

}