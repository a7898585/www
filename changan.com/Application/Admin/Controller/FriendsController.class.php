<?php

namespace Admin\Controller;

use Admin\Controller\AdminCommonController;

final class FriendsController extends AdminCommonController {

    final public function _initialize() {
        parent::_initialize();
    }

    final public function all() {
        $where = array();
        $name = I('get.name', '', 'trim');
        if ($name) {
            $where['title'] = array('like', '%' . $name . '%');
        }
        $pageNo = max(1, I('get.p', 1, 'intval'));
        $limitRow = C('PAGE_LISTROW');
        $totalRows = D('FriendLinks')->count();
        $this->assign('items', D('FriendLinks')->getList($where, $pageNo, $limitRow, 'id desc'));
        $this->assign('pager', showWebPage($totalRows, $limitRow));
        $this->assign('totalRows', $totalRows);
        $this->display();
    }

    final public function add() {
        if (IS_POST) {
            $data['title'] = I('post.title', '', 'trim');
            $data['url'] = I('post.url', '', 'trim');
            $data['order'] = I('post.order', 0, 'intval');
            $data['show_type'] = I('post.type', 0, 'intval');
            $id = I('post.id');
            if ($id) {
                $result = D('FriendLinks')->where(array('id' => intval($id)))->data($data)->save();
            } else {
                $result = D('FriendLinks')->data($data)->add();
            }
            if (!$result) {
                $this->error('添加友情链接失败，请重试。');
            }
            $this->success('添加友情链接成功。', U('Friends/all'));
            exit();
        }
        $this->display();
    }

    final public function edit() {
        if (IS_POST) {
            $id = I('post.id');
            $data['title'] = I('post.title', '', 'trim');
            $data['url'] = I('post.url', '', 'trim');
            $data['order'] = I('post.order', 0, 'intval');
            $data['show_type'] = I('post.type', 0, 'intval');

            $result = M('FriendLinks')->where(array('id' => intval($id)))->save($data);

            if (!$result) {
                $this->error('保存友情链接失败，请重试。');
            }
            $this->success('保存友情链接成功。', I('post.refer'));
            exit();
        }
        $friendInfo = D('FriendLinks')->getInfo(I('get.id'));
        $this->assign('friendInfo', $friendInfo);
        $this->display('add');
    }

    final public function delete() {
        $result = D('FriendLinks')->where(array('id' => I('get.id')))->delete();
        if ($result) {
            $this->success('删除友情链接成功。', I('server.HTTP_REFERER'));
        } else {
            $this->error('删除友情链接失败，请重试。');
        }
    }

}

?>