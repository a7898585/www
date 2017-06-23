<?php

namespace Admin\Controller;

use Admin\Controller\AdminCommonController;

final class FeedbackController extends AdminCommonController {

    final public function _initialize() {
        parent::_initialize();
    }

    final public function all() {
        $where = array();
        $name = I('get.name', '', 'trim');
        if ($name) {
            $where['msg'] = array('like', '%' . $name . '%');
        }
        $pageNo = max(1, I('get.p', 1, 'intval'));
        $limitRow = 10;
        $totalRows = M('Feedback')->count();
        $this->assign('items', M('Feedback')->where($where)->order('id desc')->page($pageNo, $limitRow)->select());
        $this->assign('pager', showPage($totalRows, $limitRow));
        $this->assign('totalRows', $totalRows);
        $this->display();
    }

    final public function reply() {
        if (IS_POST) {
            $data['reply'] = I('post.reply', '', 'trim');
            $id = I('post.id');
            if ($id) {
                $result = M('Feedback')->where(array('id' => intval($id)))->data($data)->save();
            } 
            if (!$result) {
                $this->error('回复失败，请重试。');
            }
            $this->success('回复成功。');
            exit();
        }
        $this->display();
    }

    final public function view() {
        $item = M('Feedback')->where(array('id' => I('get.id')))->find();
        $this->assign('item', $item);
        $this->display();
    }

}

?>