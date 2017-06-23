<?php

namespace Common\Model;

use Think\Model;

final class IntegralModel extends Model {

    const MODULE_LOGIN = 1; //天天首次登录
    const MODULE_ADD_SHARE = 2; //发布营销分享
    const MODULE_ADD_BAOXIAN = 3; //上传保险资料
    const MODULE_NEW_USER = 4; //新用户首次登录
    const MODULE_USER_DETAIL_DATA = 5; //完善个人资料
    const MODULE_PERSON_SITE_VIEW = 6; //个人网站浏览
    const MODULE_ADD_NEWS = 7; //发布资讯
    const MODULE_ADD_WENDA = 8; //站内提问
    const MODULE_REPLY_WENDA = 9; //回复一条提问
    const MODULE_REPLY_SURE = 10; //答案被采纳
    const MODULE_REPLY_QUICKLY = 11; //最快回复
    const MODULE_UPLOAD_PRODUCT = 12; //上传产品
    const MODULE_NEWS_COMMENT = 13; //评论资讯
    const MODULE_SUGGEST = 14; //反馈建议
    const MODULE_ADD_PROGRAM = 15; //发布方案
    const MODULE_INVITE_FRIENDS = 16; //邀请好友
    const MODULE_BINDING = 17; //绑定其他平台

    final public function getPageList($where = array(), $page = 1, $limit = 20, $order = 'addtime desc') {
        $data['total'] = $this->where($where)->count();
        if ($data['total'] > 0) {
            $data['list'] = $this->where($where)->order($order)->page($page, $limit)->select();
            $data['pagehtml'] = showWebPage($data['total'], $limit);
        }
        return $data;
    }

    /**
     * 添加积分
     * @param type $uid
     * @param type $type
     */
    function addData($uid, $type, $desc = '') {
        $param['uid'] = $uid;
        $param['type'] = $type;
        $param['desc'] = $desc ? $desc : '';
        $this->addIntegral($param);
    }

    /**
     * 添加积分
     */
    function addIntegral($param) {
        $data['uid'] = $param['uid'];
        $data['type'] = $param['type'];
        $data['addtime'] = time();
        $scora_arr = C('integral');
        $score = $scora_arr[$data['type']]['score'];
        $exp = $scora_arr[$data['type']]['exp'];
        $data['name'] = $scora_arr[$data['type']]['name'];
        $data['desc'] = $param['desc'] ? $param['desc'] : $scora_arr[$data['type']]['desc'];
        $userData = D('User')->getScore($param['uid']);
        $data['num'] = $score;
        $data['total'] = $userData['score'] + $score;
        $data['class'] = '2';
        $r = $this->add($data);
        $data['num'] = $exp;
        $data['total'] = $userData['exp'] + $exp;
        $data['class'] = '1';
        $r2 = $this->add($data);
        if ($r && $r2) {
            D('User')->addScore($param['uid'], $score, $exp);
            return $r;
        }
        return false;
    }

    /**
     * 添加登录积分
     * @param type $param
     */
    function addLoginIntegral($uid) {
        $id = $this->checkAddLogin($uid);
        if ($id) {
            return false;
        }
        $data['uid'] = $uid;
        $data['type'] = self::MODULE_LOGIN;
        $this->addIntegral($data);
    }

    /**
     * 判断是否首次登录添加积分
     * @param type $uid
     * @return type
     */
    function checkAddLogin($uid) {
        $where['uid'] = $uid;
        $where['type'] = self::MODULE_LOGIN;
        $where['addtime'] = array(array('egt', strtotime(date('Y-m-d'))), array('lt', strtotime(date('Y-m-d') . ' 23:23:23')), 'and');
        return $this->getId($where);
    }

    /**
     * 普通会员完善资料
     * @param type $uid
     * @param type $param
     */
    function memberPerfectInfo($uid, $param) {
        $where['uid'] = $uid;
        $where['type'] = self::MODULE_USER_DETAIL_DATA;

        if ($this->getId($where)) {
            return false;
        }
        if ($param['name'] && $param['nickname'] && $param['birth'] && $param['address'] && $param['sex']
                && $param['phone'] && $param['tel'] && $param['income'] && $param['area_id'] && $param['expend']
                && $param['concept'] && $param['is_married'] && $param['idcard']) {
            $this->addData($uid, $where['type']);
        }
        return true;
    }

    /**
     * 每天积分上限判断 添加
     * @param type $uid
     * @param type $type
     * @return boolean
     */
    function addDateInter($uid, $type) {
        $where['uid'] = $uid;
        $where['class'] = 1;
        $where['type'] = $type;
        $where['addtime'] = array(array('egt', strtotime(date('Y-m-d'))), array('lt', strtotime(date('Y-m-d') . ' 23:23:23')), 'and');
        $num = $this->dataCount($where);
        $scora_arr = C('integral');
        if ($num < $scora_arr[$where['type']]['num']) {
            $this->addData($uid, $where['type']);
        }
        return true;
    }

    /**
     * 获取id
     * @param type $where
     * @return type
     */
    function getId($where) {
        return $this->where($where)->getField('id');
    }

    final public function dataCount($where = array()) {
        return $this->where($where)->count();
    }

}

?>