<?php

/**
 * PublicController.class.php
 * Author: Jansen
 * QQ: 6206574
 * Date: 2015-09-28
 */

namespace Home\Controller;

use Common\Extend\NumberRoleAnalyse;

class PublicController extends HomeCommonController {

    /**
     * 会员登录
     * @author Jansen
     * @since 2015-09-29
     */
    public function login() {
        if (IS_POST) {
            if (I('post.verify_code', '', 'md5') != $_SESSION['verify']) {
                $this->ajaxReturn(array('status' => 500, 'message' => '验证码错误。'));
            }
            if (is_numeric(I('post.username'))) {
                $where['id'] = I('post.username');
            } else {
                $where['username'] = I('post.username');
            }
            $where['password'] = md5(I('post.password', '', ''));
            $result = M('Members')->where($where)->find();
            if (!is_array($result)) {
                $this->ajaxReturn(array('status' => 404, 'message' => '帐号或密码错误。'));
            } elseif ($result['status'] == '0') {
                $this->ajaxReturn(array('status' => 500, 'message' => '帐号已被禁用，请联系客服人员。'));
            } elseif ($result['status'] == '2') {
                $this->ajaxReturn(array('status' => 500, 'message' => '帐号未激活，请先激活或<a href="/public/resend_mail" data-btn="btn_resend" style="color:#ffc106;">重新获取激活邮件</a>。'));
            } else {
                session('MEMBERINFO', $result);
                $data['id'] = $result['id'];
                $data['login_time'] = date('Y-m-d H:i:s');
                $data['login_ip'] = get_client_ip();
                $data['login_count'] = $result['login_count'] + 1;
                M('Members')->data($data)->save();
                $this->ajaxReturn(array('status' => 200, 'message' => '登录成功。'));
            }
        }
        if (is_array(session('MEMBERINFO'))) {
            redirect('http://' . str_replace('www.', 'member.', I('server.HTTP_HOST')));
        }
        $seo = array(
            'title' => C('LOGIN_TITLE'),
            'key' => C('LOGIN_KEYWORDS'),
            'des' => C('LOGIN_DESC')
        );
        $this->assign('seo', $seo);
        $this->assign('refer', I('get.refer_url')?I('get.refer_url'):$_SERVER['HTTP_REFERER']);
        $this->display();
    }

