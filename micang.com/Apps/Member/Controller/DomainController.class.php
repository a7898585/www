<?php

/**
 * DomainController.class.php
 * Author: Jansen
 * QQ: 6206574
 * Date: 2015-10-16
 */

namespace Member\Controller;

use Common\Extend\Domain;
use Common\Extend\DomainApi;
use Common\Extend\PageForMember;

class DomainController extends MemberCommonController {

    public function _initialize() {
        parent::_initialize();
        $this->assign('m_tab', 'domain');
    }

    /*
     * 我关注的域名
     */

    public function notice($p = 1) {
        $total = M('MembersDomainNotice')->where(array('member_id' => session('MEMBERINFO.id')))->count();
        $mt = M('MembersDomainNotice')->where(array('member_id' => session('MEMBERINFO.id')))->page($p)->select();
        // echo M("MembersDomainNotice")->getLastSql();
        if (sizeof($mt) > 0) {
            foreach ($mt as $list) {
                $mt2[] = $list['sale_id'];
            }
            $ids = implode(',', $mt2);
            $domains = M("MembersDomainSales")->where(array('id' => array('in', $ids)))->select();
        }
        $this->assign('domains', $domains);
        $pager = new PageForMember($total);
        $pager->url = '/domain/notice?p=' . urlencode('[PAGE]');
        $this->assign('pager', $pager->show());
        $this->assign('m_tab', 'buy_domain');
        $this->display();
    }

    /**
     * 域名列表
     *
     * @param int $p            
     */
    public function my($p = 1) {
        $url = '';
        // 域名包含
        if (I('request.domain')) {
            $domain = I('request.domain');
            $url .= "&domain={$domain}";
            if (!I('request.domain_tou') && !I('request.domain_wei')) {
                $where['base'] = array('like', "%{$domain}%");
            }
            if (I('request.domain_tou')) {
                $where['base'] = array('like', "{$domain}%");
            }
            if (I('request.domain_wei')) {
                $where['base'] = array('like', "%{$domain}");
            }
        }
        // 域名过滤
        if (I('request.filter')) {
            $filter = I('request.filter');
            $url .= "&filter={$filter}";
            if (!I('request.filter_tou') && !I('request.filter_wei')) {
                $where['base'] = array('notlike', "%{$filter}%");
            }
            if (I('request.filter_tou')) {
                $where['base'] = array('notlike', "{$filter}%");
                $url .= "&filter_tou=1";
            }
            if (I('request.filter_wei')) {
                $where['base'] = array('notlike', "%{$filter}");
                $url .= "&filter_wei=1";
            }
        }
        // 域名分类
        if (I("request.classify")) {
            $classify = I("request.classify");
            $mt = M("DomainClassify")->find($classify);
            if ($mt[pid] > 0) {
                $where['classify2'] = $classify;
            } else {
                $where['classify1'] = $classify;
            }
            unset($mt);
            $url .= "&classify={$classify}";
        }
        // 域名后缀
        if (I("request.suffix")) {
            $suffix = I("request.suffix");
            $where['suffix'] = $suffix;
            $url .= "&suffix={$suffix}";
        }
        // 域名长度
        if (I("request.length_from")) {
            $length_from = I("request.length_from");
            $where['length'] = array('egt', $length_from);
            $url .= "&length_from={$length_from}";
        }
        if (I("request.length_to")) {
            $length_to = I("request.length_to");
            $where['length'] = array('elt', $length_to);
            $url .= "&length_to={$length_to}";
        }
        // op
        if (I("request.op")) {
            $op = I("request.op");
            if ($op == 'sell') {
                $status = array('103', '104', '105');
                $where['status'] = array('in', $status);
            }
            if ($op == 'shenhe') {
                $where['is_transfer'] = array('neq', '1');
            }
            if ($op == 'timeout') {
                $date = date('Y-m-d', time() + 86400 * 60);
                $where['expire_time'] = array('lt', $date);
            }
            $this->assign('op', $op);
        }
        $where['mid'] = session('MEMBERINFO.id');
        $total = M('MembersDomains')->where($where)->count();
        $domains = M('MembersDomains')->where($where)->order(array('id' => 'ASC'))->page($p)->select();
        $this->assign('domains', $domains);
        $pager = new PageForMember($total);
        $pager->url = '/domain/my?p=' . urlencode('[PAGE]') . $url;
        $this->assign('pager', $pager->show());
        $this->assign("domainClassify", M("DomainClassify")->select());
        $this->display();
    }
    public function renew($domain){
        $domain = Domain::toLower($domain);
        //检测域名是否已在用户购物车内
        $result = M('MembersDomainCart')->where(array('domain' => $domain, 'mid' => session('MEMBERINFO.id')))->find();
        if (!is_array($result)) {
            $data['domain'] = $domain;
            $data['suffix'] = Domain::getSuffix($domain);
            $data['idn'] = Domain::isChinese($domain) ? 'idn' : '';
            $data['mid'] = session('MEMBERINFO.id');
            $data['type'] = 'renew';
            M('MembersDomainCart')->data($data)->add();
        }
        header('Location: '.getDomain('www').'trolley/index');
        exit();
    }

    public function add() {
        if (IS_POST) {
            $domains = explode("\r", I('post.domain'));
            if (is_array($domains)) {
                $MembersDomains = D('MembersDomains');
                foreach ($domains as $list) {
                    $list= trim($list);
                    if (empty($list))
                        continue;
                    $check = Domain::is(trim($list));
                    if (!$check) {
                        $this->error($list . "域名格式有误");
                    }
                    $check = checkDomainexist(trim($list), session('MEMBERINFO.id'));
                    if ($check == '1') {
                        $this->error($list . "域名已经被添加。如果域名是您的，请联系客服");
                    }
                    if ($check == '0') {
                        $data['mid'] = session('MEMBERINFO.id');
                        $data['domain'] = trim($list);
                        $data['status'] = '100';
                        $data['is_transfer'] = '-1'; // 等待更新whois
                        $result = $MembersDomains->addDomain($data);
                    }
                }
                $this->success("域名添加成功", "/domain/my");
                exit();
            }
        }
        $this->assign('m_tab', 'domain');
        $this->display();
    }

