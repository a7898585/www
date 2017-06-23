<?php

namespace Agent\Controller;

use Think\Controller;
use Common\Model\AreaModel;
use Common\Extend\BrmApi;

class PublicController extends Controller {

    final public function login() {
        $this->userinfo = $_SESSION['bx_agent_info'];
        if (!empty($this->userinfo)) {
            redirect(getDoMain('agent'));
        }
        if (IS_POST) {
            $username = I('post.username');
            $password = I('post.password');
            $user = M('UserAgent')->where(array('user_name' => $username))->find();
            if (!$user) {
                echo json_encode(array("s" => 0, "m" => "用户不存在!"));
                exit;
            }
            $brmApi = new BrmApi();
            $result = $brmApi->agentLogin($username, $password);
            if ($result['s'] > 0) {
                M('UserAgent')->data(array('lastlogintime' => time(), 'agent_id' => $user['agent_id']))->save();
                session('bx_agent_info', $user);
                echo json_encode(array("s" => 1, "m" => "登录成功!!!跳转中...!"));
                exit;
            } else {
                echo json_encode(array("s" => 0, "m" => $result['r']));
                exit;
            }
        }
        $this->display();
    }

    final public function login_out() {
        session('bx_agent_info', null);
        $this->success('退出成功!!!跳转中...', '/');
    }

}