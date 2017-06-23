<?php

namespace Agent\Controller;

class PayController extends CommonController {

    public function _initialize() {
        parent::_initialize();
        
    }

    final public function index() {
        $this->assign('title', '充值_代理商后台');
        $this->display();
    }

  

}