    public function ajax() {
        header("Content-Type:application/json;charset=utf-8");
        $d = I("request.d");
        $id = intval(I("request.id"));
        $MembersDomainSales = D('MembersDomainSales');
        switch ($d) {
            case 'check_whois_email':
                $where['domain'] = I('post.domain');
                // 这里需要加入memcache，避免邮件反复发送
                // 这里需要加入memcache，避免邮件反复发送
                $where['mid'] = session('MEMBERINFO.id');
                $where['status'] = '0';
                $email = M("DomainEmailVerify")->where($where)->find();
                if (!$email) {
                    $json['status'] = '2';
                    $json['message'] = "该域名已经无需验证";
                    exit(json_encode($json));
                }
                $safeKey = C('SAFE_KEY');
                $key = md5($email['id'] . $safeKey . $email['domain']);
                $search = array('%ACTIVATE_URL%', '%DATE%', '%SYSTEM_URL%', '%DOMAIN%');
                $activateUrl = getDoMain('www') . 'domain/email_verify?verify_id=' . $email['id'] . "&key=" . $key;
                $replace = array($activateUrl, date('Y-m-d'), getDoMain('www'), $email['domain']);
                $mailContent = str_replace($search, $replace, C('MAIL_CONFIG.VERIFY_EMAIL_CONTENT'));
                $result = SendMail($email['email'], C('MAIL_CONFIG.VERIFY_EMAIL_TITLE'), $mailContent, '米仓网客户服务中心');
                if (!$result) {
                    $json['status'] = '2';
                    $json['message'] = "邮件发送失败，请稍后重试";
                    exit(json_encode($json));
                }
                $json['status'] = '1';
                $json['message'] = "邮箱：{$email['email']}，验证邮件发送成功，请查收";
                exit(json_encode($json));
            case 'del_notice':
                $mid = session('MEMBERINFO.id');
                if (!$mid) {
                    $json['status'] = 2;
                    $json['message'] = "登陆超时，请重新登陆";
                    exit(json_encode($json));
                }
                $where['member_id'] = $mid;
                $where['sale_id'] = intval(I('get.id'));
                $result = M("MembersDomainNotice")->where($where)->delete();
                if (!$result) {
                    $json['status'] = '2';
                    $json['message'] = "取消关注失败，请重试";
                    exit(json_encode($json));
                }
                $json['status'] = 1;
                $json['message'] = "取消关注成功";
                exit(json_encode($json));
                exit();
            case 'del_sale':
                if (isSeccodeTimeout(false) == 0) {
                    $json['status'] = '2';
                    $json['message'] = '安全码过期，请重试';
                    exit(json_encode($json));
                }
                M()->startTrans();
                if (I('request.domain')) {
                    // 通过域名下架
                    $where['mid'] = session('MEMBERINFO.id');
                    $where['domain'] = I('request.domain');
                    $m = M("MembersDomainSales")->where($where)->find();
                    if (!$m) {
                        M()->rollback();
                        $json['status'] = 2;
                        $json['message'] = "系统出错，请稍后重试error:001";
                        exit(json_encode($json));
                    }
                    $result = $MembersDomainSales->Del($m['id']);
                } else {
                    // 通过销售id下架
                    $result = $MembersDomainSales->Del($id);
                }
                if ($result == '1') {
                    M()->commit();
                    $json['status'] = '1';
                    $json['message'] = '下架成功';
                    exit(json_encode($json));
                }
                M()->rollback();
                $json['status'] = '2';
                $json['message'] = $result;
                exit(json_encode($json));
            case 'update':
                if (isSeccodeTimeout(false) == 0) {
                    $json['status'] = '2';
                    $json['message'] = '安全码过期，请重试';
                    exit(json_encode($json));
                }
                $data['seller_price'] = I("post.price");
                $data['summary'] = I("post.summary");
                if (!$data['seller_price']) {
                    $json['status'] = '2';
                    $json['message'] = '请输入域名价格';
                    exit(json_encode($json));
                }
                $result = $MembersDomainSales->Update($data, $id);
                if (is_numeric($result)) {
                    $json['status'] = '1';
                    $json['message'] = '修改成功';
                    exit(json_encode($json));
                }
                $json['status'] = '2';
                $json['message'] = $result;
                exit(json_encode($json));
            case 'domain_transfer':
                if (isSeccodeTimeout(false) == 0) {
                    $json['status'] = '2';
                    $json['message'] = '安全码过期，请重试';
                    exit(json_encode($json));
                }
                $where['id'] = $id;
                $where['from_mid'] = session('MEMBERINFO.id');
                $tradeDetail = M("DomainTradeLog")->where($where)->find();
                if (!$tradeDetail) {
                    exit("access deny");
                }
                $m = M("MembersDomains")->where(array('domain' => $tradeDetail['domain']))->find();
                if ($m['is_transfer'] == '1') {
                    $json['status'] = '1';
                    if ($tradeDetail['status'] == '6') {
                        // 域名已经转入，进入买家付款
                        M("DomainTradeLog")->data(array('status' => '7'))->where(array('id' => $tradeDetail['id']))->save();
                    }
                } elseif ($m['is_transfer'] == '2') {
                    $json['status'] = '2';
                    $json['message'] = '域名转入进行中..';
                } elseif ($m['is_transfer'] == '0') {
                    $json['status'] = '2';
                    $json['message'] = '域名还未转入';
                }
                exit(json_encode($json));
            case 'push_cancel':
                if (isSeccodeTimeout(false) == 0) {
                    $json['status'] = '2';
                    $json['message'] = '安全码过期，请重试';
                    exit(json_encode($json));
                }
                $mid = session('MEMBERINFO.id');
                // 检测ID状态
                $where['id'] = $id;
                $where['mid_from'] = $mid;
                $pushInfo = M('MembersDomainPush')->where($where)->find();
                if (!is_array($pushInfo)) {
                    $json['status'] = '2';
                    $json['message'] = '没有找到这条请求，请确认ID正确。';
                    exit(json_encode($json));
                }
                if ($pushInfo['status'] != '0') {
                    $json['status'] = '2';
                    $json['message'] = 'PUSH请求状态有误，不能撤回。';
                    exit(json_encode($json));
                }
                // 变更域名状态
                M('MembersDomainPush')->startTrans();
                unset($where);
                $data['status'] = '100';
                $where['domain'] = array('IN', explode("\r\n", $pushInfo['domains']));
                $where['mid'] = session('MEMBERINFO.id');
                $result = M('MembersDomains')->where($where)->data($data)->save();
                if (!$result) {
                    M('MembersDomainPush')->rollback();
                    $json['status'] = '2';
                    $json['message'] = '撤回PUSH域名请求失败，请重试。';
                    exit(json_encode($json));
                }
                // 变更记录状态
                unset($where, $data, $result);
                $data['status'] = '3';
                $data['finish_time'] = time();
                $where['id'] = $id;
                $where['mid_from'] = session('MEMBERINFO.id');
                $result = M('MembersDomainPush')->where($where)->data($data)->save();
                if (!$result) {
                    M('MembersDomainPush')->rollback();
                    $json['status'] = '2';
                    $json['message'] = '撤回PUSH域名请求失败，请重试。';
                    exit(json_encode($json));
                }
                M('MembersDomainPush')->commit();
                $json['status'] = '2';
                $json['message'] = '撤回PUSH域名请求成功。';
                $json['jump'] = '/domain/push_to';
                exit(json_encode($json));
            default:
                exit("param error");
        }
    }

    public function cancel_buyer() {
        if (isSeccodeTimeout(false) == 0) {
            $json['status'] = '2';
            $json['message'] = '安全码过期，请重试';
            exit(json_encode($json));
        }
        $trade_id = I('request.id');
        $where['to_mid'] = session('MEMBERINFO.id');
        $where['id'] = $trade_id;
        $tradeDetail = M("DomainTradeLog")->where($where)->find();
        if (!$tradeDetail) {
            $json['status'] = '2';
            $json['message'] = "交易信息获取失败，请重试";
            exit(json_encode($json));
        }
        if ($tradeDetail['status'] != '1' && $tradeDetail['status'] != '7') {
            $json['status'] = '2';
            $json['message'] = "当前交易状态不允许您操作";
            exit(json_encode($json));
        }
        M()->startTrans();
        // 违约金对半分
        $result = WeiYue($trade_id, 'to');
        if ($result != '1') {
            M()->rollback();
            $json['status'] = '2';
            $json['message'] = $result;
            exit(json_encode($json));
        }
        M()->commit();
        $json['status'] = '1';
        $json['message'] = '操作成功';
        exit(json_encode($json));
    }

    /*
     * 卖家取消交易
     */

    public function cancel_seller() {
        if (isSeccodeTimeout(false) == 0) {
            $json['status'] = '2';
            $json['message'] = '安全码过期，请重试';
            exit(json_encode($json));
        }
        $trade_id = I('request.id');
        $where['from_mid'] = session('MEMBERINFO.id');
        $where['id'] = $trade_id;
        $tradeDetail = M("DomainTradeLog")->where($where)->find();
        if (!$tradeDetail) {
            $json['status'] = '2';
            $json['message'] = "交易信息获取失败，请重试";
            exit(json_encode($json));
        }
        if ($tradeDetail['status'] != '0' && $tradeDetail['status'] != '6') {
            $json['status'] = '2';
            $json['message'] = "当前交易状态不允许您操作";
            exit(json_encode($json));
        }
        M()->startTrans();
        // 违约金对半分
        $result = WeiYue($trade_id, 'from');
        if ($result != '1') {
            M()->rollback();
            $json['status'] = '2';
            $json['message'] = $result;
            exit(json_encode($json));
        }
        M()->commit();
        $json['status'] = '1';
        $json['message'] = "操作成功";
        exit(json_encode($json));
    }

    /*
     * 卖家确认交易
     */

    public function deal_seller() {
        if (isSeccodeTimeout(false) == 0) {
            $json['status'] = '2';
            $json['message'] = '安全码过期，请重试';
            exit(json_encode($json));
        }
        $trade_id = I('request.trade_id');
        $where['from_mid'] = session('MEMBERINFO.id');
        $where['id'] = $trade_id;
        $tradeDetail = M("DomainTradeLog")->where($where)->find();
        if (!$tradeDetail) {
            $json['status'] = '2';
            $json['message'] = "交易信息获取失败，请重试";
            exit(json_encode($json));
        }
        if ($tradeDetail['status'] != 0 && $tradeDetail['status'] != 6) {
            $json['status'] = '2';
            $json['message'] = "非法操作";
            exit(json_encode($json));
        }
        M()->startTrans();
        $result = M("DomainTradeLog")->where(array('id' => $trade_id))->data(array('status' => '1', 'update_time' => time()))->save();
        if (!$result) {
            M()->rollback();
            $json['status'] = '2';
            $json['message'] = "系统出错，请稍后重试,error:001";
            exit(json_encode($json));
        }
        if ($tradeDetail['type'] == '3') {
            // 出价状态， sales表，下掉正在出售
            $result = DelDomainSales($tradeDetail['domain']);
            if (!$result) {
                M()->rollback();
                $json['status'] = '2';
                $json['message'] = "域名下架交易失败，请稍后重试";
                exit(json_encode($json));
            }
            $domainDetail = M("MembersDomains")->where(array('domain' => $tradeDetail['domain']))->find();
            if ($domainDetail['is_transfer'] != '1') {
                // 站外域名，卖家确定要卖，需要冻结50元保证金
                $result = TradeLockMoney(5000, $trade_id, 'from');
                if ($result == '-1') {
                    M()->rollback();
                    $json['status'] = '2';
                    $json['message'] = "需要冻结50元保证金，账户余额不足，请充值";
                    exit(json_encode($json));
                }
                if ($result != '1') {
                    M()->rollback();
                    $json['status'] = '2';
                    $json['message'] = "系统出错，请稍后重试,error:002";
                    exit(json_encode($json));
                }
            }
        }
        M()->commit();
        $json['status'] = '1';
        $json['message'] = "操作成功，等待买家确认";
        exit(json_encode($json));
    }

    /*
     * 买家确认交易
     */

