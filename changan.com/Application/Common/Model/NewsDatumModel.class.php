<?php

namespace Common\Model;

use Think\Model;

final class NewsDatumModel extends Model {

    final public function getInfo($id) {
        $key = 'bx_news_datum_' . $id;
        $result = S($key);
        if (!$result) {
            $result = $this->where(array('id' => $id))->find();
            if ($result) {
                S($key, $result);
            }
        }
        return $result;
    }

    final public function getList($where = array(), $limit = 10, $order = 'addtime desc') {
        return $this->where($where)->order($order)->limit($limit)->select();
    }

    final public function getPageList($where = array(), $page = 1, $limit = 10, $order = 'addtime desc') {
        $data['total'] = $this->where($where)->count();
        if ($data['total'] > 0) {
            $data['list'] = $this->where($where)->order($order)->page($page, $limit)->select();
            $data['pagehtml'] = showWebPage($data['total'], $limit);
        }
        return $data;
    }

}

?>