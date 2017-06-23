<?php

namespace Common\Model;

use Think\Model;

final class FriendLinksModel extends Model {

    final public function getList($where = array(), $page = 1, $limit = 20, $order = 'order ASC,id desc') {
        return $this->where($where)->order($order)->page($page, $limit)->select();
    }

    final public function getInfo($id) {
        return $this->where(array('id' => $id))->find();
    }

    /**
     * 获取所有链接地址
     */
    final public function getListAll($type = 0, $limit = 0) {
        $where['show_type'] = array('eq', $type);

        $m = $this->where($where)->order(array('order' => 'ASC'));
        if ($limit) {
            $m->limit($limit);
        }
        return $m->select();
    }

}

?>