    public function deal_buyer() {
        if (isSeccodeTimeout(false) == 0) {
            $json['status'] = '2';
            $json['message'] = '安全码过期，请重试';
            exit(json_encode($json));
        }
        $trade_id = I('request.trade_id');
        $where['to_mid'] = session('MEMBERINFO.id');
        $where['id'] = $trade_id;
        $tradeDetail = M("DomainTradeLog")->where($where)->find();
        if (!$tradeDetail) {
            $json['status'] = '2';
            $json['message'] = "交易信息获取失败，请重试";
            exit(json_encode($json));
        }
        if ($tradeDetail['status'] != 1 && $tradeDetail['status'] != 7) {
            $json['status'] = '2';
            $json['message'] = "非法操作";
            exit(json_encode($json));
        }
        $fromId = $tradeDetail['from_mid'];
        $toId = $tradeDetail['to_mid'];
        if ($tradeDetail['type'] == 2) {
            // 拍卖
            $tradeTypeId = 3; // 此id是域名过户的类型id
            $remark = "拍卖交易：Form:{$fromId},To:{$toId},Money:{$tradeDetail['money']}分";
            M()->startTrans();
            // 检查域名是否转入,转入了直接交易。没转入的，冻结买家50元保证金，进入等待卖家转入域名
            if (isDomainTransfer($tradeDetail['domain'])) {
                $tradeDetail['status'] = '2';
                $tradeDetail['update_time'] = time();
                $result = M("DomainTradeLog")->data($tradeDetail)->save();
                if (!$result) {
                    M()->rollback();
                    $json['status'] = '2';
                    $json['message'] = "系统出错，请稍后重试,error:001";
                    exit(json_encode($json));
                }
                unset($result);
                // 双方解冻冻结款开始
                $result = TradeUnLockTwo($trade_id);
                if (!$result) {
                    M()->rollback();
                    $json['status'] = '2';
                    $json['message'] = "系统出错，请稍后重试,error:002";
                    exit(json_encode($json));
                }
                $result = domainTransfer($tradeTypeId, $tradeDetail['domain'], $fromId, $toId, $tradeDetail['money'], $remark);
                if ($result == 1) {
                    M()->commit();
                    $json['status'] = '1';
                    $json['message'] = "操作成功";
                    $json['jump'] = "/domain/buylist?pst=finish";
                    exit(json_encode($json));
                }
                M()->rollback();
                $errorMessage = array('-1' => '余额不足', '-2' => '修改域名表的域名状态失败', '-3' => '付款失败', '-4' => '收款失败', '-5' => '生成消费日志失败', '-6' => '生成收入日志失败');
                $json['status'] = '2';
                $json['message'] = $errorMessage[$result];
                exit(json_encode($json));
            } else {
                $tradeDetail['status'] = '6';
                $tradeDetail['update_time'] = time();
                $result = M("DomainTradeLog")->data($tradeDetail)->save();
                if (!$result) {
                    M()->rollback();
                    $json['status'] = '2';
                    $json['message'] = "系统出错，请稍后重试,error:002";
                    exit(json_encode($json));
                }
                unset($result);
                M()->commit();
                $json['status'] = '1';
                $json['message'] = "操作成功，等待买家域名转入";
                exit(json_encode($json));
            }
        } elseif ($tradeDetail['type'] == 3) {
            // 出价
            $tradeTypeId = 4; // 此id是域名过户的类型id
            M()->startTrans();
            // 检查域名是否转入,转入了直接交易。没转入的，冻结买家50元保证金，进入等待卖家转入域名
            if (isDomainTransfer($tradeDetail['domain'])) {
                $tradeDetail['status'] = '2';
                $tradeDetail['update_time'] = time();
                $result = M("DomainTradeLog")->data($tradeDetail)->save();
                if (!$result) {
                    M()->rollback();
                    $json['status'] = '1';
                    $json['message'] = "系统出错，请稍后重试,error:001";
                    exit(json_encode($json));
                }
                // 双方解冻冻结款开始
                $result = TradeUnLockTwo($trade_id);
                if (!$result) {
                    M()->rollback();
                    $json['status'] = '1';
                    $json['message'] = "系统出错，请稍后重试,error:002";
                    exit(json_encode($json));
                }
                $remark = "买方出价：Form:{$fromId},To:{$toId},Money:{$tradeDetail['money']}分";
                $result = domainTransfer($tradeTypeId, $tradeDetail['domain'], $fromId, $toId, $tradeDetail['money'], $remark);
                if ($result == 1) {
                    M()->commit();
                    $json['status'] = '1';
                    $json['message'] = "操作成功";
                    $json['jump'] = "/domain/buylist?pst=finish";
                    exit(json_encode($json));
                }
                M()->rollback();
                $errorMessage = array('-1' => '余额不足', '-2' => '修改域名表的域名状态失败', '-3' => '付款失败', '-4' => '收款失败', '-5' => '生成消费日志失败', '-6' => '生成收入日志失败');
                $json['status'] = '2';
                $json['message'] = $errorMessage[$result];
                exit(json_encode($json));
            } else {
                $tradeDetail['status'] = '6';
                $tradeDetail['update_time'] = time();
                $result = M("DomainTradeLog")->data($tradeDetail)->save();
                if (!$result) {
                    M()->rollback();
                    $json['status'] = '2';
                    $json['message'] = "系统出错，请稍后重试,error:002";
                    exit(json_encode($json));
                }
                unset($result);
                $result = TradeLockMoney(5000, $trade_id, 'to');
                if ($result) {
                    M()->commit();
                    $json['status'] = '1';
                    $json['message'] = "操作成功，等待买家域名转入";
                    exit(json_encode($json));
                }
                M()->rollback();
                $json['status'] = '1';
                $json['message'] = "系统出错，请稍后重试,error:003";
                exit(json_encode($json));
            }
        }
    }

    /*
     * 买家购买域名交易列表
     */

    public function buylist() {
        if (!I('request.pst') || I('request.pst') == 'all') {
            $this->assign('pst', 'all');
        } else {
            if (I('request.pst') == 'trading') {
                $where['status'] = array('in', '0,1,6,7,8');
                $this->assign('pst', 'trading');
            }
            if (I('request.pst') == 'finish') {
                $where['status'] = array('in', '2');
                $this->assign('pst', 'finish');
            }
            if (I('request.pst') == 'bad') {
                $where['status'] = array('in', '3,4,5');
                $this->assign('pst', 'bad');
            }
        }
        if (I('request.type')) {
            $where['type'] = array('eq', I('request.type'));
        }
        $p = max(1, intval($_GET['p']));
        $where['to_mid'] = session('MEMBERINFO.id');

        $total = M('DomainTradeLog')->where($where)->count();
        $domains = M('DomainTradeLog')->where($where)->page($p)->select();
        // echo M('DomainTradeLog')->getLastSql();
        $this->assign('domains', $domains);
        $pager = new PageForMember($total);
        $pager->url = '/domain/buylist?p=' . urlencode('[PAGE]');
        $this->assign('pager', $pager->show());
        $this->assign('status', intval(I('post.status')));
        $this->assign('m_tab', 'buy_domain');
        $this->display();
    }

    /*
     * 收到的报价
     */

    public function baojialist() {
        $p = max(1, intval($_GET['p']));
        $where['from_mid'] = session('MEMBERINFO.id');
        if (!I('request.pst') || I('request.pst') == 'all') {
            $this->assign('pst', 'all');
        } else {
            if (I('request.pst') == 'trading') {
                $where['status'] = array('in', '0,1,6,7,8');
                $this->assign('pst', 'trading');
            }
            if (I('request.pst') == 'finish') {
                $where['status'] = array('in', '2');
                $this->assign('pst', 'finish');
            }
            if (I('request.pst') == 'bad') {
                $where['status'] = array('in', '3,4,5');
                $this->assign('pst', 'bad');
            }
        }
        if (I('request.type')) {
            $where['type'] = array('eq', I('request.type'));
        }
        $total = M('DomainTradeLog')->where($where)->count();
        $domains = M('DomainTradeLog')->where($where)->page($p)->select();
        $this->assign('domains', $domains);
        $pager = new PageForMember($total);
        $pager->url = '/domain/baojialist?p=' . urlencode('[PAGE]');
        $this->assign('pager', $pager->show());
        $this->assign('status', intval(I('post.status')));
        $this->assign('m_tab', 'sell_domain');
        $this->display();
    }

    public function paimailist() {
        $p = max(1, intval($_GET['p']));
        //拍卖最多90天，查询用户最近90天内参与的拍卖
        $where['mid'] = session('MEMBERINFO.id');
        $where['update_time'] = array('gt', time() - 86400 * 90);
        $paimaiLog = M("DomainAuctionLog")->where($where)->field("distinct(`pid`) as sale_id")->select();

        unset($where);
        if ($paimaiLog) {
            $mt[] = 0;
            foreach ($paimaiLog as $list) {
                $mt[] = $list['sale_id'];
            }
            $ids = implode(',', $mt);
        }
        $where['id'] = array('in', $ids);
        $total = M('MembersDomainSales')->where($where)->count();
        $domains = M('MembersDomainSales')->where($where)->page($p)->select();
        $this->assign('domains', $domains);
        $pager = new PageForMember($total);
        $pager->url = '/domain/paimailist?p=' . urlencode('[PAGE]');
        $this->assign('pager', $pager->show());
        $this->assign('m_tab', 'buy_domain');
        $this->assign('mid', session('MEMBERINFO.id'));
        $this->assign('pst', 'paimai');
        $this->display();
    }

    /*
     * 出售域名列表
     */