    /**
     * 会员注册
     * @author Jansen
     * @since 2015-09-28
     */
    public function register() {
        if (IS_POST) {
            if (I('post.verify_code', '', 'md5') != $_SESSION['verify']) {
                $this->ajaxReturn(array('status' => 500, 'message' => '验证码错误。'));
            }
            //TODO 验证各字段
            if (!filter_var(I('post.username'), FILTER_VALIDATE_EMAIL)) {
                $this->ajaxReturn(array('status' => 500, 'message' => '请输入正确的邮箱格式。'));
            }
            if (strlen(I('post.password', '', '')) < 6) {
                $this->ajaxReturn(array('status' => 500, 'message' => '请设置6位字符以上的密码。'));
            }
            if (I('post.password', '', '') != I('post.password_confirm', '', '')) {
                $this->ajaxReturn(array('status' => 500, 'message' => '两次输入的密码不一致，请重新输入。'));
            }
            //取默认会员等级
            $memberLevel = M('MemberLevels')->where(array('default' => '1'))->find();
            if (!is_array($memberLevel)) {
                $this->error('系统异常，请联系客服人员。');
            }
            M('MembersMid')->startTrans();
            //取ID
            $memberId = M('MembersMid')->where(array('status' => '0'))->getField('id');
            $data['id'] = $memberId;
            $data['username'] = strtolower(I('post.username'));
            $data['password'] = md5(I('post.password', '', ''));
            $data['status'] = '2';
            $data['register_time'] = date('Y-m-d H:i:s');
            $data['level'] = $memberLevel['id'];
            try{
                $result = M('Members')->data($data)->add();
            }catch (\Exception $e){
                M('MembersMid')->rollback();
                list($dbCode, $dbMessage) = explode(':', $e->getMessage());
                if ($dbCode == '1062') {
                    preg_match('/Duplicate entry \'(.*)\' for key \'(.*)\'/i', $dbMessage, $match);
                    if ($match[2] == 'username') {
                        $this->ajaxReturn(array('status' => 500, 'message' => '注册失败，邮箱已存在，请更换。'));
                    } elseif ($match[2] == 'PRIMARY') {
                        $this->ajaxReturn(array('status' => 500, 'message' => '注册失败，分配ID时发生异常，请联系客服。'));
                    }
                } else {
                    $this->ajaxReturn(array('status' => 500, 'message' => '注册失败，请重试。'));
                }
            }
            if (!$result){
                M('MembersMid')->rollback();
                $this->ajaxReturn(array('status' => 500, 'message' => '注册失败，请重试。'));
            }
            unset($result);
            $result = M('MembersMid')->where(array('id' => $memberId))->delete();
            if (!$result){
                M('MembersMid')->rollback();
                $this->ajaxReturn(array('status' => 500, 'message' => '注册失败，请重试。'));
            }
            M('MembersMid')->commit();
            //发送激活邮件
            $search = array('%ACTIVATE_URL%', '%DATE%', '%SYSTEM_URL%');
            $activateUrl = 'http://' . I('server.HTTP_HOST') . '/public/activate?code=' . md5($memberId . I('post.username') . date('Y-m-d')) . '&user=' . I('post.username');
            $replace = array($activateUrl, date('Y-m-d'), 'http://' . I('server.HTTP_HOST'));
            $mailContent = str_replace($search, $replace, C('MAIL_CONFIG.ACTIVATE_EMAIL_CONTENT'));
            $result = SendMail(I('post.username'), C('MAIL_CONFIG.ACTIVATE_EMAIL_TITLE'), $mailContent, '米仓网客户服务中心');
            if (!$result) {
                $this->ajaxReturn(array('status' => 200, 'message' => '注册成功，但激活邮件发送失败，您可以在登录功能中重新获取激活邮件。'));
            }
            $this->ajaxReturn(array('status' => 200, 'message' => '注册成功！系统已向您提供的邮箱发送了一封激活邮件，请登录您的邮箱激活帐号。'));
        }
        $seo = array(
            'title' => C('REGISTER_TITLE'),
            'key' => C('LOGIN_KEYWORDS'),
            'des' => C('LOGIN_DESC')
        );
        $this->assign('seo', $seo);
        $this->display();
    }

    /**
     * 忘记密码
     */
    public function forget() {
        $this->display();
    }

    public function active_password() {
        if (IS_POST) {
            $email = I('post.username');
            $code = I('post.code');
            $res = D('MobileCodes')->validCheckEmailCode($email, $code);
            if ($res['status'] == '200') {
                if (strlen(I('post.password', '', '')) < 6) {
                    $this->ajaxReturn(array('status' => 500, 'message' => '请设置6位字符以上的密码。'));
                }
                if (I('post.password', '', '') != I('post.password_confirm', '', '')) {
                    $this->ajaxReturn(array('status' => 500, 'message' => '两次输入的密码不一致，请重新输入。'));
                }
                $data['password'] = md5(I('post.password', '', ''));
                $result = M('Members')->where(array('username' => $email))->data($data)->save();
                if (!$result) {
                    $this->ajaxReturn(array('status' => 500, 'message' => '密码修改失败。'));
                }
                $this->ajaxReturn(array('status' => 200, 'message' => '密码修改成功。'));
            }
            $this->ajaxReturn(array('status' => 500, 'message' => '已过期。'));
        }
        $code = I('get.code');
        $email = I('get.email');
        $res = D('MobileCodes')->validCheckEmailCode($email, $code);
        if ($res['status'] == '200') {
            $this->assign('email', $email);
            $this->assign('code', $code);
        } else {
            if ($res['status'] == '300') {
                $this->assign('msgHtml', '验证码已经超时!');
            } elseif ($res['status'] == '400') {
                $this->assign('msgHtml', '无效验证码!');
            }
            $this->assign('nopass', 1);
        }
        $this->display();
    }

