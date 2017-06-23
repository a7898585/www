<?php

namespace Agent\Controller;

class OrderController extends CommonController {

    public function _initialize() {
        parent::_initialize();
    }

    final public function index() {
        $param = I('get.');
        $where = array();
        $page = $param['p'];
        $page = empty($page) ? 1 : $page;
        $where['agent_id'] = $this->userinfo['agent_id'];
        $where['type'] = array('in', '1,2');
        if ($param['status'] == 1) {
            $where['status'] = array('eq', 1);
        } elseif ($param['status'] == 2) {
            $where['status'] = array('eq', 0);
        }
        if ($param['k']) {
            if ($param['type'] == 'name') {
                $buyid_arr = M('User')->field('id')->where(array('name' => array('like', '%' . $param['k'] . '%')))->select();
                $buyId = array();
                foreach ($buyid_arr as $value) {
                    $buyId[] = $value['id'];
                }
                $where['buy_id'] = array('in', implode(',', $buyId));
            } else {
                $where[$param['type']] = array('like', '%' . $param['k'] . '%');
            }
        }
        $perpage = 10;
        $data = D('Order')->getPageList($where, $page, $perpage);
        $this->assign('title', '订单管理-代理商后台');
        $this->assign('data', $data);
        $this->display();
    }

    function account() {
        $param = I('get.');
        $where = array();
        $page = $param['p'];
        $page = empty($page) ? 1 : $page;
        $where['agent_id'] = $this->userinfo['agent_id'];
        if ($param['type']) {
            $where['type'] = array('eq', $param['type']);
        }
        if ($param['orderid']) {
            $where['orderid'] = array('like', '%' . $param['orderid'] . '%');
        }
        $perpage = 10;
        $data = D('Order')->getPageList($where, $page, $perpage);
        $this->assign('title', '账户明细-代理商后台');
        $this->assign('data', $data);
        $this->display();
    }

    final public function buy() {
        if (IS_POST) {
            $id = $this->userinfo['id'];
            $data = I('post.');
            if (!$data['bid'] && !$data['oid']) {
                $this->ajaxReturn(array('s' => -1, 'm' => '数据错误'));
            }
            $d_info = M('UserDaili')->field('buy_id,over_time')->where(array('uid' => $data['uid']))->find();
            if ($d_info['over_time'] > time() && $d_info['buy_id'] > 0) {
                $this->ajaxReturn(array('s' => -1, 'm' => '改代理人已被下单！'));
            }
            $o_info = D('Order')->getInfo($data['oid']);
            if ($this->userinfo['money'] < $o_info['money']) {
                $this->ajaxReturn(array('s' => -2, 'm' => '余额不足', 'data' => array('money' => $this->userinfo['money'], 'price' => $o_info['money'])));
            }
            $c_data['buy_id'] = $id;
            $c_data['buy_time'] = time();

            if ($d_info['over_time'] < time()) {
                $c_data['over_time'] = strtotime("+" . $data['year'] . " year");
            } else {
                $c_data['over_time'] = strtotime("+" . $data['year'] . " year", $d_info['over_time']);
            }

            $r1 = M('UserAgent')->where(array('uid' => $id))->save(array('money' => array('exp', 'money-' . $o_info['money'])));
            $r3 = M('Order')->where(array('id' => $data['oid']))->save(array('status' => 1));
            $r2 = M('UserDaili')->where(array('uid' => $data['bid']))->save($c_data);
            if (!$r1 && !$r2 && !$r3) {
                $this->ajaxReturn(array('s' => -1, 'm' => '购买失败'));
            } else {
                $this->ajaxReturn(array('s' => 0, 'm' => '购买成功', 'data' => array('money' => $this->userinfo['money'] - $o_info['money'], 'price' => $o_info['money'])));
            }
            exit;
        }
    }

}