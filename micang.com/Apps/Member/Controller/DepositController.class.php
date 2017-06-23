<?php

namespace Member\Controller;
use Common\Extend\PageForMember;
use Common\Extend\Domain;
class DepositController extends MemberCommonController {

    public function _initialize() {
        parent::_initialize();
        $where['id'] = session('MEMBERINFO.id');
        $auth_status = M('Members')->where($where)->getField('auth_status');
        if ($auth_status != '1') {
            $this->error("必须实名认证才能提现！正在跳转实名认证...", "/account/shiming/");
        }
        $this->assign('m_tab', 'recharge');
    }

    public function index() {
        
    }

    /**
     * 添加账户
     */
    public function add() {
        if (IS_POST) {
            $data = I('post.');
            if ($data['type'] == 1) {
                $data['account'] = $data['account2'];
            } else {
                $pdcApi = new \Common\Extend\PDCApi();
                $provinceInfo = $pdcApi->getInfoByAreaId(array(I('post.province')));
                $cityInfo = $pdcApi->getInfoByAreaId(array(I('post.city')));
                $data['address'] = $provinceInfo[I('post.province')]['simple'] . $cityInfo[I('post.city')]['simple'] . $data['address'];
            }
            $data['addtime'] = time();
            $data['mid'] = session('MEMBERINFO.id');

            try {
                $result = M('MembersAccount')->data($data)->add();
            } catch (\Exception $e) {
                $this->ajaxReturn(array('status' => 500, 'message' => '添加账户失败，请重试。'));
            }
            if (!$result) {
                $this->ajaxReturn(array('status' => 500, 'message' => '添加账户失败，请重试。'));
            }
            $this->ajaxReturn(array('status' => 200, 'message' => '添加账户成功'));
        }
        $profile = M('MembersProfile')->where(array('mid' => session('MEMBERINFO.id')))->find();
        $this->assign('profile', $profile);
        $this->display();
    }

    /**
     * 提现账户
     */
    public function account($p = 1) {
        $total = M('MembersAccount')->where(array('mid' => session('MEMBERINFO.id')))->count();
        $account = M('MembersAccount')->where(array('mid' => session('MEMBERINFO.id')))->order(array('addtime' => 'DESC'))->page($p)->limit(20)->select();
        $this->assign('account', $account);
        $pager = new PageForMember($total);
        $pager->url = '/deposit/account?p=' . urlencode('[PAGE]');
        $this->assign('pager', $pager->show());
        $this->display();
    }

