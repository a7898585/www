<?php
namespace Home\Controller;

use Think\Controller;

class HomeCommonController extends Controller
{
    public function _initialize()
    {
        $n = I('get.n', 'tuijian');
        $this->assign('n', $n);
        $user = cookie('user_info');
//        $user = array('id'=>16,'username'=>'86424258','passwords'=>'96e79218965eb72c92a549dd5a330112','head_pic'=>'/user/2015-07-13/1436793364344.jpg','email'=>'86424258@qq.com');
        $this->assign('USER', $user);
        //把登录邮箱记着
        if (cookie('u_email')) {
            $this->assign('user_email', cookie('u_email'));
        }else{
            cookie('u_email', $user['email']);
        }
        
        $this->assign('base_url', C('URL_DOMAIN'));
        $_GET = array_map(htmlentities($_GET), $_GET);
        $_POST = array_map(htmlentities($_POST), $_POST);
    }
}