    public function salelist() {
        $p = max(1, intval($_GET['p']));
        $where['mid'] = session('MEMBERINFO.id');
        $url = '';
        // 域名包含
        if (I('request.domain')) {
            $domain = I('request.domain');
            $url .= "&domain={$domain}";
            if (!I('request.domain_tou') && !I('request.domain_wei')) {
                $where['base'] = array('like', "%{$domain}%");
            }
            if (I('request.domain_tou')) {
                $where['base'] = array('like', "{$domain}%");
            }
            if (I('request.domain_wei')) {
                $where['base'] = array('like', "%{$domain}");
            }
        }
        // 域名过滤
        if (I('request.filter')) {
            $filter = I('request.filter');
            $url .= "&filter={$filter}";
            if (!I('request.filter_tou') && !I('request.filter_wei')) {
                $where['base'] = array('notlike', "%{$filter}%");
            }
            if (I('request.filter_tou')) {
                $where['base'] = array('notlike', "{$filter}%");
                $url .= "&filter_tou=1";
            }
            if (I('request.filter_wei')) {
                $where['base'] = array('notlike', "%{$filter}");
                $url .= "&filter_wei=1";
            }
        }
        // 域名分类
        if (I("request.classify")) {
            $classify = I("request.classify");
            $mt = M("DomainClassify")->find($classify);
            if ($mt[pid] > 0) {
                $where['classify2'] = $classify;
            } else {
                $where['classify1'] = $classify;
            }
            unset($mt);
            $url .= "&classify={$classify}";
        }
        // 域名后缀
        if (I("request.suffix")) {
            $suffix = I("request.suffix");
            $where['suffix'] = $suffix;
            $url .= "&suffix={$suffix}";
        }
        // 交易类型
        if (I("request.trade_type")) {
            $trade_type = I("request.trade_type");
            $where['trade_type'] = $trade_type;
            $url .= "&trade_type={$trade_type}";
        }
        // 出售类型
        // var_dump(I("request.type"));exit();
        if (I("request.type")) {
            $type = intval(I("request.type"));
            $where['type'] = $type;
            $url .= "&type={$type}";
        }
        // 域名长度
        if (I("request.length_from")) {
            $length_from = I("request.length_from");
            $where['length'] = array('egt', $length_from);
            $url .= "&length_from={$length_from}";
        }
        if (I("request.length_to")) {
            $length_to = I("request.length_to");
            $where['length'] = array('elt', $length_to);
            $url .= "&length_to={$length_to}";
        }
        // 价格范围
        if (I('request.price_from') && I('request.price_to')) {
            $price_from = I('request.price_from') * 100;
            $price_to = I('request.price_to') * 100;
            $where['seller_price'] = array(array('egt', $price_from), array('elt', $price_to));
            $url .= "&price_from=" . I('request.price_from') . "&price_to=" . I('request.price_to');
        }
        if (I("get.pst") == 'liupai') {
            $where['buyer_mid'] = 0;
            $where['type'] = '2';
            $total = M('MembersDomainSalesFinish')->where($where)->count();
            $domains = M('MembersDomainSalesFinish')->where($where)->page($p)->select();
            $url .= "&pst=liupai";
            $this->assign("pst", 'liupai');
        } if (I("get.pst") == 'suc') {
            $where['buyer_mid'] = array('gt', 0);
            $total = M('MembersDomainSalesFinish')->where($where)->count();
            $domains = M('MembersDomainSalesFinish')->where($where)->page($p)->select();
            $url .= "&pst=suc";
            $this->assign("pst", 'suc');
        } else {
            $total = M('MembersDomainSales')->where($where)->count();
            $domains = M('MembersDomainSales')->where($where)->page($p)->select();
        }
        $this->assign('domains', $domains);
        $pager = new PageForMember($total);
        $pager->url = '/domain/salelist?p=' . urlencode('[PAGE]') . $url;
        $this->assign('pager', $pager->show());
        $this->assign("domainClassify", M("DomainClassify")->select());
        $this->assign('m_tab', 'sell_domain');
        $this->display();
    }

    /*
     * 域名出售
     * 1: 一口价
     * 2：竞价
     * 3：询价
     */

    public function sale() {
        $domains = I('post.domainId');
        if (is_array($domains)) {
            $MembersDomains = D('MembersDomains');
            foreach ($domains as $list) {
                $mt = $MembersDomains->Detail($list);
                if ($mt['mid'] == session('MEMBERINFO.id') && $mt['is_transfer'] > 0 && $mt['status'] == '100') {
                    $domainsList[] = $mt;
                }
                unset($mt);
            }
        }
        if (I('post.type') == '1') {
            $template = "sale_yikoujia";
        } else if (I('post.type') == '2') {
            $template = "sale_paimai";
        } else if (I('post.type') == '3') {
            $template = "sale_chujia";
        } else if (I('post.type') == '4') {
            $domainUrl = null;
            foreach ($domainsList as $list) {
                if ($domainUrl) {
                    $domainUrl .= "\r" . $list['domain'];
                } else {
                    $domainUrl = $list['domain'];
                }
            }
            $url = "/domain/push_do?domain=" . urlencode($domainUrl);
            header("Location:{$url}");
            exit();
        }
        $domainTradeType = M('DomainTradeType')->select();
        $this->assign('type', I('post.type'));
        $this->assign('num', sizeof($domainsList));
        $this->assign('domainTradeType', $domainTradeType);
        $this->assign('domainsList', $domainsList);
        $this->display($template);
    }

    /*
     * 上架确认操作
     */

    public function sale_confirm() {
        isSeccodeTimeout();
        $type = I('post.type');
        $domainId = I('post.domainId');
        if (!is_array($domainId)) {
            $this->error("请选择需要交易域名");
        }
        $price = I("post.price");
        $day = I("post.day");
        $time = I("post.time");
        $summary = I("post.summary");
        $MembersDomains = D('MembersDomains');
        $MembersDomainSales = D('MembersDomainSales');
        $stautsArray = array('100' => '正常', '101' => 'push锁定', '102' => '一口价锁定', '103' => '拍卖锁定', '104' => '询价锁定');
        M()->startTrans();
        switch ($type) {
            case "1":
                foreach ($domainId as $key => $list) {
                    $mt = $MembersDomains->Detail($list);
                    if ($mt['mid'] != session('MEMBERINFO.id')) {
                        continue;
                    }
                    if ($mt['is_transfer'] != '1') {
                        M()->rollback();
                        $this->error("{$list}需要转入本平台才能发布一口价");
                    }
                    if ($mt['status'] != '100') {
                        M()->rollback();
                        $this->error("{$list},{$stautsArray[$mt['status']]},请先下架再重新发布交易");
                    }
                    $mt['status'] = '102'; // 一口价锁定域名
                    $result = M("MembersDomains")->data($mt)->save();
                    if (!$result) {
                        M()->rollback();
                        $this->error("系统出错请重试,error:002");
                    }
                    $array['domain'] = $mt['domain'];
                    $array['summary'] = $summary[$key];
                    $array['mid'] = session('MEMBERINFO.id');
                    $array['seller_price'] = max(1, intval($price[$key]));
                    $trade_key = $key + 1;
                    $array['trade_type'] = implode(',', I("post.tradetype{$trade_key}"));
                    $array['trade_type'] = $array['trade_type'] ? $array['trade_type'] : '';
                    $array['suffix'] = Domain::getSuffix($mt['domain']);
                    $array['create_time'] = time();
                    $array['type'] = $type;
                    $array['end_time'] = strtotime(date('Y-m-d')) + 86400 * $day[$key] + 3600 * $time[$key] + rand(1, 3600);
                    $result = $MembersDomainSales->AddSale($array);
                    if (!$result) {
                        M()->rollback();
                        $this->error("系统出错请重试,error:001");
                    }
                }
                M()->commit();
                $this->success("一口价设置成功", "/domain/my");
                exit();
            case "2":
                foreach ($domainId as $key => $list) {
                    $mt = $MembersDomains->Detail($list);
                    if ($mt['mid'] != session('MEMBERINFO.id')) {
                        continue;
                    }
                    if ($mt['is_transfer'] != '1') {
                        if ($mt['expire_time'] <= date('Y-m-d', time() + 86400 * 15)) {
                            M()->rollback();
                            $this->error($list . "域名即将到期，不能交易");
                        }
                        if (date('Y-m-d', strtotime($mt['expire_time']) + 86400 * 60) >= date('Y-m-d')) {
                            M()->rollback();
                            $this->error($list . "域名注册没超过60天，不能交易");
                        }
                    }
                    if ($mt['status'] != '100') {
                        M()->rollback();
                        $this->error("{$list},{$stautsArray[$mt['status']]},请先下架再重新发布交易");
                    }
                    $mt['status'] = '103'; // 一口价锁定域名
                    $result = M("MembersDomains")->data($mt)->save();
                    if (!$result) {
                        M()->rollback();
                        $this->error("系统出错请重试,error:002");
                    }
                    if ($mt['is_transfer'] != '1') {
                        $array['lock_money'] = '5000';
                        $result = LockUnLockMoney(session('MEMBERINFO.id'), 5000);
                        if ($result == '-1') {
                            M()->rollback();
                            $this->error("没转入的域名拍卖需要冻结50元保证金，账户余额不足");
                        }
                        if ($result != '1') {
                            M()->rollback();
                            $this->error("系统出错，请稍后重试,error:003");
                        }
                    }
                    $array['domain'] = $mt['domain'];
                    $array['summary'] = $summary[$key];
                    $array['mid'] = session('MEMBERINFO.id');
                    $array['seller_price'] = max(1, $price[$key]);
                    $trade_key = $key + 1;
                    $array['trade_type'] = implode(',', I("post.tradetype{$trade_key}"));
                    $array['trade_type'] = $array['trade_type'] ? $array['trade_type'] : '';
                    $array['suffix'] = Domain::getSuffix($mt['domain']);
                    $array['create_time'] = time();
                    $array['type'] = $type;
                    $array['end_time'] = strtotime(date('Y-m-d')) + 86400 * $day[$key] + 3600 * $time[$key] + rand(1, 3600);
                    $result = $MembersDomainSales->AddSale($array);
                    if (!$result) {
                        M()->rollback();
                        $this->error("系统出错请重试,error:001");
                    }
                    unset($array);
                }
                M()->commit();
                $this->success("拍卖设置成功", "/domain/my");
                exit();
            case "3":
                foreach ($domainId as $key => $list) {
                    $mt = $MembersDomains->Detail($list);
                    if ($mt['mid'] != session('MEMBERINFO.id')) {
                        continue;
                    }
                    if ($mt['is_transfer'] != '1') {
                        if (date('Y-m-d', strtotime($mt['expire_time'])) <= date('Y-m-d', time() + 86400 * 15)) {
                            M()->rollback();
                            $this->error($list . "域名即将到期，不能交易");
                        }
                        if (date('Y-m-d', strtotime($mt['expire_time']) + 86400 * 60) >= date('Y-m-d')) {
                            M()->rollback();
                            $this->error($list . "域名注册没超过60天，不能交易");
                        }
                    }
                    if ($mt['status'] != '100') {
                        M()->rollback();
                        $this->error("{$list},{$stautsArray[$mt['status']]},请先下架再重新发布交易");
                    }
                    $mt['status'] = '104'; // 一口价锁定域名
                    $result = M("MembersDomains")->data($mt)->save();
                    if (!$result) {
                        M()->rollback();
                        $this->error("系统出错请重试,error:002");
                    }
                    $array['domain'] = $mt['domain'];
                    $array['summary'] = $summary[$key];
                    $array['mid'] = session('MEMBERINFO.id');
                    $array['seller_price'] = max(1, intval($price[$key]));
                    $trade_key = $key + 1;
                    $array['trade_type'] = implode(',', I("post.tradetype{$trade_key}"));
                    $array['trade_type'] = $array['trade_type'] ? $array['trade_type'] : '';
                    $array['suffix'] = Domain::getSuffix($mt['domain']);
                    $array['create_time'] = time();
                    $array['type'] = $type;
                    $array['end_time'] = 0;
                    $result = $MembersDomainSales->AddSale($array);
                    if (!$result) {
                        M()->rollback();
                        $this->error("系统出错请重试,error:001");
                    }
                    unset($array);
                }
                M()->commit();
                $this->success("买家出价设置成功", "/domain/my");
                exit();
            default:
                exit();
        }
    }

