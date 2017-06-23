<?php

namespace Agent\Controller;

use Common\Extend\BrmApi;

class CustomerController extends CommonController {

    public function _initialize() {
        parent::_initialize();
    }

    final public function projectVersion() {
        $key = 'projectVersion';
        $project = S($key);
        if (!$project) {
            $api = new BrmApi();
            $project_arr = $api->projectVersion();
            foreach ($project_arr as $value) {
                $project[$value['id']] = $value;
            }
            if ($project) {
                S($key, $project);
            }
        }
        return $project;
    }

    /**
     * 我要下单
     */
    final public function index() {
        $param = I('get.');
        $where = array();
        $page = $param['p'];
        $page = empty($page) ? 1 : $page;
        $perpage = 10;
        $where['agent_id'] = $this->userinfo['agent_id'];
        $where['vip'] = '0';
        $where['buy_type'] = '0';
        if ($param['k']) {
            $buyid_arr = M('User')->field('id')->where(array($param['type'] => array('like', '%' . $param['k'] . '%')))->select();
            $buyId = array();
            foreach ($buyid_arr as $value) {
                $buyId[] = $value['id'];
            }
            $where['uid'] = array('in', implode(',', $buyId));
        }
        $data = D('UserDaili')->getPageList($where, $page, $perpage);
        $this->assign('data', $data);
        $this->assign('title', '我的客户_代理商后台');
        $this->assign('order_data', C('order_package'));

        $this->assign('project', $this->projectVersion());
        $this->display();
    }

    /**
     * 我的客户
     */
    final public function my() {
        $param = I('get.');
        $where = array();
        $page = $param['p'];
        $page = empty($page) ? 1 : $page;
        $perpage = 10;
        $where['agent_id'] = $this->userinfo['agent_id'];
        $where['vip'] = '1';
        $where['buy_type'] = array('gt', 0);
        if ($param['k']) {
            $buyid_arr = M('User')->field('id')->where(array($param['type'] => array('like', '%' . $param['k'] . '%')))->select();
            $buyId = array();
            foreach ($buyid_arr as $value) {
                $buyId[] = $value['id'];
            }
            $where['uid'] = array('in', implode(',', $buyId));
        }
        if ($param['time']) {
            $where['over_time'] = array('gt', time() + 24 * 3600 * $param['time']);
        }
        if ($param['status'] == 1) {
            $where['over_time'] = array('gt', time());
        } elseif ($param['status'] == 2) {
            $where['over_time'] = array('lt', time());
        }
        $data = D('UserDaili')->getPageList($where, $page, $perpage);
        $this->assign('data', $data);
        $this->assign('title', '我的客户_代理商后台');
        $this->assign('order_data', C('order_package'));

        $this->assign('project', $this->projectVersion());
        $this->display();
    }

    final public function add() {
        $this->assign('title', '添加客户_代理商后台');
        $this->display();
    }

    final public function pay() {
        $proid = C('PROJECT_ID');
        $url = "http://bra.niucha.com/recharge/listrecharge?agentId={$this->userinfo['agent_id']}&type=1&projectId={$proid}";
        redirect(($url));
//        $this->assign('title', '充值_代理商后台');
//        $this->display();
    }

    final public function gopay() {
        $proid = C('PROJECT_ID');
//        $price = $data['price'];
//        $param['orderid'] = date('YmdHis') . rand(1000, 9999);
//        $param['agent_id'] = $this->userinfo['agent_id'];
//        $param['type'] = 3;
//        $param['money'] = $price;
//        $param['descn'] = '代理商支付宝充值';
//        $Order = M('Order');
//        if ($Order->create($param)) {
//            $orderid = $Order->add();
//        } else {
//            $this->ajaxReturn(array('s' => -1, 'm' => '订单生成失败'));
//        }
        $url = "http://bra.niucha.com/recharge/listrecharge?agentId={$this->userinfo['agent_id']}&type=1&projectId={$proid}";
        redirect(($url));
    }

    final public function buy() {
        if (IS_POST) {
            $id = $this->userinfo['agent_id'];
            $data = I('post.');
            if (!$data['uid']) {
                $this->ajaxReturn(array('s' => -1, 'm' => '数据错误'));
            }
            $price = $data['year'] * $data['price'];
            $param['orderid'] = date('YmdHis') . rand(1000, 9999);
            $param['agent_id'] = $id;
            $param['buy_id'] = $data['uid'];
            $param['money'] = $price;
            $param['year'] = $data['year'];
            $project = $this->projectVersion();
            $param['descn'] = '(' . $project[$data['class_v']]['versionName'] . ') ￥ ' . $price . ' 元/' . $data['year'] . '年';
            $Order = M('Order');
            if ($Order->create($param)) {
                $orderid = $Order->add();
            } else {
                $this->ajaxReturn(array('s' => -1, 'm' => '订单生成失败'));
            }
            $api = new BrmApi();
            $r_buy = $api->buyProductComplete($data['uid'], $data['class_v'], $data['year']);
            if ($r_buy['s'] < 0 || !is_array($r_buy)) {
                $this->ajaxReturn(array('s' => -2, 'm' => $r_buy['r'] ? $r_buy['r'] : '您的余额不足！请先充值！', 'data' => array('money' => $this->userinfo['money'], 'price' => $price - ($this->userinfo['money']))));
            }
            $c_data['buy_type'] = $data['class_v'];
            $c_data['vip'] = '1';
            $c_data['buy_time'] = time();
            $c_data['over_time'] = strtotime("+" . $data['year'] . " year");
            $c_data['buy_desc'] = $param['descn'];
            if ($orderid) {
                $r1 = M('UserAgent')->where(array('uid' => $id))->save(array('money' => array('exp', 'money-' . $price)));
                $r3 = $Order->where(array('id' => $orderid))->save(array('status' => 1));
                $r2 = M('UserDaili')->where(array('uid' => $data['uid']))->save($c_data);
            }

            if (!$r1 && !$r2 && !$r3) {
                $this->ajaxReturn(array('s' => -1, 'm' => '购买失败'));
            } else {
                $this->ajaxReturn(array('s' => 0, 'm' => '购买成功', 'data' => array('money' => $this->userinfo['money'] - $price, 'price' => $price)));
            }
            exit;
        }
    }

    function getYearPrice() {
        $order_package = C('order_package');
        $id = I('get.id');
        echo json_encode($order_package[$id]['data']);
        exit;
    }

}