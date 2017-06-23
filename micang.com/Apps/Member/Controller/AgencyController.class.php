<?php

/*
 * 域名中介
 */

namespace Member\Controller;

use Common\Extend\PageForMember;
use Common\Extend\Domain;
use Common\Extend\SendEmail;

class AgencyController extends MemberCommonController {

    /**
     * 中介详情
     * @param type $id
     */
    public function detail($id) {
        $detail = M('DomainAgency')->where(array('id' => $id))->find();
        if ($detail['status'] != '10' && $detail['code_status'] == '0' && $detail['mid'] != session('MEMBERINFO.id')) {
            $this->error('您需要先激活', '/agency/active/?id=' . $id);
        }
        $this->assign('detail', $detail);
        $this->assign('m_tab', $detail['apply_type'] ? 'sell_domain' : 'buy_domain');
        $this->display();
    }

    /**
     * 中介激活
     * @param type $id
     */
    public function active($id) {
        $detail = M('DomainAgency')->where(array('id' => $id))->find();
        if ($detail['status'] != '10' && $detail['code_status'] == '0' && $detail['mid'] != session('MEMBERINFO.id')) {
            $this->assign('detail', $detail);
            $this->assign('m_tab', $detail['apply_type'] ? 'sell_domain' : 'buy_domain');
            $this->display();
        } else {
            $this->error('访问出错', '/agency/' . ($detail['apply_type'] ? 'sell_list' : ''));
        }
    }

    /**
     * 取消中介
     * @param type $id
     */
    public function cacel_agency($id) {
        if (IS_POST) {
            $detail = M('DomainAgency')->where(array('id' => $id))->find();
            if ($detail['mid'] != session('MEMBERINFO.id')) {
                $this->ajaxReturn(array('status' => 500, 'message' => '您没有权限。'));
            }
            $result = M('DomainAgency')->where(array('id' => $id, 'mid' => session('MEMBERINFO.id')))->save(array('status' => '10'));
            if ($result) {
                $this->ajaxReturn(array('status' => 200, 'message' => '取消成功。', 'url' => '/agency/' . ($detail['apply_type'] ? 'sell_list' : '')));
            } else {
                $this->ajaxReturn(array('status' => 500, 'message' => '取消失败。'));
            }
        }
    }

    /**
     * 中介购买列表
     */
    public function index($p = 1) {
        $limit = 15;
        $total = M('DomainAgency')->where(array('buy_email' => session('MEMBERINFO.username')))->count();
        $data = M('DomainAgency')->where(array('buy_email' => session('MEMBERINFO.username')))->order(array('addtime' => 'DESC'))->page($p)->limit($limit)->select();
        $this->assign('data', $data);
        $pager = new PageForMember($total, $limit);
        $pager->url = '/agency?p=' . urlencode('[PAGE]');
        $this->assign('pager', $pager->show());
        $this->assign('m_tab', 'buy_domain');
        $this->display();
    }

    /**
     * 中介出售
     */
    public function sell_list($p = 1) {
        $limit = 15;
        $total = M('DomainAgency')->where(array('sell_email' => session('MEMBERINFO.username')))->count();
        $data = M('DomainAgency')->where(array('sell_email' => session('MEMBERINFO.username')))->order(array('addtime' => 'DESC'))->page($p)->limit($limit)->select();
        $this->assign('data', $data);
        $pager = new PageForMember($total, $limit);
        $pager->url = '/agency/sell_list?p=' . urlencode('[PAGE]');
        $this->assign('pager', $pager->show());
        $this->assign('m_tab', 'sell_domain');
        $this->display();
    }

