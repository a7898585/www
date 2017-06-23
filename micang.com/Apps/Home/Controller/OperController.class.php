<?php

namespace Home\Controller;

use Think\Controller;
use Org\Util\String;
use Common\Extend\Sms;
use Common\Extend\NumberRoleAnalyse;

class OperController extends Controller {

    final public function upload_photo() {
        $temp = uploadPhoto($_FILES['file']);
        $this->ajaxReturn($temp);
    }

    /**
     * 发送短信验证码
     */
    public function send_sms() {
        $mobile = I('get.mobile');
        if (isMobile($mobile) == false) {
            echo '请输入正确的手机号';
            exit;
        }
        if (D('MobileCodes')->validLastCodeInterval($mobile) == false) {
            echo '短信发送太频繁';
            exit;
        }
        $str = '';
        $code = String::randString(6, 1);
        $sms = new Sms();
        $res = $sms->sendCaptcha($mobile, $code, '50333');
        if (!empty($res)) {
            D('MobileCodes')->addSms($mobile, $code, $res["smsMessageSid"]);
            echo 'ok';
        } else {
            echo '短信发送异常请稍后重试';
        }
        exit;
    }

    /**
     * 验证码验证
     */
    public function validSms() {
        if (IS_POST) {
            $mobile = I('post.mobile');
            $data['mobile'] = $mobile;
            $code = I('post.code');
            $email = I('post.email');
            if (!$code) {
                $this->ajaxReturn(array('status' => 500, 'message' => '验证码不能为空。'));
            }
            if (!$mobile) {
                $this->ajaxReturn(array('status' => 500, 'message' => '手机号不能为空。'));
            }
            if (!$email) {
                $this->ajaxReturn(array('status' => 500, 'message' => '邮箱不能为空。'));
            }
            $r = D('MobileCodes')->validCheckCode($mobile, $code);
            if ($r['status'] == 200) {
                $count = D('MobileCodes')->getEmailCodeCount($email);
                if ($count > 4) {
                    $this->ajaxReturn(array('status' => 300, 'message' => '每天最多允许找回5次密码，您已经超过。'));
                }
                $result = send_forget_email($email);
                if (!$result) {
                    $this->ajaxReturn(array('status' => 300, 'message' => '邮件发送失败，请重新发送。'));
                }
                $this->ajaxReturn(array('status' => 200, 'message' => '认证成功'));
            } elseif ($r['status'] == 300) {
                $this->ajaxReturn(array('status' => 500, 'message' => '您的验证码已经过期，请重新发送。'));
            } elseif ($r['status'] == 400) {
                $this->ajaxReturn(array('status' => 500, 'message' => '请输入正确的验证码。'));
            }
        }
    }

    /**
     * 根据邮箱获取手机
     */
    public function getMobile() {
        $email = I('post.email');
        if ($email) {
            $info = M('Members')->field('id,mobile_status')->where(array('username' => $email))->find();
            if (empty($info)) {
                $this->ajaxReturn(array('status' => 300, 'message' => '邮箱不存在。'));
            }
            if ($info['mobile_status'] == 1) {
                $mobile = M('MembersProfile')->where(array('mid' => $info['id']))->getField('mobile');
            }
            $this->ajaxReturn(array('status' => 200, 'mobile_status' => $info['mobile_status'], 'mobile' => $mobile));
        }
        $this->ajaxReturn(array('status' => 500, 'message' => '参数不正确。'));
    }

    /**
     * 发送忘记密码邮件
     */
    public function send_email() {
        $email = I('post.email');
        if ($email) {
            $count = D('MobileCodes')->getEmailCodeCount($email);
            if ($count > 4) {
                $this->ajaxReturn(array('status' => 300, 'message' => '每天最多允许找回5次密码，您已经超过。'));
            }
            $result = send_forget_email($email);
            if (!$result) {
                $this->ajaxReturn(array('status' => 300, 'message' => '邮件发送失败，请重新发送。'));
            }
            $this->ajaxReturn(array('status' => 200, 'message' => '成功。'));
        }
        $this->ajaxReturn(array('status' => 500, 'message' => '参数不正确。'));
    }

    public function addMid() {
        exit;
        set_time_limit(0);
        $numberAnalyse = new NumberRoleAnalyse();
        $start = '10000';
        $end = '100000';
        $mid = '';

        for ($start; $start < $end; $start++) {
            $mid = $start;
            if ($numberAnalyse->run($mid)) {
                continue;
            }
            $infoId = M('MembersLine')->where(array('id' => $mid))->getField('id');
            if ($infoId) {
                continue;
            }
            M('MembersMid')->add(array('id' => $mid));
            echo $mid . '<br>';
        }
        exit;
    }

}