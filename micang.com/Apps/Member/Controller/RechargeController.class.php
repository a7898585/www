<?php

/**
 * RechargeController.class.php
 * Author: Jansen
 * QQ: 6206574
 * Date: 2015-10-03
 */

namespace Member\Controller;

use Common\Extend\Alipay\Alipay;
use Common\Extend\PageForMember;

class RechargeController extends MemberCommonController {

    public function _initialize() {
        parent::_initialize();
        $this->assign('m_tab', 'recharge');
    }

    /**
     * 在线充值
     */
    public function add() {
        if (IS_POST) {
            if (I('post.money', 0, 'floatval') == 0) {
                $money = I('post.money_num', 0, 'floatval');
            } else {
                $money = I('post.money', 0, 'floatval');
            }
            //创建充值记录
            $data['id'] = 'alipay' . date('YmdHis');
            $data['mid'] = session('MEMBERINFO.id');
            $data['title'] = '会员' . session('MEMBERINFO.id') . '[' . session('MEMBERINFO.username') . ']充值RMB' . $money . '元';
            $data['money'] = $money * 100;
            $data['status'] = '0';
            $data['create_time'] = date('Y-m-d H:i:s');
            try {
                $result = M('MembersRechargeDetail')->data($data)->add();
            } catch (\Exception $e) {
                $this->error('发起支付请求时发生错误，支付失败！请关闭本窗口后重新提交充值请求。');
            }
            if (!$result) {
                $this->error('发起支付请求时发生错误，支付失败！请关闭本窗口后重新提交充值请求。');
            }
            $partner = C('ALIPAY_CONFIG.PARTNER');
            $seller = C('ALIPAY_CONFIG.SELLER_EMAIL');
            $key = C('ALIPAY_CONFIG.KEY');
            $notifyUrl = 'http://' . str_replace('member.', 'www.', I('server.HTTP_HOST')) . '/recharge/notify'; //异步通知URL
            $returnUrl = 'http://' . I('server.HTTP_HOST') . '/recharge/notify'; //同步跳转URL
            $alipay = new Alipay($partner, $seller, $key, $notifyUrl, $returnUrl);
            $alipay->pay($data['id'], $data['title'], $money, '', I('post.show'));
        }
        $memberMoney = M('MembersMoney')->where(array('mid' => session('MEMBERINFO.id')))->getField('total_money');
        $this->assign('memberMoney', $memberMoney);
        $this->assign('money', intval(I('get.money')));
        if ($_GET['show'] == 'frame') {
            $this->display('add_other');
        } else {
            $this->display();
        }
    }

    /**
     * 支付成功后的同步跳转页面
     */
    public function notify() {
        $partner = C('ALIPAY_CONFIG.PARTNER');
        $seller = C('ALIPAY_CONFIG.SELLER_EMAIL');
        $key = C('ALIPAY_CONFIG.KEY');
        $alipay = new Alipay($partner, $seller, $key);
        $result = $alipay->notify(I('get.'));
        if (!$result) {
            exit('充值失败，非法请求。');
        }
        if (I('get.trade_status') == 'TRADE_CLOSED') {
            unset($data);
            $data['id'] = I('get.out_trade_no');
            $data['tid'] = I('get.trade_no');
            $data['status'] = '2';
            M('MembersRechargeDetail')->data($data)->save();
            exit('充值交易已关闭。');
        }
        if (I('get.trade_status') == 'TRADE_FINISHED' || I('get.trade_status') == 'TRADE_SUCCESS') {
            if (I('get.extra_common_param') == 'frame') {
                exit('<script type="text/javascript">window.close();</script>');
            }else{
                exit('充值交易完成');
            }
        }
    }

    /**
     * 充值明细
     * @author Jansen
     * @since 2015-10-03
     */
    public function bill($p = 1) {
        $total = M('MembersRechargeDetail')->where(array('mid' => session('MEMBERINFO.id')))->count();
        $recharges = M('MembersRechargeDetail')->where(array('mid' => session('MEMBERINFO.id')))->order(array('create_time' => 'DESC'))->page($p)->select();
        $this->assign('recharges', $recharges);
        $pager = new PageForMember($total);
        $pager->url = '/recharge/bill?p=' . urlencode('[PAGE]');
        $this->assign('pager', $pager->show());
        $this->display();
    }

}