    /*
     * 域名批量下架
     */

    public function batch_xiajia() {
        isSeccodeTimeout();
        $saleIds = I('post.saleIds');
        if (is_array($saleIds)) {
            $MembersDomainSales = D('MembersDomainSales');
            $where['mid'] = session('MEMBERINFO.id');
            M()->startTrans();
            foreach ($saleIds as $list) {
                $where['id'] = $list;
                $mt = M('MembersDomainSales')->where(array('mid' => session('MEMBERINFO.id'), 'id' => $list))->find();
                if (!$mt) {
                    $this->error("系统出错,请稍后重试");
                }
                if ($mt['type'] == '2' && $mt['times'] > 0) {
                    $this->error($mt['domain'] . "正处于拍卖状态，并且有人出价，不能下架。");
                }
                //下架
                $result = $MembersDomainSales->Del($mt['id']);
                if ($result != '1') {
                    M()->rollback();
                    $this->error($result);
                }
            }
            M()->commit();
            $this->success("域名下架成功");
        } else {
            $this->error("请选择需要下架的域名");
        }
    }

    public function batch_sale_edit() {
        $saleIds = I('post.saleIds');
        $step = I("post.step") ? I("post.step") : '1';
        if ($step == '1') {
            if (is_array($saleIds)) {
                $where['mid'] = session('MEMBERINFO.id');
                $where['id'] = array('in', implode(',', $saleIds));
                $salesList = M("MembersDomainSales")->where($where)->select();
                $this->assign('salesList', $salesList);
                $this->assign('num', sizeof($salesList));
                $domainTradeType = M('DomainTradeType')->select();
                $this->assign('domainTradeType', $domainTradeType);
                $this->display();
                exit();
            } else {
                $this->error("请选择需要下架的域名");
            }
        }
        if ($step == '2') {
            isSeccodeTimeout();
            $MembersDomainSales = D('MembersDomainSales');
            M()->startTrans();
            $price = I("post.price");
            $day = I("post.day");
            $time = I("post.time");
            $summary = I("post.summary");
            $where['mid'] = session('MEMBERINFO.id');
            foreach ($saleIds as $key => $list) {
                $where['id'] = $list;
                $mt = M("MembersDomainSales")->where($where)->find();
                if (!$mt) {
                    continue;
                }
                if ($mt['type'] == '2' && $mt['times'] > 0) {
                    M()->rollback();
                    $this->error($mt['domain'] . "正在拍卖，已经有人出价，不能修改起拍价");
                }
                $array['summary'] = $summary[$key];
                $array['seller_price'] = max(1, intval($price[$key]));
                $trade_key = $key + 1;
                $array['trade_type'] = implode(',', I("post.tradetype{$trade_key}"));
                $array['trade_type'] = $array['trade_type'] ? $array['trade_type'] : '';
                if ($mt['type'] != '3') {
                    $array['end_time'] = strtotime(date('Y-m-d')) + 86400 * $day[$key] + 3600 * $time[$key] + rand(1, 3600);
                }
                $result = $MembersDomainSales->Update($array, $list);
                if (!$result) {
                    M()->rollback();
                    $this->error("系统出错请重试,error:001");
                }
            }
            M()->commit();
            $this->success("设置成功", "/domain/salelist");
        }
    }

    public function batch_push() {
        
    }

    /**
     * 域名PUSH
     */
    public function push_do() {
        if (IS_POST) {
            if (!isSeccodeTimeout(false)) {
                $this->error("安全码超时，请重新输入");
            }
            $domains = explode("\r\n", I('post.domains'));
            for ($i = 0; $i < count($domains); $i++) {
                if (!preg_match('/([a-zA-Z0-9\-]+)([\.a-zA-Z]+)/i', $domains[$i]))
                    unset($domains[$i]);
            }
            if (count($domains) < 1 || count($domains) > 100) {
                $this->error('要转移的域名列表为空或超过最大数量(100)限制。');
            }
            // 去重
            $domains = array_unique($domains);
            foreach ($domains as $domain) {
                // 需要先验证待转域名是否归本人所有
                $domainInfo = M('MembersDomains')->where(array('domain' => $domain, 'mid' => session('MEMBERINFO.id')))->find();
                if (!is_array($domainInfo)) {
                    $this->error($domain . '&nbsp;非本人名下域名，请剔除后重新提交。');
                }
                if ($domainInfo['is_transfer'] != '1') {
                    $this->error($domain . '非本站域名,不能push');
                }
                if ($domainInfo['expire_time'] <= date('Y-m-d')) {
                    $this->error($domain . '域名即将到期，不能push');
                }
                // 验证域名在平台内的交易状态
                if ($domainInfo['status'] == '101') {
                    $this->error($domain . '&nbsp;处于正在交易中，请先取消交易再PUSH。');
                }
            }
            if (session('MEMBERINFO.id') == I('post.mid')) {
                $this->error('请不要PUSH域名给自己。');
            }
            // 验证接收方ID是否存在
            $memberInfo = M('Members')->where(array('id' => I('post.mid')))->find();
            if (!is_array($memberInfo)) {
                $this->error('您输入的接收方ID有误，请重新输入。');
            }
            if (I('post.email') && $memberInfo['username'] != I('post.email')) {
                $this->error('对方邮箱与ID不匹配，请重新输入。');
            }
            if (!is_numeric(I('post.money')) || I('post.money', 0, 'intval') < 0) {
                $this->error('请输入正确的金额。');
            }
            M('MembersDomainPush')->startTrans();
            $data['title'] = count($domains) > 1 ? ('打包（' . count($domains) . '）') : '单个';
            $data['domains'] = implode("\r\n", $domains);
            $data['mid_from'] = session('MEMBERINFO.id');
            $data['mid_to'] = I('post.mid');
            $data['money'] = I('post.money');
            $data['talkto'] = I('post.talkto');
            $data['create_time'] = time();
            $data['status'] = '0';
            $result = M('MembersDomainPush')->data($data)->add();
            if (!$result) {
                M('MembersDomainPush')->rollback();
                $this->error('创建PUSH域名请求失败，请重试。[错误码:001]');
            }
            // 设置待转移域名的状态
            foreach ($domains as $domain) {
                unset($data, $where, $result);
                $data['status'] = '101';
                $where['domain'] = $domain;
                $where['mid'] = session('MEMBERINFO.id');
                $result = M('MembersDomains')->where($where)->data($data)->save();
                if (!$result) {
                    M('MembersDomainPush')->rollback();
                    $this->error('创建PUSH域名请求失败，请重试。[错误码:002]');
                }
            }
            // 发送站内信
            unset($data, $result);
            $title = '您收到来自[' . session('MEMBERINFO.id') . ']的域名PUSH';
            $message = '尊敬的客户[' . I('post.mid') . ']<br/>您好，您收到来自[' . session('MEMBERINFO.id') . ']的域名PUSH，请尽快处理。<br/>[' . implode(',', $domains) . ']<br/><a href="http://' . I('server.HTTP_HOST') . '/domain/push_from">点击这里处理PUSH请求</a>。';
            $data['title'] = $title;
            $data['content'] = $message;
            $data['create_time'] = time();
            $data['mid_to'] = I('post.mid');
            $data['status'] = '0';
            $result = M('Message')->data($data)->add();
            if (!$result) {
                M('MembersDomainPush')->rollback();
                $this->error('创建PUSH域名请求失败，请重试。[错误码:003]');
            }
            // 发送邮件
            if (I('post.email')) {
                unset($result);
                $result = SendMail($memberInfo['username'], $title, $message, '米仓网客户服务中心');
                if (!$result) {
                    M('MembersDomainPush')->rollback();
                    $this->error('创建PUSH域名请求失败，邮件发送失败，请确认邮件地址正确和邮箱可用。[错误码:004]');
                }
            }
            M('MembersDomainPush')->commit();
            $this->success('PUSH域名请求已发送。', '/domain/push_to');
            exit();
        }
        $where['id'] = session('MEMBERINFO.id');
        $memberInfo = M('Members')->where($where)->find();
        $this->assign('memberInfo', $memberInfo);
        $this->display();
    }