    /**
     * 提现申请
     */
    final public function apply() {
        $money = M('MembersMoney')->where(array('mid' => session('MEMBERINFO.id')))->find();
        if ($money['lock_money'] > $money['recharge_money']) {
            $money['yes_trade_money'] = ($money['trade_money'] - ($money['lock_money'] - $money['recharge_money'])) / 100;
            $money['no_trade_money'] = $money['lock_money'] / 100;
        } else {
            $money['yes_trade_money'] = $money['trade_money'] / 100;
            $money['no_trade_money'] = $money['recharge_money'] / 100;
        }
        if (IS_POST) {
            $data = I('post.');
            if ($money['yes_trade_money'] < $data['money']) {
                $this->ajaxReturn(array('status' => 500, 'message' => '您可提现的金额为' . $money['yes_trade_money'] . '元。'));
            }
            if (ereg("^[0-9]*[1-9][0-9]*$", $data['money']) != 1) {
                $this->ajaxReturn(array('status' => 500, 'message' => '请输入整数金额'));
            }
            if (isMobile($data['mobile']) == false) {
                $this->ajaxReturn(array('status' => 500, 'message' => '手机号码格式不对。'));
            }
            $data['counterfee'] = ceil($data['money'] / 100);
            $data['mid'] = session('MEMBERINFO.id');
            $data['id'] = 'MC' . date('YmdHis');
            M('MembersMoney')->startTrans();
            // ----扣除所需费用
            $mdata['mid'] = session('MEMBERINFO.id');
            $mdata['trade_money'] = array('exp', 'trade_money-' . $data['money'] * 100);
            $mdata['total_money'] = array('exp', 'total_money-' . $data['money'] * 100);
            $res = M('MembersMoney')->data($mdata)->save();
            if (!$res) {
                M('MembersMoney')->rollback();
                $this->ajaxReturn(array('status' => 500, 'message' => '提现申请失败-01，请重试。'));
            }
            try {
                $result = M('MembersDepositDetail')->data($data)->add();
            } catch (\Exception $e) {
                M('MembersMoney')->rollback();
                $this->ajaxReturn(array('status' => 500, 'message' => '提现申请失败-02，请重试。'));
            }
            if (!$result) {
                M('MembersMoney')->rollback();
                $this->ajaxReturn(array('status' => 500, 'message' => '提现申请失败-03，请重试。'));
            }
            M('MembersMoney')->commit();
            $this->ajaxReturn(array('status' => 200, 'message' => '提现申请成功'));
        }
        $aid = I('get.aid');
        $account = M('MembersAccount')->where(array('mid' => session('MEMBERINFO.id')))->order(array('addtime' => 'DESC'))->select();
        if (empty($account)) {
            $this->error('必须添加提现账户！正在跳转...', '/deposit/add/');
        }
        $select_info_bank = array();
        foreach ($account as $value) {
            if ($value['type'] == 1) {
                $select_info_bank[$value['id']] = '支付宝,账号：' . $value['account'] . ',户名：*' . msubstr($value['realname'], -2, 2);
            } else {
                $select_info_bank[$value['id']] = $value['bankname'] . ',卡号：****' . substr($value['account'], -4) . ',户名：*' . msubstr($value['realname'], -2, 2);
            }
        }
        if ($aid) {
            $account[0] = M('MembersAccount')->where(array('id' => intval($aid), 'mid' => session('MEMBERINFO.id')))->find();
        }
        $this->assign('select_info_bank', $select_info_bank);
        $profile = M('MembersProfile')->where(array('mid' => session('MEMBERINFO.id')))->field('realname,mobile')->find();
        $this->assign('realname', $profile['realname']);
        $this->assign('mobile', $profile['mobile']);
        $this->assign('account', $account[0]);
        $this->assign('money', $money);
        $this->display();
    }

    /**
     * 提现列表
     */
    public function detail($p = 1) {
        $total = M('MembersDepositDetail')->where(array('mid' => session('MEMBERINFO.id')))->count();
        $detail = M('MembersDepositDetail')->where(array('mid' => session('MEMBERINFO.id')))->order(array('addtime' => 'DESC'))->page($p)->limit(20)->select();
        if (is_array($detail)) {
            foreach ($detail as &$value) {
                $value['account'] = M('MembersAccount')->field('account,bankname,type')->where(array('id' => $value['account_id']))->find();
            }
        }

        $this->assign('detail', $detail);
        $pager = new PageForMember($total);
        $pager->url = '/deposit/detail?p=' . urlencode('[PAGE]');
        $this->assign('pager', $pager->show());
        $this->display();
    }

    /**
     * 提现详情
     * @param type $p
     */
    public function view($id = 1) {
        $detail = M('MembersDepositDetail')->where(array('id' => $id))->find();
        $Account = D('MembersAccount');
        $detail['account'] = $Account->where(array('id' => $detail['account_id']))->find();
        $this->assign('detail', $detail);
        $this->display();
    }

    public function get_acount($id) {
        $account = M('MembersAccount')->where(array('id' => intval($id), 'mid' => session('MEMBERINFO.id')))->find();
        if ($account) {
            if ($account['type'] == 1) {
                $this->ajaxReturn(array('status' => 200, 'account' => $account['account'], 'bankname' => '支付宝'));
            } else {
                $this->ajaxReturn(array('status' => 200, 'account' => '****' . substr($account['account'], -4), 'bankname' => $account['bankname']));
            }
        } else {
            $this->ajaxReturn(array('status' => 500));
        }
    }

    public function del_account($id) {
        $res = M('MembersAccount')->where(array('id' => intval($id)))->delete();
        if ($res) {
            $this->ajaxReturn(array('status' => 200, 'message' => '删除成功。'));
        } else {
            $this->ajaxReturn(array('status' => 500, 'message' => '提现申请失败-02，请重试。'));
        }
    }

}