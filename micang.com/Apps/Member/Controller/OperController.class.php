<?php

namespace Member\Controller;

use Think\Controller;
use Org\Util\String;
use Common\Extend\Sms;
use Common\Extend\Image;

class OperController extends Controller {

    final public function upload_photo() {
        $temp = uploadPhoto($_FILES['file']);
        $this->ajaxReturn($temp);
    }

    /**
     * 微信验证码发送
     */
    public function send_weixin_sms() {
        $id = I('get.id');
        $weixin = I('get.weixin');
        if (!$id || !$weixin) {
            echo '参数错误';
            exit;
        }
        if (D('MobileCodes')->validLastCodeInterval($weixin) == false) {
            echo '验证码发送太频繁';
            exit;
        }
        $note = '您正在执行PUSH域名操作';
        if (sendWeixinCode($id, $note)) {
            echo 'ok';
        } else {
            echo '验证码发送异常';
        }
        exit;
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
        $res = $sms->sendCaptcha($mobile, $code, '51098');
        if (!empty($res)) {
            D('MobileCodes')->addSms($mobile, $code, $res["smsMessageSid"]);
            echo 'ok';
        } else {
            echo '短信发送异常请稍后重试';
        }
        exit;
    }

    /**
     * 验证码显示
     */
    public function verify() {
        \Common\Extend\Image::buildImageVerify();
    }

    public function verify_code() {
        if (I('get.code', '', 'md5') == $_SESSION['verify']) {
            echo 'ok';
        } else {
            echo '验证码错误';
        }
        exit;
    }

}