<?php

namespace Home\Model;

use Think\Model;

final class DingyueModel extends Model {

    final public function getInfoByName($name) {
        $key = 'dingyue_' . $name;
        $result = S($key);
        if ($result == false) {
            $result = $this->where(array('name' => $name))->getField('id');
            if (!$result) {
                $result = $this->add(array('name' => $name));
            }
            S($key, $result);
        }
        return $result;
    }

    final public function getInfo($id) {
        $key = 'dingyue_info_' . $id;
        $result = S($key);
        if ($result == false) {
            $result = $this->where(array('id' => $id))->find();
            if ($result) {
                S($key, $result);
            }
        }
        return $result;
    }

    //用户订阅
    final public function addDingyue($uid, $sid) {
        $temp = false;
        if (!empty($uid) && !empty($sid)) {
            $data['add_time'] = time();
            $data['uid'] = $uid;
            $data['sid'] = $sid;
            $temp = M('users_dingyue')->add($data);
            if ($temp) {
                M('users_dingyue')->where(array('id' => $temp))->save(array('sort_id' => $temp));
            }
        }
        return empty($temp) ? false : true;
    }

    //用户取消订阅
    final public function deleteDingyue($uid, $sid) {
        $temp = false;
        if (!empty($uid) && !empty($sid)) {
            $temp = M('users_dingyue')->where('uid=' . $uid . ' and sid=' . $sid)->delete();
        }
        return empty($temp) ? false : true;
    }

    //判断用户是否订阅
    final public function isDingyue($uid, $sid) {
        if (!empty($uid) && !empty($sid)) {
            $temp = M('users_dingyue')->where('uid=' . $uid . ' and sid=' . $sid)->select();
        }
        return empty($temp) ? false : true;
    }

    //用户的订阅列表
    final public function getList($uid) {
        if (!empty($uid)) {
            $temp = M('users_dingyue ud')
                            ->join('xw_dingyue dy on ud.sid=dy.id')
                            ->field('ud.*,dy.name')
                            ->where("ud.uid={$uid}")->select();
        }
        return $temp ? : array();
    }

}

?>