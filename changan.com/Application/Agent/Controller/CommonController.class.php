<?php

namespace Agent\Controller;

use Think\Controller;

class CommonController extends Controller {

    public $userinfo = fasle;
    public $base_url = fasle;

    public function _initialize() {
        $this->userinfo = $_SESSION['bx_agent_info'];
        if (empty($this->userinfo)) {
            redirect(getDoMain('agent') . 'public/login/');
        }
        $params['m'] = MODULE_NAME;
        $params['c'] = CONTROLLER_NAME;
        $params['a'] = ACTION_NAME;
        $this->base_url = getDoMain();
        $this->assign("userInfo", $this->userinfo);
        $this->assign("params", $params);
        $this->assign('base_url', $this->base_url);
    }

}