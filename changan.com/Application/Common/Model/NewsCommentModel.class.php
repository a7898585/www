<?php

namespace Common\Model;

use Think\Model;

final class NewsCommentModel extends Model {

    final public function getInfo($id) {
        $key = 'bx_news_comment_' . $id;
        $result = S($key);
        if (!$result) {
            $result = $this->where(array('id' => $id))->find();
            if ($result) {
                S($key, $result);
            }
        }
        return $result;
    }

    /**
     * 根据指定条数取列表
     * @param number $page
     * @param number $limit
     */
    final public function getList($where = array(), $limit = 10, $order = 'addtime desc') {
        $data = $this->where($where)->order(array($order))->limit($limit)->select();
        foreach ($data as $key => $value) {
            $data[$key]['title'] = D('News')->getFieldName($value['news_id'], 'title');
        }
        return $data;
    }

    final public function getPageList($where = array(), $page = 1, $limit = 10, $showpage = 'showWebPage', $showid = '', $scriptName = '', $order = 'addtime desc') {
        $data['total'] = $this->where($where)->count();
        if ($data['total'] > 0) {
            $data['list'] = $this->where($where)->order($order)->page($page, $limit)->select();
            foreach ($data['list'] as $key => $value) {
                if ((time() - $value['addtime']) < 3600 * 24) {
                    $data['list'][$key]['upc'] = 1;
                }
                $data['list'][$key]['title'] = D('News')->getFieldName($value['news_id'], 'title');
                $data['list'][$key]['userinfo'] = D('User')->getInfo($value['uid']);
            }
            $data['pagehtml'] = $showpage($data['total'], $limit, $showid, $scriptName);
        }
        return $data;
    }

    /**
     * 根据id 、评论类型获取评论
     * @param type $id
     * @param type $type  评论类型
     * @param type $page
     * @param type $limit
     * @param type $showpage
     * @param type $showid
     * @param type $scriptName
     * @param type $order
     * @return type
     */
    final public function getPageListById($id, $type, $page = 1, $limit = 10, $showpage = 'showWebPage', $showid = '', $scriptName = '', $order = 'addtime desc') {
        $data = $this->getPageList(array('news_id' => intval($id), 'type_id' => $type), $page, $limit, $showpage, $showid, $scriptName, $order);
        return $data;
    }

    final public function dataCount($where = array()) {
        return $this->where($where)->count();
    }

}

?>