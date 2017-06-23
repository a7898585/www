<?php

namespace Common\Model;

use Think\Model;

final class CompanyTypeModel extends Model {

    /**
     * 根据指定条数取列表
     * @param number $page
     * @param number $limit
     */
    final public function getList($where = array(), $limit = 20, $order = 'id') {
        $where['is_hide'] = array('eq', '1');
        return $this->where($where)->order($orderF)->limit($limit)->select();
    }

    final public function getInfo($id) {
        $key = 'company_type_' . $id;
        $result = S($key);
        if (!$result) {
            $result = $this->where(array('id' => $id))->find();
            if ($result) {
                S($key, $result);
            }
        }
        return $result;
    }

    final public function getAllList() {
        $key = 'company_type_all';
        $result = S($key);
        if (!$result) {
            $result = $this->field('id,pinyin,name')->where(array('is_hide' => '1'))->order(array('id' => 'asc'))->select();
            if ($result) {
                S($key, $result);
            }
        }
        return $result;
    }

}

?>