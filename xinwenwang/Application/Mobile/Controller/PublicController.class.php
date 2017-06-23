<?php
namespace Mobile\Controller;

use Common\Extend\PinYin;
use Common\Model\NewsModel;
use Think\Log;

class PublicController extends MobileCommonController {
    public function _initialize() {
        parent::_initialize();
    }
    public function login(){
        if(IS_POST){
            $u = I('post.username','','trim');
            $p = I('post.password','','md5');
            if(filter_var($u, FILTER_VALIDATE_EMAIL)){
                $u = M('Users')->where(array('email'=>$u))->getField('username');
            }
            if(empty($u)||empty($p)){
                $this->ajaxReturn(array('code'=>203,'msg'=>'用户名或密码不能为空'));
            }
            $user = M('Users')->where(array('username'=>$u))->find();
            if(!$user){
                $this->ajaxReturn(array('code'=>201,'msg'=>'用户名不存在'));
            }
            if($user['password']!=$p){
                $this->ajaxReturn(array('code'=>202,'msg'=>'密码错误'));
            }
            unset($user['password']);
            //将用户信息用cookies存着 避免关掉浏览器会退出登录
            cookie('user_info', $user, 7 * 24 * 3600);
            $this->ajaxReturn(array('code'=>200,'data'=>$user));
        }
        $this->display();
    }
    final public function loginout(){
        cookie('user_info',null);
        echo 1;exit;
    }
}