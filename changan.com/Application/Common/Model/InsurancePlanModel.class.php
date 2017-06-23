<?php

namespace Common\Model;

use Think\Model;

final class InsurancePlanModel extends Model {

    /**
     * 根据指定条数取列表
     * @param number $page
     * @param number $limit
     */
    final public function getList($where = array(), $limit = 20, $order = 'ctime desc') {
        $where['is_hide'] = '1';
        return $this->where($where)->order($order)->limit($limit)->select();
    }

    final public function getPageList($where = array(), $page = 1, $limit = 20, $order = 'ctime desc') {
        $where['is_hide'] = '1';
        $data['total'] = $this->where($where)->count();
        if ($data['total'] > 0) {
            $data['list'] = $this->where($where)->order($order)->page($page, $limit)->select();
            $data['pagehtml'] = showWebPage($data['total'], $limit);
        }
        return $data;
    }

    /**
     * 根据ID取详情
     * @param number $id
     * @return array
     */
    final public function getOneRow($where = array()) {
        $where['is_hide'] = '1';
        return $this->where($where)->find();
    }

    final public function dataCount($where = array()) {
        $where['is_hide'] = '1';
        return $this->where($where)->count();
    }
    
    final public function getInfo($id) {
        $key = 'user_online_insure' . $id;
        $result = S($key);
        if (!$result) {
            $result = $this->where(array('id' => $id))->find();
            if ($result) {
                S($key, $result);
            }
        }
        return $result;
    }

}

?>