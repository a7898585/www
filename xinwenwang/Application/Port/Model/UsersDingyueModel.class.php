<?php

namespace Port\Model;
use Think\Model;

final class UsersDingyueModel extends Model {
    final public function getList($uid){
        $list = $this->where(array('uid'=>$uid))->order('id desc')->getField('sid',true);
        $ids = implode(',',$list);
        if($ids){
            $list = M('Dingyue')->field('id,name,pic,url')->where(array('id'=>array('exp',' IN ('.$ids.') ')))->select();
            foreach($list as &$item){
                $item['update_time'] =fdate(time());
            }
        }
        return $list;
    }
}
?>