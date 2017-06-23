<?php
namespace Home\Controller;
use Home\Model\UsersModel;
use Think\Controller;
class PublicController extends HomeCommonController {
    public function _initialize() {
        parent::_initialize();
    }
    public function login(){
        if(IS_POST){
            $u = I('post.u','','trim');
            $p = I('post.p','','md5');
            $m = new UsersModel();
            $user = $m->login($u);
            $result = array();
            if(!$user){
                $result['code'] = 201;
                $result['msg'] = '邮箱不存在';
                $this->ajaxReturn($result);
            }
            if($user['password']!=$p){
                $result['code'] = 202;
                $result['msg'] = '密码错误';
                $this->ajaxReturn($result);
            }
            $result['code']=200;
            $result['url']=C('URL_DOMAIN');
            //将用户信息用cookies存着 避免关掉浏览器会退出登录
            cookie('user_info', $user, 7 * 24 * 3600);
            $this->ajaxReturn($result);
        }
        $this->display();
    }
    final public function loginout(){
        cookie('user_info',null);
        echo 1;exit;
    }
    final public function register(){
        $username = I('post.username');
        $email = I('post.email');
        $password = I('post.password');
        $head_pic = '/Uploads/2015-02-13/default.png';
        $um = new UsersModel();
        $temp = $um->checkUsername($username);
        if($temp){
            $this->ajaxReturn(array('code'=>201,'msg'=>'用户名已存在'));
        }
        $temp = $um->checkEmail($email);
        if($temp){
            $this->ajaxReturn(array('code'=>202,'msg'=>'邮箱已存在'));
        }
        $temp = $um->register($username,$email,$password,$head_pic);
        if($temp==false){
            $this->ajaxReturn(array('code'=>210,'msg'=>'内部错误,请联系管理员xmlijian@vip.qq.com'));
        }else{
            cookie('user_info', $um->getInfoById($temp), 7 * 24 * 3600);
            $this->ajaxReturn(array('code'=>200));
        }
    }
    final public function sysNav(){
        $list = array_merge(defaultCategory(),otherCategory());
        $str = I('post.str');
        $str = explode('_',$str);
        $defaultCategory = "";
        foreach($str as $item){
            if($item){
                $defaultCategory[$item] = $list[$item];
                unset($list[$item]);
            }
        }
        cookie('defaultCategory',$defaultCategory);
        cookie('otherCategory',$list);
    }
    final public function noLike(){
        $id = I('post.id');
        $this->ajaxReturn(array('code'=>200,'id'=>$id));
    }
    final public function newsTool(){
        $user = cookie('user_info');
        $uid = $user['id'];
        $t = I('post.t');
        $id = I('post.id');
        if($t=='like'){//喜欢
            $temp = D('News')->updGoodSum($id);
            if($temp){
                $this->ajaxReturn(array('code'=>200));
            }
        }else if($t=='hate'){//踩
            $temp = D('News')->updBadSum($id,$uid);
            if($temp){
                $this->ajaxReturn(array('code'=>200));
            }
        }else if($t=='fav'){//收藏
            $temp = D('UsersCollect')->checkCollect($id,$uid);

            if($temp){
                $temp = D('UsersCollect')->delCollect($id,$temp);
                if($temp){
                    $this->ajaxReturn(array('code'=>201));
                }
            }else{
                $temp = D('UsersCollect')->addCollect($id,$uid);
                if($temp){
                    $this->ajaxReturn(array('code'=>200));
                }
            }

        }
        $this->ajaxReturn(array('code'=>210));
    }

}