<?php

/**
 * MessageController.class.php
 * Author: Jansen
 * QQ: 6206574
 * Date: 2015-11-09
 */

namespace Member\Controller;

use Common\Extend\PageForMember;

class MessageController extends MemberCommonController {

    public function _initialize() {
        parent::_initialize();
        $this->assign('m_tab', 'account');
    }

    public function index($p = 1) {
        $total = M('Message')->where(array('mid_to' => session('MEMBERINFO.id')))->count();
        $lists = M('Message')->where(array('mid_to' => session('MEMBERINFO.id')))->page($p)->select();
        $this->assign('lists', $lists);
        $pager = new PageForMember($total);
        $pager->url = '/message/index?p=' . urlencode('[PAGE]');
        $this->assign('pager', $pager->show());
        $this->display();
    }

    public function detail($id) {
        $where['id'] = $id;
        $where['mid_to'] = session('MEMBERINFO.id');
        $detail = M('Message')->where($where)->find();
        if (!is_array($detail)) {
            $this->error('无此消息。');
        }
        $this->assign('detail', $detail);
        //变更阅读状态
        M('Message')->where($where)->setField('status', '1');
        $this->display();
    }

}