    /**
     * 中介购买
     */
    public function buy() {
        if (IS_POST) {
            $param = I('post.');
            $param['mid'] = session('MEMBERINFO.id');
            if (Domain::is($param['domain']) == false) {
                $this->ajaxReturn(array('status' => 500, 'message' => '请输入正确的域名。'));
            }
            $id = M('DomainAgency')->where(array('domain' => $param['domain'], 'mid' => $param['mid'], 'apply_type' => '0'))->getField('id');
            if ($id) {
                $this->ajaxReturn(array('status' => 500, 'message' => '已经添加过交易，请不要重复提交。'));
            }
            $param['buy_email'] = session('MEMBERINFO.username');
            $param['sell_email'] = $param['email'];
            $param['code'] = randomkeys(6) . '-' . randomkeys(4) . '-' . randomkeys(4) . '-' . randomkeys(10);
            if ($param['buy_email'] == $param['sell_email']) {
                $this->ajaxReturn(array('status' => 500, 'message' => '对方接收邮件不能是自己。'));
            }
            $param['addtime'] = time();
            $param['uptime'] = time();
            $param['counter_fee'] = round((floor($param['pay_price'] * 0.04 * 1000) / 10) / 100);
            switch ($param['sel_paytype']) {
                case 3:
                    $param['counter_fee'] = $param['counter_fee'] / 2;
                    $param['counter_fee'] = $param['counter_fee'] < 1 ? '1' : $param['counter_fee'];
                    break;
                case 2:
                    $param['counter_fee'] = 0;
                    break;
                default:
                    $param['counter_fee'] = ceil($param['pay_price'] * 0.04);
                    break;
            }

            try {
                $result = M('DomainAgency')->add($param);
            } catch (\Exception $e) {
                $this->ajaxReturn(array('status' => 500, 'message' => '中介购买失败，请重试。'));
            }
            if (!$result) {
                $this->ajaxReturn(array('status' => 500, 'message' => '中介购买失败，请重试。'));
            }
            $res1 = SendEmail::sendAgencyBuyCode($param['domain'], $result, $param['code'], $param['email']);
            $res2 = SendEmail::sendAgencyBuy($param['domain'], $result, $param['buy_email'], $param['email']);
            if (!$res2 || !$res1) {
                $this->ajaxReturn(array('status' => 300, 'message' => '中介购买申请已提交，邮件发送提醒对方失败，请联系对方把激活码发给对方激活<br>激活码：' . $param['code']));
            }
            $this->ajaxReturn(array('status' => 200, 'message' => '中介购买申请成功。'));
        }
        $this->assign('m_tab', 'buy_domain');
        $this->display();
    }

    /**
     * 中介出售
     */
    public function sell() {
        if (IS_POST) {
            $param = I('post.');
            $param['mid'] = session('MEMBERINFO.id');
            $id = M('DomainAgency')->where(array('domain' => $param['domain'], 'mid' => $param['mid'], 'apply_type' => '1'))->getField('id');
            if ($id) {
                $this->ajaxReturn(array('status' => 500, 'message' => '已经添加过中介出售，请不要重复提交。'));
            }
            $param['buy_email'] = $param['email'];
            $param['sell_email'] = session('MEMBERINFO.username');
            $param['addtime'] = time();
            $param['uptime'] = time();
            $param['apply_type'] = '1';
            $param['counter_fee'] = round((floor($param['pay_price'] * 0.04 * 1000) / 10) / 100);
            switch ($param['sel_paytype']) {
                case 3:
                    $param['counter_fee'] = $param['counter_fee'] / 2;
                    $param['counter_fee'] = $param['counter_fee'] < 1 ? '1' : $param['counter_fee'];
                    break;
                case 1:
                    $param['counter_fee'] = 0;
                    break;
                default:
                    $param['counter_fee'] = ceil($param['pay_price'] * 0.04);
                    break;
            }
            if (Domain::is($param['domain']) == false) {
                $this->ajaxReturn(array('status' => 500, 'message' => '请输入正确的域名。'));
            }
            try {
                $result = M('DomainAgency')->add($param);
            } catch (\Exception $e) {
                $this->ajaxReturn(array('status' => 500, 'message' => '中介出售失败，请重试。'));
            }
            if (!$result) {
                $this->ajaxReturn(array('status' => 500, 'message' => '中介出售失败，请重试。'));
            }
            $this->ajaxReturn(array('status' => 200, 'message' => '中介出售成功。'));
        }
        $this->assign('m_tab', 'sell_domain');
        $this->display();
    }

