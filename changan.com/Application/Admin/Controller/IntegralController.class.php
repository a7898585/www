<?php

namespace Admin\Controller;

class IntegralController extends AdminCommonController {

    /**
     * 经验值明细
     */
    final public function index() {
        $page = I('get.p');
        $page = empty($page) ? 1 : $page;
        $perpage = 10;
        $where['uid'] = I('get.id');
        $score = D('User')->getScore($where['uid']);
//        $score['exp'] = 0;
        $score['level_arr'] = getLevel($score['exp']);
        $this->assign('score', $score);
        $where['class'] = 1;
        $data = D('Integral')->getPageList($where, $page, $perpage);
        $this->assign('data', $data);
        $this->assign('title', '经验值明细_积分管理');
        $this->display();
    }

    /**
     * 积分明细
     */
    final public function jifen() {
        $page = I('get.p');
        $page = empty($page) ? 1 : $page;
        $perpage = 15;
        $where['uid'] = I('get.id');
        $score = D('User')->getScore($where['uid']);
//        $score['exp'] = 0;
        $score['level_arr'] = getLevel($score['exp']);
        $this->assign('score', $score);
        $where['class'] = 2;
        $data = D('Integral')->getPageList($where, $page, $perpage);
        $this->assign('title', '积分明细_积分管理');
        $this->assign('data', $data);
        $this->display();
    }

}