<?php

namespace Common\Model;

use Think\Model;

final class NewsModel extends Model {

    final public function getPageList($where = array(), $page = 1, $limit = 20) {
        $where['is_hide'] = array('eq', '1');
        $data['total'] = $this->where($where)->count();
        if ($data['total'] > 0) {
            $data['list'] = $this->field("title,id,left(content,250) as content,add_time,keyword,type_id")->where($where)->order(array('id' => 'desc'))->page($page, $limit)->select();
            $data['pagehtml'] = showWebPage($data['total'], $limit);
        }
        return $data;
    }

    /**
     * 根据资讯类型
     * @param type $typeId 资讯类型
     * @param type $page
     * @param type $limit
     */
    final public function getPageListByTypeId($typeId, $page = 1, $limit = 20) {
        $key = C('B000');
        $param = 'type_id:' . $typeId . '_' . $page . '_' . $limit;
        $key = str_replace('{param}', $param, $key);
        $news = S($key);
        $total = S(C('N000'));
        if (empty($data) || empty($total)) {
            $data = $this->getPageList(array('type_id' => $typeId), $page, $limit);
            S($key, $data, C('expire_day'));
            S(C('N000'), $data['total'], C('expire_day'));
        }
        return $data;
    }

    /**
     * 根据指定条数取列表
     * @param number $page
     * @param number $limit
     */
    final public function getList($where = array(), $limit = 10, $order = 'id desc') {
        $where['is_hide'] = array('eq', '1');
        return $this->field("title,id,left(content,250) as content,add_time,keyword,type_id")->where($where)->order($order)->limit($limit)->select();
    }

    /**
     * 获取分组列表
     * @param type $where
     * @param type $start
     * @param type $rowCount
     * @param type $group
     * @param type $order
     * @param type $by
     * @return type
     */
    public function getGroupList($where = array(), $start = 0, $rowCount = 10, $group = 'type_id', $order = "id desc") {
        $where['is_hide'] = array('eq', '1');
        $result = $this->field("title,id,left(content,250) as content,add_time,keyword,type_id")->where($where)->order($order)->group($group)->limit($start, $rowCount)->select();
        return $result;
    }

    /**
     * 获取一条数据
     * @param number $where
     * @return array
     */
    final public function getOneRow($where) {
        $where['is_hide'] = array('eq', '1');
        return $this->where($where)->find();
    }

    final public function dataCount($where = array()) {
        $where['is_hide'] = array('eq', '1');
        return $this->where($where)->count();
    }

    final public function getFieldName($id, $field = 'title') {
        $res = $this->getInfo($id);
        return $res[$field];
    }

    final public function getInfo($id) {
        $key = 'bx_news_info_' . $id;
        $result = S($key);
        if (!$result)
        {
            $result = $this->where(array('id' => $id))->find();
            if ($result) {
                S($key, $result);
            }
        }
        return $result;
    }

    final public function getListByPage($class_id = 0, $title = '', $page = 1, $count = 10, $order = 'id desc', $is_page = 0) {
        $where = array();
        if ($class_id) {
            $where['type_id'] = $class_id;
        }
        if ($title) {
            $where['title'] = array('like', '%' . $title . '%');
        }
        $list = $this->where($where)->page($page, $count)->order($order)->limit(5)->select();
        if ($is_page) {
            $totalRow = $this->where($where)->count();
            $pager = showWebPage($totalRow, $count);
            return array('list' => $list, 'pager' => $pager);
        } else {
            return $list;
        }
    }
    final public function addComment($data){
        $temp = M('NewsComment')->data($data)->add();
        if(!$temp&&M('UserComment')->getDbError()){
            return false;
        }else{
            return true;
        }
    }
}

?>