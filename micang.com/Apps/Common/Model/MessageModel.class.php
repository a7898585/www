<?php

namespace Common\Model;

use Think\Model;

final class MessageModel extends Model {

    /**
     * 获取未读
     * @param type $mid
     * @return type
     */
    final public function getCountByMid($mid) {
        return $this->where(array('mid_to' => $mid, 'status' => '0'))->count();
    }

    /**
     * 获取最新一条
     * @param type $mid
     * @return type
     */
    final public function getInfoByMid($mid) {
        return $this->where(array('mid_to' => $mid, 'status' => '0'))->order('id desc')->find();
    }

    /**
     * 添加信息
     * @param type $mid
     * @param type $title
     * @param type $content
     * @return boolean
     */
    final public function addMessage($mid, $title, $content) {
        $data['mid_to'] = $mid;
        $data['title'] = $title;
        $data['content'] = $content;
        $data['create_time'] = time();
        $result = $this->add($data);
        if (!$result || $this->getDbError()) {
            return false;
        }
        return $result;
    }

    /**
     * 读信息
     * @param type $id
     * @param type $mid
     * @return type
     */
    final public function readMessage($id, $mid) {
        return $this->where(array('mid_to' => $mid, 'id' => $id))->save(array('status' => '1'));
    }

}