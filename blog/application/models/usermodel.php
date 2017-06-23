<?php

/**
 * @ 用户信息模块
 *
 * @ Copyright (c) 2004-2012 CNFOL Inc. (http://www.cnfol.com)
 * @ version 3.0.1
 * @ author jainglw
 */
class UserModel extends MY_Model {

    function __construct() {
        parent::__construct();
        $this->cache->configs = $this->config->item('passport_cache');
    }

    /**
     * @ getUserAuthInfo 获取用户实名验证信息
     *
     * @param array $param
     * UserID 用户编号
     * @access public
     * @return array
     */
    public function getUserAuthInfo($param) {
        $m_key = 'u_' . $param['UserID'] . '_userauthinfo';
        $rs = $this->cache->get($m_key);

        if (!$this->iscache) {
            $rs = '';
        }

        if (empty($rs)) {
            $type = 'U037';
            $rs = $this->socket['passport']->senddata($type, $param);
            $rs = xmltoarray($rs);

            if ($rs['Code'] == '00') {
                $this->cache->set($m_key, serialize($rs), $this->expire);
            }
        } else {
            $rs = unserialize($rs);
        }

        return $rs;
    }

    /**
     * @ addUserAuthInfo 增修用户实名信息-真实信息部分
     *
     * @param array $param
     * UserID 用户ID
     * TrueName 姓名
     * Sex 性别
     * IdentityNumber 身份证号
     * IdentityPicFront 身份证正面
     * IdentityPicBack 身份证反面
     * Remark 备注
     * @access public
     * @return array
     */
    public function addUserAuthInfo($param) {
        if (empty($param['UserID']) || !is_numeric($param['UserID']))
            return false;


        $type = 'U035';
        $rs = $this->socket['passport']->senddata($type, $param);
        $rs = xmltoarray($rs);
        if (empty($rs) || !is_array($rs)) {
            return array('flag' => -1, 'msg' => '接口出错', 'desc' => '');
        } elseif ($rs['Code'] != '00') {
            return array('flag' => -2, 'msg' => '保存失败，您的错误代码是：' . $rs['Code'], 'desc' => $rs['Code']);
        }
        $m_key = sprintf('user_%d_authinfo', $param['UserID']);
        $this->cache->set($m_key, '');

        return array('flag' => 1, 'msg' => '保存成功', 'desc' => $rs['Code']);
    }

    /**
     * @ getUserBaseInfo 获取用户基本信息
     *
     * @param array $param
     * UserID 用户编号
     * UserName 用户名
     * Mobile 手机
     * Email 邮箱
     * @access public
     * @return array
     */
    public function getUserBaseInfo($param) {
        $m_key = 'u_' . $param['UserID'] . '_userbaseinfo';
        if ($param['UserID'] > 0) {
            $rs = $this->cache->get($m_key);
        }

        if (!$this->iscache) {
            $rs = '';
        }

        if (empty($rs)) {
            $rs = $this->getUserBase($param);

            if ($rs['Code'] == '00') {
                $this->cache->set($m_key, $rs, $this->expire);
            }
        }

        return $rs;
    }

    /**
     * @ getUserBase 获取用户基本信息公共函数
     *
     * @param array $param
     * UserID 用户编号
     * UserName 用户名
     * Mobile 手机
     * Email 邮箱
     * @access public
     * @return array
     */
    public function getUserBase($param) {
        $type = 'U005';
        $rs = $this->socket['passport']->senddata($type, $param);
        $rs = xmltoarray($rs);

        return $rs;
    }

    /**
     * 记录保存时间
     * @param type $userid
     * @return type
     */
    public function setUserDate($userid, $status) {
        $m_key = 'u_' . $userid . '_setuserdate';
        $time = $status == 1 ? 3600 * 24 * 3 : 3600 * 24 * 30;
        $this->cache->set($m_key, time(), $time);
        return;
    }

    /**
     * 获取保存时间
     * @param type $userid
     * @return type
     */
    public function getUserDate($userid) {
        $m_key = 'u_' . $userid . '_setuserdate';
        return $this->cache->get($m_key);
    }

    /**
     * 是否可以申请认证
     * @param type $userid 用户id
     * @param type $status 认证状态
     * @return string|boolean
     */
    public function isApplyAdd($userid, $status) {
        $ftime = $this->getUserDate($userid);
        if (!empty($ftime)) {
            if ($status == 1) {
                $ttime = time() - 3600 * 24 * 3;
            } else {
                $ttime = time() - 3600 * 24 * 30;
            }
            if ($ttime < $ftime) {
                return trun;
            }
        }
        return false;
    }

}
