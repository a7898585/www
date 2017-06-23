<?php

namespace Admin\Controller;

use Think\Controller;

class AdminCommonController extends Controller {

    public $userinfo = fasle;

    public function _initialize() {
        $this->userinfo = session('admin');
        if (!$this->userinfo) {
//            $this->error('用户未登录,无法使用后台功能!!!','/public/login');
            redirect('/public/login');
        }
        $this->assign('home_url', C('HOME_URL'));
    }

}

?>