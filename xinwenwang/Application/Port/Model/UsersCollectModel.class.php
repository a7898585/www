<?php

namespace Port\Model;
use Think\Model;

final class UsersCollectModel extends Model {

    final public function addCollect($uid,$news_id){
        $data = array(
            'uid'=>$uid,
            'news_id'=>$news_id,
            'add_time'=>time()
        );
        M('UsersCollect')->data($data)->add();
    }
    final public function checkCollect($uid,$news_id){
        return $this->where(array('uid'=>$uid,'news_id'=>$news_id))->count();
    }
    final public function delCollect($uid,$news_id){
        return $this->where(array('uid'=>$uid,'news_id'=>$news_id))->delete();
    }
    final public function getList($uid,$page,$limit){
        $list = M('UsersCollect')->where(array('uid'=>$uid))->page($page,$limit)->select();
        return $list;
    }

}
?>