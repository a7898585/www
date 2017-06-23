<?php

namespace Admin\Controller;

class OrderController extends AdminCommonController {

    public function _initialize() {
        parent::_initialize();
        $this->assign('nav', 'order');
    }

    final public function index() {
        $param = I('get.');
        $where = array();
        $page['pageNum'] = max(1, I('get.p', 1, 'intval'));
        $page['numPerPage'] = I('get.numPerPage', 20, 'intval');
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
        $data = D('Order')->getPageList($where, $page['pageNum'], $page['numPerPage'], 'id desc', 'showAdminPage');
//        echo D('Order')->getLastSql();
        $this->assign('data', $data);
        $this->display();
    }

}