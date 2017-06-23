<?php

namespace Admin\Controller;

use Common\Extend\PageForAdmin;

final class FriendsController extends AdminCommonController {

    final public function _initialize() {
        parent::_initialize();
    }

    final public function index() {
        $where = array();
        $name = I('get.name', '', 'trim');
        if ($name) {
            $where['title'] = array('like', '%' . $name . '%');
        }
        $total = M('FriendLinks')->where($where)->count();
        $result = M('FriendLinks')->where($where)->page(max(1, I('get.p', 1, 'intval')))->order('id desc')->limit(20)->select();
        if (is_array($result)) {
            $page['data'] = $result;
        }
        $pager = new PageForAdmin($total);
        $page['html'] = $pager->show();
        $this->assign('page', $page);
        $this->display();
    }

    final public function add() {
        if (IS_POST) {
            $data['title'] = I('post.title', '', 'trim');
            $data['url'] = I('post.url', '', 'trim');
            $data['sort'] = I('post.sort', 0, 'intval');
            $data['status'] = I('post.status', 1, 'intval');
            $data['remark'] = I('post.remark', '', 'trim');
            $id = I('post.id');
            if (!$data['url']) {
                $this->ajaxReturn(array('status' => 500, 'message' => '链接地址不能为空。'));
            }
            if (!$data['title']) {
                $this->ajaxReturn(array('status' => 500, 'message' => '标题不能为空。'));
            }
            if ($id) {
                $result = M('FriendLinks')->where(array('id' => intval($id)))->data($data)->save();
            } else {
                $result = M('FriendLinks')->data($data)->add();
            }
            if (!$result) {
                $this->ajaxReturn(array('status' => 500, 'message' => '添加友情链接失败，请重试。'));
            }
            D('AdminOperLog')->addLog(session('ADMIN_ID'), '7', '添加修改友情链接', '添加修改友情链接操作,操作ID:' . ($id ? $id : $result) . ',标题：' . $data['title'] . ',字段：' . json_encode($data), ($id ? $id : $result));
            $this->ajaxReturn(array('status' => 200, 'message' => '添加友情链接成功'));
        }
        $id = I('get.id');
        $this->assign('info', M('FriendLinks')->where(array('id' => $id))->find());
        $this->display();
    }

    final public function delete() {
        $id = I('get.id');
        $result = M('FriendLinks')->where(array('id' => intval($id)))->delete();
        if ($result) {
            D('AdminOperLog')->addLog(session('ADMIN_ID'), '7', '删除友情链接', '删除友情链接操作,操作ID:' . $id);
            $this->ajaxReturn(array('status' => 200, 'message' => '删除友情链接成功'));
        } else {
            $this->ajaxReturn(array('status' => 500, 'message' => '删除友情链接失败，请重试。'));
        }
    }

    public function status($id, $status) {
        $data['id'] = $id;
        $data['status'] = $status;
        $result = M('FriendLinks')->data($data)->save();
        if (!$result) {
            $this->ajaxReturn(array('status' => 500, 'message' => '变更状态失败，请重试。'));
        }
        D('AdminOperLog')->addLog(session('ADMIN_ID'), '7', '友情链接变更状态', '友情链接变更状态操作,操作ID:' . $id . ',状态：' . $status, $id);
        $this->ajaxReturn(array('status' => 200, 'message' => '变更状态成功。'));
    }

}

?>