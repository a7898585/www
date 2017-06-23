<?php

namespace Home\Model;

use Think\Model;

final class UsersCollectModel extends Model {

    final public function addCollect($news_id, $uid) {
        M('UsersCollect')->startTrans();
        $temp = M('UsersCollect')->data(array('uid' => $uid, 'news_id' => $news_id))->add();
        if (!$temp && !M('UsersCollect')->getDbError()) {
            M('UsersCollect')->rollback();
            return false;
        }
        D('News')->updCollectSum($news_id);
        M('UsersCollect')->commit();
        return true;
    }

    final public function delCollect($news_id, $collect_id) {

        $temp = M('UsersCollect')->where(array('id' => $collect_id))->delete();
        if (!$temp && !M('UsersCollect')->getDbError()) {
            return false;
        }
        D('News')->updCollectSum($news_id, '');
        return true;
    }

    final public function checkCollect($news_id, $uid) {
        return M('UsersCollect')->where(array('uid' => $uid, 'news_id' => $news_id))->getField('id');
    }

    final public function getMyCollect($uid, $page = 1, $limit = '10') {
        $total = $this->where(array('uid' => $uid))->count();
        if ($total > 0) {
            $list = $this->field('id as c_id,news_id')->where(array('uid' => $uid))->select();
            foreach ($list as $k => $value) {
                $strArr = D('News')->field("id,title,source_name,dingyue_id, Concat('/r',id) as url")->where(array('id' => $value['news_id']))->find();
                $value = array_merge($value, $strArr);
                $list[$k] = $value;
            }
        }
//        print_r($list);
        $r['page_html'] = showHomePage($total, $limit);
        $r['list'] = $list;
        return $r;
    }

}

?>