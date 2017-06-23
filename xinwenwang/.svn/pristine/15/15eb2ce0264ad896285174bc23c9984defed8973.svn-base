<?php

namespace Admin\Controller;

class UsersController extends AdminCommonController {

    public function _initialize() {
        parent::_initialize();
        $this->assign('nav', 'user');
    }

    final public function upload_photo() {
        $temp = uploadPhoto($_FILES['file']);
        $this->ajaxReturn($temp);
    }

    final public function index() {
        $where = array();
        $name = I('get.name', '', 'trim');
        $sdate = I('get.sdate', '', 'trim');
        $edate = I('get.edate', '', 'trim');
        if ($name) {
            $where['username'] = array('like', $name . '%');
        }
        if ($sdate) {
            $where['add_time'] = array('egt', strtotime($sdate));
        }
        if ($edate) {
            $where['_string'] = 'add_time < ' . strtotime($edate . ' 23:59:59');
        }
        $page['pageNum'] = max(1, I('get.p', 1, 'intval'));
        $page['numPerPage'] = I('get.numPerPage', 20, 'intval');
        $conn = M('Users');
        $list = $conn->where($where)->order('add_time desc')->page($page['pageNum'], $page['numPerPage'])
                ->select();
//        echo $conn->getLastSql();
        $page['totalCount'] = $conn->where($where)->count();
        $pager = showPage($page['totalCount'], $page['numPerPage']);
        $this->assign('list', $list);
        $this->assign('pager', $pager);
        $this->assign('page', $page);
        $this->display();
    }

    final public function tongji() {
        $where = array();
        $name = I('get.name', '', 'trim');
        $sdate = I('get.sdate', '', 'trim');
        $edate = I('get.edate', '', 'trim');
        $id = I('get.uid', '', 'intval');
        if ($id) {
            $where['uid'] = array('eq', $id);
        }
        if ($name) {
            $where['title'] = array('like', $name . '%');
        }
        if ($sdate) {
            $where['add_time'] = array('egt', strtotime($sdate));
        }
        if ($edate) {
            $where['_string'] = 'add_time < ' . strtotime($edate . ' 23:59:59');
        }
        $page['pageNum'] = max(1, I('get.p', 1, 'intval'));
        $page['numPerPage'] = I('get.numPerPage', 20, 'intval');
        $conn = M('UserView');
        $list = $conn->field('*,COUNT(*) AS tp_count')->where($where)->order('add_time desc')->group('uid')->page($page['pageNum'], $page['numPerPage'])
                ->select();

        $page['totalCount'] = count($list);
//        echo $conn->getLastSql();
        $pager = showPage($page['totalCount'], $page['numPerPage']);
        $this->assign('list', array_slice($list, ($page['pageNum'] - 1) * $page['numPerPage'], $page['numPerPage']));
        $this->assign('pager', $pager);
        $this->assign('page', $page);
        $this->display();
    }

    final public function dtongji() {
        $where = array();
        $name = I('get.name', '', 'trim');
        $sdate = I('get.sdate', '', 'trim');
        $edate = I('get.edate', '', 'trim');
        $id = I('get.id', '', 'intval');
        if ($id) {
            $where['id'] = array('eq', $id);
        }
        if ($name) {
            $where['title'] = array('like', $name . '%');
        }
        if ($sdate) {
            $where['add_time'] = array('egt', strtotime($sdate));
        }
        if ($edate) {
            $where['_string'] = 'add_time < ' . strtotime($edate . ' 23:59:59');
        }
        $page['pageNum'] = max(1, I('get.p', 1, 'intval'));
        $page['numPerPage'] = I('get.numPerPage', 20, 'intval');
        $conn = M('UserView');
        $list = $conn->where($where)->order('add_time desc')->page($page['pageNum'], $page['numPerPage'])
                ->select();
//        echo $conn->getLastSql();
        $page['totalCount'] = $conn->where($where)->count();
        $pager = showPage($page['totalCount'], $page['numPerPage']);
        $this->assign('list', $list);
        $this->assign('pager', $pager);
        $this->assign('page', $page);
        $this->display();
    }

    final public function info() {
        $id = I('get.id');
        if (IS_POST) {
            $data = array(
                'username' => I('post.username'),
                'email' => I('post.email'),
                'singn' => I('post.singn', '', 'trim'),
                'head_pic' => I('post.head_pic'),
                'status' => I('post.status'),
            );
            $password = I('post.password');
            if ($password) {
                $data['password'] = md5($password);
            }
            if ($id) {
                $temp = M('Users')->where(array('id' => $id))->save($data);
            } else {
                $data['add_time'] = time();
                $temp = M('Users')->add($data);
            }
            if (!$temp && M('Users')->getDbError()) {
                $this->error('操作失败!');
            } else {
                $this->success('操作成功!');
            }
            exit;
        }
        if ($id) {
            $info = M('Users')->where(array('id' => $id))->find();
        } else {
            $info['status'] = '1';
        }
        $this->assign('info', $info);
        $this->display();
    }

    final public function message() {
        $where = array();
        $name = I('get.title', '', 'trim');
        if ($name) {
            $where['title'] = array('like', $name . '%');
        }
        $page['pageNum'] = max(1, I('get.p', 1, 'intval'));
        $page['numPerPage'] = I('get.numPerPage', 20, 'intval');
        $conn = M('UsersMessage');
        $list = $conn->where($where)->order('add_time desc')->page($page['pageNum'], $page['numPerPage'])
                ->select();
        $page['totalCount'] = $conn->where($where)->count();
        $pager = showPage($page['totalCount'], $page['numPerPage']);
        $this->assign('list', $list);
        $this->assign('pager', $pager);
        $this->assign('page', $page);
        $this->display();
    }

    final public function sendMsm() {
        if (IS_POST) {
            $data = array(
                'show_name' => I('post.show_name'),
                'title' => I('post.title'),
                'desc' => I('post.desc', '', 'trim'),
                'is_root' => '1',
            );
            $data['uid'] = I('post.uid', 0, 'trim');
            $data['add_time'] = time();
            if (!$data['uid']) {
                $uidArr = M('Users')->field('id')->select();
                foreach ($uidArr as $val) {
                    $data['uid'] = $val['id'];
                    $temp = M('UsersMessage')->add($data);
                }
            } else {
                $uidArr = explode(',', $data['uid']);
                foreach ($uidArr as $val) {
                    $data['uid'] = $val;
                    $temp = M('UsersMessage')->add($data);
                }
            }

            if (!$temp && M('UsersMessage')->getDbError()) {
                $this->error('发送失败!');
            } else {
                $this->success('发送成功!');
            }
            exit;
        }
        $this->display();
    }

}