    /**
     * 域名PUSH 发送的请求
     *
     * @param int $p            
     */
    public function push_to($p = 1) {
        if (IS_POST) {
            if (I('post.domains') != '') {
                $where['domains'] = I('post.domains');
            }
            if (I('post.status') != '') {
                $where['domains'] = I('post.status');
            }
        }
        $where['mid_from'] = session('MEMBERINFO.id');
        $order['create_time'] = 'DESC';
        $total = M('MembersDomainPush')->where($where)->count();
        $result = M('MembersDomainPush')->where($where)->order($order)->page($p)->select();
        $this->assign('lists', $result);
        $pager = new PageForMember($total);
        $pager->url = '/domain/push_to?p=' . urlencode('[PAGE]');
        $this->assign('pager', $pager->show());
        $this->display();
    }

    /**
     * 域名PUSH收到的请求
     *
     * @param int $p            
     */
    public function push_from($p = 1) {
        if (IS_POST) {
            if (I('post.domains') != '') {
                $where['domains'] = I('post.domains');
            }
            if (I('post.status') != '') {
                $where['domains'] = I('post.status');
            }
        }
        $where['mid_to'] = session('MEMBERINFO.id');
        $order['create_time'] = 'DESC';
        $total = M('MembersDomainPush')->where($where)->count();
        $result = M('MembersDomainPush')->where($where)->order($order)->page($p)->select();
        $this->assign('lists', $result);
        $pager = new PageForMember($total);
        $pager->url = '/domain/push_from?p=' . urlencode('[PAGE]');
        $this->assign('pager', $pager->show());
        $this->display();
    }

    /**
     * 接收域名PUSH
     */
    public function push_accept($id) {
        // 检测ID状态
        $where['id'] = $id;
        $where['mid_to'] = session('MEMBERINFO.id');
        $pushInfo = M('MembersDomainPush')->where($where)->find();
        if (!is_array($pushInfo)) {
            $this->error('没有找到这条请求，请确认ID正确。');
        }
        if ($pushInfo['status'] != '0') {
            $this->error('PUSH请求状态有误。', '/domain/push_from');
        }
        if (IS_POST) {
            $templateId = I('post.tmpl', 0, 'intval');
            // 检测是否有域名模板
            unset($where, $result);
            $where['id'] = $templateId;
            $where['mid'] = $pushInfo['mid_to'];
            $result = M('MembersDomainTemplate')->where($where)->count();
            if ($result == 0) {
                $this->error('没有找到对应的域名模板，请确认。');
            }
            // 开始执行PUSH接收操作
            M('MembersDomainPush')->startTrans();
            unset($result);
            $domains = array_map('strtolower', explode("\r\n", $pushInfo['domains']));
            $domainsMoney = $pushInfo['money'] * 100;
            $result = domainTransfer(1, $domains, $pushInfo['mid_from'], $pushInfo['mid_to'], $domainsMoney, 'PUSH ID:' . $id);
            switch ($result) {
                case 0:
                    $this->error('功能异常，请联系客服人员。');
                case -1:
                    $this->error('帐户余额不足，请先充值。', '/recharge/add');
                    break;
                case -2:
                    $this->error('接收域名失败，请重试[错误码:001]。');
                    break;
                case -3:
                    $this->error('接收域名失败，请重试[错误码:002]。');
                    break;
                case -4:
                    $this->error('接收域名失败，请重试[错误码:003]。');
                    break;
                case -5:
                    $this->error('接收域名失败，请重试[错误码:004]。');
                    break;
                case -6:
                    $this->error('接收域名失败，请重试[错误码:005]。');
                    break;
            }
            // 变更记录状态
            unset($where, $data, $result);
            $data['status'] = '1';
            $data['finish_time'] = time();
            $where['id'] = $id;
            $where['mid_to'] = $pushInfo['mid_to'];
            $result = M('MembersDomainPush')->where($where)->data($data)->save();
            if (!$result) {
                M('MembersDomainPush')->rollback();
                $this->error('接收域名失败，请重试[错误码:006]。');
            }
            // 待变更域名入队列
            // ----获取各域名的注册商
            $domainsRegistrar = M('MembersDomains')->where(array('domain' => array('IN', $domains)))->getField('domain, registrar');
            for ($i = 0; $i < count($domains); $i++) {
                unset($result);
                $result = D('DomainWhoisQueue')->push($domains[$i], $templateId, $domainsRegistrar[$domains[$i]]);
                if (!$result) {
                    M('MembersDomainPush')->rollback();
                    $this->error('接收域名失败，请重试[错误码:007]。');
                }
            }
            unset($data);
            // 写入交易日志表
            $data['domain'] = $pushInfo['domains'];
            $data['from_mid'] = $pushInfo['mid_from'];
            $data['to_mid'] = $pushInfo['mid_to'];
            $data['money'] = $domainsMoney;
            $data['type'] = '4'; // 域名push
            $data['status'] = '2'; // 成功交易
            $data['update_time'] = time();
            $data['memo'] = "域名push交易,PUSH ID:{$id}";
            $result = M("DomainTradeLog")->data($data)->add();
            if (!$result) {
                $this->error("交易日志写入失败，请重试");
            }
            M('MembersDomainPush')->commit();
            $this->success('接收域名成功。', '/domain/push_from');
            exit();
        }
        $this->assign('pushInfo', $pushInfo);
        $templates = M('MembersDomainTemplate')->where(array('mid' => $pushInfo['mid_to']))->getField('id,title');
        if (!is_array($templates)) {
            $this->error('您还没有填写域名模板，无法接收域名。');
        }
        $this->assign('templates', $templates);
        $where['id'] = session('MEMBERINFO.id');
        $memberInfo = M('Members')->where($where)->find();
        if (!$memberInfo['weixin'] && $memberInfo['code_status'] == '2') {
            $res['url'] = '/account/weixin';
            $res['msg'] = '您的微信未绑定，点击跳转...';
        } elseif (!$memberInfo['seccode'] && $memberInfo['code_status'] == '1') {
            $res['url'] = '/account/password';
            $res['msg'] = '您的安全码未设置，点击跳转...';
        }
        $this->assign('res', $res);
        $this->display();
    }

    /**
     * 拒绝域名PUSH
     */
    public function push_refuse($id) {
        // 检测ID状态
        $where['id'] = $id;
        $where['mid_to'] = session('MEMBERINFO.id');
        $pushInfo = M('MembersDomainPush')->where($where)->find();
        if (!is_array($pushInfo)) {
            $this->ajaxReturn(array('status' => 500, 'message' => '没有找到这条请求，请确认ID正确。'));
        }
        if ($pushInfo['status'] != '0') {
            $this->ajaxReturn(array('status' => 500, 'message' => 'PUSH请求状态有误。'));
        }
        // 变更域名状态
        M('MembersDomainPush')->startTrans();
        unset($where);
        $data['status'] = '100';
        $where['domain'] = array('IN', explode("\r\n", $pushInfo['domains']));
        $where['mid'] = $pushInfo['mid_from'];
        $result = M('MembersDomains')->where($where)->data($data)->save();
        if (!$result) {
            M('MembersDomainPush')->rollback();
            $this->ajaxReturn(array('status' => 500, 'message' => '拒绝PUSH域名请求失败，请重试。'));
        }
        // 变更记录状态
        unset($where, $data, $result);
        $data['status'] = '2';
        $data['finish_time'] = time();
        $where['id'] = $id;
        $where['mid_to'] = session('MEMBERINFO.id');
        $result = M('MembersDomainPush')->where($where)->data($data)->save();
        if (!$result) {
            M('MembersDomainPush')->rollback();
            $this->ajaxReturn(array('status' => 500, 'message' => '拒绝PUSH域名请求失败，请重试。'));
        }
        M('MembersDomainPush')->commit();
        $this->ajaxReturn(array('status' => 200, 'message' => '拒绝PUSH域名请求成功', 'url' => '/domain/push_from'));
    }
    public function transfer_in(){
        
    }
    public function transfer_out(){
        
    }

