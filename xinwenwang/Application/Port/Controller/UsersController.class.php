<?php

namespace Port\Controller;

use Org\Util\String;
use Port\Model\UsersModel;
use Think\Log;

require("MailController.class.php");

class UsersController extends PortCommonController {

    public function _initialize() {
        parent::_initialize();
    }

    final public function index() {
        responseString(1, array(), 12);
    }

    /**
     * 注册用户
     */
    final public function register() {
        $data = $this->responce;
        $um = new UsersModel();
        $temp = $um->checkUsername(trim($data['username']));
        if ($temp) {
            responseString(0, array(), '用户名已存在');
        }
        $temp = $um->checkEmail(trim($data['email']));
        if ($temp) {
            responseString(0, array(), '邮箱已存在');
        }
        $temp = $um->register(trim($data['username']), trim($data['passwrod']), trim($data['email']), trim($data['device_token']));
        if (!$temp && $um->getDbError()) {
            responseString(0, array(), '注册失败,请重试!!');
        } else {
            responseString(1, array(), '');
        }
    }

    /**
     * 登陆
     */
    final public function login() {
        $um = new UsersModel();
        $username = trim($this->responce['username']);
        if (filter_var($username, FILTER_VALIDATE_EMAIL)) {
            $username = M('Users')->where(array('email' => $username))->getField('username');
        }
        $info = $um->getInfoByName($username);
        if (!$info) {
            responseString(0, array(), '用户名不存在');
        }
        if ($info['password'] != md5(trim($this->responce['passwrod']))) {
            responseString(0, array(), '用户密码错误');
        }
        $login_key = $um->loginLog($info['id']);
        if (!$info['deviceid']) {
            M('Users')->data(array('device_token' => $this->responce['device_token'], 'id' => $info['id']))->save();
        }
        responseString(1, array('login_key' => $login_key, 'username' => $info['username'], 'email' => $info['email'], 'singn' => $info['singn'], 'head_pic' => setUpUrl($info['head_pic'])));
    }

    final public function upd_head_pic() {
        $uid = $this->uid;
        $head_pic = $this->responce['head_pic'];
        $head_pic = str_replace(C('YOU_PAI_YUN'), '', $head_pic);
        M('Users')->data(array('id' => $uid, 'head_pic' => $head_pic))->save();
        responseString('1', array(), '');
    }

    final public function upd_users() {
        $uid = $this->uid;
        $email = $this->responce['email'];
        $singn = $this->responce['singn'];
        $data = array(
            'email' => $email,
            'singn' => $singn
        );
        M('Users')->data($data)->where(array('id' => $uid))->save();
        responseString('1', array(), '');
    }

    final public function change_pwd() {
        Log::write(var_export($_POST, true));
        Log::write(var_export($_GET, true));
        $news_pwd = $this->responce['news_pwd'];  //旧密码
        $old_pwd = $this->responce['old_pwd']; //新密码
        $uid = $this->uid;
        $user = M('Users')->where(array('id' => $uid))->find();
        if ($user['password'] != md5($old_pwd)) {
            responseString('0', array(), '原密码不正确');
        }
        $temp = M('Users')->data(array('password' => md5($news_pwd)))->where(array('id' => $uid))->save();
        if (!$temp && M('User')->getDbError()) {
            responseString('0', array(), '密码修改失败');
        } else {
            responseString('1', array(), '密码修改成功');
        }
    }

    final public function reset_pwd() {
        $username = $this->responce['username'];
        $userEmail = $this->responce['email'];
        $where['username'] = array('eq', $username);
        $u = new UsersModel();
        $info = $u->getInfoByName($username);
        if (!$info) {
            responseString('0', array(), '用户名不存在');
        }
        if ($info['email'] != $userEmail) {
            responseString('0', array(), '邮箱不存在');
        }

        if ($userEmail) {
            $passwd = String::randString(6, 1);
            M('Users')->where($where)->save(array('password' => md5($passwd)));
            //
            $mail = new Mailcontroller('smtp.126.com', 25, 'ydleternal@126.com', 'nwcziontig');
            $mail->isHTML();
            $mail_body = '<div style="width:680px;padding:0 10px;margin:0 auto;">'
                    . '<div style="line-height:1.5;font-size:14px;margin-bottom:25px;color:#4d4d4d;">'
                    . '<strong style="display:block;margin-bottom:15px;">亲爱的 ' . $username . ' 用户： 您好！'
                    . '</strong>'
                    . '<p>您的新密码为：' . $passwd . '，感谢您对新闻王的支持</p>';
            $mail->send('新闻王', 'ydleternal@126.com', $userEmail, '新闻王 - 更懂你', $mail_body);
        }
        responseString('1', array(), '');
    }

    final public function my_fans() {
        $uid = $this->uid;
        $page = $this->responce['page'];
        $limit = $this->responce['limit'];
        $m = new UsersModel();
        $list = $m->myFans($uid, $page, $limit);
        responseString('1', $list, '');
    }

    final public function hot_fans() {
        $uid = $this->uid;
        $m = new UsersModel();
        $list = $m->hotFans($uid);
        responseString('1', $list, '');
    }

    final public function add_fans() {
        $uid = $this->uid;
        $fuid = $this->responce['fuid'];
//        $temp = M('UsersFans')->where(array('uid'=>$uid ,'fuid' =>$fuid))->select();
//        if($temp){
//            responseString(0,array(),'你已有该好友');
//        }
        M('UsersFans')->where(array('uid' => $fuid))->count();
        $data = array(
            'uid' => $uid,
            'fuid' => $fuid,
            'add_time' => time()
        );
        $temp = M('UsersFans')->data($data)->add();
        responseString('1', array(), '');
    }

    final public function del_fans() {
        $uid = $this->uid;
        $fuid = $this->responce['fuid'];
        $where = array(
            'uid' => $uid,
            'fuid' => $fuid
        );
        $temp = M('UsersFans')->where($where)->delete();
        responseString('1', array(), '');
    }


}