    /**
     * 域名代购
     */
    public function purchase() {
        if (IS_POST) {
            $param = I('post.');
            $param['mid'] = session('MEMBERINFO.id');
            $param['addtime'] = time();
            $param['uptime'] = time();
            $param['password'] = rand(100000, 999999);
            if (Domain::is($param['domain']) == false) {
                $this->ajaxReturn(array('status' => 500, 'message' => '请输入正确的域名。'));
            }
            $id = M('DomainPurchase')->where(array('domain' => $param['domain'], 'mid' => $param['mid'], 'role' => '0'))->getField('id');
            if ($id) {
                $this->ajaxReturn(array('status' => 500, 'message' => '已经添加过此域名代购，请不要重复提交。'));
            }
            try {
                $result = M('DomainPurchase')->add($param);
            } catch (\Exception $e) {
                $this->ajaxReturn(array('status' => 500, 'message' => '代购申请失败，请重试。'));
            }
            if (!$result) {
                $this->ajaxReturn(array('status' => 500, 'message' => '代购申请失败，请重试。'));
            }
            $this->ajaxReturn(array('status' => 200, 'message' => '代购申请成功。'));
        }
        if (session('MEMBERINFO.id') > 0) {
            $profile = M('MembersProfile')->where(array('mid' => session('MEMBERINFO.id')))->find();
            $this->assign('profile', $profile);
        }
        $this->assign('m_tab', 'buy_domain');
        $this->display();
    }

    /**
     * 域名代售
     */
    public function purchase_sell() {
        if (IS_POST) {
            $param = I('post.');
            $param['mid'] = session('MEMBERINFO.id');
            $param['addtime'] = time();
            $param['uptime'] = time();
            $param['password'] = rand(100000, 999999);
            if (Domain::is($param['domain']) == false) {
                $this->ajaxReturn(array('status' => 500, 'message' => '请输入正确的域名。'));
            }
            $id = M('DomainPurchase')->where(array('domain' => $param['domain'], 'mid' => $param['mid'], 'role' => '1'))->getField('id');
            if ($id) {
                $this->ajaxReturn(array('status' => 500, 'message' => '已经添加过此域名代售，请不要重复提交。'));
            }
            try {
                $result = M('DomainPurchase')->add($param);
            } catch (\Exception $e) {
                $this->ajaxReturn(array('status' => 500, 'message' => '代售申请失败，请重试。'));
            }
            if (!$result) {
                $this->ajaxReturn(array('status' => 500, 'message' => '代售申请失败，请重试。'));
            }
            $this->ajaxReturn(array('status' => 200, 'message' => '代售申请成功。'));
        }
        if (session('MEMBERINFO.id') > 0) {
            $profile = M('MembersProfile')->where(array('mid' => session('MEMBERINFO.id')))->find();
            $this->assign('profile', $profile);
        }
        $this->assign('m_tab', 'sell_domain');
        $this->display();
    }

    /**
     * 委托购买的域名
     */
    public function purchase_list($p = 1) {
        $limit = 15;
        $total = M('DomainPurchase')->where(array('mid' => session('MEMBERINFO.id'), 'role' => '0'))->count();
        $data = M('DomainPurchase')->where(array('mid' => session('MEMBERINFO.id'), 'role' => '0'))->order(array('addtime' => 'DESC'))->page($p)->limit($limit)->select();
        $this->assign('data', $data);
        $pager = new PageForMember($total, $limit);
        $pager->url = '/agency/purchase_list?p=' . urlencode('[PAGE]');
        $this->assign('pager', $pager->show());
        $this->assign('m_tab', 'buy_domain');
        $this->display();
    }

    /**
     * 委托售出的域名
     */
    public function purchase_sell_list($p = 1) {
        $limit = 15;
        $total = M('DomainPurchase')->where(array('mid' => session('MEMBERINFO.id'), 'role' => '1'))->count();
        $data = M('DomainPurchase')->where(array('mid' => session('MEMBERINFO.id'), 'role' => '1'))->order(array('addtime' => 'DESC'))->page($p)->limit($limit)->select();
        $this->assign('data', $data);
        $pager = new PageForMember($total, $limit);
        $pager->url = '/agency/purchase_sell_list?p=' . urlencode('[PAGE]');
        $this->assign('pager', $pager->show());
        $this->assign('m_tab', 'buy_domain');
        $this->display();
    }

    /**
     * 委托购买的域名详细
     * @param type $id
     */
    public function purchase_view($id) {
        $data = M('DomainPurchase')->where(array('mid' => session('MEMBERINFO.id'), 'id' => $id))->find();
        $this->assign('profile', $data);
        $this->assign('m_tab', 'buy_domain');
        $this->display();
    }

}

