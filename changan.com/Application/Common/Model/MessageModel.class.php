<?php

namespace Common\Model;

use Think\Model;

final class MessageModel extends Model {

    /**
     * 根据指定条数取列表
     * @param number $page
     * @param number $limit
     */
    final public function getDatePageList($where = array(), $page = 1, $limit = 10, $order = 'addtime desc') {
        $data['total'] = count($this->where($where)->order('datetime desc')->group('datetime')->select());
        if ($data['total'] > 0) {
            $data['list'] = $this->field('datetime')->where($where)->order('datetime desc')->page($page, $limit)->group('datetime')->select();  
            foreach ($data['list'] as $key => $value) {
                $data['list'][$key]['data'] = $this->where(array('datetime' => $value['datetime']))->order($order)->select();
            }
            $data['pagehtml'] = showWebPage($data['total'], $limit);
        }
        return $data;
    }

    final public function getInfo($id) {
        $key = 'bx_message_' . $id;
        $result = S($key);
        if (!$result) {
            $result = $this->where(array('id' => $id))->find();
            if ($result) {
                S($key, $result);
            }
        }
        return $result;
    }

    final public function dataCount($where = array()) {
        return $this->where($where)->count();
    }

}

?>