    /**
     * 注册协议
     * @author Jansen
     * @since 2015-09-28
     */
    public function agreement() {
        $this->display();
    }

    /**
     * 帐号激活
     * @author Jansen
     * @since 2015-09-29
     */
    public function activate($user, $code) {
        $memberInfo = M('Members')->where(array('username' => $user))->find();
        if (!is_array($memberInfo)) {
            $this->_empty();
        }
        if ($memberInfo['status'] == '1') {
            $this->error('帐号已激活，感谢您使用米仓平台！', '/public/login');
        } elseif (md5($memberInfo['id'] . $user . date('Y-m-d')) != $code) {
            $this->error('激活失败，请确认URL正确或者在有效期内。', '/public/login');
        }
        M('Members')->startTrans();
        $result = M('Members')->where(array('username' => $user))->data(array('status' => '1'))->save();
        if (!$result) {
            M('Members')->rollback();
            $this->error('激活失败，请重试。');
        }
        $result = M('MembersMoney')->data(array('mid' => $memberInfo['id'], 'recharge_money' => 0))->add();
        if (!$result) {
            M('Members')->rollback();
            $this->error('激活失败，请重试。');
        }
        M('Members')->commit();
        $this->success('帐号激活成功，感谢您使用米仓平台！', '/public/login');
        exit();
    }

    public function resend_mail($user) {
        $memberInfo = M('Members')->where(array('username' => $user))->find();
        //发送激活邮件
        $search = array('%ACTIVATE_URL%', '%DATE%', '%SYSTEM_URL%');
        $activateUrl = 'http://' . I('server.HTTP_HOST') . '/public/activate?code=' . md5($memberInfo['id'] . $memberInfo['username'] . date('Y-m-d')) . '&user=' . $memberInfo['username'];
        $replace = array($activateUrl, date('Y-m-d'), 'http://' . I('server.HTTP_HOST'));
        $mailContent = str_replace($search, $replace, C('MAIL_CONFIG.ACTIVATE_EMAIL_CONTENT'));
        $result = SendMail($memberInfo['username'], C('MAIL_CONFIG.ACTIVATE_EMAIL_TITLE'), $mailContent, '米仓网客户服务中心');
        if (!$result) {
            $this->ajaxReturn(array('status' => 200, 'message' => '激活邮件发送失败。'));
        }
        $this->ajaxReturn(array('status' => 200, 'message' => '激活邮件发送成功，请登录您的邮箱激活帐号。'));
    }

    /**
     * 生成验证码
     * @author Jansen
     * @since 2015-09-29
     */
    public function vcode() {
        $vcode = new \Think\Verify(array('length' => 6, 'seKey' => 'micang.com', 'fontttf' => '4.ttf'));
        $vcode->entry();
    }

    /**
     * 检查验证码
     * @author Jansen
     * @since 2015-09-29
     * @param $name
     * @param $param
     */
    public function vcode_check($name, $param) {
        $vcode = new \Think\Verify(array('length' => 6, 'seKey' => 'micang.com'));
        $key = md5(substr(md5($vcode->seKey), 5, 8) . substr(md5($vcode->seKey), 8, 10));
        // 验证码不能为空
        $secode = session($key);
        if (empty($param) || empty($secode)) {
            $this->ajaxReturn(array('status' => 'n', 'info' => '请输入验证码'));
        }
        // session 过期
        if (NOW_TIME - $secode['verify_time'] > $vcode->expire) {
            session($key, null);
            $this->ajaxReturn(array('status' => 'n', 'info' => '验证码已过期，请刷新'));
        }
        $verifyCode = md5(substr(md5($vcode->seKey), 5, 8) . substr(md5(strtoupper($param)), 8, 10));
        if ($verifyCode != $secode['verify_code']) {
            $this->ajaxReturn(array('status' => 'n', 'info' => '验证码错误'));
        }
        $this->ajaxReturn(array('status' => 'y', 'info' => ''));
    }

}