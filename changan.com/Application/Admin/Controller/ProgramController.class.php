<?php

namespace Admin\Controller;

class ProgramController extends AdminCommonController {

    public function _initialize() {
        parent::_initialize();
        $this->assign('nav', 'program');
    }

    final public function index() {
        $where = array();
        $name = I('get.name', '', 'trim');
        $status = I('get.status');
        if ($name) {
            $where['title'] = array('like', '%' . $name . '%');
        }
        if ($status) {
            if ($status == 2) {
                $where['status'] = 0;
            } else {
                $where['status'] = $status;
            }
        }
        $page['pageNum'] = max(1, I('get.p', 1, 'intval'));
        $page['numPerPage'] = I('get.numPerPage', 20, 'intval');
        $conn = M('ProProgram');
        $list = $conn->where($where)
                ->order('id desc')
                ->page($page['pageNum'], $page['numPerPage'])
                ->select();
        $page['totalCount'] = $conn->where($where)->count();
        $pager = showAdminPage($page['totalCount'], $page['numPerPage']);
        $this->assign('list', $list);
        $this->assign('pager', $pager);
        $this->assign('page', $page);
        $this->display();
    }

    final public function check() {
        $id = I('get.id');
        $info = D('ProProgram')->getInfo($id);
        if (empty($info)) {
            $this->error('不存在这个方案');
        }
        $this->assign('info', $info);
        $this->display();
    }

    public function upStatus() {
        if (IS_POST) {
            $data = I('post.');
            if ($data['id']) {
                $param['status'] = $data['status'];
//                $param['divide_time'] = time();
                $r = M('ProProgram')->where(array('id' => $data['id']))->save($param);

                if (!$r && M('ProProgram')->getDbError()) {
                    echo json_encode(array("msg" => '审核失败', "s" => "0"));
                } else {
                    $key = 'product_program_' . $data['id'];
                    S($key, null);
                    echo json_encode(array("msg" => '审核成功', "s" => "1"));
                }
                exit;
            }
        }
    }

}