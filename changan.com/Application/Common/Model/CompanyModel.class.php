<?php

namespace Common\Model;

use Think\Model;

final class CompanyModel extends Model {

    /**
     * 根据指定条数取列表
     * @param number $page
     * @param number $limit
     */
    final public function getList($where = array(), $limit = 20, $order = 'id', $by = 'ASC') {
        $where['is_hide'] = array('eq', '1');
        return $this->where($where)->order(array($order => $by))->limit($limit)->select();
    }

    final public function getPageList($where = array(), $page = 1, $limit = 20, $order = 'id desc') {
        $data['list'] = $this->where($where)->order($order)->page($page, $limit)->select();
        $data['total'] = $this->where($where)->count();
        $data['pagehtml'] = showWebPage($data['total'], $limit);
        return $data;
    }

    final public function getListByPage($where = array(), $page = 1, $count = 10, $is_page = 0, $order = 'ctime desc') {
        $where['is_hide'] = array('eq', '1');
        $list = $this->where($where)->page($page, $count)->order($order)->limit(5)->select();
        if ($is_page) {
            $totalRow = $this->where($where)->count();
            $pager = showWebPage($totalRow, $count);
            return array('list' => $list, 'pager' => $pager);
        } else {
            return $list;
        }
    }

    final public function getInfo($id) {
        $key = 'company_' . $id;
        $result = S($key);
        if (!$result) {
            $result = $this->where(array('id' => $id))->find();
            if ($result) {
                S($key, $result);
            }
        }
        return $result;
    }

    final public function getOneRow($where = array()) {
        $where['is_hide'] = array('eq', '1');
        return $this->where($where)->find();
    }

    final public function getAllRow($where = array(), $field = '*') {
        $where['is_hide'] = array('eq', '1');
        return $this->field($field)->where($where)->select();
    }

    final public function getAllList($where) {
        $key = 'company_list';
//        $result = S($key);
        if (!$result) {
            $where['status'] = array('eq', '1');
            $result = M('CompanyParent')->field('id,pinyin_f,name,short_name,pinyin')->where($where)->order(array('id' => 'asc'))->select();
            if ($result) {
                S($key, $result);
            }
        }
        return $result;
    }

}

?>