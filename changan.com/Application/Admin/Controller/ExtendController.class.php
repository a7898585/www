<?php

namespace Admin\Controller;

use Common\Extend\Sms;

class ExtendController extends AdminCommonController {

    final public function index() {

        $this->display();
    }

    final public function send_email() {
        $mailTitle = '常安保险（changan.com）';
        $subject = '常安保险（changan.com）邀请你加入(请勿回复此邮箱)';
        $this->assign('name', $name);
        $message = $this->fetch("send_email");
        $email_arr = M('User')->field('mail')->where(array('is_test' => '1'))->select();
        foreach ($email_arr as $value) {
            $email[] = $value['mail'];
        }
        $r = think_send_mail($email, $mailTitle . '-' . $subject, $message);
        if ($r) {
            $this->ajaxReturn(array('code' => 200));
        } else {
            $this->ajaxReturn(array('code' => 201, 'msg' => '邮件发送失败，请重新发送'));
        }
    }

    /**
     * 【常安保险ChangAn.com】还在为展业发愁吗？还在为交费前后态度不一样而生气吗？常安保险是完全免费的平台，多重优先特权，轻松推广更全面
     */
    final public function send_mobile() {
        set_time_limit(0);
        $p_arr = M('User')->field('phone')->where(array('is_test' => '1', 'phone' => array('gt', 0)))->select();
        foreach ($p_arr as $value) {
            $content = "【常安保险ChangAn.com】还在为展业发愁吗？还在为交费前后态度不一样而生气吗？常安保险是完全免费的平台，多重优先特权，轻松推广更全面";
            Sms::sendSMS($value['phone'], $content, time());
            sleep(2);
        }
        $this->ajaxReturn(array('code' => 200));
//        think_send_mail($email, $mailTitle . '-' . $subject, $message);
    }

}