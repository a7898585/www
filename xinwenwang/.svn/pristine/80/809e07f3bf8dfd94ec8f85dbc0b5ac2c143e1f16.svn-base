<?php

namespace Port\Controller;

use Port\Model\UsersMessageModel;

class ToolController extends PortCommonController {

    public function _initialize() {
        parent::_initialize();
    }

    final public function update() {
        $channel = $this->responce['channel'];
        $code = $this->responce['code'];
        $update_array = array();
        $temp = M('AppFiles')->where(array('channel' => $channel, 'status_id' => '1'))->order('add_time desc')->find();
        if ($temp) {
            if ($temp['code'] > $code) {
                $update_array = array('msg' => $temp['msg'], 'version' => $temp['version'], 'file_url' => 'http://www.xinwenwang.com' . $temp['file_url']);
            }
        }
        responseString('1', $update_array, '');
    }

    final public function feedback() {
        $uid = $this->uid;
        $msg = $this->responce['msg'];
        $temp = M('Feedback')->data(array('msg' => $msg, 'uid' => $uid, 'add_time' => date('Y-m-d H:i:s')))->add();
        if (!$temp && M('Feedback')->getDbError()) {
            responseString('0', array(), '失败');
        } else {
            responseString('1', array(), '');
        }
    }

    final public function message() {
        $page = $this->responce['page'];
        $limit = $this->responce['limit'];
        $uid = $this->uid;
        $m = new UsersMessageModel();
        $data = $m->getList($uid, $page, $limit);
        responseString('1', $data, '');
    }

    final public function delMsg() {
        $uid = $this->uid;
        if (!$uid) {
            responseString('0', array(), '无权限');
        }
        $id = $this->responce['id'];
        $temp = M('UsersMessage')->where(array('id' => intval($id)))->delete();
        if (!$temp && M('UsersMessage')->getDbError()) {
            responseString('0', array(), '失败');
        } else {
            responseString('1', array(), '');
        }
    }

    final public function isReadMsg() {
        $uid = $this->uid;
        $id = $this->responce['id'];
        if (!$uid) {
            responseString('0', array(), '无权限');
        }
        $temp = M('UsersMessage')->where(array('id' => intval($id)))->save(array('isread' => '1'));
        if (!$temp && M('UsersMessage')->getDbError()) {
            responseString('0', array(), '失败');
        } else {
            responseString('1', array(), '');
        }
    }

}

