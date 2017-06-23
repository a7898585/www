<?php

namespace Common\Model;

use Think\Model;

final class MobileCodesModel extends Model {

    /**
     * 检测验证码是否有效，有效时间为10分钟
     * @param string $mobile
     * @param number $code
     * @param number $expire  有效时间
     * @return boolean
     */
    final public function validCode($mobile, $code, $expire = '600') {
        $result = $this->where(array('mobile' => $mobile, 'code' => $code))->order(array('id' => 'DESC'))->find();
        if (!is_array($result))
            return false;
        if ((time() - strtotime($result['time_add'])) > $expire)
            return false;
        return true;
    }

    /**
     * 验证码校验
     * @param type $mobile
     * @param type $code
     * @return type
     */
    final public function validCheckCode($mobile, $code) {
        $result = $this->where(array('mobile' => $mobile, 'code' => $code))->order(array('id' => 'DESC'))->find();
        if (!is_array($result))
            return array('status' => 400, 'message' => '无验证码');
        if ((time() - strtotime($result['time_add'])) > 600)
            return array('status' => 300, 'message' => '超时');
        return array('status' => 200, 'message' => '成功');
    }

    /**
     * 检测是否可以发送下一条短信
     * @param string $mobile
     * @param number $interval
     * @return boolean
     */
    final public function validLastCodeInterval($mobile, $interval = 60) {
        $result = $this->where(array('mobile' => $mobile))->order(array('id' => 'DESC'))->find();
        if (is_array($result)) {
            $prevCodeTime = strtotime($result['time_add']);
            if ((time() - $prevCodeTime) < $interval)
                return false;
        }
        return true;
    }

    /**
     * 手机验证码
     * @param type $mobile
     * @param type $code
     * @return boolean
     */
    final public function addSms($mobile, $code, $message_id) {
        $data['mobile'] = $mobile;
        $data['code'] = $code;
        $data['message_id'] = $message_id;
        $result = $this->add($data);
        if (!$result || $this->getDbError()) {
            return false;
        }
        return true;
    }

    /**
     * 邮箱验证码
     * @param type $email
     * @param type $code
     * @return boolean
     */
    final public function addEmailSms($email, $code, $message_id) {
        $data['mobile'] = $email;
        $data['email_code'] = $code;
        $data['message_id'] = $message_id;
        $result = $this->add($data);
        if (!$result || $this->getDbError()) {
            return false;
        }
        return true;
    }

    /**
     * 邮箱验证码校验
     * @param type $mobile
     * @param type $code
     * @return type
     */
    final public function validCheckEmailCode($email, $code, $expire = '3600*2') {
        $result = $this->where(array('mobile' => $email, 'email_code' => $code))->order(array('id' => 'DESC'))->find();
        if (!is_array($result))
            return array('status' => 400, 'message' => '无验证码');
        if ((time() - strtotime($result['time_add'])) > $expire)
            return array('status' => 300, 'message' => '超时');
        return array('status' => 200, 'message' => '成功');
    }

    /**
     * 获取当天条数
     * @param type $email
     */
    final public function getEmailCodeCount($email) {
        if (!$email) {
            return false;
        }
        $count = $this->where(array('mobile' => $email, 'time_add' => array('gt', date('Y-m-d 00:00:00'))))->count();
        return $count;
    }

}

?>