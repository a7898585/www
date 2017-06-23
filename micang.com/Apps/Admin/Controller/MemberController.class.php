<?php

/*
 * @Created on 2015/09/21
 * @Author  Jansen<6206574@qq.com>
 *
 */

namespace Admin\Controller;

use Common\Extend\PageForAdmin;
use Common\Extend\PDCApi;

//后台管理员
class MemberController extends AdminCommonController {

    //会员列表
    public function index() {
        //开始筛选
        $where = array();
        if (I('get.username') != '') {
            $where['username'] = I('get.username');
        }
        if (I('get.id') != '') {
            $where['id'] = I('get.id');
        }
        $total = D('Members')->where($where)->count();
        $result = D('Members')->where($where)->page(max(1, I('get.p', 1, 'intval')))->limit(20)->select();
        if (is_array($result)) {
            $page['data'] = $result;
        }
        $pager = new PageForAdmin($total);
        $page['html'] = $pager->show();
        $this->assign('page', $page);
        $memberLevels = M('MemberLevels')->getField('id, name');
        $this->assign('memberLevels', $memberLevels);
        $this->display();
    }

    public function detail($mid) {
        //取用户详细资料
        $memberBase = M('Members')->where(array('id' => $mid))->find();
        $memberProfile = M('MembersProfile')->where(array('mid' => $mid))->find();
        $memberInfo = array_merge($memberBase, is_array($memberProfile) ? $memberProfile : array());
        if (is_array($memberInfo) && !empty($memberInfo)) {
            $areaIds[] = $memberInfo['province'];
            $areaIds[] = $memberInfo['city'];
            $areaIds[] = $memberInfo['county'];
            $areaIds = array_unique(array_filter($areaIds));
            if (!empty($areaIds)) {
                $areas = PDCApi::getInfoByAreaId($areaIds);
            }
            $memberInfo['province'] = $areas[$memberInfo['province']]['simple'];
            $memberInfo['city'] = $areas[$memberInfo['city']]['city'];
            $memberInfo['county'] = $areas[$memberInfo['county']]['county'];
        }
        $this->assign('memberInfo', $memberInfo);
        $memberLevels = M('MemberLevels')->getField('id,name');
        $this->assign('memberLevels', $memberLevels);
        $this->display();
    }

    //取充值明细
    public function ajax_get_recharge_for_detail($mid, $p = 1) {
        sleep(2);
        $where['mid'] = $mid;
        $where['status'] = '1';
        $total = M('MembersRechargeDetail')->where($where)->count();
        $recharges = M('MembersRechargeDetail')->where($where)->order(array('notify_time' => 'DESC'))->page($p, 10)->getField('id,money,notify_time,buyer_email');
        $page['data'] = $recharges;
        $pager = new PageForAdmin($total, 10);
        $page['html'] = $pager->show();
        $this->assign('page', $page);
        $this->display();
    }

    //取消费明细
    public function ajax_get_consume_for_detail($mid, $p = 1) {
        sleep(2);
        $where['mid'] = $mid;
        $total = M('MembersConsumeDetail')->where($where)->count();
        $consumes = M('MembersConsumeDetail')->where($where)->order(array('time' => 'DESC'))->page($p, 10)->getField('id,type,money,time,content');
        $page['data'] = $consumes;
        $pager = new PageForAdmin($total, 10);
        $page['html'] = $pager->show();
        $this->assign('page', $page);
        $this->display();
    }

    //取交易收入明细
    public function ajax_get_income_for_detail($mid) {
        sleep(2);
        $where['mid'] = $mid;
        $total = M('MembersIncomeDetail')->where($where)->count();
        $incomes = M('MembersIncomeDetail')->where($where)->order(array('time' => 'DESC'))->page($p, 10)->getField('id,type,money,time,content');
        $page['data'] = $incomes;
        $pager = new PageForAdmin($total, 10);
        $page['html'] = $pager->show();
        $this->assign('page', $page);
        $this->display();
    }

    //取提现明细
    public function ajax_get_deposit_for_detail($mid) {
        $where['mid'] = $mid;
        $Members = D('MembersDepositDetail');
        $Account = D('MembersAccount');
        $total = $Members->where($where)->count();
        $result = $Members->where($where)->page(max(1, I('get.p', 1, 'intval')))->order(array('addtime' => 'DESC'))->limit(10)->select();
        if (is_array($result)) {
            foreach ($result as &$value) {
                $value['account'] = $Account->field('account,bankname')->where(array('id' => $value['account_id']))->find();
            }
            $page['data'] = $result;
        }
        $pager = new PageForAdmin($total, 10);
        $page['html'] = $pager->show();
        $this->assign('page', $page);
        $this->display();
    }

    //启用、禁用帐号
    public function status($uid, $status) {
        $data['id'] = $uid;
        $data['status'] = $status;
        $result = M('Members')->data($data)->save();
        if (!$result) {
            $this->ajaxReturn(array('status' => 500, 'message' => '变更帐号状态失败，请重试。'));
        }
        $this->ajaxReturn(array('status' => 200, 'message' => '变更帐号状态成功。'));
    }

}

