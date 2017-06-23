<?php

namespace Common\Model;

use Think\Model;

final class CompanyParentModel extends Model {

    /**
     * 根据指定条数取列表
     * @param number $page
     * @param number $limit
     */
    final public function getList($where = array(), $limit = 20, $order = 'id', $by = 'ASC') {
        $where['status'] = array('eq', '1');
        return $this->where($where)->order(array($order => $by))->limit($limit)->select();
    }

    final public function getPageList($where = array(), $page = 1, $limit = 20, $order = 'id desc') {
        $data['list'] = $this->where($where)->order($order)->page($page, $limit)->select();
        $data['total'] = $this->where($where)->count();
        $data['pagehtml'] = showWebPage($data['total'], $limit);
        return $data;
    }

    final public function getInfo($id) {
        $key = 'company_jigou_' . $id;
        $result = S($key);
        if (!$result) {
            $result = $this->where(array('id' => $id))->find();
            if ($result) {
                S($key, $result, 3600 * 24);
            }
        }
        return $result;
    }

    final public function getOneRow($where = array()) {
        return $this->where($where)->find();
    }

    final public function getAllRow($where = array(), $field = '*') {
        $where['status'] = array('eq', '1');
        return $this->field($field)->where($where)->select();
    }

    /**
     * 获取产品险种类别
     * @param type $comArr
     * @param type $id
     * @return type
     */
    final public function getProTypeById($cids, $id) {
        if (!empty($cids)) { 
            $this->model = new Model();
            $sql = "SELECT p.pro_type_id,t.name,t.pinyin  from  bx_pro p LEFT JOIN  bx_pro_type t  on p.pro_type_id = t.id WHERE p.company_id in({$cids}) GROUP BY p.pro_type_id ";
            $data = $this->model->query($sql);
            return $data;
        } else {
            return array();
        }
    }

    /**
     * 获取产品
     * @param type $comArr
     * @param type $id
     * @param type $limit
     * @return type
     */
    final public function getProById($cids, $id, $limit = '4') {
        if (!empty($cids)) {
            $where['company_id'] = array('in', $cids);
            $data = D('Pro')->getList($where, $limit);
            return $data;
        } else {
            return array();
        }
    }

}

?>