    public function ajax_register_do($domain){
        $domain = strtolower($domain);
        $returnType = I('get.' . C('VAR_JSONP_HANDLER'))?'jsonp':'';
        // 从购物车里取出该域名对应的信息
        $where['mid'] = session('MEMBERINFO.id');
        $where['domain'] = $domain;
        $cartInfo = M('MembersDomainCart')->where($where)->find();
        if(!is_array($cartInfo)){
            $this->ajaxReturn(array('status'=>404, 'message'=>'待注册域名列表中查无此域名', 'data'=>Domain::encode($domain)), $returnType);
        }
        if($cartInfo['template'] == 0){
            $this->ajaxReturn(array('status'=>500, 'message'=>'待注册域名未指定注册资料模板', 'data'=>Domain::encode($domain)), $returnType);
        }
        if($cartInfo['age'] == 0){
            $this->ajaxReturn(array('status'=>500, 'message'=>'待注册域名未指定注册年限', 'data'=>Domain::encode($domain)), $returnType);
        }
        $suffix = $cartInfo['idn'] . $cartInfo['suffix']; // 域名后缀
        // 检测后缀是否开放注册
        $domainSuffixInfo = M('DomainSuffix')->where(array('name'=>$suffix))->find();
        if(!is_array($domainSuffixInfo)){
            $this->ajaxReturn(array('status'=>500, 'message'=>'不支持的域名后缀', 'data'=>Domain::encode($domain)), $returnType);
        }
        if($domainSuffixInfo['status'] == '0'){
            $this->ajaxReturn(array('status'=>500, 'message'=>'不支持的域名后缀', 'data'=>Domain::encode($domain)), $returnType);
        }
        if (!in_array($cartInfo['age'], explode(',', $domainSuffixInfo['allow_register_age']))){
            //检测域名注册年限是否在允许范围内
            $this->ajaxReturn(array('status'=>500, 'message'=>'域名注册年限不在允许范围内', 'data'=>Domain::encode($domain)), $returnType);
        }
        $registrar = $domainSuffixInfo['registrar']; // 使用哪个平台注册
        // 取注册资料
        unset($where);
        $where['id'] = $cartInfo['template'];
        $where['mid'] = session('MEMBERINFO.id');
        $template = M('MembersDomainTemplate')->where($where)->find();
        if(!is_array($template)){
            $this->ajaxReturn(array('status'=>404, 'message'=>'没有找到对应的注册资料模板', 'data'=>Domain::encode($domain)), $returnType);
        }
        // 检测资金是否足够
        // ----取用户最新资料
        $memberInfo = M('Members')->where(array('id'=>session('MEMBERINFO.id')))->find();
        // ----获取对应后缀的注册和续费价格
        unset($where);
        $where['suffix'] = $suffix;
        $where['level'] = $memberInfo['level'];
        $priceInfo = M('MemberLevelPriceMap')->where($where)->find();
        if(!is_array($priceInfo)){
            $this->ajaxReturn(array('status'=>404, 'message'=>'没有找到对应的价格', 'data'=>Domain::encode($domain)), $returnType);
        }
        // ----计算注册此域名所需费用
        $domainPrice = $priceInfo['price_register_year' . $cartInfo['age']];
        if($domainPrice <= 0){
            $this->ajaxReturn(array('status'=>404, 'message'=>'程序异常，域名费用计算有误', 'data'=>Domain::encode($domain)), $returnType);
        }
        // ----获取用户账户余额
        $memberMoney = M('MembersMoney')->where(array('mid'=>session('MEMBERINFO.id')))->find();
        if($domainPrice > $memberMoney['total_money']){
            $this->ajaxReturn(array('status'=>300, 'message'=>'账户资金不足，请充值', 'data'=>Domain::encode($domain)), $returnType);
        }
        // 开始执行注册流程
        M('MembersMoney')->startTrans();
        // ----扣除所需费用
        unset($where, $result, $data);
        $data['mid'] = session('MEMBERINFO.id');
        $data['total_money'] = array('exp', 'total_money-' . $domainPrice);
        if($domainPrice <= $memberMoney['recharge_money']){
            $data['recharge_money'] = array('exp', 'recharge_money-' . $domainPrice);
        }elseif($domainPrice > $memberMoney['recharge_money'] && $memberMoney['recharge_money'] > 0){
            // 充值余额字段大于0,但余额小于域名价格
            $tradeMoney = $domainPrice - $memberMoney['recharge_money'];
            $data['recharge_money'] = 0;
            $data['trade_money'] = array('exp', 'trade_money-' . $tradeMoney);
        }else{
            $data['trade_money'] = array('exp', 'trade_money-' . $domainPrice);
        }
        try{
            $result = M('MembersMoney')->data($data)->save();
        }finally {
            if(!$result){
                M('MembersMoney')->rollback();
                $this->ajaxReturn(array('status'=>500, 'message'=>'（001）注册失败，执行注册操作时发生异常', 'data'=>Domain::encode($domain)), $returnType);
            }
        }
        // ----记录用户资金消费日志
        unset($where, $result, $data);
        $data['mid'] = session('MEMBERINFO.id');
        $data['type'] = 'register';
        $data['registrar'] = $registrar;
        $data['time'] = time();
        $data['money'] = $domainPrice;
        $data['content'] = '域名：' . $domain . '，年限：' . $cartInfo['age'] . '年';
        try{
            $result = M('MembersConsumeDetail')->data($data)->add();
        }finally {
            if(!$result){
                M('MembersMoney')->rollback();
                $this->ajaxReturn(array('status'=>500, 'message'=>'（002）注册失败，执行注册操作时发生异常', 'data'=>Domain::encode($domain)), $returnType);
            }
        }
        // ----删除购物车数据
        unset($where, $result);
        $where['id'] = $cartInfo['id'];
        try{
            $result = M('MembersDomainCart')->where($where)->delete();
        }finally {
            if(!$result){
                M('MembersMoney')->rollback();
                $this->ajaxReturn(array('status'=>500, 'message'=>'（003）注册失败，执行注册操作时发生异常', 'data'=>Domain::encode($domain)), $returnType);
            }
        }
        // ----将域名加到“我的域名”列表中
        unset($where, $data, $result);
        $data['mid'] = session('MEMBERINFO.id');
        $data['domain'] = $domain;
        $data['is_transfer'] = 1;
        $data['length'] = Domain::getDomainNameLengthForWord($data['domain']);
        $data['base'] = Domain::getDomainName($data['domain']);
        $data['suffix'] = Domain::getSuffix($data['domain']);
        $data['register_time'] = date('Y-m-d');
        $data['expire_time'] = date('Y-m-d', strtotime('+' . $cartInfo['age'] . ' years'));
        $data['registrar'] = $registrar;
        $data['status'] = '100';
        try{
            $result = M('MembersDomains')->data($data)->add();
        }finally {
            if(!$result){
                M('MembersMoney')->rollback();
                $this->ajaxReturn(array('status'=>500, 'message'=>'（004）注册失败，执行注册操作时发生异常', 'data'=>Domain::encode($domain)), $returnType);
            }
        }
        // ----取出厂价
        unset($where);
        $where['name'] = $suffix;
        $suffixFactoryPrices = M('DomainSuffixPrice' . ucfirst($registrar))->where($where)->find();
        if(!is_array($suffixFactoryPrices)){
            M('MembersMoney')->rollback();
            $this->ajaxReturn(array('status'=>500, 'message'=>'（005）注册失败，执行注册操作时发生异常', 'data'=>Domain::encode($domain)), $returnType);
        }
        // ----取汇率
        if($registrar == 'webnic'){
            $moneyRate = M('Config')->where(array('name'=>'money_rate'))->find();
            if(!is_array($moneyRate)){
                M('MembersMoney')->rollback();
                $this->ajaxReturn(array('status'=>500, 'message'=>'（006）注册失败，执行注册操作时发生异常', 'data'=>Domain::encode($domain)), $returnType);
            }
        }
        // ----记录平台在厂商处的消费日志
        unset($where, $result, $data);
        $data['domain'] = $domain;
        $data['age'] = $cartInfo['age'];
        $data['type'] = 'register';
        $data['registrar'] = $registrar;
        $data['time'] = time();
        $data['money_factory'] = $suffixFactoryPrices['price_register_year' . $cartInfo['age']];
        $data['money_selling'] = $domainPrice; // 销售价
        $data['money_rate'] = $registrar == 'webnic'?$moneyRate['value']:''; // 汇率
        $data['content'] = '';
        try{
            $result = M('PlatformConsumeDetail')->data($data)->add();
        }finally {
            if(!$result){
                M('MembersMoney')->rollback();
                $this->ajaxReturn(array('status'=>500, 'message'=>'（007）注册失败，执行注册操作时发生异常', 'data'=>Domain::encode($domain)), $returnType);
            }
        }
        // ----调用注册接口
        unset($result);
        $domainApi = new DomainApi($registrar);
        $result = $domainApi->register($domain, $cartInfo['age'], $template);
        if(!$result){
            $errorInfo = $domainApi->getError();
            if($errorInfo['status'] === '-1'){
                // 因为接口异常引起的false
                // 如果连接中断,则域名入队列待检测注册状态,若检测结果为未注册,则返还注册金额
                $data['domain'] = $domain;
                $data['mid'] = session('MEMBERINFO.id');
                $data['registrar'] = $registrar;
                $data['money'] = $domainPrice;
                $data['type'] = 'register';
                $data['create_time'] = date('Y-m-d H:i:s');
                try{
                    M('MembersDomainBrokenQueue')->data($data)->add();
                }catch(\Exception $e){
                }
                // 需要变更域名表的status状态为锁定
                unset($where, $data);
                $where['mid'] = session('MEMBERINFO.id');
                $where['domain'] = $domain;
                $where['registrar'] = $registrar;
                $data['status'] = '105';
                try{
                    M('MembersDomains')->where($where)->data($data)->save();
                }catch(\Exception $e){
                }
                M('MembersMoney')->commit();
                $this->ajaxReturn(array('status'=>200, 'message'=>$errorInfo['message'], 'data'=>Domain::encode($domain)), $returnType);
            }else{
                M('MembersMoney')->rollback();
            }
            $this->ajaxReturn(array('status'=>500, 'message'=>$errorInfo['message'], 'data'=>Domain::encode($domain)), $returnType);
        }
        M('MembersMoney')->commit();
        $this->ajaxReturn(array('status'=>200, 'message'=>'注册成功', 'data'=>Domain::encode($domain)), $returnType);
    }
    public function ajax_renew_do($domain){
        $domain = strtolower($domain);
        $returnType = I('get.' . C('VAR_JSONP_HANDLER'))?'jsonp':'';
        // 从购物车里取出该域名对应的信息
        $where['mid'] = session('MEMBERINFO.id');
        $where['domain'] = $domain;
        $cartInfo = M('MembersDomainCart')->where($where)->find();
        if(!is_array($cartInfo)){
            $this->ajaxReturn(array('status'=>404, 'message'=>'待续费域名列表中查无此域名', 'data'=>Domain::encode($domain)), $returnType);
        }
        if($cartInfo['age'] == 0){
            $this->ajaxReturn(array('status'=>500, 'message'=>'待续费域名未指定续费年限', 'data'=>Domain::encode($domain)), $returnType);
        }
        $suffix = $cartInfo['idn'] . $cartInfo['suffix']; // 域名后缀
        // 检测后缀是否开放
        $domainSuffixInfo = M('DomainSuffix')->where(array('name'=>$suffix))->find();
        if(!is_array($domainSuffixInfo)){
            $this->ajaxReturn(array('status'=>500, 'message'=>'不支持的域名后缀', 'data'=>Domain::encode($domain)), $returnType);
        }
        if($domainSuffixInfo['status'] == '0'){
            $this->ajaxReturn(array('status'=>500, 'message'=>'不支持的域名后缀', 'data'=>Domain::encode($domain)), $returnType);
        }
        if (!in_array($cartInfo['age'], explode(',', $domainSuffixInfo['allow_renew_age']))){
            //检测域名续费年限是否在允许范围内
            $this->ajaxReturn(array('status'=>500, 'message'=>'域名续费年限不在允许范围内', 'data'=>Domain::encode($domain)), $returnType);
        }
        $registrar = $domainSuffixInfo['registrar']; // 使用哪个平台注册
        // 检测资金是否足够
        // ----取用户最新资料
        $memberInfo = M('Members')->where(array('id'=>session('MEMBERINFO.id')))->find();
        // ----获取对应后缀的注册和续费价格
        unset($where);
        $where['suffix'] = $suffix;
        $where['level'] = $memberInfo['level'];
        $priceInfo = M('MemberLevelPriceMap')->where($where)->find();
        if(!is_array($priceInfo)){
            $this->ajaxReturn(array('status'=>404, 'message'=>'没有找到对应的价格', 'data'=>Domain::encode($domain)), $returnType);
        }
        // ----计算续费此域名所需费用
        $domainPrice = $priceInfo['price_renew_year' . $cartInfo['age']];
        if($domainPrice <= 0){
            $this->ajaxReturn(array('status'=>404, 'message'=>'程序异常，域名费用计算有误', 'data'=>Domain::encode($domain)), $returnType);
        }
        // ----获取用户账户余额
        $memberMoney = M('MembersMoney')->where(array('mid'=>session('MEMBERINFO.id')))->find();
        if($domainPrice > $memberMoney['total_money']){
            $this->ajaxReturn(array('status'=>300, 'message'=>'账户资金不足，请充值', 'data'=>Domain::encode($domain)), $returnType);
        }
        // 开始执行续费流程
        M('MembersMoney')->startTrans();
        // ----扣除所需费用
        unset($where, $result, $data);
        $data['mid'] = session('MEMBERINFO.id');
        $data['total_money'] = array('exp', 'total_money-' . $domainPrice);
        if($domainPrice <= $memberMoney['recharge_money']){
            $data['recharge_money'] = array('exp', 'recharge_money-' . $domainPrice);
        }elseif($domainPrice > $memberMoney['recharge_money'] && $memberMoney['recharge_money'] > 0){
            // 充值余额字段大于0,但余额小于域名价格
            $tradeMoney = $domainPrice - $memberMoney['recharge_money'];
            $data['recharge_money'] = 0;
            $data['trade_money'] = array('exp', 'trade_money-' . $tradeMoney);
        }else{
            $data['trade_money'] = array('exp', 'trade_money-' . $domainPrice);
        }
        try{
            $result = M('MembersMoney')->data($data)->save();
        }finally {
            if(!$result){
                M('MembersMoney')->rollback();
                $this->ajaxReturn(array('status'=>500, 'message'=>'（001）续费失败，执行续费操作时发生异常', 'data'=>Domain::encode($domain)), $returnType);
            }
        }
        // ----记录用户资金消费日志
        unset($where, $result, $data);
        $data['mid'] = session('MEMBERINFO.id');
        $data['type'] = 'renew';
        $data['registrar'] = $registrar;
        $data['time'] = time();
        $data['money'] = $domainPrice;
        $data['content'] = '域名：' . $domain . '，年限：' . $cartInfo['age'] . '年';
        try{
            $result = M('MembersConsumeDetail')->data($data)->add();
        }finally {
            if(!$result){
                M('MembersMoney')->rollback();
                $this->ajaxReturn(array('status'=>500, 'message'=>'（002）续费失败，执行续费操作时发生异常', 'data'=>Domain::encode($domain)), $returnType);
            }
        }
        // ----删除购物车数据
        unset($where, $result);
        $where['id'] = $cartInfo['id'];
        try{
            $result = M('MembersDomainCart')->where($where)->delete();
        }finally {
            if(!$result){
                M('MembersMoney')->rollback();
                $this->ajaxReturn(array('status'=>500, 'message'=>'（003）续费失败，执行续费操作时发生异常', 'data'=>Domain::encode($domain)), $returnType);
            }
        }
        // ----取域名信息
        unset($where, $data, $result);
        $where['mid'] = session('MEMBERINFO.id');
        $where['domain'] = $domain;
        $domainInfo = M('MembersDomains')->where($where)->find();
        // ----将更新域名到期时间
        $data['expire_time'] = date('Y-m-d', strtotime('+'.$cartInfo['age'].' years', strtotime($domainInfo['expire_time'])));
        try{
            $result = M('MembersDomains')->where($where)->data($data)->save();
        }finally {
            if(!$result){
                M('MembersMoney')->rollback();
                $this->ajaxReturn(array('status'=>500, 'message'=>'（004）续费失败，执行续费操作时发生异常', 'data'=>Domain::encode($domain)), $returnType);
            }
        }
        // ----取出厂价
        unset($where);
        $where['name'] = $suffix;
        $suffixFactoryPrices = M('DomainSuffixPrice' . ucfirst($registrar))->where($where)->find();
        if(!is_array($suffixFactoryPrices)){
            M('MembersMoney')->rollback();
            $this->ajaxReturn(array('status'=>500, 'message'=>'（005）续费失败，执行续费操作时发生异常', 'data'=>Domain::encode($domain)), $returnType);
        }
        // ----取汇率
        if($registrar == 'webnic'){
            $moneyRate = M('Config')->where(array('name'=>'money_rate'))->find();
            if(!is_array($moneyRate)){
                M('MembersMoney')->rollback();
                $this->ajaxReturn(array('status'=>500, 'message'=>'（006）续费失败，执行续费操作时发生异常', 'data'=>Domain::encode($domain)), $returnType);
            }
        }
        // ----记录平台在厂商处的消费日志
        unset($where, $result, $data);
        $data['domain'] = $domain;
        $data['age'] = $cartInfo['age'];
        $data['type'] = 'renew';
        $data['registrar'] = $registrar;
        $data['time'] = time();
        $data['money_factory'] = $suffixFactoryPrices['price_renew_year' . $cartInfo['age']];
        $data['money_selling'] = $domainPrice; // 销售价
        $data['money_rate'] = $registrar == 'webnic'?$moneyRate['value']:''; // 汇率
        $data['content'] = '';
        try{
            $result = M('PlatformConsumeDetail')->data($data)->add();
        }finally {
            if(!$result){
                M('MembersMoney')->rollback();
                $this->ajaxReturn(array('status'=>500, 'message'=>'（007）续费失败，执行续费操作时发生异常', 'data'=>Domain::encode($domain)), $returnType);
            }
        }
        // ----调用续费接口
        unset($result);
        $domainApi = new DomainApi($registrar);
        $result = $domainApi->renew($domain, $cartInfo['age']);
        if(!$result){
            $errorInfo = $domainApi->getError();
            if($errorInfo['status'] === '-1'){
                // 因为接口异常引起的false
                // 如果连接中断,则域名入队列待检测注册状态,若检测结果为未注册,则返还注册金额
                $data['domain'] = $domain;
                $data['mid'] = session('MEMBERINFO.id');
                $data['registrar'] = $registrar;
                $data['money'] = $domainPrice;
                $data['type'] = 'renew';
                $data['create_time'] = date('Y-m-d H:i:s');
                try{
                    M('MembersDomainBrokenQueue')->data($data)->add();
                }catch(\Exception $e){
                }
                // 需要变更域名表的status状态为锁定
                unset($where, $data);
                $where['mid'] = session('MEMBERINFO.id');
                $where['domain'] = $domain;
                $where['registrar'] = $registrar;
                $data['status'] = '105';
                try{
                    M('MembersDomains')->where($where)->data($data)->save();
                }catch(\Exception $e){
                }
                M('MembersMoney')->commit();
                $this->ajaxReturn(array('status'=>200, 'message'=>$errorInfo['message'], 'data'=>Domain::encode($domain)), $returnType);
            }else{
                M('MembersMoney')->rollback();
            }
            $this->ajaxReturn(array('status'=>500, 'message'=>$errorInfo['message'], 'data'=>Domain::encode($domain)), $returnType);
        }
        M('MembersMoney')->commit();
        $this->ajaxReturn(array('status'=>200, 'message'=>'续费成功', 'data'=>Domain::encode($domain)), $returnType);
    }
}