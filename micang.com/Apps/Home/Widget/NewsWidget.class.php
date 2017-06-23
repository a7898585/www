<?php

namespace Home\Widget;

use Think\Controller;

class NewsWidget extends Controller {

    public function _initialize() {
        
    }

    public function tab_list($type = '17', array $order = array('create_time' => 'DESC'), $limit = '8') {
        $data = M('News')->field('id,title')->where(array('cid' => $type))->order($order)->limit($limit)->select();
        $this->assign('data', $data);
        $this->display('Widget:News/tab_list');
    }

    public function index_list($type = '17', array $order = array('create_time' => 'DESC'), $limit = '7') {
        $data = M('News')->field('id,title,create_time')->where(array('cid' => $type))->order($order)->limit($limit)->select();
        $this->assign('data', $data);
        $this->display('Widget:News/index_list');
    }

    public function news_class() {
        $category = M('NewsCategory')->select();
        $this->assign('category', $category);
        $this->display('Widget:News/news_class');
    }

    public function hot($limit = '5') {
        $data = M('News')->field('id,title')->order('click_nums desc')->limit($limit)->select();
        $this->assign('data', $data);
        $this->display('Widget:News/tab_list');
    }

    public function domain_trade() {
        $this->display('Widget:News/domain_trade');
    }

    public function domain_sale() {
        $this->display('Widget:News/domain_sale');
    }

}

