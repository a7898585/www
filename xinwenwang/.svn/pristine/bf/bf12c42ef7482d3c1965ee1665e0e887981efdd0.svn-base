<?php

namespace Port\Model;
use Think\Model;

final class NewsVotingModel extends Model {

    final public function addVoting($uid,$news_id,$type){
        $data = array(
            'uid'=>$uid,
            'news_id'=>$news_id,
            'param'=>$type,
            'add_time'=>time()
        );
        return $this->add($data);
    }

    final public function delVoting($uid,$news_id){
        return $this->where(array('uid'=>$uid,'news_id'=>$news_id))->delete();
    }

    final public function checkVoting($uid,$news_id){
        return $this->where(array('uid'=>$uid,'news_id'=>$news_id))->getField('id');
    }
}
?>